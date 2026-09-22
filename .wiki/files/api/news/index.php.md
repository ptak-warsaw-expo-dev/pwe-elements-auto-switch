# `api/news/index.php`

Bezpośredni endpoint HTTP ładowany poza WordPress REST API; plik sam ładuje WordPress i obsługuje żądanie.

## Metadane

- **Kategoria:** `direct-http-endpoint`
- **Rozmiar:** 11359 B
- **Liczba linii:** 366
- **Źródło:** `api/news/index.php`

## Funkcje globalne

- `pwe_json_response($status_code, $data)` — linia 7
- `pwe_assign_news_category($post_id, $language = 'pl')` — linia 17
- `pwe_prepare_uncode_raw_html($html)` — linia 65
- `pwe_set_featured_image_from_url($image_url, $post_id, $title)` — linia 83

## API WordPress używane w pliku

- `wp_insert_post()`
- `wp_update_post()`
- `wp_delete_post()`
- `get_posts()`
- `get_page_by_path()`
- `apply_filters()`
- `do_action()`

## Dołączane pliki / wyrażenia include

- `_once __DIR__ . '/../../../../../wp-load.php'`
- `_once ABSPATH . 'wp-admin/includes/image.php'`
- `_once ABSPATH . 'wp-admin/includes/file.php'`
- `_once ABSPATH . 'wp-admin/includes/media.php'`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
