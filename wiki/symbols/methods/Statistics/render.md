# `Statistics::render()`

**Źródło:** `elements/main/statistics/statistics.php:18`  
**Sygnatura:** `public static render($group = '', $params = [], $atts = [])`

## Krótki opis

Renderuje dane zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_associates_data()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`
- `self::get_data()`

### Shortcody wywoływane
- `[pwe_edition]`
- `[pwe_visitors]`
- `[pwe_visitors_foreign]`

### API WordPress
- `wp_enqueue_style()`

## Kontekst

- Klasa: [Statistics](../../classes/Statistics.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/main/statistics/statistics.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
