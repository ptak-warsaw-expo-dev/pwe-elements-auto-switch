# `includes/class-functions.php`

Plik warstwy rdzeniowej definiujący klasę `PWE_Functions` i jej logikę pomocniczą/integracyjną.

## Metadane

- **Kategoria:** `core`
- **Rozmiar:** 192441 B
- **Liczba linii:** 5051
- **Źródło:** `includes/class-functions.php`

## Klasy i metody

### `PWE_Functions` — linia 4

- `public static set_translation_context($element_slug, $group, $element_type = 'main')` — linia 14
- `public static multi_translation($key)` — linia 22
- `private static load_translation_file($file_path)` — linia 110
- `public static assets_per_element($element_slug, $element_type = 'main', $folder = 'elements')` — linia 128
- `public static assets_per_group($element_slug, $group, $element_type = 'main', $folder = 'elements', $atts = null)` — linia 161
- `public static exhibitor_logos($count = 16, $shuffle = true)` — linia 206
- `public static get_gf_form_id(string $base_title)` — linia 357
- `public static render_component($slug, $group = 'all', $params = [])` — linia 473
- `public static is_pwe_session_page()` — linia 507
- `public static id_rnd()` — linia 574
- `public static lang()` — linia 582
- `public static add_log($message, $filename = 'logs')` — linia 606
- `private static debug_log($message, $type = 'log')` — linia 624
- `public static output_db_connection_logs()` — linia 650
- `private static resolve_server_addr_fallback()` — linia 676
- `private static get_database_servers()` — linia 695
- `public static connect_database()` — linia 754
- `public static set_db_timeout()` — linia 844
- `private static get_database_json_cache_dir()` — linia 865
- `private static get_database_json_cache_path($source, $cache_key)` — linia 897
- `private static pack_database_json_value($value)` — linia 911
- `private static unpack_database_json_value($value)` — linia 935
- `private static read_database_json_cache($source, $cache_key)` — linia 962
- `private static write_database_json_cache($source, $cache_key, $data, array $args = [])` — linia 993
- `public static refresh_database_json_cache($domain = null)` — linia 1054
- `public static get_database_fairs_data($fair_domain = null)` — linia 1450
- `public static get_database_fairs_data_adds($fair_domain = null)` — linia 1696
- `public static get_database_translations_data($fair_domain = null)` — linia 1819
- `public static get_database_associates_data($fair_domain = null, bool $fair_block = false)` — linia 2042
- `public static get_database_store_data()` — linia 2176
- `public static get_database_store_packages_data()` — linia 2269
- `public static get_database_meta_data($data_id = null, $domain = null)` — linia 2357
- `public static get_database_groups_contacts_data()` — linia 2470
- `public static get_database_groups_callcenter_data()` — linia 2550
- `public static get_database_groups_data()` — linia 2629
- `public static get_database_week_data($fair_domain = null)` — linia 2708
- `public static get_database_week_all($fair_domain = null)` — linia 2795
- `public static get_all_week_domains()` — linia 2883
- `public static get_database_logotypes_data($fair_domain = null)` — linia 2970
- `public static get_database_conferences_data($domain = null)` — linia 3089
- `public static get_database_conference_adds_data($conf_id)` — linia 3191
- `public static get_database_fairs_data_profiles($fair_domain = null)` — linia 3287
- `public static get_database_premieres_data($fair_domain = null)` — linia 3362
- `public static get_database_fairs_data_opinions($fair_domain = null)` — linia 3445
- `public static get_database_fairs_data_sectors($fair_domain = null)` — linia 3530
- `public static get_database_fairs_data_tickets($fair_domain = null)` — linia 3614
- `public static get_database_fairs_data_speakers($fair_domain = null)` — linia 3698
- `public static get_database_fairs_data_guests($fair_domain = null)` — linia 3787
- `public static get_database_fairs_data_attractions($fair_domain = null)` — linia 3874
- `public static get_database_fairs_data_files($fair_domain = null)` — linia 3961
- `public static get_database_elements_data()` — linia 4048
- `public static get_database_elements_order_data()` — linia 4141
- `private static remove_logo_duplicates(array $logos)` — linia 4236
- `public static pwe_color($color)` — linia 4267
- `public static generate_fair_data($fair)` — linia 4292
- `public static generate_fair_translation_data($fair)` — linia 4358
- `public static json_fairs()` — linia 4396
- `public static transform_dates($start_date, $end_date, $include_hours = true)` — linia 4498
- `public static decode_clean_content($encoded_content)` — linia 4533
- `public static json_decode($encoded_variable)` — linia 4542
- `public static findColor($primary, $secondary, $default = '')` — linia 4551
- `public static findPalletColorsStatic()` — linia 4566
- `public findPalletColors()` — linia 4596
- `public static lang_pl()` — linia 4626
- `public static languageChecker($pl, $en = '', $de = '')` — linia 4637
- `public static adjustBrightness($hex, $steps)` — linia 4652
- `public findFormsGF($mode = '')` — linia 4675
- `public static findFormsID($form_name)` — linia 4700
- `public static checkForMobile()` — linia 4719
- `public static findBestLogo($logo_color = false)` — linia 4729
- `public static findAllImages($firstPath, $image_count = false, $secondPath = '/doc/galeria')` — linia 4779
- `public static findBestFile($file_path)` — linia 4807
- `public static isTradeDateExist()` — linia 4826
- `public static inputRange()` — linia 4843
- `public static input_range_field_html($settings, $value)` — linia 4848
- `public static gravity_forms_smtp_monitor($is_success = null, $to = '', $subject = '', $message = '', $headers = [], $attachments = [], $message_format = '', $from = '', $from_name = '', $bcc = '', $reply_to = '', $entry = false)` — linia 4868

## Rejestracje WordPress wykryte w pliku

- **action:** `phpmailer_init` — linia 4948
- **action:** `phpmailer_init` — linia 5039
- **action:** `wp_footer` — linia 5050
- **filter:** `wpdb_connect_timeout` — linia 768

## Shortcody wywoływane przez plik

- `[trade_fair_catalog]`
- `[trade_fair_catalog_id]`
- `[trade_fair_date]`

## Wybrane zależności wywołań

- `GFAPI::get_form()`
- `GFAPI::get_forms()`
- `PWE_Elements_Data::get_all_components()`

## Tabele SQL widoczne statycznie

- `CAP`
- `DateTime`
- `JSON`
- `STATIC`
- `TRANSIENT`
- `all`
- `associates`
- `being`
- `conf_adds`
- `conferences`
- `database`
- `existing`
- `fair_adds`
- `fair_attractions`
- `fair_files`
- `fair_guests`
- `fair_lectures`
- `fair_opinions`
- `fair_premieres`
- `fair_profiles`
- `fair_sectors`
- `fair_tickets`
- `fair_weeks`
- `fairs`
- `form_senders`
- `groups`
- `logos`
- `matching`
- `meta_data`
- `method`
- `plugin`
- `pwe_order`
- `pwelements`
- `shop`
- `shop_packs`
- `the`
- `translations`

## API WordPress używane w pliku

- `get_option()`
- `get_transient()`
- `set_transient()`
- `delete_transient()`
- `wp_enqueue_style()`
- `wp_enqueue_script()`
- `wp_localize_script()`
- `apply_filters()`

## Dołączane pliki / wyrażenia include

- `_once $file`
- `_hours = true) { $format = $include_hours ? "Y/m/d H:i" : "Y/m/d"`

## Powiązana dokumentacja

- [Symbole tego pliku](../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
