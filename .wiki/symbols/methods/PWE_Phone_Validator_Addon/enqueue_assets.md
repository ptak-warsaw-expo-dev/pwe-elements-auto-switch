# `PWE_Phone_Validator_Addon::enqueue_assets()`

**Źródło:** `addons/phone-validator/phone-validator.php:56`  
**Sygnatura:** `public static enqueue_assets()`

## Krótki opis

Realizuje logikę techniczną związaną z `enqueue_assets`.

## Wykryte zależności statyczne

### Wywołania statyczne
- `self::get_language()`
- `self::get_messages()`
- `self::print_late_styles()`

### API WordPress
- `wp_enqueue_style()`
- `wp_enqueue_script()`

## Kontekst

- Klasa: [PWE_Phone_Validator_Addon](../../classes/PWE_Phone_Validator_Addon.md)
- Plik: [Otwórz dokument pliku](../../../files/addons/phone-validator/phone-validator.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
