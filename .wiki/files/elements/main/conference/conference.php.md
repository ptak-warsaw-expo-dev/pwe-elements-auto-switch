# `elements/main/conference/conference.php`

Element strony AutoSwitch implementowany przez `Conference`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 13599 B
- **Liczba linii:** 398
- **Źródło:** `elements/main/conference/conference.php`

## Klasy i metody

### `Conference` — linia 5

- `public static get_data()` — linia 7
- `public static get_conferences_brief($domain)` — linia 71
- `public static conference_overlaps_fair(string $conf_date_range)` — linia 81
- `public static getConferenceOrganizer($conf_id, $conf_slug, $lang)` — linia 125
- `public static getConferenceOrganizersAll($conf_slug)` — linia 183
- `public static render($group = '', $params = [], $atts = [])` — linia 269

## Shortcody wywoływane przez plik

- `[pwe_conference_desc_]`
- `[pwe_conference_title_]`
- `[trade_fair_datetotimer]`
- `[trade_fair_enddata]`

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_conference_adds_data()`
- `PWE_Functions::get_database_conferences_data()`
- `PWE_Functions::get_database_fairs_data_adds()`
- `PWE_Functions::get_database_logotypes_data()`
- `PWE_Functions::lang()`
- `PWE_Functions::languageChecker()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Dołączane pliki / wyrażenia include

- `$home_preset_file`
- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
