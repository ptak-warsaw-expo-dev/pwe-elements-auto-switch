# `addons/email-validator/email-validator.php`

Samodzielny dodatek integracyjny wtyczki, przede wszystkim dla Gravity Forms i walidacji pól.

## Metadane

- **Kategoria:** `addon`
- **Rozmiar:** 10495 B
- **Liczba linii:** 322
- **Źródło:** `addons/email-validator/email-validator.php`

## Klasy i metody

### `PWE_Email_Validator_Addon` — linia 13

- `public static init()` — linia 19
- `public static enqueue_assets()` — linia 30
- `private static get_domain_corrections()` — linia 76
- `private static get_provider_domains()` — linia 159
- `private static get_messages()` — linia 176
- `private static get_language()` — linia 241
- `public static validate_email_domain($result, $value, $form, $field)` — linia 249

## Rejestracje WordPress wykryte w pliku

- **action:** `wp_enqueue_scripts` — linia 27
- **filter:** `gform_field_validation` — linia 26

## Wybrane zależności wywołań

- `PWE_Email_Validator_Addon::init()`

## Tabele SQL widoczne statycznie

- `an`

## API WordPress używane w pliku

- `wp_enqueue_style()`
- `wp_enqueue_script()`
- `wp_localize_script()`
- `apply_filters()`

## Dołączane pliki / wyrażenia include

- `_once plugin_dir_path( __FILE__ ) . 'addons/email-validator/email-validator.php'`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
