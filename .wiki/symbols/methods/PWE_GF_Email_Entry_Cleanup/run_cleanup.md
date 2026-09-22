# `PWE_GF_Email_Entry_Cleanup::run_cleanup()`

**Źródło:** `includes/class-hooks.php:337`  
**Sygnatura:** `public static run_cleanup()`

## Krótki opis

Realizuje logikę techniczną związaną z `run_cleanup`.

## Wykryte zależności statyczne

### Wywołania statyczne
- `GFAPI::delete_entry()`
- `GFAPI::get_entry()`
- `GFAPI::get_entry_ids()`
- `GFAPI::get_forms()`
- `self::acquire_lock()`
- `self::entry_matches_prefix()`
- `self::get_cleanup_rules_hash()`
- `self::get_email_field_ids()`
- `self::get_email_prefixes()`
- `self::get_emails()`
- `self::is_current_domain_excluded()`
- `self::release_lock()`
- `self::save_error_result()`

### API WordPress
- `update_option()`

## Kontekst

- Klasa: [PWE_GF_Email_Entry_Cleanup](../../classes/PWE_GF_Email_Entry_Cleanup.md)
- Plik: [Otwórz dokument pliku](../../../files/includes/class-hooks.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
