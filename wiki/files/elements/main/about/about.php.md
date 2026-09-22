# `elements/main/about/about.php`

Element strony AutoSwitch implementowany przez `About`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 2856 B
- **Liczba linii:** 78
- **Źródło:** `elements/main/about/about.php`

## Klasy i metody

### `About` — linia 4

- `public static get_data()` — linia 6
- `public static render($group = '', $params = [], $atts = [])` — linia 18

## Shortcody wywoływane przez plik

- `[pwe_about_desc_]`
- `[pwe_about_title_]`
- `[trade_fair_name]`
- `[trade_fair_name_eng]`

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::exhibitor_logos()`
- `PWE_Functions::lang()`
- `PWE_Functions::languageChecker()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Dołączane pliki / wyrażenia include

- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
