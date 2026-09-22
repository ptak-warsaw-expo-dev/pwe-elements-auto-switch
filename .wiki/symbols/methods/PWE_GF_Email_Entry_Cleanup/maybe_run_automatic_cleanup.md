# `PWE_GF_Email_Entry_Cleanup::maybe_run_automatic_cleanup()`

**Źródło:** `includes/class-hooks.php:178`  
**Sygnatura:** `public static maybe_run_automatic_cleanup()`

## Krótki opis

Realizuje logikę techniczną związaną z `maybe_run_automatic_cleanup`.

## Wykryte zależności statyczne

### Wywołania statyczne
- `self::get_cleanup_rules_hash()`
- `self::get_email_prefixes()`
- `self::get_emails()`
- `self::is_current_domain_excluded()`
- `self::run_cleanup()`

### API WordPress
- `get_option()`

## Kontekst

- Klasa: [PWE_GF_Email_Entry_Cleanup](../../classes/PWE_GF_Email_Entry_Cleanup.md)
- Plik: [Otwórz dokument pliku](../../../files/includes/class-hooks.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
