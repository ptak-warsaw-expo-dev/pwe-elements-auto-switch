# `includes/class-hooks.php`

Plik warstwy rdzeniowej definiujący klasę `PWE_GF_Email_Entry_Cleanup` i jej logikę pomocniczą/integracyjną.

## Metadane

- **Kategoria:** `core`
- **Rozmiar:** 20398 B
- **Liczba linii:** 555
- **Źródło:** `includes/class-hooks.php`

## Klasy i metody

### `PWE_GF_Email_Entry_Cleanup` — linia 134

- `public static init()` — linia 174
- `public static maybe_run_automatic_cleanup()` — linia 178
- `private static normalize_emails($emails)` — linia 206
- `private static get_emails()` — linia 224
- `private static get_email_prefixes()` — linia 228
- `private static get_cleanup_rules_hash($emails, $prefixes)` — linia 242
- `private static normalize_domain($domain)` — linia 246
- `private static get_current_domain()` — linia 260
- `private static is_current_domain_excluded()` — linia 270
- `private static entry_matches_prefix($entry, $email_field_ids, $prefixes)` — linia 286
- `private static get_email_field_ids($form)` — linia 304
- `private static acquire_lock()` — linia 324
- `private static release_lock()` — linia 333
- `public static run_cleanup()` — linia 337
- `private static save_error_result($message)` — linia 539

## Rejestracje WordPress wykryte w pliku

- **action:** `plugins_loaded` — linia 5
- **action:** `init` — linia 14
- **action:** `template_redirect` — linia 39
- **action:** `gform_after_email` — linia 119
- **action:** `wp_loaded` — linia 175
- **filter:** `pwe_override_menu_output` — linia 6
- **filter:** `gform_save_field_value` — linia 108

## Wybrane zależności wywołań

- `GFAPI::delete_entry()`
- `GFAPI::get_entry()`
- `GFAPI::get_entry_ids()`
- `GFAPI::get_forms()`
- `Menu::render()`
- `PWE_Functions::get_database_fairs_data_files()`
- `PWE_GF_Email_Entry_Cleanup::init()`

## API WordPress używane w pliku

- `get_option()`
- `update_option()`
- `get_transient()`
- `set_transient()`
- `delete_transient()`
- `wp_redirect()`

## Dołączane pliki / wyrażenia include

- `_once $file_visitors`
- `_once $file_exhibitors`

## Powiązana dokumentacja

- [Symbole tego pliku](../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
