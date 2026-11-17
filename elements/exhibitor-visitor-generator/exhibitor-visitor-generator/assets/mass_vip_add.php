<?php
// Import gości wystawców do Gravity Forms: klasy do użycia z dowolnego miejsca + endpoint HTTP.
//
// JAK TO DZIAŁA
//   1. test_get_data.php sprawdza, że przyszedł poprawny JSON (max 20 000 wierszy), zapisuje go bez zmian jako plik
//      uploads/generator_wystawcow/odłożone/<batch_id>.json i budzi mass_vip_add.php małym żądaniem z samą nazwą pliku.
//   2. Endpoint HTTP na dole tego pliku zakłada znacznik <plik>.lock (jeszcze przed załadowaniem WordPressa) i wywołuje
//      Importer::processFile(): importer czyta dane, sprawdza je, dodaje gości do aktywnego formularza
//      "(ROK) Rejestracja gości wystawców" (ROK = bieżący lub następny), zapisuje ID wpisów w kolejce (status 'new'),
//      dopisuje statystyki do uploads/generator_wystawcow/wysylki.jsonl i na koniec USUWA plik razem ze znacznikiem.
//   3. Znacznik .lock to TRWAŁY znacznik: jeśli import się nie dokończy (błąd, timeout, zabicie procesu), plik zostaje
//      z .lock, a cron go pomija i przechodzi do następnych (jeden problematyczny plik nie zatrzymuje reszty).
//      Pliki ze znacznikiem sprawdza poranny mail; po obsłużeniu ręcznym usuwasz .lock i plik pójdzie jeszcze raz.
//   4. Przy KAŻDYM przetwarzaniu każdy wiersz jest najpierw sprawdzany w GF (to samo imię + firma + e-mail): już dodani są
//      pomijani. Dzięki temu ponowne wysłanie tej samej listy (albo listy z nakładającymi się adresami) niczego nie dubluje.
//
// FORMAT DANYCH (JSON od strony)
//   v2 (zalecany): {"schema_version":2, "file_name":"x.xlsx",
//                   "rows":[{"row":1,"cells":["Imię i nazwisko","Firma","E-mail"]}, {"row":2,"cells":["Jan Kowalski","ABC","jan@abc.pl"]}]}
//      "cells" to SUROWE wartości komórek (tekst, liczby, null), bez sprawdzania po stronie przeglądarki. Serwer sam
//      znajduje wiersz nagłówka i kolumny (RowsParser), pomija puste wiersze i dodatkowe kolumny.
//   v1 (dotychczasowy): {"schema_version":1, "file_name":"x.csv", "guests":[{"row":2,"full_name":"..","inviting_company":"..","email":".."}]}
//   Oba formaty mogą mieć pole "language": "pl" albo "en" (wersja strony; wszystko poza "en" = "pl"). Trafia do ukrytego pola Lang w GF; bez pola wpis dostaje "pl".
//
// UŻYCIE Z INNEGO PLIKU (WordPress musi być już załadowany):
//
//   require_once '/ścieżka/do/mass_vip_add.php';   // samo wczytanie niczego nie wykonuje
//   use PWE\GuestImport\Importer;
//
//   $importer = Importer::make();
//   $store    = $importer->pendingStore();
//   foreach ($store->available(120) as $file) {       // pliki BEZ .lock, starsze niż 120 s
//       $result = $importer->processFile($file);      // po porażce zostawia .lock
//       // $result: null = plik zajęty/nieistnieje; $result->problem, ->failed, ->alreadyAdded, ->rejectedTotal...
//   }
//   $stuck     = $store->stuck(3600);      // pliki z .lock starszym niż godzina (problematyczne): do porannego maila
//   $untouched = $store->available(3600);  // pliki bez .lock, których nikt nie ruszył
//
//   albo bezpośrednio:  $result = Importer::make()->process($guests, ['file_name' => 'x.csv']);
//
// Niczego nie wysyła dalej i nie pisze do error_log.

namespace PWE\GuestImport;

/**
 * Wynik jednego przetworzenia (zwracany przez Importer::process).
 */
class Result
{
    /** @var string */
    public $batchId = '';
    /** @var string */
    public $company = '';
    /** @var int ile wierszy z danymi dostał importer */
    public $received = 0;
    /** @var int adresy, które przeszły wszystkie sprawdzenia i zostały dodane teraz */
    public $good = 0;
    /** @var int dodane do GF */
    public $added = 0;
    /** @var int ID zapisane w kolejce */
    public $queued = 0;
    /** @var int błędy dodawania do GF */
    public $failed = 0;
    /** @var int ile wierszy pominięto, bo już są w GF (to samo imię + firma + e-mail) */
    public $alreadyAdded = 0;
    /** @var array odrzuceni: row, email, code, note (tylko pierwsze maxRejectedLogged, żeby nie rosło w pamięci) */
    public $rejected = [];
    /** @var int ile adresów odrzucono łącznie (także tych, których nie ma już w $rejected) */
    public $rejectedTotal = 0;
    /** @var string niepusty = przetwarzanie nie doszło do skutku (np. brak formularza) */
    public $problem = '';

    public function ok(): bool
    {
        return $this->problem === '';
    }
}

/**
 * Kody odrzucenia adresów.
 */
class Reject
{
    public const EMPTY_EMAIL    = 1;   // pusty adres
    public const BAD_FORMAT     = 2;   // zły format
    public const DUPLICATE      = 3;   // duplikat w pliku
    public const BAD_ROW        = 4;   // uszkodzony wiersz
    public const DOMAIN         = 5;   // nie przeszedł walidatora e-mail
    public const ALREADY_IN_GF  = 6;   // adres jest już w formularzu zbyt wiele razy
    public const FORMULA        = 7;   // komórka zaczyna się od = + lub @ albo jest formułą: wiersz pominięty
    public const BAD_NAME       = 8;   // imię zaczyna się od znaku specjalnego albo zawiera < > " lub znak sterujący
}

/**
 * Ochrona katalogu przed odczytem z przeglądarki (Apache): .htaccess z zakazem i pusty index.php.
 */
class DirGuard
{
    public static function protect(string $dir): void
    {
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        if (!is_file($dir . '/.htaccess')) {
            @file_put_contents(
                $dir . '/.htaccess',
                "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n"
            );
        }
        if (!is_file($dir . '/index.php')) {
            @file_put_contents($dir . '/index.php', "<?php // Silence is golden.\n");
        }
    }
}

/**
 * Wspólny plik statystyk (JSON Lines) w uploads/generator_wystawcow/ wraz z ochroną katalogu przed odczytem.
 */
class StatsLog
{
    private $dir;
    private $file;
    private $batchId;
    private $legend;

    public function __construct(string $dir, string $batchId, int $maxExisting, int $maxRejectedLogged)
    {
        $this->dir     = $dir;
        $this->file    = $dir . '/wysylki.jsonl';
        $this->batchId = $batchId;
        $this->legend  = self::buildLegend($maxExisting, $maxRejectedLogged);
    }

    public function dir(): string
    {
        return $this->dir;
    }

    /** Tworzy katalog oraz .htaccess (zakaz odczytu) i index.php, jeśli ich brakuje. */
    public function protect(): void
    {
        DirGuard::protect($this->dir);
    }

