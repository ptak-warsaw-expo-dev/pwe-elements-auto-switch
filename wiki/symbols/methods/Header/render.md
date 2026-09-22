# `Header::render()`

**Źródło:** `elements/main/header/header.php:18`  
**Sygnatura:** `public static render($group = '', $params = [], $atts = [])`

## Krótki opis

Renderuje dane zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::id_rnd()`
- `PWE_Functions::lang()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`
- `self::get_data()`

### Shortcody wywoływane
- `[pwe_desc_]`
- `[pwe_name_]`
- `[trade_fair_date_custom_format]`
- `[trade_fair_date_multilang]`
- `[trade_fair_edition]`

## Kontekst

- Klasa: [Header](../../classes/Header.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/main/header/header.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
