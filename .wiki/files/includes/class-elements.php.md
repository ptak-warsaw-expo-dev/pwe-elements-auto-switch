# `includes/class-elements.php`

Plik warstwy rdzeniowej definiujący klasę `PWE_Elements` i jej logikę pomocniczą/integracyjną.

## Metadane

- **Kategoria:** `core`
- **Rozmiar:** 18129 B
- **Liczba linii:** 427
- **Źródło:** `includes/class-elements.php`

## Klasy i metody

### `PWE_Elements` — linia 4

- `public static init()` — linia 7
- `public static adding_styles()` — linia 260
- `public static adding_scripts()` — linia 283
- `public static render_single_element($class_name, $atts = [])` — linia 290
- `public static render_elements($type, $atts = [])` — linia 350

## Rejestracje WordPress wykryte w pliku

- **action:** `wp_enqueue_scripts` — linia 20
- **action:** `wp_enqueue_scripts` — linia 21
- **action:** `vc_before_init` — linia 31
- **action:** `vc_before_init` — linia 140
- **action:** `vc_before_init` — linia 232

## Wybrane zależności wywołań

- `PWE_Elements::render_single_element()`
- `PWE_Elements_Data::get_all_components()`
- `PWE_Elements_Data::get_all_elements()`
- `PWE_Elements_Data::require_elements()`
- `PWE_Groups::get_current_group()`
- `PWE_Groups::is_b2c()`

## Tabele SQL widoczne statycznie

- `page`

## API WordPress używane w pliku

- `wp_enqueue_style()`
- `wp_enqueue_script()`

## Dołączane pliki / wyrażenia include

- `_elements($type)`
- `_once $swiper_file`
- `_once $swiper_file`
- `_elements($type, $group)`

## Powiązana dokumentacja

- [Symbole tego pliku](../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