    /**
     * Dopisuje jedną linię JSON. LOCK_EX chroni przed pomieszaniem linii przy równoległych zgłoszeniach.
     * Przy pustym pliku najpierw zapisuje legendę. Do każdej linii dodaje zużycie pamięci.
     */
    public function event(string $event, array $data = [], bool $separator = false): void
    {
        $this->protect();
        $memory = [
            'mem_mb'    => round(memory_get_usage(true) / 1048576, 1),
            'peak_mb'   => round(memory_get_peak_usage(true) / 1048576, 1),
            'mem_limit' => (string) ini_get('memory_limit'),
        ];
        $line = json_encode(
            ['batch_id' => $this->batchId, 'event' => $event, 'time' => date('c')] + $data + $memory,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $fh = @fopen($this->file, 'ab');
        if ($fh === false) {
            return;
        }
        if (flock($fh, LOCK_EX)) {
            $stat = fstat($fh);
            if ($stat !== false && $stat['size'] === 0) {
                fwrite($fh, $this->legend . "\n");
            }
            // Pusta linia przed pierwszym wpisem wysyłki oddziela ją od poprzedniej (łatwiej czytać). Czytniki JSONL ją pomijają.
            fwrite($fh, ($separator ? "\n" : '') . $line . "\n");
            fflush($fh);
            flock($fh, LOCK_UN);
        }
        fclose($fh);
    }

    private static function buildLegend(int $maxExisting, int $maxRejectedLogged): string
    {
        return (string) json_encode([
            'event'           => 'legend',
            'info'            => 'Jedna linia = jedno zdarzenie (JSON). Wpisy jednej wysyłki łączy batch_id (= nazwa pliku w odłożone/), a pusta linia oddziela kolejne wysyłki. Wysyłka zakończona poprawnie kończy się linią gf_added; jeśli jej brak, import trwa albo został przerwany (plik zostaje w odłożone/ ze znacznikiem .lock, cron takich plików nie rusza, dopóki ktoś nie usunie .lock; wtedy ten sam batch_id pojawi się ponownie).',
            'events'          => [
                'received'      => 'Dane odebrane: sent = ile wierszy z danymi (po odrzuceniu nagłówka i pustych wierszy); columns = jak rozpoznano kolumny w pliku (numery od 0, header_row = numer wiersza nagłówka lub null gdy go nie było).',
                'validated'     => 'Wynik wszystkich sprawdzeń: good = adresy, które je przeszły (idą do GF), bad = pominięte; rejected = lista pominiętych (ucięta do ' . $maxRejectedLogged . ', rejected_truncated = ile pominięto w liście); already_added = ile wierszy pominięto, bo już są w GF (to samo imię + firma + e-mail).',
                'gf_added'      => 'gf_added = dodane do formularza GF, queued = ID zapisane w kolejce (status new), gf_failed = błędy dodawania. Ostatnia linia poprawnie zakończonej wysyłki.',
                'mails_sent_ok' => 'Dopisywane później przez skrypt wysyłający: ile maili wysłano poprawnie.',
                'problem'       => 'Coś zawiodło w imporcie (opis w polu problem).',
            ],
            'memory_fields'   => [
                'mem_mb'    => 'pamięć zajmowana przez skrypt w chwili zapisu tej linii (MB)',
                'peak_mb'   => 'największe zużycie pamięci skryptu do tej linii (MB)',
                'mem_limit' => 'limit pamięci PHP na serwerze (memory_limit)',
            ],
            'rejected_fields' => [
                'row'   => 'numer wiersza w pliku wystawcy (taki jak w arkuszu)',
                'email' => 'adres e-mail taki, jak przyszedł (przy kodzie 7 może to być początek formuły)',
                'code'  => 'kod powodu pominięcia (patrz rejected_codes)',
                'note'  => 'dodatkowy opis (np. komunikat walidatora e-mail przy kodzie 5, której kolumny dotyczy formuła przy kodzie 7)',
            ],
            'rejected_codes'  => [
                '1' => 'pusty adres e-mail',
                '2' => 'zły format adresu e-mail',
                '3' => 'duplikat w pliku wystawcy: wiersz o tym samym imieniu, firmie i e-mailu (ten sam e-mail z innym imieniem lub firmą nie jest duplikatem)',
                '4' => 'wiersz w pliku nie jest poprawnym wierszem (uszkodzony)',
                '5' => 'nie przeszedł walidatora e-mail: literówka w domenie popularnego dostawcy lub domena nie istnieje',
                '6' => $maxExisting > 0
                    ? 'adres jest już w formularzu GF co najmniej ' . $maxExisting . ' razy (limit zaproszeń na jeden adres)'
                    : 'adres jest już w formularzu GF zbyt wiele razy (obecnie sprawdzanie wyłączone: max = 0, kod 6 nie występuje)',
                '7' => 'imię, firma lub e-mail zaczyna się od = + lub @ albo jest formułą lub wartością złożoną: wiersz pominięty bez żadnego czyszczenia (wystawca powinien wkleić wartość jako zwykły tekst)',
                '8' => 'imię zaczyna się od znaku specjalnego (musi od litery lub cyfry) albo zawiera < > " lub znak sterujący (np. tabulator, nowa linia); wiersz pominięty (w note początek imienia). Pozostałe znaki w imieniu (apostrof, kropka, myślnik, przecinek itd.) są dozwolone',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

/**
 * Pliki czekające na przetworzenie (katalog odłożone/ wewnątrz generator_wystawcow/).
 *
 * Plik <nazwa>.json to jedna wysyłka od wystawcy. Obok niego może leżeć <nazwa>.json.lock: TRWAŁY znacznik
 * "ktoś to przetwarza albo przetwarzanie się nie udało". Istnienie znacznika blokuje plik (nikt inny go nie weźmie).
 * Znacznik znika tylko po udanym przetworzeniu (razem z plikiem) albo gdy ktoś go usunie ręcznie, żeby plik poszedł
 * jeszcze raz. Dzięki temu problematyczny plik nie blokuje pozostałych: cron bierze tylko pliki bez znacznika.
 */
class PendingStore
{
    public const DIR = 'odłożone';

    /** Nazwa pliku bez .json: <data>_<godzina>_<hex>. */
    public const NAME_PATTERN = '/^\d{8}_\d{6}_[a-f0-9]{6}$/';

    private $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
    }

    /** Katalog generator_wystawcow w uploads, wyliczony z DOCUMENT_ROOT (gdy WordPress nie jest jeszcze załadowany). */
    public static function baseDir(string $documentRoot): string
    {
        return str_replace('private_html', 'public_html', $documentRoot) . '/wp-content/uploads/generator_wystawcow';
    }

    public function dir(): string
    {
        return $this->dir;
    }

    /** Zapisuje dane (JSON) jako <name>.json. Najpierw plik .part, potem zmiana nazwy (nikt nie zobaczy połowy). */
    public function save(string $name, string $json): ?string
    {
        DirGuard::protect(dirname($this->dir));
        DirGuard::protect($this->dir);

        $path = $this->dir . '/' . $name . '.json';
        $part = $path . '.part';
        if (@file_put_contents($part, $json, LOCK_EX) === false) {
            @unlink($part);
            return null;
        }
        if (!@rename($part, $path)) {
            @unlink($part);
            return null;
        }
        return $path;
    }

    /** @return string[] wszystkie pliki .json w katalogu (ze znacznikiem lub bez), od najstarszego */
    public function pending(): array
    {
        $files = glob($this->dir . '/*.json') ?: [];
        sort($files);
        return $files;
    }

    /**
     * Pliki DO PRZETWORZENIA: bez znacznika .lock.
     * @param int $minAgeSeconds pomija pliki młodsze niż tyle sekund (świeżo zapisane obsługuje ich własne wybudzenie)
     * @return string[] od najstarszego
     */
    public function available(int $minAgeSeconds = 0): array
    {
        return array_values(array_filter($this->pending(), static function (string $f) use ($minAgeSeconds): bool {
            return !is_file($f . '.lock') && (int) @filemtime($f) <= time() - $minAgeSeconds;
        }));
    }

    /**
     * Pliki PROBLEMATYCZNE: ze znacznikiem .lock starszym niż podana liczba sekund (przetwarzanie trwa za długo
     * albo się nie udało, np. timeout). Młodszy znacznik oznacza, że ktoś właśnie pracuje.
     * @return string[] od najstarszego
     */
    public function stuck(int $minAgeSeconds = 3600): array
    {
        return array_values(array_filter($this->pending(), static function (string $f) use ($minAgeSeconds): bool {
            $lock = $f . '.lock';
            return is_file($lock) && (int) @filemtime($lock) <= time() - $minAgeSeconds;
        }));
    }

    /** Zakłada znacznik .lock. Zwraca false, gdy już istnieje (ktoś przetwarza albo plik jest problematyczny). */
    public function claim(string $path): bool
    {
        $handle = @fopen($path . '.lock', 'x');   // 'x' = utwórz, a jeśli już jest: błąd (bez wyścigu)
        if ($handle === false) {
            return false;
        }
        fwrite($handle, getmypid() . ' ' . time());
        fclose($handle);
        return true;
    }

    /** Zdejmuje znacznik .lock (plik wraca do kolejki). */
    public function release(string $path): void
    {
        @unlink($path . '.lock');
    }

    /** @return array|null zdekodowany JSON z kluczem 'rows' (v2) albo 'guests' (v1), obydwa jako tablica */
    public function load(string $path): ?array
    {
        $raw  = @file_get_contents($path);
        $data = $raw === false ? null : json_decode($raw, true);
        if (!is_array($data)) {
            return null;
        }
        $hasRows   = isset($data['rows']) && is_array($data['rows']);
        $hasGuests = isset($data['guests']) && is_array($data['guests']);
        return ($hasRows || $hasGuests) ? $data : null;
    }

    /** Całość obsłużona: usuwa plik danych i znacznik. */
    public function finish(string $path): void
    {
        @unlink($path);
        @unlink($path . '.lock');
    }
}

/**
 * Zamienia surowe wiersze z arkusza (format v2) na listę gości: sam znajduje wiersz nagłówka i kolumny.
 * Niczego tu nie "naprawia" ani nie odrzuca: formuły, złe adresy i duplikaty ocenia Importer::process().
 */
class RowsParser
{
    private const ALIASES = [
        'full_name'        => ['imieinazwisko', 'imienazwisko', 'fullname', 'nameandsurname', 'guestfullname', 'name', 'gosc', 'guest'],
        'inviting_company' => ['firmazapraszajaca', 'invitingcompany', 'company', 'firma', 'nazwafirmy'],
        'email'            => ['adresemail', 'email', 'emailaddress', 'guestemail', 'emailosobyzapraszanej', 'mail'],
    ];

    /**
     * @param array $rows lista ['row' => numer wiersza w arkuszu, 'cells' => [wartości komórek...]]
     * @return array ['guests' => [...], 'columns' => ['header_row' => ?int, 'name' => ?int, 'company' => ?int, 'email' => int]]
     * @throws \RuntimeException gdy nie da się wskazać kolumny z adresami e-mail
     */
    public static function parse(array $rows): array
    {
        $items = [];
        foreach ($rows as $i => $r) {
            $items[] = [
                'row'   => (is_array($r) && isset($r['row'])) ? (int) $r['row'] : ((int) $i + 1),
                'cells' => (is_array($r) && isset($r['cells']) && is_array($r['cells'])) ? array_values($r['cells']) : null,
            ];
        }

        // 1. Wiersz nagłówka: w pierwszych 10 niepustych wierszach ten, który ma kolumnę z e-mailem wg aliasów.
        $headerAt = null;
        $map      = [];
        $checked  = 0;
        foreach ($items as $k => $it) {
            if ($it['cells'] === null || self::blankRow($it['cells'])) {
                continue;
            }
            if (++$checked > 10) {
                break;
            }
            $found = [];
            foreach ($it['cells'] as $col => $cell) {
                if (!is_string($cell)) {
                    continue;
                }
                $key = self::key($cell);
                foreach (self::ALIASES as $field => $aliases) {
                    if (!isset($found[$field]) && in_array($key, $aliases, true)) {
                        $found[$field] = $col;
                    }
                }
            }
            if (isset($found['email'])) {
                $headerAt = $k;
                $map      = $found;
                break;
            }
        }

        $dataItems = $headerAt !== null ? array_slice($items, $headerAt + 1) : $items;

        // 2. Bez nagłówka: kolumna z adresami to ta, w której najwięcej komórek zawiera "@"; reszta wg kolejności.
        if ($headerAt === null) {
            $counts = [];
            $limit  = 200;
            foreach ($dataItems as $it) {
                if ($it['cells'] === null || self::blankRow($it['cells'])) {
                    continue;
                }
                foreach ($it['cells'] as $col => $cell) {
                    if (is_string($cell) && strpos($cell, '@') !== false) {
                        $counts[$col] = ($counts[$col] ?? 0) + 1;
                    }
                }
                if (--$limit <= 0) {
                    break;
                }
            }
            if (!$counts) {
                throw new \RuntimeException('Nie znaleziono kolumny z adresami e-mail (brak nagłówka i żadnej komórki z @).');
            }
            arsort($counts);
            $emailCol = (int) array_key_first($counts);
            $others   = array_values(array_diff([0, 1, 2], [$emailCol]));
            $map      = ['email' => $emailCol, 'full_name' => $others[0] ?? null, 'inviting_company' => $others[1] ?? null];
        }

        // 3. Lista gości: puste wiersze pomijamy po cichu, uszkodzone oznaczamy dla Importera.
        $guests = [];
        foreach ($dataItems as $it) {
            if ($it['cells'] === null) {
                $guests[] = ['row' => $it['row'], '_bad' => true];
                continue;
            }
            if (self::blankRow($it['cells'])) {
                continue;
            }
            $g = ['row' => $it['row']];
            foreach (['full_name', 'inviting_company', 'email'] as $field) {
                $col       = $map[$field] ?? null;
                $g[$field] = ($col !== null && array_key_exists($col, $it['cells'])) ? self::text($it['cells'][$col]) : '';
            }
            $guests[] = $g;
        }

        // Pusta firma w wierszu = firma z pierwszego wiersza, który ją ma (jeden plik to jedna firma zapraszająca).
        $defaultCompany = '';
        foreach ($guests as $g) {
            $c = isset($g['inviting_company']) ? trim($g['inviting_company']) : '';
            if ($c !== '' && !preg_match('/^[=+@]/', $c)) {
                $defaultCompany = $c;
                break;
            }
        }
        if ($defaultCompany !== '') {
            foreach ($guests as &$g) {
                if (isset($g['inviting_company']) && trim($g['inviting_company']) === '') {
                    $g['inviting_company'] = $defaultCompany;
                }
            }
            unset($g);
        }

        return [
            'guests'  => $guests,
            'columns' => [
                'header_row' => $headerAt !== null ? $items[$headerAt]['row'] : null,
                'name'       => $map['full_name'] ?? null,
                'company'    => $map['inviting_company'] ?? null,
                'email'      => $map['email'],
            ],
        ];
    }

    /** Wartość komórki jako tekst. Tablice i obiekty (np. formuła przekazana jako obiekt) dają tekst zaczynający się od "=". */
    private static function text($value): string
    {
        if ($value === null || is_bool($value)) {
            return '';
        }
        if (is_float($value) && floor($value) === $value && abs($value) < 1e15) {
            return (string) (int) $value;
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        return '=[formuła lub wartość złożona]';
    }

    private static function blankRow(array $cells): bool
    {
        foreach ($cells as $cell) {
            if (!($cell === null || $cell === false || (is_string($cell) && trim($cell) === ''))) {
                return false;
            }
        }
        return true;
    }

    /** Klucz porównania nagłówka: małe litery, bez polskich znaków i znaków innych niż a-z0-9. */
    private static function key(string $value): string
    {
        $value = strtr(mb_strtolower($value, 'UTF-8'), ['ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z']);
        return (string) preg_replace('/[^a-z0-9]/', '', $value);
    }
}

/**
 * Walidator e-mail z wtyczki (ta sama walidacja co w formularzach) z pamięcią wyników w plikach tekstowych, żeby znanych
 * domen nie sprawdzać przez DNS za każdym razem. Ręczny plik stały leży obok klasy walidatora
 * (wp-content/plugins/pwe-elements-auto-switch/addons/email-validator/, patrz Importer::domainDir()), a pliki dopisywane
 * przez skrypt w uploads/generator_wystawcow/domeny/ (chronionym przed odczytem z przeglądarki):
 *   domeny_stale.txt                  domeny poprawne NA STAŁE (lista ręczna, np. gmail.com); jedna w linii, # = komentarz
 *   domeny_ok_RRRRMMDD_GGMMSS.txt     domeny poprawne sprawdzone przez DNS; plik ważny 30 dni od daty w nazwie
 *   domeny_zle_RRRRMMDD_GGMMSS.txt    domeny błędne ("domena<TAB>komunikat"); plik ważny 1 dzień od daty w nazwie
 * Wiek pliku wynika z jego NAZWY (data modyfikacji nie nadaje się, bo zmienia się przy każdym dopisaniu). Przeterminowany
 * plik jest kasowany, a przy następnym dopisaniu powstaje nowy. Skrypty działające równolegle tylko dopisują linie na
 * końcu (LOCK_EX), więc nie przeszkadzają sobie; powtórzona domena nie szkodzi. Dodatkowo wynik jest pamiętany w pamięci
 * na czas jednego importu.
 */
class DomainChecker
{
    public const DIR         = 'domeny';     // podkatalog uploads/generator_wystawcow z plikami dopisywanymi przez skrypt
    public const OK_SECONDS  = 30 * 86400;   // jak długo ważny jest plik z domenami poprawnymi
    public const BAD_SECONDS = 86400;        // jak długo ważny jest plik z domenami błędnymi

    private $dir;              // katalog pliku stałego (obok klasy walidatora)
    private $cacheDir;         // katalog plików domeny_ok_* i domeny_zle_* (uploads)
    private $stable  = [];     // domena => true (plik stały)
    private $okFile  = [];     // domena => true (plik poprawnych)
    private $badFile = [];     // domena => komunikat (plik błędnych)
    private $okPath  = null;   // plik, do którego dopisujemy poprawne
    private $badPath = null;   // plik, do którego dopisujemy błędne
    private $ok      = [];     // pamięć jednego importu: domena => bool
    private $note    = [];     // pamięć jednego importu: domena => komunikat
    private $field;

    public function __construct(string $stableDir, string $cacheDir)
    {
        $this->dir      = rtrim($stableDir, '/\\');
        $this->cacheDir = rtrim($cacheDir, '/\\');
        $this->load();

        if (!class_exists('PWE_Email_Validator_Addon')) {
            $file = WP_PLUGIN_DIR . '/pwe-elements-auto-switch/addons/email-validator/email-validator.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
        if (!class_exists('PWE_Email_Validator_Addon')) {
            throw new \RuntimeException('Nie znaleziono walidatora e-mail (PWE_Email_Validator_Addon).');
        }
        // Walidator oczekuje pola GF (typ 'email' i klasa CSS 'pwe-email-validate'), więc dajemy mu atrapę.
        $this->field = new class {
            public $cssClass = 'pwe-email-validate';
            public function get_input_type() { return 'email'; }
        };
    }

    /** @return array [bool czy domena przeszła, string komunikat walidatora] */
    public function check(string $email): array
    {
        $domain = strtolower(substr((string) strrchr($email, '@'), 1));

        if (!isset($this->ok[$domain])) {
            if (isset($this->stable[$domain]) || isset($this->okFile[$domain])) {
                $this->ok[$domain]   = true;     // znana domena poprawna: bez DNS
                $this->note[$domain] = '';
            } elseif (isset($this->badFile[$domain])) {
                $this->ok[$domain]   = false;    // znana domena błędna (z ostatniej doby): bez DNS
                $this->note[$domain] = $this->badFile[$domain];
            } else {
                [$ok, $note] = $this->validate($email);
                if (!$ok) {
                    // Brak odpowiedzi DNS bywa chwilowy, a błędna domena trafia do pliku na całą dobę, więc przed
                    // zapisem sprawdzamy ją jeszcze raz (kosztuje tylko przy domenach, które wyszły jako błędne).
                    [$ok, $note] = $this->validate($email);
                }
                $this->ok[$domain]   = $ok;
                $this->note[$domain] = $note;
                if ($ok) {
                    $this->okFile[$domain] = true;
                    $this->remember('ok', $domain);
                } else {
                    $this->badFile[$domain] = $note;
                    $this->remember('zle', $domain . "\t" . str_replace(["\t", "\r", "\n"], ' ', $note));
                }
            }
        }
        return [$this->ok[$domain], $this->note[$domain]];
    }

    /** Jedno sprawdzenie walidatorem wtyczki (literówki w popularnych domenach + DNS). */
    private function validate(string $email): array
    {
        $result = \PWE_Email_Validator_Addon::validate_email_domain(
            ['is_valid' => true, 'message' => ''],
            $email,
            [],
            $this->field
        );
        $ok = !empty($result['is_valid']);
        return [$ok, $ok ? '' : trim(wp_strip_all_tags((string) ($result['message'] ?? '')))];
    }

    /** Wczytuje plik stały oraz ważne pliki poprawnych i błędnych domen (przeterminowane kasuje). */
    private function load(): void
    {
        $stable = $this->dir . '/domeny_stale.txt';
        if (is_file($stable)) {
            foreach (file($stable, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $line = strtolower(trim($line));
                if ($line !== '' && $line[0] !== '#') {
                    $this->stable[$line] = true;
                }
            }
        }
        $this->okPath = $this->loadDated('ok', self::OK_SECONDS, function (string $line): void {
            $line = strtolower(trim($line));
            if ($line !== '') {
                $this->okFile[$line] = true;
            }
        });
        $this->badPath = $this->loadDated('zle', self::BAD_SECONDS, function (string $line): void {
            $parts  = explode("\t", $line, 2);
            $domain = strtolower(trim($parts[0]));
            if ($domain !== '') {
                $this->badFile[$domain] = $parts[1] ?? '';
            }
        });
    }

    /**
     * Czyta ważne pliki domeny_<typ>_RRRRMMDD_GGMMSS.txt (wiek z nazwy), przeterminowane kasuje.
     * @return string|null najnowszy ważny plik (tam dopisujemy dalsze wyniki) albo null, gdy nie ma żadnego
     */
    private function loadDated(string $type, int $ttl, callable $each): ?string
    {
        $current = null;
        $files   = glob($this->cacheDir . '/domeny_' . $type . '_*.txt') ?: [];
        sort($files);
        foreach ($files as $file) {
            if (!preg_match('/domeny_' . $type . '_(\d{8})_(\d{6})\.txt$/', $file, $m)) {
                continue;
            }
            $created = \DateTime::createFromFormat('YmdHis', $m[1] . $m[2]);
            if ($created === false || $created->getTimestamp() + $ttl <= time()) {
                @unlink($file);   // przeterminowany: kasujemy, nowy powstanie przy następnym dopisaniu
                continue;
            }
            foreach (file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $each($line);
            }
            $current = $file;
        }
        return $current;
    }

    /** Dopisuje linię na końcu bieżącego pliku danego typu (tworzy nowy, jeśli nie ma ważnego). */
    private function remember(string $type, string $line): void
    {
        $prop = $type === 'ok' ? 'okPath' : 'badPath';
        if ($this->$prop === null || !is_file($this->$prop)) {
            DirGuard::protect($this->cacheDir);   // uploads: .htaccess z zakazem odczytu i pusty index.php
            $this->$prop = $this->cacheDir . '/domeny_' . $type . '_' . date('Ymd_His') . '.txt';
        }
        @file_put_contents($this->$prop, $line . "\n", FILE_APPEND | LOCK_EX);
    }
}

/**
 * Aktywny formularz "(ROK) Rejestracja gości wystawców" i dodawanie do niego wpisów.
 */
class GuestForm
{
    public $id = 0;
    /** @var array ID pól: name, email, company, lang */
    public $fields = [];

    /** @throws \RuntimeException gdy nie ma dokładnie jednego aktywnego formularza lub brakuje pól */
    public static function locate(): self
    {
        if (!class_exists('GFAPI')) {
            throw new \RuntimeException('Gravity Forms nie jest załadowany.');
        }

        $yearNow = (int) wp_date('Y');
        $matches = [];
        foreach (\GFAPI::get_forms(true) as $form) {   // true = tylko aktywne formularze
            if (
                preg_match('/^\s*\((\d{4})\)\s*Rejestracja\s+gości\s+wystawców\s*$/iu', (string) $form['title'], $m)
                && in_array((int) $m[1], [$yearNow, $yearNow + 1], true)
            ) {
                $matches[] = $form;
            }
        }
        if (count($matches) !== 1) {
            // 0 = brak aktywnego formularza, >1 = niejednoznaczne. W obu przypadkach nic nie dodajemy.
            throw new \RuntimeException('Znaleziono formularzy: ' . count($matches) . ' (oczekiwany: 1).');
        }

        $form = $matches[0];
        $self = new self();
        $self->id = (int) $form['id'];

        // ID pól wg etykiety administratora: name, company, email, lang (lang = ukryte pole języka, zawsze "pl").
        foreach ($form['fields'] as $field) {
            $adminLabel = strtolower(trim((string) ($field['adminLabel'] ?? '')));
            if (in_array($adminLabel, ['name', 'company', 'email', 'lang'], true)) {
                $self->fields[$adminLabel] = $field['id'];
            }
        }
        // Zapasowo, gdy pola nie mają etykiety administratora: dopasowanie po widocznej etykiecie (jak w mass_vip.php).
        foreach ($form['fields'] as $field) {
            $label = strtolower((string) $field['label']);
            if (empty($self->fields['name']) && (strpos($label, 'full name') !== false || strpos($label, 'nazwisko') !== false)) {
                $self->fields['name'] = $field['id'];
            } elseif (empty($self->fields['email']) && strpos($label, 'e-mail') !== false) {
                $self->fields['email'] = $field['id'];
            } elseif (empty($self->fields['company']) && (strpos($label, 'firma') !== false || strpos($label, 'inviting company') !== false)) {
                $self->fields['company'] = $field['id'];
            }
        }
        if (empty($self->fields['name']) || empty($self->fields['email']) || empty($self->fields['company'])) {
            throw new \RuntimeException('W formularzu ID ' . $self->id . ' nie znaleziono pól: name, email, company.');
        }
        return $self;
    }

    /** Ile razy adres jest już w tym formularzu (tylko aktywne wpisy). */
    public function countEmail(string $email): int
    {
        $count = \GFAPI::count_entries($this->id, [
            'status'        => 'active',
            'field_filters' => [['key' => $this->fields['email'], 'value' => $email]],
        ]);
        return is_wp_error($count) ? 0 : (int) $count;
    }

    /**
     * Ile razy dokładnie ten wpis już jest w formularzu (wpisy aktywne): e-mail, firma ORAZ imię muszą się zgadzać.
     * Gdy imię jest puste, porównujemy tylko e-mail i firmę (wpis bez imienia nie ma w GF wartości imienia do porównania).
     */
    public function countSameEntry(string $name, string $email, string $company): int
    {
        $filters = [
            'mode' => 'all',
            ['key' => $this->fields['email'], 'value' => $email],
            ['key' => $this->fields['company'], 'value' => $company],
        ];
        if ($name !== '') {
            $filters[] = ['key' => $this->fields['name'], 'value' => $name];
        }
        $count = \GFAPI::count_entries($this->id, ['status' => 'active', 'field_filters' => $filters]);
        return is_wp_error($count) ? 0 : (int) $count;
    }

    /** Dodaje wpis do GF. Zwraca ID wpisu albo null przy błędzie. */
    public function add(string $name, string $email, string $company, string $lang = 'pl'): ?int
    {
        $entry = [
            'form_id'                => $this->id,
            $this->fields['name']    => $name,
            $this->fields['email']   => $email,
            $this->fields['company'] => $company,
        ];
        if (!empty($this->fields['lang'])) {
            $entry[$this->fields['lang']] = $lang === 'en' ? 'en' : 'pl';   // ukryte pole Lang: "pl" albo "en" (wersja strony, z której przyszedł plik)
        }
        $id = \GFAPI::add_entry($entry);
        return is_wp_error($id) ? null : (int) $id;
    }
}

/**
 * Kolejka wysyłki maili (jak w mass_vip.php): gf_entry_id + status ('new' = czeka na wysyłkę).
 */
class Queue
{
    private $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'mass_exhibitors_invite_query';
        if ($wpdb->get_var("SHOW TABLES LIKE '{$this->table}'") != $this->table) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta("CREATE TABLE {$this->table} (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                gf_entry_id BIGINT(20) UNSIGNED,
                status VARCHAR(20) DEFAULT 'new',
                PRIMARY KEY  (id)
            ) " . $wpdb->get_charset_collate() . ";");
        }
    }

    public function push(int $entryId): bool
    {
        global $wpdb;
        return (bool) $wpdb->insert($this->table, ['gf_entry_id' => $entryId, 'status' => 'new'], ['%d', '%s']);
    }
}

/**
 * Główna klasa: sprawdza gości i dodaje ich do GF. Opcje ustawia się łańcuchowo, przetwarzanie robi process()
 * (lista gości), processPayload() (dane w formacie v1 lub v2) albo processFile() (plik z katalogu odłożone/).
 * Wymaga załadowanego WordPressa i Gravity Forms.
 */
class Importer
{
    /** Imię wstawiane, gdy w pliku go brak. */
    public const DEFAULT_NAME = '';   // brak imienia = puste pole: szablon maila sam pisze "Pana/Panią" (wpisanie "Pan/i" dałoby "Pana/Panią Pan/i")

    private $maxExisting       = 0;      // 0 = nie sprawdzamy powtórzeń adresu w formularzu u różnych firm (kod 6 nie występuje)
    private $maxRejectedLogged = 500;    // ile pominiętych adresów najwyżej zapisujemy w statystykach
    private $baseDir           = null;   // domyślnie uploads/generator_wystawcow

    public static function make(): self
    {
        return new self();
    }

    public function maxExisting(int $max): self
    {
        $this->maxExisting = max(0, $max);
        return $this;
    }

    public function maxRejectedLogged(int $max): self
    {
        $this->maxRejectedLogged = max(0, $max);
        return $this;
    }

    public function baseDir(string $dir): self
    {
        $this->baseDir = rtrim($dir, '/\\');
        return $this;
    }

    /** Katalog statystyk: uploads/generator_wystawcow (wg wp_upload_dir, jeśli nie ustawiono baseDir()). */
    public function resolveDir(): string
    {
        if ($this->baseDir !== null) {
            return $this->baseDir;
        }
        $upload = wp_upload_dir();
        return (empty($upload['error']) ? $upload['basedir'] : WP_CONTENT_DIR . '/uploads') . '/generator_wystawcow';
    }

    /** Katalog ręcznego pliku domeny_stale.txt: obok klasy walidatora e-mail. Pliki dopisywane przez skrypt idą do uploads. */
    public function domainDir(): string
    {
        return WP_PLUGIN_DIR . '/pwe-elements-auto-switch/addons/email-validator';
    }

    public function pendingStore(): PendingStore
    {
        return new PendingStore($this->resolveDir() . '/' . PendingStore::DIR);
    }

    /**
     * Przetwarza plik z katalogu odłożone/: zakłada znacznik .lock (chyba że już założony), importuje i po CAŁOŚCI
     * usuwa plik i znacznik. Jeśli coś się nie dokończy (problem, błąd dodawania do GF, timeout, zabicie procesu),
     * plik zostaje ze znacznikiem .lock i cron go pomija, dopóki ktoś nie usunie znacznika.
     *
     * @param bool $keepMarkerOnFailure true (domyślnie) = po niepowodzeniu zostaje .lock (plik problematyczny).
     *                                  false = po niepowodzeniu, które nie zabiło procesu, znacznik jest zdejmowany,
     *                                  żeby cron spróbował jeszcze raz (zabicie procesu zawsze zostawia znacznik).
     * @param bool $alreadyClaimed      true = wywołujący sam już założył .lock (endpoint HTTP robi to przed ładowaniem WordPressa).
     *
     * Zwraca wynik albo null, gdy pliku nie ma albo ma znacznik (ktoś go przetwarza lub jest problematyczny).
     */
    public function processFile(string $path, bool $keepMarkerOnFailure = true, bool $alreadyClaimed = false): ?Result
    {
        $store = $this->pendingStore();
        if (!$alreadyClaimed && (!is_file($path) || !$store->claim($path))) {
            return null;
        }
        if (!is_file($path)) {   // nie ma pliku albo ktoś skończył w międzyczasie
            $store->release($path);
            return null;
        }

        $batchId = basename($path, '.json');
        if (!preg_match(PendingStore::NAME_PATTERN, $batchId)) {
            $batchId = '';   // nietypowa nazwa pliku: importer nada własny identyfikator
        }

        $data = $store->load($path);
        if ($data === null) {
            $result = $this->problemResult($batchId !== '' ? $batchId : basename($path), 'Plik jest uszkodzony albo nie zawiera listy rows ani guests.');
            if (!$keepMarkerOnFailure) {
                $store->release($path);
            }
            return $result;
        }

        $result = $this->processPayload($data, ['batch_id' => $batchId]);

        if ($result->ok() && $result->failed === 0) {
            $store->finish($path);   // wszystko dodane: usuwamy plik i znacznik
        } elseif (!$keepMarkerOnFailure) {
            $store->release($path);  // plik wraca do kolejki
        }
        return $result;
    }

    /**
     * Przetwarza dane w formacie v2 ('rows' z surowymi komórkami) albo v1 ('guests').
     * @param array $meta opcjonalnie: 'batch_id'; 'file_name' jest brane z danych
     */
    public function processPayload(array $data, array $meta = []): Result
    {
        // file_name tylko jako tekst (tablica lub obiekt dałyby ostrzeżenie "Array to string conversion").
        $meta['file_name'] = is_scalar($data['file_name'] ?? null) ? (string) $data['file_name'] : (string) (is_scalar($meta['file_name'] ?? null) ? $meta['file_name'] : '');
        $meta['language'] = is_scalar($data['language'] ?? null) ? (string) $data['language'] : (string) (is_scalar($meta['language'] ?? null) ? $meta['language'] : 'pl');   // wersja strony: "en" albo "pl"

        if (isset($data['rows']) && is_array($data['rows'])) {
            try {
                $parsed = RowsParser::parse($data['rows']);
            } catch (\RuntimeException $e) {
                $batchId = (isset($meta['batch_id']) && preg_match(PendingStore::NAME_PATTERN, (string) $meta['batch_id']))
                    ? (string) $meta['batch_id']
                    : date('Ymd_His') . '_' . bin2hex(random_bytes(3));
                return $this->problemResult($batchId, $e->getMessage());
            }
            $meta['columns'] = $parsed['columns'];
            return $this->process($parsed['guests'], $meta);
        }
        return $this->process((array) ($data['guests'] ?? []), $meta);
    }

    /** Zapisuje w statystykach problem, który uniemożliwił start, i zwraca wynik z opisem. */
    private function problemResult(string $batchId, string $message): Result
    {
        $result = new Result();
        $result->batchId = $batchId;
        $result->problem = $message;
        (new StatsLog($this->resolveDir(), $batchId, $this->maxExisting, $this->maxRejectedLogged))
            ->event('problem', ['problem' => $message], true);
        return $result;
    }

    /**
     * @param array $guests lista wierszy: ['row'?, 'full_name', 'email', 'inviting_company'?]
     * @param array $meta   opcjonalnie: 'file_name', 'batch_id' (stały identyfikator wysyłki), 'columns' (opis kolumn do logu)
     */
    public function process(array $guests, array $meta = []): Result
    {
        @set_time_limit(0);

        $result           = new Result();
        $result->batchId  = (isset($meta['batch_id']) && preg_match(PendingStore::NAME_PATTERN, (string) $meta['batch_id']))
            ? (string) $meta['batch_id']
            : date('Ymd_His') . '_' . bin2hex(random_bytes(3));   // łączy wszystkie wpisy jednej wysyłki
        $result->received = count($guests);

        $log = new StatsLog($this->resolveDir(), $result->batchId, $this->maxExisting, $this->maxRejectedLogged);

        // Wystawca = pierwsza niepusta, nie-formułowa firma w wierszach.
        foreach ($guests as $g) {
            $c = is_array($g) ? self::cleanText($g['inviting_company'] ?? '') : '';
            if ($c !== '' && !self::isFormula($c)) {
                $result->company = $c;
                break;
            }
        }

        // Pierwszy wpis wysyłki (z pustą linią przed nim): wystawca i ile wierszy wysłał (bez adresów e-mail).
        $log->event('received', [
            'company'   => $result->company,
            'file_name' => self::clean($meta['file_name'] ?? '', 200),
            'sent'      => $result->received,
        ] + (isset($meta['columns']) ? ['columns' => $meta['columns']] : []), true);

        try {
            $domains = new DomainChecker($this->domainDir(), $this->resolveDir() . '/' . DomainChecker::DIR);
            $form    = GuestForm::locate();
            $queue   = new Queue();
        } catch (\RuntimeException $e) {
            $result->problem = $e->getMessage();
            $log->event('problem', ['problem' => $result->problem]);
            return $result;
        }

        $seen   = [];   // wiersze (imię + firma + e-mail) już widziane w tym pliku
        $lang   = ((string) ($meta['language'] ?? 'pl') === 'en') ? 'en' : 'pl';   // język gości w GF: wersja strony, z której przyszedł plik
        $labels = ['full_name' => 'Imię i nazwisko', 'inviting_company' => 'Firma', 'email' => 'Adres e-mail'];

        // Kolejność sprawdzeń (od najtańszego): uszkodzony wiersz -> formuła -> imię -> format e-maila -> duplikat w pliku ->
        // walidator domeny (raz na domenę) -> czy ten wiersz (imię + firma + e-mail) już jest w GF (zapytanie do bazy) ->
        // [opcjonalnie: liczba wpisów adresu w formularzu] -> dodanie do GF.
        foreach ($guests as $index => $g) {
            $row = (is_array($g) && isset($g['row'])) ? (int) $g['row'] : ((int) $index + 2);   // w pliku nagłówek = wiersz 1

            if (!is_array($g) || !empty($g['_bad'])) {
                $this->reject($result, $row, '', Reject::BAD_ROW);
                continue;
            }

            $emailRaw = self::clean($g['email'] ?? '', 255);

            // Komórka z formułą (albo zaczynająca się od = + @) nie jest czyszczona: cały wiersz jest pomijany i zapisany jako błąd.
            $formulaIn = '';
            foreach ($labels as $field => $label) {
                if (array_key_exists($field, $g) && self::isFormulaValue($g[$field])) {
                    $formulaIn = $label;
                    break;
                }
            }
            if ($formulaIn !== '') {
                $this->reject($result, $row, $emailRaw, Reject::FORMULA, 'Kolumna: ' . $formulaIn . '.');
                continue;
            }

            // Imię: prawie każde przechodzi. Musi zaczynać się od litery lub cyfry (żadnych znaków specjalnych na początku)
            // i nie może zawierać < > " ani znaków sterujących; inaczej wiersz jest pomijany i zapisany jako błąd.
            // Puste imię jest dozwolone.
            $nameRaw = self::normalizeName($g['full_name'] ?? '');
            if ($nameRaw !== '' && !self::validName($nameRaw)) {
                $this->reject($result, $row, $emailRaw, Reject::BAD_NAME, 'Imię zaczyna się od znaku specjalnego albo zawiera < > " lub znak sterujący: ' . mb_substr($nameRaw, 0, 60) . '.');
                continue;
            }

            $email = strtolower($emailRaw);
            if ($email === '') {
                $this->reject($result, $row, $emailRaw, Reject::EMPTY_EMAIL);
                continue;
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->reject($result, $row, $emailRaw, Reject::BAD_FORMAT);
                continue;
            }
            $name    = $nameRaw !== '' ? mb_substr($nameRaw, 0, 150) : self::DEFAULT_NAME;   // brak imienia: puste pole (patrz DEFAULT_NAME)
            $company = self::cleanText($g['inviting_company'] ?? '');
            if ($company === '') {
                $company = $result->company;   // brak firmy w wierszu: firma z pliku (jeden plik = jedna firma zapraszająca)
            }

            // Duplikat w pliku = identyczny wiersz: to samo imię, ta sama firma i ten sam e-mail (wielkość liter bez znaczenia).
            // Ten sam e-mail z innym imieniem albo inną firmą NIE jest duplikatem.
            $key = mb_strtolower($name) . "\x1F" . mb_strtolower($company) . "\x1F" . $email;
            if (isset($seen[$key])) {
                $this->reject($result, $row, $emailRaw, Reject::DUPLICATE);
                continue;
            }
            $seen[$key] = true;

            [$domainOk, $domainNote] = $domains->check($email);
            if (!$domainOk) {
                // Walidator nie poprawia adresu: odrzucamy go i zapisujemy jego komunikat.
                $this->reject($result, $row, $emailRaw, Reject::DOMAIN, $domainNote);
                continue;
            }

            // Ten sam wiersz (imię + firma + e-mail) już jest w GF (np. ponowna wysyłka tej samej listy lub przerwany import): pomijamy.
            if ($form->countSameEntry($name, $email, $company) > 0) {
                $result->alreadyAdded++;
                continue;
            }

            // Ile razy ten adres jest już w formularzu (u wszystkich firm). Pomijane, gdy maxExisting = 0.
            if ($this->maxExisting > 0) {
                $existing = $form->countEmail($email);
                if ($existing >= $this->maxExisting) {
                    $this->reject($result, $row, $emailRaw, Reject::ALREADY_IN_GF, 'W formularzu już ' . $existing . ' razy.');
                    continue;
                }
            }

            // Adres przeszedł wszystkie sprawdzenia: dodajemy do GF i do kolejki.
            $result->good++;
            $entryId = $form->add($name, $email, $company, $lang);
            if ($entryId === null) {
                $result->failed++;
                continue;
            }
            $result->added++;
            if ($queue->push($entryId)) {
                $result->queued++;
            }
        }

        // Wynik sprawdzeń: liczby i lista pominiętych (już ucięta do maxRejectedLogged). Kody opisuje legenda.
        $shown = array_map(static function (array $r): array {
            return array_filter($r, static function ($v) { return $v !== ''; });
        }, $result->rejected);
        $cut = $result->rejectedTotal - count($result->rejected);

        $log->event('validated', [
            'company'       => $result->company,
            'good'          => $result->good,
            'bad'           => $result->rejectedTotal,
            'already_added' => $result->alreadyAdded,
            'rejected'      => $shown,
        ] + ($cut > 0 ? ['rejected_truncated' => $cut] : []));

        // Ile dodano do GF, ile ID zapisano w kolejce i ile razy dodawanie się nie udało (ostatnia linia wysyłki).
        $log->event('gf_added', ['gf_added' => $result->added, 'queued' => $result->queued, 'gf_failed' => $result->failed]);

        return $result;
    }

    private function reject(Result $result, int $row, string $email, int $code, string $note = ''): void
    {
        $result->rejectedTotal++;
        if (count($result->rejected) < $this->maxRejectedLogged) {   // dalej tylko liczymy, żeby lista nie rosła w pamięci
            $result->rejected[] = ['row' => $row, 'email' => $email, 'code' => $code, 'note' => $note];
        }
    }

    /** Imię do sprawdzenia: tekst, twarde spacje (z Excela) zamienione na zwykłe, przycięte, wielokrotne spacje zwinięte. */
    private static function normalizeName($value): string
    {
        $value = is_scalar($value) ? (string) $value : '';
        $value = str_replace("\xC2\xA0", ' ', $value);
        return (string) preg_replace('/ {2,}/', ' ', trim($value));
    }

    /**
     * Imię jest poprawne, gdy zaczyna się literą lub cyfrą (z dowolnego alfabetu, np. polskie znaki) i nie zawiera
     * znaków < > " ani znaków sterujących. Apostrof, kropka, myślnik, przecinek, nawiasy itd. są dozwolone.
     */
    private static function validName(string $name): bool
    {
        // Pierwszy znak: litera lub cyfra. Dalej wszystko poza < > ", znakami sterujących i niewidocznych (\p{C}) oraz separatorami linii/akapitu.
        return (bool) preg_match('/^[\p{L}\p{N}][^\p{C}\p{Zl}\p{Zp}<>"]*$/u', $name);
    }

    /** Czy wartość wygląda na formułę: zaczyna się od = + lub @ (po spacjach) albo jest tablicą/obiektem. */
    private static function isFormulaValue($value): bool
    {
        if ($value === null) {
            return false;
        }
        if (!is_scalar($value)) {
            return true;
        }
        return self::isFormula((string) $value);
    }

    private static function isFormula(string $value): bool
    {
        return (bool) preg_match('/^[\s\x{00A0}]*[=+@]/u', $value);
    }

    /** Usuwa znaki sterujące, przycina spacje i długość. Wartości nieskalarne (tablice) dają pusty tekst. */
    public static function clean($value, int $max = 150): string
    {
        $value = is_scalar($value) ? (string) $value : '';
        $value = (string) preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        return mb_substr(trim($value), 0, $max);
    }

    /** Jak clean(), a dodatkowo usuwa znaki < i > (tekst trafia do maili HTML i do eksportów). Dla imienia i firmy. */
    public static function cleanText($value, int $max = 150): string
    {
        return trim(str_replace(['<', '>'], '', self::clean($value, $max)));
    }
}

/**
 * Endpoint HTTP: tylko POST z jednorazowym tokenem od test_get_data.php, z nazwą pliku w nagłówku X-PWE-File.
 * Przy odmowie kończy skrypt z samym kodem HTTP, w przeciwnym razie zwraca nazwę pliku (bez ścieżki) do przetworzenia.
 */
function read_http_request(): string
{
    ignore_user_abort(true);   // nikt nie czeka na odpowiedź, a skrypt ma się dokończyć
    @set_time_limit(0);

    $deny = static function (int $status): void {
        http_response_code($status);
        exit;
    };

    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        $deny(405);
    }

    $token = (string) ($_SERVER['HTTP_X_PWE_TOKEN'] ?? '');
    if (!preg_match('/^[a-f0-9]{32}$/', $token)) {
        $deny(403);
    }
    $tokenFile = sys_get_temp_dir() . '/pwe_import_' . $token;
    $tokenTime = is_file($tokenFile) ? (int) @file_get_contents($tokenFile) : 0;
    @unlink($tokenFile);   // token jest jednorazowy: zużywamy go od razu
    if ($tokenTime === 0 || time() - $tokenTime > 300) {
        $deny(403);
    }
    // Przy okazji sprzątamy porzucone tokeny starsze niż godzina.
    foreach (glob(sys_get_temp_dir() . '/pwe_import_*') ?: [] as $oldToken) {
        if (@filemtime($oldToken) < time() - 3600) {
            @unlink($oldToken);
        }
    }

    // Nazwa pliku w odłożone/: tylko ściśle określony wzór (nazwa + .json), żadnych ścieżek.
    $file = (string) ($_SERVER['HTTP_X_PWE_FILE'] ?? '');
    if (substr($file, -5) !== '.json' || !preg_match(PendingStore::NAME_PATTERN, substr($file, 0, -5))) {
        $deny(400);
    }
    return $file;
}

// Uruchomienie endpointu tylko wtedy, gdy ten plik jest wywołany bezpośrednio (a nie wczytany z innego pliku).
// WordPress ładujemy tutaj, na najwyższym poziomie pliku, a NIE wewnątrz funkcji: wp-load.php w funkcji zrobiłby
// ze zmiennych globalnych WordPressa zmienne lokalne (np. $wpdb byłby pusty).
if (
    PHP_SAPI !== 'cli'
    && isset($_SERVER['SCRIPT_FILENAME'])
    && realpath((string) $_SERVER['SCRIPT_FILENAME']) === realpath(__FILE__)
) {
    $pwe_file    = \PWE\GuestImport\read_http_request();
    $pwe_base    = \PWE\GuestImport\PendingStore::baseDir((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $pwe_store   = new \PWE\GuestImport\PendingStore($pwe_base . '/' . \PWE\GuestImport\PendingStore::DIR);
    $pwe_path    = $pwe_store->dir() . '/' . $pwe_file;
    $pwe_wp_load = str_replace('private_html', 'public_html', (string) ($_SERVER['DOCUMENT_ROOT'] ?? '')) . '/wp-load.php';

    // Znacznik .lock zakładamy PRZED ładowaniem WordPressa (to trwa kilka sekund), żeby test_get_data.php od razu
    // wiedział, że praca ruszyła. Jeśli pliku nie ma albo ktoś go już przetwarza, kończymy.
    if (!is_file($pwe_path) || !$pwe_store->claim($pwe_path)) {
        exit;
    }

    if (!file_exists($pwe_wp_load)) {
        // Bez WordPressa zapisujemy problem w domyślnym katalogu uploads obok wp-load.php; plik wraca do kolejki dla crona.
        (new \PWE\GuestImport\StatsLog($pwe_base, substr($pwe_file, 0, -5), 0, 500))
            ->event('problem', ['problem' => 'Nie znaleziono wp-load.php.'], true);
        $pwe_store->release($pwe_path);
        exit;
    }
    require_once $pwe_wp_load;

    // Katalog bierzemy z DOCUMENT_ROOT, tak samo jak test_get_data.php przy zapisie pliku, żeby oba widziały to samo miejsce.
    // Po zwykłym niepowodzeniu znacznik jest zdejmowany (cron spróbuje w nocy); zabicie procesu zostawia go.
    \PWE\GuestImport\Importer::make()
        ->baseDir($pwe_base)
        ->processFile($pwe_path, false, true);
    exit;
}
