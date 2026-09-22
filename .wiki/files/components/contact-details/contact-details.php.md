# `components/contact-details/contact-details.php`

Wielokrotnego użytku komponent renderowany przez AutoSwitch (`Contact_Details`).

## Metadane

- **Kategoria:** `component`
- **Rozmiar:** 14555 B
- **Liczba linii:** 412
- **Źródło:** `components/contact-details/contact-details.php`

## Klasy i metody

### `Contact_Details` — linia 4

- `public static get_data()` — linia 6
- `private static pwe_clean_value($value)` — linia 21
- `private static pwe_option_value($option_name)` — linia 36
- `private static pwe_first_not_empty($manual_value, $default_value = '')` — linia 49
- `private static pwe_split_emails($value)` — linia 65
- `private static pwe_phone_href($phone)` — linia 89
- `private static pwe_data_value($data, $field)` — linia 100
- `private static pwe_render_email_links($emails)` — linia 126
- `public static render($group = '', $params = [], $atts = [])` — linia 153

## Shortcody wywoływane przez plik

- `[pwe_edition]`

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::set_translation_context()`

## Tabele SQL widoczne statycznie

- `WordPress`
- `decoded`
- `one`
- `panel`

## API WordPress używane w pliku

- `get_option()`

## Dołączane pliki / wyrażenia include

- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
