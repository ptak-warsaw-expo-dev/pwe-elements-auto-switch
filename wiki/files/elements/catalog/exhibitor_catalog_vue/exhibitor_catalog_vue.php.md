# `elements/catalog/exhibitor_catalog_vue/exhibitor_catalog_vue.php`

Element strony AutoSwitch implementowany przez `Exhibitor_Catalog`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 26047 B
- **Liczba linii:** 749
- **Źródło:** `elements/catalog/exhibitor_catalog_vue/exhibitor_catalog_vue.php`

## Klasy i metody

### `Exhibitor_Catalog` — linia 7

- `public static get_data()` — linia 9
- `public static get_info()` — linia 16
- `public static render($group = '', $params = [], $atts = [])` — linia 23
- `private static enqueue_assets()` — linia 228
- `private static enqueue_feedback_assets()` — linia 262
- `private static get_plugin_version()` — linia 296
- `private static get_catalog_type()` — linia 306
- `private static sync_archive_catalog_entry($atts)` — linia 371
- `private static inject_config($atts)` — linia 563

## Shortcody wywoływane przez plik

- `[pwe_katalog]`

## Wybrane zależności wywołań

- `PWE_Functions::add_log()`

## Tabele SQL widoczne statycznie

- `catalog_year`
- `data`
- `fair_adds`
- `fairs`

## API WordPress używane w pliku

- `wp_remote_get()`
- `wp_enqueue_style()`
- `wp_enqueue_script()`
- `wp_localize_script()`

## Dołączane pliki / wyrażenia include

- `_once $feedback`
- `_once $translates`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
