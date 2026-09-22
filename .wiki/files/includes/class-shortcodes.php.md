# `includes/class-shortcodes.php`

Plik warstwy rdzeniowej definiujący klasę `PWE_Shortcodes` i jej logikę pomocniczą/integracyjną.

## Metadane

- **Kategoria:** `core`
- **Rozmiar:** 192117 B
- **Liczba linii:** 4337
- **Źródło:** `includes/class-shortcodes.php`

## Klasy i metody

### `PWE_Shortcodes` — linia 5

- `public static init()` — linia 10
- `private __construct()` — linia 17
- `private get_shortcodes_map()` — linia 33
- `private get_gf_shortcodes_map()` — linia 148
- `private get_yoast_shortcodes_map()` — linia 237
- `private other_shortcodes_map()` — linia 261
- `private translates_shortcodes_map()` — linia 321
- `public register_shortcodes()` — linia 339
- `private shorten_value($value, $length = 30)` — linia 352
- `public add_menu()` — linia 363
- `public theme_options_page()` — linia 375
- `public register_settings()` — linia 691
- `public header_section()` — linia 839
- `public display_trade_fair_name()` — linia 845
- `public display_trade_fair_name_eng()` — linia 862
- `public display_trade_fair_desc()` — linia 881
- `public display_trade_fair_desc_eng()` — linia 898
- `public display_trade_fair_desc_short()` — linia 915
- `public display_trade_fair_desc_short_eng()` — linia 932
- `public get_trade_fair_dates()` — linia 949
- `public format_trade_fair_date($start_date, $end_date, $lang = "pl")` — linia 968
- `public display_trade_fair_date_field($lang = "pl")` — linia 1313
- `public display_trade_fair_date()` — linia 1342
- `public display_trade_fair_date_eng()` — linia 1346
- `public display_trade_fair_datetotimer()` — linia 1350
- `public display_trade_fair_enddata()` — linia 1382
- `public display_trade_fair_date_custom_format()` — linia 1414
- `public display_trade_fair_date_multilang()` — linia 1444
- `private get_trade_fair_days()` — linia 1459
- `public display_trade_fair_first_day()` — linia 1481
- `public display_trade_fair_second_day()` — linia 1502
- `public display_trade_fair_third_day()` — linia 1523
- `public display_trade_fair_catalog()` — linia 1545
- `public display_trade_fair_catalog_id()` — linia 1562
- `public display_trade_fair_catalog_archive()` — linia 1579
- `public display_trade_fair_catalog_id_archive()` — linia 1596
- `public display_trade_fair_catalog_year()` — linia 1613
- `public display_trade_fair_conference()` — linia 1631
- `public display_trade_fair_conference_title()` — linia 1648
- `public display_trade_fair_conference_title_eng()` — linia 1665
- `public display_trade_fair_1stbuildday()` — linia 1682
- `public display_trade_fair_2ndbuildday()` — linia 1700
- `public display_trade_fair_1stdismantlday()` — linia 1718
- `public display_trade_fair_2nddismantlday()` — linia 1736
- `public display_trade_fair_actualyear()` — linia 1754
- `public display_trade_fair_branzowy_field($lang = "pl")` — linia 1763
- `public display_trade_fair_branzowy()` — linia 1808
- `public display_trade_fair_branzowy_eng()` — linia 1812
- `public display_trade_fair_hall()` — linia 1816
- `public display_trade_fair_hall_entrance()` — linia 1833
- `public display_trade_fair_edition()` — linia 1850
- `public display_trade_fair_accent()` — linia 1867
- `public display_trade_fair_main2()` — linia 1884
- `public display_trade_fair_badge()` — linia 1900
- `public display_trade_fair_feed_prefix()` — linia 1917
- `public display_trade_fair_domainadress()` — linia 1935
- `public display_trade_fair_facebook()` — linia 1944
- `public display_trade_fair_instagram()` — linia 1961
- `public display_trade_fair_linkedin()` — linia 1978
- `public display_trade_fair_youtube()` — linia 1995
- `private get_group_contact_default_value($groups_slug, $field = 'email')` — linia 2013
- `private show_contact_field_with_default($option_name, $groups_slug, $field = 'email')` — linia 2060
- `private display_contact_field_with_default($option_name, $default_value = '')` — linia 2070
- `public display_trade_fair_rejestracja()` — linia 2084
- `public display_trade_fair_contact()` — linia 2093
- `public display_trade_fair_contact_service_name()` — linia 2098
- `public display_trade_fair_contact_service_phone()` — linia 2102
- `public display_trade_fair_contact_service_email()` — linia 2106
- `public display_trade_fair_contact_media_phone()` — linia 2110
- `public display_trade_fair_contact_media_name()` — linia 2114
- `public display_trade_fair_contact_media_person_name()` — linia 2118
- `public display_trade_fair_contact_media_person_phone()` — linia 2122
- `public display_trade_fair_contact_media_person_email()` — linia 2126
- `public display_trade_fair_contact_media_person_name_2()` — linia 2130
- `public display_trade_fair_contact_media_person_phone_2()` — linia 2134
- `public display_trade_fair_contact_media_person_email_2()` — linia 2138
- `public display_trade_fair_contact_media_person_name_3()` — linia 2142
- `public display_trade_fair_contact_media_person_phone_3()` — linia 2146
- `public display_trade_fair_contact_media_person_email_3()` — linia 2150
- `public display_trade_fair_contact_tech()` — linia 2154
- `public display_trade_fair_contact_media()` — linia 2158
- `public display_trade_fair_lidy()` — linia 2162
- `public display_trade_fair_contact_email_vip()` — linia 2166
- `public display_trade_fair_contact_phone_vip()` — linia 2170
- `public display_trade_fair_contact_medal_ceremony_email()` — linia 2174
- `public days_difference()` — linia 2178
- `public display_trade_fair_registration_benefits_pl()` — linia 2199
- `public display_trade_fair_registration_benefits_en()` — linia 2217
- `public display_trade_fair_ticket_benefits_pl()` — linia 2235
- `public display_trade_fair_ticket_benefits_en()` — linia 2256
- `public display_trade_fair_group()` — linia 2277
- `public show_trade_fair_name()` — linia 2305
- `public show_trade_fair_name_eng()` — linia 2315
- `public show_trade_fair_desc()` — linia 2327
- `public show_trade_fair_desc_eng()` — linia 2334
- `public show_trade_fair_desc_short()` — linia 2341
- `public show_trade_fair_desc_short_eng()` — linia 2352
- `public show_trade_fair_datetotimer()` — linia 2362
- `public show_trade_fair_enddata()` — linia 2381
- `public show_trade_fair_date_custom_format()` — linia 2400
- `public show_trade_fair_date()` — linia 2414
- `public show_trade_fair_date_eng()` — linia 2428
- `public show_trade_fair_date_multilang($atts = [])` — linia 2442
- `private get_trade_fair_day(int $offset = 0)` — linia 2550
- `public show_trade_fair_first_day()` — linia 2604
- `public show_trade_fair_second_day()` — linia 2612
- `public show_trade_fair_third_day()` — linia 2620
- `public show_trade_fair_catalog()` — linia 2628
- `public show_trade_fair_catalog_id()` — linia 2635
- `public show_trade_fair_catalog_archive()` — linia 2642
- `public show_trade_fair_catalog_id_archive()` — linia 2649
- `public show_trade_fair_catalog_year()` — linia 2656
- `public show_trade_fair_conference()` — linia 2663
- `public show_trade_fair_conference_title()` — linia 2673
- `public show_trade_fair_conference_title_eng()` — linia 2683
- `public show_trade_fair_1stbuildday()` — linia 2693
- `public show_trade_fair_2ndbuildday()` — linia 2701
- `public show_trade_fair_1stdismantlday()` — linia 2709
- `public show_trade_fair_2nddismantlday()` — linia 2720
- `public show_trade_fair_hall()` — linia 2731
- `public show_trade_fair_hall_entrance()` — linia 2739
- `public show_trade_fair_edition($entry = null, $fields = null)` — linia 2747
- `public show_trade_fair_accent()` — linia 2765
- `public show_trade_fair_main2()` — linia 2772
- `public trade_fair_branzowy_result($lang = "pl")` — linia 2779
- `public show_trade_fair_branzowy()` — linia 2806
- `public show_trade_fair_branzowy_eng()` — linia 2815
- `public show_trade_fair_badge()` — linia 2824
- `public show_trade_fair_feed_prefix()` — linia 2831
- `public show_trade_fair_facebook()` — linia 2840
- `public show_trade_fair_instagram()` — linia 2850
- `public show_trade_fair_linkedin()` — linia 2860
- `public show_trade_fair_youtube()` — linia 2870
- `public show_trade_fair_domainadress()` — linia 2880
- `get_lang_domain($atts = [])` — linia 2888
- `public show_trade_fair_actualyear()` — linia 2934
- `public show_trade_fair_rejestracja()` — linia 2939
- `public show_trade_fair_contact()` — linia 2949
- `public show_trade_fair_contact_service_name()` — linia 2953
- `public show_trade_fair_contact_service_phone()` — linia 2957
- `public show_trade_fair_contact_service_email()` — linia 2961
- `public show_trade_fair_contact_media_phone()` — linia 2965
- `public show_trade_fair_contact_media_name()` — linia 2969
- `public show_trade_fair_contact_media_person_name()` — linia 2973
- `public show_trade_fair_contact_media_person_phone()` — linia 2977
- `public show_trade_fair_contact_media_person_email()` — linia 2981
- `public show_trade_fair_contact_tech()` — linia 2985
- `public show_trade_fair_contact_media()` — linia 2989
- `public show_trade_fair_lidy()` — linia 2993
- `public show_trade_fair_contact_email_vip()` — linia 2997
- `public show_trade_fair_contact_phone_vip()` — linia 3001
- `public show_trade_fair_contact_medal_ceremony_email()` — linia 3005
- `public show_trade_fair_group()` — linia 3009
- `public show_trade_fair_registration_benefits_pl()` — linia 3021
- `public show_trade_fair_registration_benefits_en()` — linia 3035
- `public show_trade_fair_ticket_benefits_pl()` — linia 3049
- `public show_trade_fair_ticket_benefits_en()` — linia 3066
- `public show_trade_fair_exhibitor_generator_icons()` — linia 3083
- `public show_trade_fair_exhibitor_generator_text()` — linia 3294
- `public show_trade_fair_exhibitor_generator_header_url()` — linia 3332
- `public show_trade_fair_exhibitor_generator_badge_url()` — linia 3371
- `public sc_pwe_trade_fair_full_desc()` — linia 3396
- `get_translated_field($fair, $field_base_name)` — linia 3402
- `get_pwe_shortcode($shortcode, $domain)` — linia 3420
- `check_available_pwe_shortcode($shortcodes_active, $shortcode)` — linia 3426
- `public sc_pwe_text_news()` — linia 3448
- `public sc_pwe_text_for_visitors()` — linia 3456
- `public sc_pwe_text_for_exhibitors()` — linia 3464
- `public sc_pwe_text_add_calendar()` — linia 3472
- `public sc_pwe_text_gallery()` — linia 3480
- `public sc_pwe_text_org_info()` — linia 3488
- `public sc_pwe_text_exh_catalog()` — linia 3496
- `public sc_pwe_text_events()` — linia 3504
- `public sc_pwe_text_contact()` — linia 3512
- `public sc_pwe_text_fair_plan()` — linia 3520
- `public sc_pwe_text_registration()` — linia 3528
- `public sc_pwe_text_promote_yourself()` — linia 3536
- `public sc_pwe_text_become_an_exhibitor()` — linia 3544
- `public sc_pwe_text_store()` — linia 3552
- `public wpseo_register_extra_replacements()` — linia 3560
- `public wpseo_replacements($replacements)` — linia 3568
- `public replace_multilang_date_in_notification($notification, $form, $entry)` — linia 3587
- `private get_language_from_notification_name($notification_name)` — linia 3665
- `public replace_gf_merge_tags($text, $form, $entry, $url_encode, $esc_html, $nl2br, $format)` — linia 3732
- `private read_urls_json_file($json_file)` — linia 3779
- `private get_urls_data()` — linia 3828
- `private get_url_shortcode_language($requested_lang = '')` — linia 3911
- `private get_url_language_data(array $url_entry, $lang)` — linia 3954
- `public show_multilang_url($atts = [], $content = null, $shortcode_tag = '')` — linia 4002
- `private register_url_shortcodes()` — linia 4129
- `private get_gf_url_shortcodes_map()` — linia 4158
- `public show_pwe_mailing_header_url()` — linia 4190
- `public show_pwe_mailing_header_platyna_url($requested_lang = '')` — linia 4258

