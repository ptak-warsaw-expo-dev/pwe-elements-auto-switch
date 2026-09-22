# `Conference::render()`

**Źródło:** `elements/main/conference/conference.php:269`  
**Sygnatura:** `public static render($group = '', $params = [], $atts = [])`

## Krótki opis

Renderuje dane zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_adds()`
- `PWE_Functions::get_database_logotypes_data()`
- `PWE_Functions::lang()`
- `PWE_Functions::languageChecker()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`
- `self::get_data()`

### Shortcody wywoływane
- `[pwe_conference_desc_]`
- `[pwe_conference_title_]`

## Kontekst

- Klasa: [Conference](../../classes/Conference.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/main/conference/conference.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
