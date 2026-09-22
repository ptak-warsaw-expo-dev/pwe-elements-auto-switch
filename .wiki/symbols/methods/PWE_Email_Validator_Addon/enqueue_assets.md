# `PWE_Email_Validator_Addon::enqueue_assets()`

**Źródło:** `addons/email-validator/email-validator.php:30`  
**Sygnatura:** `public static enqueue_assets()`

## Krótki opis

Realizuje logikę techniczną związaną z `enqueue_assets`.

## Wykryte zależności statyczne

### Wywołania statyczne
- `self::get_domain_corrections()`
- `self::get_messages()`
- `self::get_provider_domains()`

### API WordPress
- `wp_enqueue_style()`
- `wp_enqueue_script()`

## Kontekst

- Klasa: [PWE_Email_Validator_Addon](../../classes/PWE_Email_Validator_Addon.md)
- Plik: [Otwórz dokument pliku](../../../files/addons/email-validator/email-validator.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
