# `Menu::render()`

**Źródło:** `components/menu/menu.php:14`  
**Sygnatura:** `public static render($group = '', $params = [], $atts = [])`

## Krótki opis

Renderuje dane zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_files()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`
- `self::get_data()`

### Shortcody wywoływane
- `[trade_fair_catalog_year]`
- `[trade_fair_datetotimer]`
- `[trade_fair_enddata]`

### Opcje WordPress
- `pwe_menu_options`

### API WordPress
- `get_option()`

## Kontekst

- Klasa: [Menu](../../classes/Menu.md)
- Plik: [Otwórz dokument pliku](../../../files/components/menu/menu.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
