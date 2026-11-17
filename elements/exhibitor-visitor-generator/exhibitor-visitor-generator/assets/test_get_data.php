<?php
// Endpoint dla strony generatora gości wystawców: odbiera JSON (POST), sprawdza tylko, że jest poprawny i nie ma za
// dużo wierszy, ZAPISUJE go bez zmian jako plik w uploads/generator_wystawcow/odłożone/ i budzi mass_vip_add.php małym
// żądaniem z samą nazwą pliku (bez danych). Potem od razu odpowiada stronie.
//
// Przyjmuje dwa formaty (szczegóły w mass_vip_add.php):
//   v2: {"schema_version":2, "file_name":"x.xlsx", "rows":[{"row":1,"cells":[...]}, ...]}   (surowe komórki arkusza)
//   v1: {"schema_version":1, "file_name":"x.csv",  "guests":[{"row":2,"full_name":..,"inviting_company":..,"email":..}, ...]}
//
// Odpowiedź: {"success":true,"received":N} albo {"success":false}, gdzie N to liczba elementów "rows" (v2) albo
// "guests" (v1) wysłanych przez stronę (strona porównuje ją z liczbą, którą wysłała). Żadnych szczegółów o odrzuconych
// adresach. Cała praca (WordPress, czytanie wierszy, walidacja, Gravity Forms, kolejka, plik statystyk, usunięcie pliku)
// dzieje się w mass_vip_add.php. "success" znaczy: dane są bezpiecznie zapisane w pliku. Gdyby wybudzenie się nie udało,
// plik zostaje w katalogu i zostanie przetworzony przez cron.

$max_rows = 20100;   // limit strony to 20 000 gości; zapas na wiersz nagłówka i puste wiersze

// Odpowiedź dla przeglądarki.
$respond = static function (array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');
    echo json_encode($data);
    exit;
};
$fail = static function (int $status) use ($respond): void {
    $respond(['success' => false], $status);
};

// ---------------------------------------------------------------------------
// 1. Odczyt i sprawdzenie danych
// ---------------------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    $fail(405);
}

$content_type = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '', 2)[0]));
if ($content_type !== 'application/json') {
    $fail(415);
}

$max_body_bytes = 8 * 1024 * 1024;
if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $max_body_bytes) {
    $fail(413);
}

$raw = file_get_contents('php://input', false, null, 0, $max_body_bytes + 1);
if ($raw === false) {
    $fail(400);
}
if (strlen($raw) > $max_body_bytes) {
    $fail(413);
}

try {
    $payload = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
} catch (JsonException $error) {
    $fail(400);
}

// v2 ('rows') albo v1 ('guests'): potrzebna niepusta lista, nie za długa.
$items = null;
if (is_array($payload)) {
    if (isset($payload['rows']) && is_array($payload['rows'])) {
        $items = $payload['rows'];
    } elseif (isset($payload['guests']) && is_array($payload['guests'])) {
        $items = $payload['guests'];
    }
}
if (!$items) {
    $fail(422);
}
$received = count($items);   // dokładnie tyle elementów wysłała strona (do odpowiedzi)
if ($received > $max_rows) {
    $fail(413);
}
unset($payload, $items);   // do zapisania wystarczy surowy tekst $raw

// ---------------------------------------------------------------------------
// 2. Zapis do pliku (odłożone/<batch_id>.json)
// ---------------------------------------------------------------------------
$worker_file = __DIR__ . '/mass_vip_add.php';
if (!is_file($worker_file)) {
    $fail(500);   // brak drugiego pliku: lepiej od razu powiedzieć "false" niż udawać sukces
}
require_once $worker_file;   // tylko klasy (PendingStore): samo wczytanie tego pliku niczego nie wykonuje

