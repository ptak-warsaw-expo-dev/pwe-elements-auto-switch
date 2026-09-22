# `PWE_Functions::get_database_groups_contacts_data()`

**Źródło:** `includes/class-functions.php:2470`  
**Sygnatura:** `public static get_database_groups_contacts_data()`

## Krótki opis

Pobiera lub wylicza database groups contacts data zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `self::connect_database()`
- `self::debug_log()`
- `self::read_database_json_cache()`
- `self::write_database_json_cache()`

### Opcje WordPress
- `_transient_timeout_`

### API WordPress
- `get_option()`
- `get_transient()`
- `set_transient()`

## Kontekst

- Klasa: [PWE_Functions](../../classes/PWE_Functions.md)
- Plik: [Otwórz dokument pliku](../../../files/includes/class-functions.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
