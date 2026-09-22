# Klasa `PWE_Functions`

**Źródło:** `includes/class-functions.php:4`  
**Metody:** 76

## Rola

Klasa `PWE_Functions` jest zdefiniowana w pliku `includes/class-functions.php`. Jej dokładna rola wynika z metod i zależności poniżej; dla klas należących do elementów/komponentów dodatkowy opis domenowy znajduje się w `.wiki/elements/` lub `.wiki/components/`.

## Metody

- [`public static set_translation_context($element_slug, $group, $element_type = 'main')`](../methods/PWE_Functions/set_translation_context.md) — linia 14
- [`public static multi_translation($key)`](../methods/PWE_Functions/multi_translation.md) — linia 22
- [`private static load_translation_file($file_path)`](../methods/PWE_Functions/load_translation_file.md) — linia 110
- [`public static assets_per_element($element_slug, $element_type = 'main', $folder = 'elements')`](../methods/PWE_Functions/assets_per_element.md) — linia 128
- [`public static assets_per_group($element_slug, $group, $element_type = 'main', $folder = 'elements', $atts = null)`](../methods/PWE_Functions/assets_per_group.md) — linia 161
- [`public static exhibitor_logos($count = 16, $shuffle = true)`](../methods/PWE_Functions/exhibitor_logos.md) — linia 206
- [`public static get_gf_form_id(string $base_title)`](../methods/PWE_Functions/get_gf_form_id.md) — linia 357
- [`public static render_component($slug, $group = 'all', $params = [])`](../methods/PWE_Functions/render_component.md) — linia 473
- [`public static is_pwe_session_page()`](../methods/PWE_Functions/is_pwe_session_page.md) — linia 507
- [`public static id_rnd()`](../methods/PWE_Functions/id_rnd.md) — linia 574
- [`public static lang()`](../methods/PWE_Functions/lang.md) — linia 582
- [`public static add_log($message, $filename = 'logs')`](../methods/PWE_Functions/add_log.md) — linia 606
- [`private static debug_log($message, $type = 'log')`](../methods/PWE_Functions/debug_log.md) — linia 624
- [`public static output_db_connection_logs()`](../methods/PWE_Functions/output_db_connection_logs.md) — linia 650
- [`private static resolve_server_addr_fallback()`](../methods/PWE_Functions/resolve_server_addr_fallback.md) — linia 676
- [`private static get_database_servers()`](../methods/PWE_Functions/get_database_servers.md) — linia 695
- [`public static connect_database()`](../methods/PWE_Functions/connect_database.md) — linia 754
- [`public static set_db_timeout()`](../methods/PWE_Functions/set_db_timeout.md) — linia 844
- [`private static get_database_json_cache_dir()`](../methods/PWE_Functions/get_database_json_cache_dir.md) — linia 865
- [`private static get_database_json_cache_path($source, $cache_key)`](../methods/PWE_Functions/get_database_json_cache_path.md) — linia 897
- [`private static pack_database_json_value($value)`](../methods/PWE_Functions/pack_database_json_value.md) — linia 911
- [`private static unpack_database_json_value($value)`](../methods/PWE_Functions/unpack_database_json_value.md) — linia 935
- [`private static read_database_json_cache($source, $cache_key)`](../methods/PWE_Functions/read_database_json_cache.md) — linia 962
- [`private static write_database_json_cache($source, $cache_key, $data, array $args = [])`](../methods/PWE_Functions/write_database_json_cache.md) — linia 993
- [`public static refresh_database_json_cache($domain = null)`](../methods/PWE_Functions/refresh_database_json_cache.md) — linia 1054
- [`public static get_database_fairs_data($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data.md) — linia 1450
- [`public static get_database_fairs_data_adds($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_adds.md) — linia 1696
- [`public static get_database_translations_data($fair_domain = null)`](../methods/PWE_Functions/get_database_translations_data.md) — linia 1819
- [`public static get_database_associates_data($fair_domain = null, bool $fair_block = false)`](../methods/PWE_Functions/get_database_associates_data.md) — linia 2042
- [`public static get_database_store_data()`](../methods/PWE_Functions/get_database_store_data.md) — linia 2176
- [`public static get_database_store_packages_data()`](../methods/PWE_Functions/get_database_store_packages_data.md) — linia 2269
- [`public static get_database_meta_data($data_id = null, $domain = null)`](../methods/PWE_Functions/get_database_meta_data.md) — linia 2357
- [`public static get_database_groups_contacts_data()`](../methods/PWE_Functions/get_database_groups_contacts_data.md) — linia 2470
- [`public static get_database_groups_callcenter_data()`](../methods/PWE_Functions/get_database_groups_callcenter_data.md) — linia 2550
- [`public static get_database_groups_data()`](../methods/PWE_Functions/get_database_groups_data.md) — linia 2629
- [`public static get_database_week_data($fair_domain = null)`](../methods/PWE_Functions/get_database_week_data.md) — linia 2708
- [`public static get_database_week_all($fair_domain = null)`](../methods/PWE_Functions/get_database_week_all.md) — linia 2795
- [`public static get_all_week_domains()`](../methods/PWE_Functions/get_all_week_domains.md) — linia 2883
- [`public static get_database_logotypes_data($fair_domain = null)`](../methods/PWE_Functions/get_database_logotypes_data.md) — linia 2970
- [`public static get_database_conferences_data($domain = null)`](../methods/PWE_Functions/get_database_conferences_data.md) — linia 3089
- [`public static get_database_conference_adds_data($conf_id)`](../methods/PWE_Functions/get_database_conference_adds_data.md) — linia 3191
- [`public static get_database_fairs_data_profiles($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_profiles.md) — linia 3287
- [`public static get_database_premieres_data($fair_domain = null)`](../methods/PWE_Functions/get_database_premieres_data.md) — linia 3362
- [`public static get_database_fairs_data_opinions($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_opinions.md) — linia 3445
- [`public static get_database_fairs_data_sectors($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_sectors.md) — linia 3530
- [`public static get_database_fairs_data_tickets($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_tickets.md) — linia 3614
- [`public static get_database_fairs_data_speakers($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_speakers.md) — linia 3698
- [`public static get_database_fairs_data_guests($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_guests.md) — linia 3787
- [`public static get_database_fairs_data_attractions($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_attractions.md) — linia 3874
- [`public static get_database_fairs_data_files($fair_domain = null)`](../methods/PWE_Functions/get_database_fairs_data_files.md) — linia 3961
- [`public static get_database_elements_data()`](../methods/PWE_Functions/get_database_elements_data.md) — linia 4048
- [`public static get_database_elements_order_data()`](../methods/PWE_Functions/get_database_elements_order_data.md) — linia 4141
- [`private static remove_logo_duplicates(array $logos)`](../methods/PWE_Functions/remove_logo_duplicates.md) — linia 4236
- [`public static pwe_color($color)`](../methods/PWE_Functions/pwe_color.md) — linia 4267
- [`public static generate_fair_data($fair)`](../methods/PWE_Functions/generate_fair_data.md) — linia 4292
- [`public static generate_fair_translation_data($fair)`](../methods/PWE_Functions/generate_fair_translation_data.md) — linia 4358
- [`public static json_fairs()`](../methods/PWE_Functions/json_fairs.md) — linia 4396
- [`public static transform_dates($start_date, $end_date, $include_hours = true)`](../methods/PWE_Functions/transform_dates.md) — linia 4498
- [`public static decode_clean_content($encoded_content)`](../methods/PWE_Functions/decode_clean_content.md) — linia 4533
- [`public static json_decode($encoded_variable)`](../methods/PWE_Functions/json_decode.md) — linia 4542
- [`public static findColor($primary, $secondary, $default = '')`](../methods/PWE_Functions/findColor.md) — linia 4551
- [`public static findPalletColorsStatic()`](../methods/PWE_Functions/findPalletColorsStatic.md) — linia 4566
- [`public findPalletColors()`](../methods/PWE_Functions/findPalletColors.md) — linia 4596
- [`public static lang_pl()`](../methods/PWE_Functions/lang_pl.md) — linia 4626
- [`public static languageChecker($pl, $en = '', $de = '')`](../methods/PWE_Functions/languageChecker.md) — linia 4637
- [`public static adjustBrightness($hex, $steps)`](../methods/PWE_Functions/adjustBrightness.md) — linia 4652
- [`public findFormsGF($mode = '')`](../methods/PWE_Functions/findFormsGF.md) — linia 4675
- [`public static findFormsID($form_name)`](../methods/PWE_Functions/findFormsID.md) — linia 4700
- [`public static checkForMobile()`](../methods/PWE_Functions/checkForMobile.md) — linia 4719
- [`public static findBestLogo($logo_color = false)`](../methods/PWE_Functions/findBestLogo.md) — linia 4729
- [`public static findAllImages($firstPath, $image_count = false, $secondPath = '/doc/galeria')`](../methods/PWE_Functions/findAllImages.md) — linia 4779
- [`public static findBestFile($file_path)`](../methods/PWE_Functions/findBestFile.md) — linia 4807
- [`public static isTradeDateExist()`](../methods/PWE_Functions/isTradeDateExist.md) — linia 4826
- [`public static inputRange()`](../methods/PWE_Functions/inputRange.md) — linia 4843
- [`public static input_range_field_html($settings, $value)`](../methods/PWE_Functions/input_range_field_html.md) — linia 4848
- [`public static gravity_forms_smtp_monitor($is_success = null, $to = '', $subject = '', $message = '', $headers = [], $attachments = [], $message_format = '', $from = '', $from_name = '', $bcc = '', $reply_to = '', $entry = false)`](../methods/PWE_Functions/gravity_forms_smtp_monitor.md) — linia 4868

## Dokument pliku

- [Otwórz dokumentację `includes/class-functions.php`](../../files/includes/class-functions.php.md)
