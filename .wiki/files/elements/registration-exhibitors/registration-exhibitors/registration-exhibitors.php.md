# `elements/registration-exhibitors/registration-exhibitors/registration-exhibitors.php`

Element strony AutoSwitch implementowany przez `Registration_Exhibitors`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 3182 B
- **Liczba linii:** 113
- **Źródło:** `elements/registration-exhibitors/registration-exhibitors/registration-exhibitors.php`

## Klasy i metody

### `Registration_Exhibitors` — linia 4

- `public static get_data()` — linia 8
- `public static render($group = '', $params = [], $atts = [])` — linia 17
- `private static register_session_handler()` — linia 66
- `public static entry_to_session($entry, $form)` — linia 77

## Rejestracje WordPress wykryte w pliku

- **action:** `gform_after_submission` — linia 74

## Shortcody wywoływane przez plik

- `[gravityform]`
- `[trade_fair_group]`

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::exhibitor_logos()`
- `PWE_Functions::get_gf_form_id()`
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
