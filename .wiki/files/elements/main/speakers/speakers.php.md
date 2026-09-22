# `elements/main/speakers/speakers.php`

Element strony AutoSwitch implementowany przez `Speakers`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 9894 B
- **Liczba linii:** 335
- **Źródło:** `elements/main/speakers/speakers.php`

## Klasy i metody

### `Speakers` — linia 4

- `public static get_data()` — linia 6
- `public static render($group = '', $params = [], $atts = [])` — linia 17
- `private static create_speakers_pages($speakers)` — linia 129
- `private static find_or_create_speakers_page(string $title, string $slug, string $content)` — linia 202
- `private static find_page_by_slug($slug)` — linia 266
- `private static assign_wpml_language(int $page_id, string $language_code, ?string $source_language_code = null, ?int $translation_group_id = null)` — linia 292

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_speakers()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## API WordPress używane w pliku

- `wp_insert_post()`
- `wp_update_post()`
- `get_posts()`
- `apply_filters()`
- `do_action()`

## Dołączane pliki / wyrażenia include

- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
