# `elements/fair-plan/fair-plan/fair-plan.php`

Element strony AutoSwitch implementowany przez `Fair_Plan`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 19298 B
- **Liczba linii:** 623
- **Źródło:** `elements/fair-plan/fair-plan/fair-plan.php`

## Klasy i metody

### `Fair_Plan` — linia 4

- `public static get_data()` — linia 10
- `private static set_featured_image_by_url($post_id, $image_path)` — linia 19
- `private static find_fair_plan_post($year, $language, $slug)` — linia 91
- `private static remove_news_without_active_plan($active_years)` — linia 137
- `public static pwe_create_fair_plan_news($year)` — linia 169
- `public static create_or_update_fair_plan_pages()` — linia 381
- `public static set_uncode_header_none(int $post_id)` — linia 449
- `public static set_uncode_show_title_off(int $post_id)` — linia 456
- `public static create_missing_news_for_files($files)` — linia 462
- `public static render($group = '', $params = [], $atts = [])` — linia 500

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_files()`
- `PWE_Functions::set_translation_context()`

## API WordPress używane w pliku

- `wp_insert_post()`
- `wp_update_post()`
- `get_posts()`
- `get_page_by_path()`
- `apply_filters()`
- `do_action()`

## Dołączane pliki / wyrażenia include

- `_once ABSPATH . 'wp-admin/includes/image.php'`
- `_once ABSPATH . 'wp-admin/includes/file.php'`
- `_once ABSPATH . 'wp-admin/includes/media.php'`
- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
