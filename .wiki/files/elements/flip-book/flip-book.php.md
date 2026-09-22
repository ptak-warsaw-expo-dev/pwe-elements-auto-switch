# `elements/flip-book/flip-book.php`

Element strony AutoSwitch implementowany przez `Flip_Book`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 4068 B
- **Liczba linii:** 106
- **Źródło:** `elements/flip-book/flip-book.php`

## Klasy i metody

### `Flip_Book` — linia 11

- `public static init()` — linia 15
- `private __construct()` — linia 22
- `public static get_data()` — linia 28
- `public static register_assets()` — linia 33
- `public static pwe_exclude_from_wp_rocket($patterns)` — linia 39
- `public static pwe_add_attributes($tag, $handle)` — linia 49
- `public static render($group = '', $params = [], $atts = [])` — linia 57

## Rejestracje WordPress wykryte w pliku

- **action:** `wp_enqueue_scripts` — linia 23
- **filter:** `rocket_delay_js_exclusions` — linia 24
- **filter:** `script_loader_tag` — linia 25

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`

## Tabele SQL widoczne statycznie

- `delaying`

## API WordPress używane w pliku

- `wp_enqueue_script()`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
