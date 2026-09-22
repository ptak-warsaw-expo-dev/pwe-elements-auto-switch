# `includes/class-registration-log.php`

Plik warstwy rdzeniowej definiujący klasę `PWE_Registration_Log` i jej logikę pomocniczą/integracyjną.

## Metadane

- **Kategoria:** `core`
- **Rozmiar:** 65854 B
- **Liczba linii:** 2290
- **Źródło:** `includes/class-registration-log.php`

## Klasy i metody

### `PWE_Registration_Log` — linia 9

- `public __construct()` — linia 64
- `private get_storage_directory()` — linia 113
- `private get_csv_path()` — linia 133
- `private get_csv_headers()` — linia 146
- `public log_submission_attempt($validation_result)` — linia 171
- `private get_form_values($form)` — linia 332
- `private get_field_value($field)` — linia 401
- `private get_validation_errors($form)` — linia 511
- `private get_user_ip()` — linia 565
- `private can_access_logs()` — linia 593
- `private read_csv()` — linia 630
- `private get_statistics($rows)` — linia 694
- `private get_download_url()` — linia 819
- `public handle_csv_download()` — linia 866
- `public render_shortcode()` — linia 979
- `private render_styles()` — linia 1297
- `private render_script()` — linia 1828
- `public ensure_logs_page_exists()` — linia 2251

## Rejestracje WordPress wykryte w pliku

- **action:** `template_redirect` — linia 88
- **action:** `wp_loaded` — linia 97
- **filter:** `gform_validation` — linia 71

## Tabele SQL widoczne statycznie

- `CSV`
- `being`
- `breaking`

## API WordPress używane w pliku

- `wp_insert_post()`
- `get_page_by_path()`

## Powiązana dokumentacja

- [Symbole tego pliku](../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
