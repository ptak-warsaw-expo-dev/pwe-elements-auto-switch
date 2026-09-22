# `addons/phone-validator/phone-validator.php`

Samodzielny dodatek integracyjny wtyczki, przede wszystkim dla Gravity Forms i walidacji pól.

## Metadane

- **Kategoria:** `addon`
- **Rozmiar:** 9894 B
- **Liczba linii:** 260
- **Źródło:** `addons/phone-validator/phone-validator.php`

## Klasy i metody

### `PWE_Phone_Validator_Addon` — linia 8

- `public static init()` — linia 16
- `public static maybe_enqueue_assets_for_form($form)` — linia 33
- `public static enqueue_assets()` — linia 56
- `private static print_late_styles(array $handles)` — linia 119
- `private static get_messages()` — linia 135
- `private static get_language()` — linia 211
- `public static validate_phone_field($result, $value, $form, $field)` — linia 223

## Rejestracje WordPress wykryte w pliku

- **filter:** `gform_pre_render` — linia 24
- **filter:** `gform_field_validation` — linia 25

## Wybrane zależności wywołań

- `PWE_Phone_Validator_Addon::init()`

## Tabele SQL widoczne statycznie

- `legacy`

## API WordPress używane w pliku

- `wp_enqueue_style()`
- `wp_enqueue_script()`
- `wp_localize_script()`

## Dołączane pliki / wyrażenia include

- `dMessage' => $current_messages['required'] ?? $messages['en']['required'], 'libraryErrorMessage' => $current_messages['library_error'] ?? $messages['en']['library_error'], ])`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
