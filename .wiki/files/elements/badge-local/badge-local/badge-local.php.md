# `elements/badge-local/badge-local/badge-local.php`

Element strony AutoSwitch implementowany przez `Badge_Local`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 15614 B
- **Liczba linii:** 478
- **Źródło:** `elements/badge-local/badge-local/badge-local.php`

## Klasy i metody

### `Badge_Local` — linia 4

- `public static get_data()` — linia 6
- `public static massGenerator($badge_form_id)` — linia 26
- `public static qrOnlyDownload($badge_form_id)` — linia 155
- `public static pwe_download_temp_qr($url)` — linia 370
- `public static badge_name_changer($content, $field, $value, $lead_id, $form_id)` — linia 424
- `public static render($group = '', $params = [], $atts = [])` — linia 439

## Rejestracje WordPress wykryte w pliku

- **filter:** `gform_field_content` — linia 466

## Shortcody wywoływane przez plik

- `[trade_fair_badge]`

## Wybrane zależności wywołań

- `GFAPI::add_entry()`
- `GFAPI::get_entry()`
- `GFAPI::get_feeds()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::set_translation_context()`

## Tabele SQL widoczne statycznie

- `URL`
- `the`

## API WordPress używane w pliku

- `wp_remote_get()`
- `do_action()`

## Dołączane pliki / wyrażenia include

- `s("?") ? "&" : "?"`
- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
