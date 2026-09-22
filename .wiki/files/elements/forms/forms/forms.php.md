# `elements/forms/forms/forms.php`

Element strony AutoSwitch implementowany przez `Forms`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 20985 B
- **Liczba linii:** 558
- **Źródło:** `elements/forms/forms/forms.php`

## Klasy i metody

### `Forms` — linia 7

- `public static get_data()` — linia 9
- `public static get_presets()` — linia 16
- `private static resolve_group(string $group = '')` — linia 29
- `public static render($group = '', $params = [], $atts = [])` — linia 44

## Funkcje globalne

- `pwe_forms_nav_items()` — linia 96
- `pwe_forms_render_nav(string $active)` — linia 107
- `pwe_forms_hidden_access_fields(int $post_id)` — linia 122
- `pwe_forms_export_dir()` — linia 130
- `pwe_forms_export_url(string $filename)` — linia 147
- `pwe_forms_unique_filename(string $base)` — linia 153
- `pwe_forms_prepare_filename(string $base)` — linia 158
- `pwe_forms_download_snippet(string $file_path, string $filename, string $display_name)` — linia 162
- `pwe_forms_fair_name()` — linia 171
- `pwe_forms_domain_name()` — linia 176
- `pwe_forms_csv_value($value)` — linia 181
- `pwe_forms_csv_file_start(string $filename)` — linia 194
- `pwe_forms_get_views(int $form_id)` — linia 202
- `pwe_forms_form_stats(?array $forms = null)` — linia 209
- `pwe_forms_find_language_field(array $form)` — linia 224
- `pwe_forms_form_columns(array $form)` — linia 235
- `pwe_forms_write_form_csv($handle, int $form_id, string $language = '')` — linia 245
- `pwe_forms_get_qr_url(array $entry, int $form_id)` — linia 284
- `pwe_forms_entry_for_json(array $entry, array $form)` — linia 308
- `pwe_forms_handle_export(int $post_id)` — linia 320
- `pwe_forms_language_map(array $forms)` — linia 480
- `pwe_forms_qr_view(array $forms)` — linia 531
- `pwe_forms_tools_shortcode()` — linia 553

## Rejestracje WordPress wykryte w pliku

- **shortcode:** `gf_download_autoswitch` — linia 557

## Shortcody wywoływane przez plik

- `[trade_fair_domainadress]`
- `[trade_fair_name]`

## Wybrane zależności wywołań

- `GFAPI::count_entries()`
- `GFAPI::get_entries()`
- `GFAPI::get_entry()`
- `GFAPI::get_feeds()`
- `GFAPI::get_form()`
- `GFAPI::get_forms()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## API WordPress używane w pliku

- `apply_filters()`

## Dołączane pliki / wyrażenia include

- `d($post_id)) { return ''`
- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
