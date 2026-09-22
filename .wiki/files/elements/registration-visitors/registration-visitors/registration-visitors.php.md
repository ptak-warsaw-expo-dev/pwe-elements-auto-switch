# `elements/registration-visitors/registration-visitors/registration-visitors.php`

Element strony AutoSwitch implementowany przez `Registration_Visitors`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 8279 B
- **Liczba linii:** 257
- **Źródło:** `elements/registration-visitors/registration-visitors/registration-visitors.php`

## Klasy i metody

### `Registration_Visitors` — linia 4

- `public static get_data()` — linia 9
- `public static render($group = '', $params = [], $atts = [])` — linia 21
- `private static get_existing_document($path)` — linia 104
- `private static get_vip_badge_mockup()` — linia 112
- `private static register_gravity_forms_filters()` — linia 126
- `private static register_session_handler()` — linia 137
- `public static entry_to_session($entry, $form)` — linia 149
- `public static add_utm_to_confirmation_redirect($confirmation, $form, $entry, $ajax)` — linia 206
- `public static hide_registration_fields($form)` — linia 244

## Rejestracje WordPress wykryte w pliku

- **action:** `gform_after_submission` — linia 145
- **filter:** `gform_pre_render` — linia 133
- **filter:** `gform_confirmation` — linia 134

## Shortcody wywoływane przez plik

- `[gravityform]`
- `[pwe_industry]`
- `[trade_fair_group]`

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::exhibitor_logos()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::is_pwe_session_page()`
- `PWE_Functions::lang()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`

## Dołączane pliki / wyrażenia include

- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
