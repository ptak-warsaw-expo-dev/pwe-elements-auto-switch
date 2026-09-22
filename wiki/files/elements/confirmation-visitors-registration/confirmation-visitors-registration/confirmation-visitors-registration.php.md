# `elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php`

Element strony AutoSwitch implementowany przez `Confirmation_Visitors_Registration`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 12034 B
- **Liczba linii:** 344
- **Źródło:** `elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php`

## Klasy i metody

### `Confirmation_Visitors_Registration` — linia 4

- `public static init()` — linia 9
- `public static get_data()` — linia 13
- `public static render($group = '', $params = [], $atts = [])` — linia 25
- `private static register_gravity_forms_filters()` — linia 127
- `public static prepare_registration_form($form)` — linia 138
- `private static register_ajax_handlers()` — linia 160
- `public static update_registration_address()` — linia 171
- `private static add_apartment_field($form_id)` — linia 286

## Rejestracje WordPress wykryte w pliku

- **action:** `wp_ajax_update_registration_address` — linia 167
- **action:** `wp_ajax_nopriv_update_registration_address` — linia 168
- **filter:** `gform_pre_render` — linia 134
- **filter:** `gform_pre_validation` — linia 135

## Shortcody wywoływane przez plik

- `[gravityform]`
- `[trade_fair_date_custom_format]`
- `[trade_fair_datetotimer]`
- `[trade_fair_edition]`
- `[trade_fair_enddata]`
- `[trade_fair_group]`

## Wybrane zależności wywołań

- `GFAPI::get_entry()`
- `GFAPI::get_form()`
- `GFAPI::update_entry()`
- `GFAPI::update_form()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::is_pwe_session_page()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## API WordPress używane w pliku

- `get_option()`
- `update_option()`
- `wp_safe_redirect()`
- `wp_send_json_success()`
- `wp_send_json_error()`

## Dołączane pliki / wyrażenia include

- `$preset_file`
- `_once ABSPATH . 'wp-content/plugins/custom-element/gf_integration/gf_integration.php'`
- `_once ABSPATH . 'wp-content/plugins/custom-element/pwe-cdb/activation_db.php'`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
