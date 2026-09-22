# `About::render()`

**Źródło:** `elements/main/about/about.php:18`  
**Sygnatura:** `public static render($group = '', $params = [], $atts = [])`

## Krótki opis

Renderuje dane zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::exhibitor_logos()`
- `PWE_Functions::lang()`
- `PWE_Functions::languageChecker()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`
- `self::get_data()`

### Shortcody wywoływane
- `[pwe_about_desc_]`
- `[pwe_about_title_]`
- `[trade_fair_name]`
- `[trade_fair_name_eng]`

## Kontekst

- Klasa: [About](../../classes/About.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/main/about/about.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