## Rejestracje WordPress wykryte w pliku

- **action:** `admin_menu` — linia 19
- **action:** `admin_init` — linia 20
- **action:** `init` — linia 22
- **filter:** `wpseo_register_extra_replacements` — linia 24
- **filter:** `wpseo_replacements` — linia 25
- **filter:** `gform_replace_merge_tags` — linia 26
- **filter:** `gform_notification` — linia 28

## Shortcody wywoływane przez plik

- `[pwe_badge]`
- `[pwe_catalog]`
- `[pwe_catalog_archive]`
- `[pwe_catalog_id]`
- `[pwe_catalog_id_archive]`
- `[pwe_color_accent]`
- `[pwe_color_main2]`
- `[pwe_conference_name]`
- `[pwe_conference_title_en]`
- `[pwe_conference_title_pl]`
- `[pwe_date_end]`
- `[pwe_date_start]`
- `[pwe_desc_en]`
- `[pwe_desc_pl]`
- `[pwe_edition]`
- `[pwe_facebook]`
- `[pwe_hall]`
- `[pwe_hall_entrance]`
- `[pwe_instagram]`
- `[pwe_linkedin]`
- `[pwe_name_en]`
- `[pwe_name_pl]`
- `[pwe_short_desc_en]`
- `[pwe_short_desc_pl]`
- `[pwe_youtube]`
- `[trade_fair_catalog_year]`
- `[trade_fair_date_custom_format]`
- `[trade_fair_date_multilang]`
- `[trade_fair_group]`
- `[trade_fair_name]`
- `[trade_fair_name_eng]`

## Wybrane zależności wywołań

- `PWE_Functions::get_database_fairs_data_files()`
- `PWE_Functions::get_database_groups_contacts_data()`
- `PWE_Functions::get_database_groups_data()`
- `PWE_Functions::get_database_translations_data()`
- `PWE_Functions::lang()`
- `PWE_Functions::transform_dates()`
- `PWE_Shortcodes::init()`

## Tabele SQL widoczne statycznie

- `Gravity`
- `an`
- `conferences`
- `date`
- `free`
- `notification`
- `pwe`
- `the`

## API WordPress używane w pliku

- `get_option()`

## Powiązana dokumentacja

- [Symbole tego pliku](../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
