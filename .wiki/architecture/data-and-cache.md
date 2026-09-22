---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Dane CAP i warstwa cache

Centralnym punktem dostępu do danych jest `includes/class-functions.php` (`PWE_Functions`).

## Połączenia CAP

`PWE_Functions::get_database_servers()` konfiguruje cztery znane hosty Cyber_Folks i pobiera dane dostępowe ze stałych `PWE_DB_*`. Na podstawie `$_SERVER['SERVER_ADDR']` host odpowiadający bieżącemu serwerowi jest zmieniany na `localhost` i preferowany jako jedyny host.

Dla wywołań bez `SERVER_ADDR` istnieje fallback oparty o nazwę hosta (`php_uname('n')`).

`connect_database()`:

1. zwraca połączenie z cache requestu, jeśli już istnieje,
2. wybiera dostępne serwery,
3. ustawia timeout połączenia `wpdb` na 2 sekundy,
4. pomija hosty bez kompletu credentiali,
5. tworzy osobny obiekt `wpdb`,
6. wykonuje test `SELECT 1`,
7. cache'uje pierwsze poprawne połączenie na czas requestu.

## Wielowarstwowy cache danych

Komentarz w kodzie definiuje priorytet normalnego odczytu:

```text
STATIC -> TRANSIENT -> JSON FILE -> DATABASE
```

Trwały cache JSON znajduje się pod katalogiem uploadów WordPress w `pwe_cache/PWE_Functions`. Katalog otrzymuje `.htaccess` blokujący dostęp przez Apache i `index.php`.

Plik JSON przechowuje m.in.:

- `version`,
- `source` (nazwa metody getter),
- `cache_key`,
- `generated_at_utc`,
- argumenty potrzebne do odtworzenia wariantu,
- spakowane dane.

Kod zachowuje różnicę między tablicami a obiektami `stdClass/wpdb` poprzez własny format `__pwe_cache_type` / `__pwe_cache_value`.

Zapis JSON jest atomowy: dane trafiają do pliku tymczasowego, a następnie plik jest podmieniany przez `rename()`.

## Wymuszone odświeżenie

`refresh_database_json_cache($domain)` skanuje istniejące pliki JSON. Każdy cache zawiera nazwę getter'a oraz argumenty, więc metoda może odtworzyć zapytanie. Dla bezpieczeństwa wykonanie jest ograniczone whitelistą dozwolonych metod.

Przy wymuszonym refreshu przepływ jest opisany w kodzie jako:

```text
DATABASE -> JSON FILE -> TRANSIENT -> STATIC
```

Jeżeli baza podczas wymuszonego odświeżenia jest niedostępna, fallbackiem są istniejący JSON i transient.

Ważne: wariant cache musi zostać utworzony przynajmniej raz podczas normalnego użycia, aby automatyczny refresh mógł go później odkryć.

## Główne rodziny danych

Wersja 1.8.8 posiada gettery m.in. dla:

- danych targów i rozszerzeń (`fairs`, `fair_adds`),
- tłumaczeń,
- grup i kontaktów,
- współprac/associate,
- sklepu i pakietów,
- meta danych,
- tygodni/week,
- logotypów,
- konferencji i dodatków konferencyjnych,
- profili,
- premier,
- opinii,
- sektorów,
- biletów,
- prelegentów,
- gości,
- atrakcji,
- plików targów,
- konfiguracji elementów i kolejności.

Pełna lista tabel rozpoznanych w SQL: [../reference/database-tables.md](../reference/database-tables.md).

## Diagnostyka

`PWE_Functions` agreguje debug logi połączeń; dla administratora na frontendzie mogą one zostać wypisane w konsoli przeglądarki. Kod celowo nie wypisuje tych logów w panelu admina.
