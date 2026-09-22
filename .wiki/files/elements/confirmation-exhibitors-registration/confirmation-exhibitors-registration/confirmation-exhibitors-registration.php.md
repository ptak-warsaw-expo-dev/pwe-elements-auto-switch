# `elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php`

Element strony AutoSwitch implementowany przez `Confirmation_Exhibitors_Registration`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 12252 B
- **Liczba linii:** 363
- **Źródło:** `elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php`

## Klasy i metody

### `Confirmation_Exhibitors_Registration` — linia 6

- `public static init()` — linia 11
- `public static get_data()` — linia 16
- `public static render($group = '', $params = [], $atts = [])` — linia 27
- `private static register_gravity_filters()` — linia 75
- `private static get_session_info()` — linia 92
- `private static get_session_data()` — linia 119
- `public static prepare_form($form)` — linia 124
- `public static fix_validation_and_inject($validation_result)` — linia 159
- `public static inject_session_data($form)` — linia 190
- `public static override_saved_field_value($value, $entry, $field, $form, $input_id)` — linia 210
- `private static register_ajax_handlers()` — linia 228
- `private static force_clear_pwe_session()` — linia 242
- `public static ajax_clear_session()` — linia 253
- `public static update_exhibitor_data()` — linia 258
- `public static clear_session_after_submission($entry, $form)` — linia 353
- `public static clear_session_on_confirmation($confirmation, $form, $entry, $ajax)` — linia 357

## Rejestracje WordPress wykryte w pliku

- **action:** `gform_pre_submission` — linia 86
- **action:** `gform_after_submission` — linia 88
- **action:** `wp_ajax_update_exhibitor_data` — linia 235
- **action:** `wp_ajax_nopriv_update_exhibitor_data` — linia 236
- **action:** `wp_ajax_clear_pwe_session` — linia 238
- **action:** `wp_ajax_nopriv_clear_pwe_session` — linia 239
- **filter:** `gform_pre_render` — linia 82
- **filter:** `gform_pre_validation` — linia 83
- **filter:** `gform_validation` — linia 84
- **filter:** `gform_save_field_value` — linia 85
- **filter:** `gform_confirmation` — linia 89

## Shortcody wywoływane przez plik

- `[gravityform]`

## Wybrane zależności wywołań

- `GFAPI::get_entry()`
- `GFAPI::get_form()`
- `GFAPI::send_notifications()`
- `GFAPI::update_entry()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::is_pwe_session_page()`
- `PWE_Functions::set_translation_context()`

## API WordPress używane w pliku

- `wp_remote_post()`
- `wp_send_json_success()`
- `wp_send_json_error()`

## Dołączane pliki / wyrażenia include

- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