$batch_id = date('Ymd_His') . '_' . bin2hex(random_bytes(3));
$store    = new \PWE\GuestImport\PendingStore(
    \PWE\GuestImport\PendingStore::baseDir((string) ($_SERVER['DOCUMENT_ROOT'] ?? '')) . '/' . \PWE\GuestImport\PendingStore::DIR
);
$saved = $store->save($batch_id, $raw);
if ($saved === null) {
    $fail(500);   // nie udało się zapisać danych: nie udajemy sukcesu
}
unset($raw);

// ---------------------------------------------------------------------------
// 3. Wybudzenie mass_vip_add.php (mała prośba z nazwą pliku, bez czekania na przetwarzanie)
// ---------------------------------------------------------------------------
// Token jednorazowy: mass_vip_add.php przyjmie żądanie tylko z tokenem, który tu zapisujemy,
// i od razu go zużyje. Dzięki temu nikt z zewnątrz nie wywoła go bezpośrednio.
$token      = bin2hex(random_bytes(16));
$token_file = sys_get_temp_dir() . '/pwe_import_' . $token;
$woken      = false;

if (@file_put_contents($token_file, (string) time()) !== false) {
    // Adres drugiego pliku: ta sama witryna i katalog. Łączymy się z WŁASNYM adresem serwera (SERVER_ADDR),
    // a nagłówek Host służy tylko do wyboru witryny. Nigdy nie łączymy się z hostem podanym przez klienta.
    $host_header = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $https       = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
    $port        = (int) ($_SERVER['SERVER_PORT'] ?? ($https ? 443 : 80));
    $server_ip   = (string) ($_SERVER['SERVER_ADDR'] ?? '127.0.0.1');
    $path        = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? ''))), '/') . '/mass_vip_add.php';

    if (preg_match('/^[A-Za-z0-9.\-]+(:\d{1,5})?$/', $host_header)) {
        $target  = ($https ? 'ssl://' : 'tcp://') . (strpos($server_ip, ':') !== false ? '[' . $server_ip . ']' : $server_ip) . ':' . $port;
        $context = stream_context_create(['ssl' => [
            'verify_peer'      => false,   // łączymy się z własnym serwerem po jego adresie IP
            'verify_peer_name' => false,
            'SNI_enabled'      => true,
            'peer_name'        => preg_replace('/:\d+$/', '', $host_header),
        ]]);

        $fp = @stream_socket_client($target, $errno, $errstr, 3, STREAM_CLIENT_CONNECT, $context);
        if ($fp) {
            stream_set_timeout($fp, 5);
            $head = "POST " . $path . " HTTP/1.1\r\n"
                  . "Host: " . $host_header . "\r\n"
                  . "Content-Length: 0\r\n"
                  . "X-PWE-Token: " . $token . "\r\n"
                  . "X-PWE-File: " . basename($saved) . "\r\n"
                  . "Connection: close\r\n\r\n";

            $written = @fwrite($fp, $head);
            $woken   = ($written !== false && $written === strlen($head));

            // Krótko czekamy (maks. ok. 3 s), aż mass_vip_add.php zacznie pracę (założy znacznik .lock albo zdąży już
            // skończyć), żeby nie zamknąć połączenia przed jego startem. NIE czekamy na samo przetwarzanie.
            if ($woken) {
                for ($i = 0; $i < 60; $i++) {
                    clearstatcache(true, $saved . '.lock');
                    clearstatcache(true, $saved);
                    if (is_file($saved . '.lock') || !is_file($saved)) {
                        break;
                    }
                    usleep(50000);
                }
            }
            fclose($fp);
        }
    }
}

if (!$woken) {
    @unlink($token_file);   // nie wybudzono, więc token jest niepotrzebny; plik zostaje dla crona
}

// ---------------------------------------------------------------------------
// 4. Odpowiedź dla przeglądarki
// ---------------------------------------------------------------------------
// Dane są bezpiecznie zapisane w pliku, więc odpowiadamy sukcesem także wtedy, gdy wybudzenie się nie udało
// (plik zostanie przetworzony przez cron). To jedynie echo liczby elementów (nie zdradza nic o odrzuconych adresach).
$respond(['success' => true, 'received' => $received]);
