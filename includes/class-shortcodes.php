<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class PWE_Shortcodes {

    private static $instance;

    public static function init() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // menu and settings
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);

        add_action('init', [$this, 'register_shortcodes'], 20);

        add_filter('wpseo_register_extra_replacements', [$this, 'wpseo_register_extra_replacements']);
        add_filter('wpseo_replacements', [$this, 'wpseo_replacements']);
        add_filter('gform_replace_merge_tags', [$this, 'replace_gf_merge_tags'], 10, 7);
    }

    // ALL SHORTCODES START <------------------------------------------------------------------------------<

    private function get_shortcodes_map() {
        return [
            'trade_fair_name' => 'show_trade_fair_name', // [trade_fair_name]
            'trade_fair_name_eng' => 'show_trade_fair_name_eng', // [trade_fair_name_eng]
            'trade_fair_desc' => 'show_trade_fair_desc',
            'trade_fair_desc_eng' => 'show_trade_fair_desc_eng',
            'trade_fair_desc_short' => 'show_trade_fair_desc_short',
            'trade_fair_desc_short_eng' => 'show_trade_fair_desc_short_eng',
            'trade_fair_datetotimer' => 'show_trade_fair_datetotimer',
            'trade_fair_enddata' => 'show_trade_fair_enddata',
            'trade_fair_date_custom_format' => 'show_trade_fair_date_custom_format',
            'trade_fair_catalog' => 'show_trade_fair_catalog',
            'trade_fair_catalog_id' => 'show_trade_fair_catalog_id',
            'trade_fair_catalog_year' => 'show_trade_fair_catalog_year',
            'trade_fair_conferance' => 'show_trade_fair_conferance',
            'trade_fair_conferance_eng' => 'show_trade_fair_conferance_eng',
            'trade_fair_1stbuildday' => 'show_trade_fair_1stbuildday',
            'trade_fair_2ndbuildday' => 'show_trade_fair_2ndbuildday',
            'trade_fair_1stdismantlday' => 'show_trade_fair_1stdismantlday',
            'trade_fair_2nddismantlday' => 'show_trade_fair_2nddismantlday',
            'trade_fair_date' => 'show_trade_fair_date',
            'trade_fair_date_eng' => 'show_trade_fair_date_eng',
            'trade_fair_date_multilang' => 'show_trade_fair_date_multilang',
            'trade_fair_edition' => 'show_trade_fair_edition',
            'trade_fair_accent' => 'show_trade_fair_accent',
            'trade_fair_main2' => 'show_trade_fair_main2',
            'trade_fair_branzowy' => 'show_trade_fair_branzowy',
            'trade_fair_branzowy_eng' => 'show_trade_fair_branzowy_eng',
            'trade_fair_badge' => 'show_trade_fair_badge',
            'trade_fair_opisbranzy' => 'show_trade_fair_opisbranzy',
            'trade_fair_opisbranzy_eng' => 'show_trade_fair_opisbranzy_eng',
            'trade_fair_facebook' => 'show_trade_fair_facebook',
            'trade_fair_instagram' => 'show_trade_fair_instagram',
            'trade_fair_linkedin' => 'show_trade_fair_linkedin',
            'trade_fair_youtube' => 'show_trade_fair_youtube',
            'first_day' => 'show_first_day',
            'first_day_eng' => 'show_first_day_eng',
            'second_day' => 'show_second_day',
            'second_day_eng' => 'show_second_day_eng',
            'third_day' => 'show_third_day',
            'third_day_eng' => 'show_third_day_eng',
            'super_shortcode_1' => 'show_super_shortcode_1',
            'super_shortcode_2' => 'show_super_shortcode_2',
            'trade_fair_domainadress' => 'show_trade_fair_domainadress',
            'trade_fair_actualyear' => 'show_trade_fair_actualyear',
            'trade_fair_rejestracja' => 'show_trade_fair_rejestracja',
            'trade_fair_contact' => 'show_trade_fair_contact',
            'trade_fair_contact_tech' => 'show_trade_fair_contact_tech',
            'trade_fair_contact_media' => 'show_trade_fair_contact_media',
            'trade_fair_lidy' => 'show_trade_fair_lidy',
            'trade_fair_group' => 'show_trade_fair_group',
            'trade_fair_registration_benefits_pl' => 'show_trade_fair_registration_benefits_pl',
            'trade_fair_registration_benefits_en' => 'show_trade_fair_registration_benefits_en',
            'trade_fair_ticket_benefits_pl' => 'show_trade_fair_ticket_benefits_pl',
            'trade_fair_ticket_benefits_en' => 'show_trade_fair_ticket_benefits_en',

            // shortcodes for Yoast SEO
            'trade_fair_full_desc' => 'sc_pwe_trade_fair_full_desc',
            'sc_pwe_text_news' => 'sc_pwe_text_news',
            'sc_pwe_text_for_visitors' => 'sc_pwe_text_for_visitors',
            'sc_pwe_text_for_exhibitors' => 'sc_pwe_text_for_exhibitors',
            'sc_pwe_text_add_calendar' => 'sc_pwe_text_add_calendar',
            'sc_pwe_text_gallery' => 'sc_pwe_text_gallery',
            'sc_pwe_text_org_info' => 'sc_pwe_text_org_info',
            'sc_pwe_text_exh_catalog' => 'sc_pwe_text_exh_catalog',
            'sc_pwe_text_events' => 'sc_pwe_text_events',
            'sc_pwe_text_contact' => 'sc_pwe_text_contact',
            'sc_pwe_text_fair_plan' => 'sc_pwe_text_fair_plan',
            'sc_pwe_text_registration' => 'sc_pwe_text_registration',
            'sc_pwe_text_promote_yourself' => 'sc_pwe_text_promote_yourself',
            'sc_pwe_text_become_an_exhibitor' => 'sc_pwe_text_become_an_exhibitor',
            'sc_pwe_text_store' => 'sc_pwe_text_store',
        ];
    }

    private function get_gf_shortcodes_map() {
        return [
            'trade_fair_name' => 'show_trade_fair_name', // {trade_fair_name}
            'trade_fair_name_eng' => 'show_trade_fair_name_eng', // {trade_fair_name_eng}
            'trade_fair_desc' => 'show_trade_fair_desc',
            'trade_fair_desc_eng' => 'show_trade_fair_desc_eng',
            'trade_fair_desc_short' => 'show_trade_fair_desc_short',
            'trade_fair_desc_short_eng' => 'show_trade_fair_desc_short_eng',
            'trade_fair_datetotimer' => 'show_trade_fair_datetotimer',
            'trade_fair_enddata' => 'show_trade_fair_enddata',
            'trade_fair_catalog' => 'show_trade_fair_catalog',
            'trade_fair_catalog_year' => 'show_trade_fair_catalog_year',
            'trade_fair_conferance' => 'show_trade_fair_conferance',
            'trade_fair_conferance_eng' => 'show_trade_fair_conferance_eng',
            'trade_fair_1stbuildday' => 'show_trade_fair_1stbuildday',
            'trade_fair_2ndbuildday' => 'show_trade_fair_2ndbuildday',
            'trade_fair_1stdismantlday' => 'show_trade_fair_1stdismantlday',
            'trade_fair_2nddismantlday' => 'show_trade_fair_2nddismantlday',
            'trade_fair_date' => 'show_trade_fair_date',
            'trade_fair_date_eng' => 'show_trade_fair_date_eng',
            'trade_fair_accent' => 'show_trade_fair_accent',
            'trade_fair_edition' => 'show_trade_fair_edition',
            'trade_fair_main2' => 'show_trade_fair_main2',
            'trade_fair_branzowy' => 'show_trade_fair_branzowy',
            'trade_fair_branzowy_eng' => 'show_trade_fair_branzowy_eng',
            'trade_fair_badge' => 'show_trade_fair_badge',
            'trade_fair_opisbranzy' => 'show_trade_fair_opisbranzy',
            'trade_fair_opisbranzy_eng' => 'show_trade_fair_opisbranzy_eng',
            'trade_fair_facebook' => 'show_trade_fair_facebook',
            'trade_fair_instagram' => 'show_trade_fair_instagram',
            'trade_fair_linkedin' => 'show_trade_fair_linkedin',
            'trade_fair_youtube' => 'show_trade_fair_youtube',
            'trade_fair_domainadress' => 'show_trade_fair_domainadress',
            'trade_fair_actualyear' => 'show_trade_fair_actualyear',
            'trade_fair_rejestracja' => 'show_trade_fair_rejestracja',
            'trade_fair_contact' => 'show_trade_fair_contact',
            'trade_fair_contact_tech' => 'show_trade_fair_contact_tech',
            'trade_fair_contact_media' => 'show_trade_fair_contact_media',
            'trade_fair_lidy' => 'show_trade_fair_lidy',
            'trade_fair_registration_benefits_pl' => 'show_trade_fair_registration_benefits_pl',
            'trade_fair_registration_benefits_en' => 'show_trade_fair_registration_benefits_en',
            'trade_fair_ticket_benefits_pl' => 'show_trade_fair_ticket_benefits_pl',
            'trade_fair_ticket_benefits_en' => 'show_trade_fair_ticket_benefits_en',
        ];
    }

    private function get_yoast_shortcodes_map() {
        $lang = ICL_LANGUAGE_CODE;
        return [
            'sc_pwe_trade_fair_year'          => 'show_trade_fair_catalog_year', // %%sc_pwe_trade_fair_year%% || [trade_fair_catalog_year]
            'sc_pwe_trade_fair_desc'          => $lang === 'pl' ? 'show_trade_fair_desc' : 'show_trade_fair_desc_eng', // %%sc_pwe_trade_fair_desc%% || [trade_fair_desc] && [trade_fair_desc_eng]
            'sc_pwe_trade_fair_full_desc'     => 'sc_pwe_trade_fair_full_desc', // %%sc_pwe_trade_fair_full_desc%% || [trade_fair_full_desc]
            'sc_pwe_text_news'                => 'sc_pwe_text_news', // %%sc_pwe_text_news%% || [sc_pwe_text_news]
            'sc_pwe_text_for_visitors'        => 'sc_pwe_text_for_visitors', // %%sc_pwe_text_for_visitors%% || [sc_pwe_text_for_visitors]
            'sc_pwe_text_for_exhibitors'      => 'sc_pwe_text_for_exhibitors', // %%sc_pwe_text_for_exhibitors%% || [sc_pwe_text_for_exhibitors]
            'sc_pwe_text_add_calendar'        => 'sc_pwe_text_add_calendar', // %%sc_pwe_text_add_calendar%% || [sc_pwe_text_add_calendar]
            'sc_pwe_text_gallery'             => 'sc_pwe_text_gallery', // %%sc_pwe_text_gallery%% || [sc_pwe_text_gallery]
            'sc_pwe_text_org_info'            => 'sc_pwe_text_org_info', // %%sc_pwe_text_org_info%% || [sc_pwe_text_org_info]
            'sc_pwe_text_exh_catalog'         => 'sc_pwe_text_exh_catalog', // %%sc_pwe_text_exh_catalog%% || [sc_pwe_text_exh_catalog]
            'sc_pwe_text_events'              => 'sc_pwe_text_events', // %%sc_pwe_text_events%% || [sc_pwe_text_events]
            'sc_pwe_text_contact'             => 'sc_pwe_text_contact', // %%sc_pwe_text_contact%% || [sc_pwe_text_contact]
            'sc_pwe_text_fair_plan'           => 'sc_pwe_text_fair_plan', // %%sc_pwe_text_fair_plan%% || [sc_pwe_text_fair_plan]
            'sc_pwe_text_registration'        => 'sc_pwe_text_registration', // %%sc_pwe_text_registration%% || [sc_pwe_text_registration]
            'sc_pwe_text_promote_yourself'    => 'sc_pwe_text_promote_yourself', // %%sc_pwe_text_promote_yourself%% || [sc_pwe_text_promote_yourself]
            'sc_pwe_text_become_an_exhibitor' => 'sc_pwe_text_become_an_exhibitor', // %%sc_pwe_text_become_an_exhibitor%% || [sc_pwe_text_become_an_exhibitor]
            'sc_pwe_text_store'               => 'sc_pwe_text_store', // %%sc_pwe_text_store%% || [sc_pwe_text_store]
        ];
    }

    // ALL SHORTCODES END <------------------------------------------------------------------------------<

    public function register_shortcodes() {
        // If the shortcode already exists, remove it
        foreach ($this->get_shortcodes_map() as $tag => $callback) {
            if (shortcode_exists($tag)) {
                remove_shortcode($tag);
            }
            add_shortcode($tag, [$this, $callback]);
        }
    }

    private function shorten_value($value, $length = 30) {
        $value = wp_strip_all_tags((string) $value);
        $value = trim($value);

        if (mb_strlen($value) <= $length) {
            return $value;
        }

        return mb_substr($value, 0, $length) . '...';
    }

    public function add_menu() {
        add_menu_page(
            "PWE Shortcodes",
            "PWE Shortcodes",
            "manage_options",
            "pwe-shortcodes",
            [$this, 'theme_options_page'],
            'dashicons-editor-code',
            100
        );
    }

    public function theme_options_page() {

        $shortcodes = array_keys($this->get_shortcodes_map());

        ?>
        <style>
            #pweShortcodes .form-table tbody {
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                gap: 10px;
            }
            #pweShortcodes .form-table tr {
                display: flex;
                align-items: center;
                box-shadow: 0 0 10px #dedede;
                border-radius: 10px;
                padding: 10px;
                gap: 20px;
                width: 100%;
            }
            #pweShortcodes .form-table tr th,
            #pweShortcodes .form-table tr td {
                padding: 0 !important;
                margin: 0 !important;
                min-width: 250px;
            }
            #pweShortcodes .form-table tr td {
                width: 80%;
            }
            #pweShortcodes .form-wrap .form-field,
            #pweShortcodes .form-wrap p {
                margin: 0 !important;
            }

            #pweShortcodes details.shortcodes-box {
                margin: 10px 0;
            }
            #pweShortcodes details summary {
                cursor: pointer;
                font-weight: 600;
            }
            #pweShortcodes details ul {
                margin: 10px 0 0 20px;
            }
            #pweShortcodes details code {
                background: #f4f4f4;
                padding: 2px 6px;
                border-radius: 3px;
            }
            #pweShortcodes .form-wrap p.submit {
                margin: 20px 0 !important;
            }
        </style>

        <div id="pweShortcodes" class="pwe-shortcodes wrap" style="margin-top:40px">
            <?php settings_errors(); ?>
            <div class="postbox-container">
                <div class="col-wrap">
                    <div class="postbox">
                        <div class="inside">
                            <div class="main">
                                <p style="text-align:center;"><strong>O PWE Shortcodes</strong></p>
                                <hr>
                                <p>
                                    Wtyczka pobiera dane z bazy danych CAP (PWE Centralized Administration Panel) i na ich podstawie generuje shortcody,
                                    które można wykorzystać w dowolnym miejscu na stronie WordPress oraz w formularzach Gravity Forms.
                                </p>
                                <p><strong>Przykład użycia na stronie:</strong> <code>[trade_fair_name]</code></p>

                                <details class="shortcodes-box">
                                    <summary>Dostępne shortkody (WordPress)</summary>
                                    <ul>
                                        <?php
                                        foreach ($this->get_shortcodes_map() as $tag => $callback) {

                                            $value = '';
                                            if (is_callable([$this, $callback])) {
                                                $value = call_user_func([$this, $callback]);
                                            }

                                            echo '
                                            <li>
                                                <code>[' . esc_html($tag) . ']</code>
                                                <small style="margin-left:10px;color:#666;">'
                                                . esc_html($this->shorten_value($value)) .
                                                '</small>
                                            </li>';
                                        }
                                        ?>
                                    </ul>
                                </details>

                                <p><strong>Przykład użycia w formularzu Gravity Forms:</strong> <code>{trade_fair_name}</code></p>

                                <details class="shortcodes-box">
                                    <summary>Dostępne shortkody Gravity Forms</summary>
                                    <ul>
                                        <?php
                                        foreach ($this->get_gf_shortcodes_map() as $tag => $callback) {

                                            $value = '';
                                            if (is_callable([$this, $callback])) {
                                                $value = call_user_func([$this, $callback]);
                                            }

                                            echo '
                                            <li>
                                                <code>{' . esc_html($tag) . '}</code>
                                                <small style="margin-left:10px;color:#666;">'
                                                . esc_html($this->shorten_value($value)) .
                                                '</small>
                                            </li>';
                                        }
                                        ?>
                                    </ul>
                                </details>

                                <p><strong>Przykład użycia w polach Yoast SEO:</strong> <code>%%sc_pwe_trade_fair_desc%%</code></p>

                                <details class="shortcodes-box">
                                    <summary>Dostępne shortkody Yoast SEO</summary>
                                    <ul>
                                        <?php
                                        foreach ($this->get_yoast_shortcodes_map() as $key => $callback) {

                                            $value = '';

                                            if (is_callable([$this, $callback])) {
                                                $value = call_user_func([$this, $callback]);
                                            }

                                            echo '
                                            <li>
                                                <code>%%' . esc_html($key) . '%%</code>
                                                <small style="margin-left:10px;color:#666;">'
                                                    . esc_html($this->shorten_value($value)) .
                                                '</small>
                                            </li>';
                                        }
                                        ?>
                                    </ul>
                                </details>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="postbox-container">
                <div class="col-wrap">
                    <div class="form-wrap">
                        <form method="POST" action="options.php" enctype="multipart/form-data">
                            <div class="postbox">
                                <div class="inside">
                                    <div class="main">
                                        <?php
                                        settings_fields("pwe_code_checker");
                                        do_settings_sections("pwe-code-checker");
                                        submit_button();
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function register_settings() {

        // Main section
        add_settings_section("pwe_code_checker", "PWE Shortcodes", [$this, 'header_section'], "pwe-code-checker");

        // List of fields for registration
        $fields = [
            'trade_fair_name' => 'Nazwa targów PL<hr><p>[trade_fair_name]</p>',
            'trade_fair_name_eng' => 'Nazwa targów EN<hr><p>[trade_fair_name_eng]</p>',
            'trade_fair_catalog' => 'ID katalogu wystawców (OLD)<hr><p>[trade_fair_catalog]</p>',
            'trade_fair_catalog_id' => 'ID/IDs katalogu/ów wystawców (NEW)<hr><p>[trade_fair_catalog_id]</p>',
            'trade_fair_catalog_year' => 'Rok aktualnego katalogu wystawców<hr><p>[trade_fair_catalog_year]</p>',
            'trade_fair_desc' => 'Opis targów PL<hr><p>[trade_fair_desc]</p>',
            'trade_fair_desc_eng' => 'Opis targów EN<hr><p>[trade_fair_desc_eng]</p>',
            'trade_fair_desc_short' => 'Skrócony Opis targów PL<hr><p>[trade_fair_desc_short]</p>',
            'trade_fair_desc_short_eng' => 'Skrócony Opis targów EN<hr><p>[trade_fair_desc_short_eng]</p>',
            'trade_fair_datetotimer' => 'Data targów do licznika<hr><p>[trade_fair_datetotimer]</p>',
            'trade_fair_enddata' => 'Data zakończenia targów do licznika<hr><p>[trade_fair_enddata]</p>',
            'trade_fair_date_custom_format' => 'Data targów [D-D|M|Y]<hr><p>[trade_fair_date_custom_format]</p>',
            'trade_fair_date' => 'Data Targów PL<hr><p>[trade_fair_date]</p>',
            'trade_fair_date_eng' => 'Data Targów EN<hr><p>[trade_fair_date_eng]</p>',
            'trade_fair_edition' => 'Numer Edycji targów<hr><p>[trade_fair_edition]</p>',
            'trade_fair_accent' => 'Kolor Accent (Main) strony<hr><p>[trade_fair_accent]</p>',
            'trade_fair_main2' => 'Kolor Main2 (secondary)<hr><p>[trade_fair_main2]</p>',
            'trade_fair_conferance' => 'Główna nazwa konferencji <hr><p>[trade_fair_conferance]</p>',
            'trade_fair_conferance_eng' => 'Główna nazwa konferencji (ENG) <hr><p>[trade_fair_conferance_eng]</p>',
            'trade_fair_1stbuildday' => 'Data pierwszego dnia zabudowy<hr><p>[trade_fair_1stbuildday]</p>',
            'trade_fair_2ndbuildday' => 'Data drugiego dnia zabudowy<hr><p>[trade_fair_2ndbuildday]</p>',
            'trade_fair_1stdismantlday' => 'Data pierwszego dnia rozbiórki<hr><p>[trade_fair_1stdismantlday]</p>',
            'trade_fair_2nddismantlday' => 'Data drugiego dnia rozbiórki<hr><p>[trade_fair_2nddismantlday]</p>',
            'trade_fair_branzowy' => 'Data dni branżowych targów<hr><p>[trade_fair_branzowy]</p>',
            'trade_fair_branzowy_eng' => 'Data dni branżowych targów (ENG)<hr><p>[trade_fair_branzowy_eng]</p>',
            'trade_fair_badge' => 'Początek nazwy badge -> ..._gosc_a6 <hr><p>[trade_fair_badge]</p>',
            'trade_fair_opisbranzy' => 'Krótki opis branży<hr><p>[trade_fair_opisbranzy]</p>',
            'trade_fair_opisbranzy_eng' => 'Krótki opis branży ENG <hr><p>[trade_fair_opisbranzy_eng]</p>',
            'trade_fair_facebook' => 'Adres wydarzenia na facebook<hr><p>[trade_fair_facebook]</p>',
            'trade_fair_instagram' => 'Adres wydarzenia na instagram<hr><p>[trade_fair_instagram]</p>',
            'trade_fair_linkedin' => 'Adres wydarzenia na linkedin<hr><p>[trade_fair_linkedin]</p>',
            'trade_fair_youtube' => 'Adres wydarzenia na youtube<hr><p>[trade_fair_youtube]</p>',
            'first_day' => 'Pierwszy dzień targów<hr><p>[first_day]</p>',
            'second_day' => 'Drugi dzień targów<hr><p>[second_day]</p>',
            'third_day' => 'Trzeci dzień targów<hr><p>[third_day]</p>',
            'first_day_eng' => 'Pierwszy dzień targów (ENG)<hr><p>[first_day_eng]</p>',
            'second_day_eng' => 'Drugi dzień targów (ENG)<hr><p>[second_day_eng]</p>',
            'third_day_eng' => 'Trzeci dzień targów (ENG)<hr><p>[third_day_eng]</p>',
            'super_shortcode_1' => 'Shortcode dodatkowy 1<hr><p>[super_shortcode_1]</p>',
            'super_shortcode_2' => 'Shortcode dodatkowy 2<hr><p>[super_shortcode_2]</p>',
            'trade_fair_rejestracja' => 'Adres email do automatycznej odpowiedzi<hr><p>[trade_fair_rejestracja]</p>',
            'trade_fair_contact' => 'Adres email do formularza kontaktu<hr><p>[trade_fair_contact]</p>',
            'trade_fair_contact_tech' => 'Adres email do formularza kontaktu działu technicznego<hr><p>[trade_fair_contact_tech]</p>',
            'trade_fair_contact_media' => 'Adres email do formularza kontaktu działu marketingowego i media<hr><p>[trade_fair_contact_media]</p>',
            'trade_fair_lidy' => 'Adres email do wysyłania lidów<hr><p>[trade_fair_lidy]</p>',
            'trade_fair_registration_benefits_pl' => 'Benefity rejestracyjne PL<hr><p>[trade_fair_registration_benefits_pl]</p>',
            'trade_fair_registration_benefits_en' => 'Benefity rejestracyjne EN<hr><p>[trade_fair_registration_benefits_en]</p>',
            'trade_fair_ticket_benefits_pl' => 'Benefity biletowe PL<hr><p>[trade_fair_ticket_benefits_pl]</p>',
            'trade_fair_ticket_benefits_en' => 'Benefity biletowe EN<hr><p>[trade_fair_ticket_benefits_en]</p>',
            'trade_fair_group' => 'Grupa targów<hr><p>[trade_fair_group]</p>',
            'trade_fair_domainadress' => 'Adres strony<hr><p>[trade_fair_domainadress]</p>',
            'trade_fair_actualyear' => 'Aktualny rok<hr><p>[trade_fair_actualyear]</p>',
        ];

        // Rejestrujemy pola
        foreach ($fields as $key => $label) {
            add_settings_field($key, $label, [$this, "display_{$key}"], "pwe-code-checker", "pwe_code_checker");
            register_setting("pwe_code_checker", $key);
        }
    }

    public function header_section() { echo ""; }



    // CREATE FIELDS <----------------------------------------------------------------------<

    public function display_trade_fair_name() {
        $pwe_name_pl = shortcode_exists("pwe_name_pl") ? do_shortcode('[pwe_name_pl]') : "";
        $pwe_name_pl_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_name_pl) && $pwe_name_pl !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_name_pl_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text" 
                    name="trade_fair_name" 
                    id="trade_fair_name" 
                    value="<?php echo $pwe_name_pl_available ? $pwe_name_pl : get_option('trade_fair_name'); ?>" 
                />
                <p><?php echo $pwe_name_pl_available ? "Dane pobrane z CAP DB" : "np. Warsaw Fleet Expo"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_name_eng() {
        $pwe_name_pl = shortcode_exists("pwe_name_pl") ? do_shortcode('[pwe_name_pl]') : "";
        $pwe_name_en = shortcode_exists("pwe_name_en") ? do_shortcode('[pwe_name_en]') : "";
        $pwe_name_en = !empty($pwe_name_en) ? $pwe_name_en : $pwe_name_pl; 
        $pwe_name_en_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_name_en) && $pwe_name_en !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_name_en_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text" 
                    name="trade_fair_name_eng" 
                    id="trade_fair_name_eng" 
                    value="<?php echo $pwe_name_en_available ? $pwe_name_pl : get_option('trade_fair_name_eng'); ?>" 
                />
                <p><?php echo $pwe_name_en_available ? "Dane pobrane z CAP DB" : "np. Warsaw Fleet Expo"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_desc() {
        $pwe_desc_pl = shortcode_exists("pwe_desc_pl") ? do_shortcode('[pwe_desc_pl]') : "";
        $pwe_desc_pl_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_desc_pl) && $pwe_desc_pl !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_desc_pl_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_desc" 
                    id="trade_fair_desc" 
                    value="<?php echo $pwe_desc_pl_available ? $pwe_desc_pl : get_option('trade_fair_desc'); ?>" 
                />
                <p><?php echo $pwe_desc_pl_available ? "Dane pobrane z CAP DB" : "np. Międzynarodowe targi bla bla bla"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_desc_eng() {
        $pwe_desc_en = shortcode_exists("pwe_desc_en") ? do_shortcode('[pwe_desc_en]') : "";
        $pwe_desc_en_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_desc_en) && $pwe_desc_en !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_desc_en_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_desc_eng" 
                    id="trade_fair_desc_eng" 
                    value="<?php echo $pwe_desc_en_available ? $pwe_desc_en : get_option('trade_fair_desc_eng'); ?>" 
                />
                <p><?php echo $pwe_desc_en_available ? "Dane pobrane z CAP DB" : "np. Międzynarodowe targi bla bla bla"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_desc_short() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_desc_short" id="trade_fair_desc_short" value="<?php echo get_option('trade_fair_desc_short'); ?>" />
                <p>"np. Międzynarodowe targi bla bla bla"</p>
            </div>
        <?php
    }

    public function display_trade_fair_desc_short_eng() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_desc_short_eng" id="trade_fair_desc_short_eng" value="<?php echo get_option('trade_fair_desc_short_eng'); ?>" />
                <p>"np. Międzynarodowe targi bla bla bla"</p>
            </div>
        <?php
    }

    public function get_trade_fair_dates() {
        $pwe_shortcodes_available = empty(get_option("pwe_general_options", [])["pwe_dp_shortcodes_unactive"]);

        $pwe_date_start = shortcode_exists("pwe_date_start") ? do_shortcode("[pwe_date_start]") : "";
        $pwe_date_start_available = (empty(get_option("pwe_general_options", [])["pwe_dp_shortcodes_unactive"]) && !empty($pwe_date_start));
        $pwe_date_end = shortcode_exists("pwe_date_end") ? do_shortcode("[pwe_date_end]") : "";
        $pwe_date_end_available = (empty(get_option("pwe_general_options", [])["pwe_dp_shortcodes_unactive"]) && !empty($pwe_date_end));

        // Getting dates or default values
        $start_date = $pwe_date_start_available ? $pwe_date_start : get_option("trade_fair_datetotimer");
        $end_date = $pwe_date_end_available ? $pwe_date_end : get_option("trade_fair_enddata");

        // Remove time from date if exists
        $start_date = preg_replace("/^(\d{4}\/\d{2}\/\d{2}) \d{2}:\d{2}$/", "$1", $start_date);
        $end_date = preg_replace("/^(\d{4}\/\d{2}\/\d{2}) \d{2}:\d{2}$/", "$1", $end_date);

        return [$start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available];
    }

    public function format_trade_fair_date($start_date, $end_date, $lang = "pl") {
        $months = [
            "pl" => [
                "01" => "stycznia",
                "02" => "lutego",
                "03" => "marca",
                "04" => "kwietnia",
                "05" => "maja",
                "06" => "czerwca",
                "07" => "lipca",
                "08" => "sierpnia",
                "09" => "września",
                "10" => "października",
                "11" => "listopada",
                "12" => "grudnia",
            ],
            "en" => [
                "01" => "january",
                "02" => "february",
                "03" => "march",
                "04" => "april",
                "05" => "may",
                "06" => "june",
                "07" => "july",
                "08" => "august",
                "09" => "september",
                "10" => "october",
                "11" => "november",
                "12" => "december",
            ],
            "de" => [
                "01" => "januar",
                "02" => "februar",
                "03" => "märz",
                "04" => "april",
                "05" => "mai",
                "06" => "juni",
                "07" => "juli",
                "08" => "august",
                "09" => "september",
                "10" => "oktober",
                "11" => "november",
                "12" => "dezember",
            ],
            "lt" => [
                "01" => "sausio",
                "02" => "vasario",
                "03" => "kovo",
                "04" => "balandžio",
                "05" => "gegužės",
                "06" => "birželio",
                "07" => "liepos",
                "08" => "rugpjūčio",
                "09" => "rugsėjo",
                "10" => "spalio",
                "11" => "lapkričio",
                "12" => "gruodžio",
            ],
            "lv" => [
                "01" => "janvāris",
                "02" => "februāris",
                "03" => "marts",
                "04" => "aprīlis",
                "05" => "maijs",
                "06" => "jūnijs",
                "07" => "jūlijs",
                "08" => "augusts",
                "09" => "septembris",
                "10" => "oktobris",
                "11" => "novembris",
                "12" => "decembris",
            ],
            "uk" => [
                "01" => "січня",
                "02" => "лютого",
                "03" => "березня",
                "04" => "квітня",
                "05" => "травня",
                "06" => "червня",
                "07" => "липня",
                "08" => "серпня",
                "09" => "вересня",
                "10" => "жовтня",
                "11" => "листопада",
                "12" => "грудня",
            ],
            "cs" => [
                "01" => "ledna",
                "02" => "února",
                "03" => "března",
                "04" => "dubna",
                "05" => "května",
                "06" => "června",
                "07" => "července",
                "08" => "srpna",
                "09" => "září",
                "10" => "října",
                "11" => "listopadu",
                "12" => "prosince",
            ],
            "sk" => [
                "01" => "januára",
                "02" => "februára",
                "03" => "marca",
                "04" => "apríla",
                "05" => "mája",
                "06" => "júna",
                "07" => "júla",
                "08" => "augusta",
                "09" => "septembra",
                "10" => "októbra",
                "11" => "novembra",
                "12" => "decembra",
            ],
            "ru" => [
                "01" => "января",
                "02" => "февраля",
                "03" => "марта",
                "04" => "апреля",
                "05" => "мая",
                "06" => "июня",
                "07" => "июля",
                "08" => "августа",
                "09" => "сентября",
                "10" => "октября",
                "11" => "ноября",
                "12" => "декабря",
            ]
        ];

        $lang_key = strtoupper($lang);

        if (empty($start_date) || empty($end_date)) {
            return "";
        }

        $start_parts = explode("/", $start_date);
        $end_parts = explode("/", $end_date);

        $start_day = intval($start_parts[2]);
        $start_month = $start_parts[1];
        $start_year = $start_parts[0];

        $end_day = intval($end_parts[2]);
        $end_month = $end_parts[1];
        $end_year = $end_parts[0];

        $year = $start_year;

        $start_month_name = $months[$lang][$start_month] ?? "";
        $end_month_name = $months[$lang][$end_month] ?? "";

        switch ($lang_key) {

            case "PL":
            case "UK":
            case "RU":
                if ($start_month === $end_month) {
                    return "$start_day - $end_day $start_month_name $year";
                }
                return "$start_day $start_month_name - $end_day $end_month_name $year";

            case "EN":
                if ($start_month === $end_month) {
                    return "$start_month_name $start_day-$end_day, $year";
                }
                return "$start_month_name $start_day - $end_month_name $end_day, $year";

            case "DE":
            case "CS":
                if ($start_month === $end_month) {
                    return "$start_day.-$end_day. $start_month_name $year";
                }
                return "$start_day. $start_month_name - $end_day. $end_month_name $year";

            case "SK":
                if ($start_month === $end_month) {
                    return "$start_day. - $end_day. $start_month_name $year";
                }
                return "$start_day. $start_month_name - $end_day. $end_month_name $year";

            case "LV":
                if ($start_month === $end_month) {
                    return "$start_day. - $end_day. $start_month_name $year";
                }
                return "$start_day. $start_month_name - $end_day. $end_month_name $year";

            case "LT":
                if ($start_month === $end_month) {
                    return "$year m. $start_month_name $start_day-$end_day d.";
                }
                return "$year m. $start_month_name $start_day d. - $end_month_name $end_day d.";

            default:
                // fallback EN
                if ($start_month === $end_month) {
                    return "$start_month_name $start_day-$end_day, $year";
                }
                return "$start_month_name $start_day - $end_month_name $end_day, $year";
        }
    }

    public function display_trade_fair_date_field($lang = "pl") {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $current_time = strtotime("now");
        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon";
        $formatted_date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? $new_date_comming_soon
            : $this->format_trade_fair_date($start_date, $end_date, $lang);

        $option_name = ($lang === "pl") ? "trade_fair_date" : "trade_fair_date_eng";
        $placeholder = ($lang === "pl") ? "np. 15-16 grudnia 2026" : "e.g. December 15-16, 2026";

        ?>
        <div class="form-field">
            <input
                <?php echo $pwe_shortcodes_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                type="text"
                name="<?php echo $option_name; ?>"
                id="<?php echo $option_name; ?>"
                placeholder="<?php echo $pwe_shortcodes_available ? $formatted_date : get_option($option_name) ?>"
                value="<?php echo !$pwe_shortcodes_available ? get_option($option_name) : "" ?>"
            />
            <p>
                <?php echo ($pwe_date_start_available && $pwe_date_end_available) ? "Dane pobrane z CAP DB" : $placeholder; ?>
            </p>
        </div>
        <?php
    }

    public function display_trade_fair_date() {
        $this->display_trade_fair_date_field("pl");
    }

    public function display_trade_fair_date_eng() {
        $this->display_trade_fair_date_field("en");
    }

    public function display_trade_fair_datetotimer() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $lang = strtolower(ICL_LANGUAGE_CODE);
        $current_time = strtotime("now");

        $date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? ""
            : $start_date;

        // Check if the result is in YYYY/MM/DD format (10 characters)
        if (is_string($date) && preg_match('/^\d{4}[\/-]\d{2}[\/-]\d{2}$/', $date)) {
            $date .= " 10:00"; // Add hour 10:00
        }

        $option_name = "trade_fair_datetotimer";

        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_shortcodes_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text"
                    name="<?php echo $option_name; ?>"
                    id="<?php echo $option_name; ?>"
                    placeholder="<?php echo $pwe_shortcodes_available ? $date : get_option($option_name) ?>"
                    value="<?php echo !$pwe_shortcodes_available ? get_option($option_name) : "" ?>"
                />
                <p><?php echo $pwe_date_start_available ? "Dane pobrane z CAP DB" : "2025/10/14 10:00 (Y:M:D H:M)"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_enddata() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $lang = strtolower(ICL_LANGUAGE_CODE);
        $current_time = strtotime("now");

        $date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? ""
            : $end_date;

        // Check if the result is in YYYY/MM/DD format (10 characters)
        if (is_string($date) && preg_match('/^\d{4}[\/-]\d{2}[\/-]\d{2}$/', $date)) {
            $date .= " 17:00"; // Add hour 17:00
        }

        $option_name = "trade_fair_enddata";

        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_shortcodes_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text"
                    name="<?php echo $option_name; ?>"
                    id="<?php echo $option_name; ?>"
                    placeholder="<?php echo $pwe_shortcodes_available ? $date : get_option($option_name) ?>"
                    value="<?php echo !$pwe_shortcodes_available ? get_option($option_name) : "" ?>"
                />
                <p><?php echo $pwe_date_end_available ? "Dane pobrane z CAP DB" : "2025/10/16 10:00 (Y:M:D H:M)"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_date_custom_format() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $lang = strtolower(ICL_LANGUAGE_CODE);
        $new_date_comming_soon = "Nowa data wkrótce / New date comming soon";

        $current_time = strtotime("now");

        $custom_date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? $new_date_comming_soon
            : PWE_Functions::transform_dates($start_date, $end_date, false);

        $option_name = "trade_fair_date_custom_format";
        $placeholder = "14-16|10|2025 (D-D|M|Y)";
        
        ?>
            <div class="form-field">
                <input
                    <?php echo $pwe_shortcodes_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text"
                    name="<?php echo $option_name; ?>"
                    id="<?php echo $option_name; ?>"
                    placeholder="<?php echo $pwe_shortcodes_available ? $custom_date : get_option($option_name) ?>"
                    value="<?php echo !$pwe_shortcodes_available ? get_option($option_name) : "" ?>"
                />
                <p><?php echo ($pwe_date_start_available && $pwe_date_end_available) ? "Dane pobrane z CAP DB" : $placeholder; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_catalog() {
        $pwe_catalog = shortcode_exists("pwe_catalog") ? do_shortcode('[pwe_catalog]') : "";
        $pwe_catalog_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_catalog) && $pwe_catalog !== "");
        ?>
            <div class="form-field">
                <input 	
                    <?php echo $pwe_catalog_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text" 
                    name="trade_fair_catalog" 
                    id="trade_fair_catalog" 
                    value="<?php echo $pwe_catalog_available ? $pwe_catalog : get_option('trade_fair_catalog'); ?>" 
                />
                <p><?php echo $pwe_catalog_available ? "Dane pobrane z CAP DB" : "np. 69"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_catalog_id() {
        $pwe_catalog_id = shortcode_exists("pwe_catalog_id") ? do_shortcode('[pwe_catalog_id]') : "";
        $pwe_catalog_id_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_catalog_id) && $pwe_catalog_id !== "");
        ?>
            <div class="form-field">
                <input 	
                    <?php echo $pwe_catalog_id_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text" 
                    name="trade_fair_catalog_id" 
                    id="trade_fair_catalog_id" 
                    value="<?php echo $pwe_catalog_id_available ? $pwe_catalog_id : get_option('trade_fair_catalog_id'); ?>" 
                />
                <p><?php echo $pwe_catalog_id_available ? "Dane pobrane z CAP DB" : "np. 69"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_catalog_year() {
        $pwe_date_start = shortcode_exists("pwe_date_start") ? do_shortcode('[pwe_date_start]') : "";
        $pwe_date_start_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_start));
        $result = $pwe_date_start_available ? date('Y', strtotime($pwe_date_start)) : get_option('trade_fair_catalog_year');
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_date_start_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text" 
                    name="trade_fair_catalog_year" 
                    id="trade_fair_catalog_year" 
                    value="<?php echo $result ?>" 
                />
                <p><?php echo $pwe_date_start_available ? "Dane pobrane z CAP DB" : "2026"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_conferance() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_conferance" id="trade_fair_conferance" value="<?php echo get_option('trade_fair_conferance'); ?>" />
                <p>"Przykład -> <?php echo get_option('trade_fair_name') ?> Innowations"</p>
            </div>
        <?php
    }

    public function display_trade_fair_conferance_eng() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_conferance_eng" id="trade_fair_conferance_eng" value="<?php echo get_option('trade_fair_conferance_eng'); ?>" />
                <p>"Przykład -> <?php echo get_option('trade_fair_name') ?> Innowations"</p>
            </div>
        <?php
    }

    public function display_trade_fair_1stbuildday() {
        $pwe_date_start = shortcode_exists("pwe_date_start") ? do_shortcode('[pwe_date_start]') : "";
        $pwe_date_start_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_start));
        $result = $pwe_date_start_available ? $pwe_date_start : get_option('trade_fair_datetotimer');
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_date_start_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_1stbuildday" 
                    id="trade_fair_1stbuildday" 
                    value="<?php echo $pwe_date_start_available ? (date('d.m.Y', strtotime($result . ' -2 day')) . ' 8:00-18:00') : get_option('trade_fair_1stbuildday') ?>" 
                />
                <p><?php echo $pwe_date_start_available ? "Dane pobrane z CAP DB" : 'wartość domyślna -> ' . date('d.m.Y', strtotime($result . ' -2 day')) . ' 8:00-18:00' ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_2ndbuildday() {
        $pwe_date_start = shortcode_exists("pwe_date_start") ? do_shortcode('[pwe_date_start]') : "";
        $pwe_date_start_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_start));
        $result = $pwe_date_start_available ? (date('d.m.Y', strtotime($pwe_date_start . ' -1 day')) . ' 8:00-20:00') : get_option('trade_fair_2ndbuildday');
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_date_start_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_2ndbuildday" 
                    id="trade_fair_2ndbuildday" 
                    value="<?php echo $result ?>" 
                    />
                <p><?php echo $pwe_date_start_available ? "Dane pobrane z CAP DB" : 'wartość domyślna -> ' . date('d.m.Y', strtotime($result . ' -1 day')) . ' 8:00-18:00' ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_1stdismantlday() {
        $pwe_date_end = shortcode_exists("pwe_date_end") ? do_shortcode('[pwe_date_end]') : "";
        $pwe_date_end_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_end));
        $result = $pwe_date_end_available ? $pwe_date_end : get_option('trade_fair_enddata');
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_date_end_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_1stdismantlday" 
                    id="trade_fair_1stdismantlday" 
                    value="<?php echo $pwe_date_end_available ? date('d.m.Y', strtotime($result)) . ' 17:00-24:00' : get_option('trade_fair_1nddismantlday'); ?>" 
                />
                <p><?php echo $pwe_date_end_available ? "Dane pobrane z CAP DB" : 'wartość domyślna -> ' . date('d.m.Y', strtotime($result)) . ' 17:00-24:00' ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_2nddismantlday() {
        $pwe_date_end = shortcode_exists("pwe_date_end") ? do_shortcode('[pwe_date_end]') : "";
        $pwe_date_end_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_end));
        $result = $pwe_date_end_available ? $pwe_date_end : get_option('trade_fair_enddata');
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_date_end_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_2nddismantlday" 
                    id="trade_fair_2nddismantlday" 
                    value="<?php echo $pwe_date_end_available ? date('d.m.Y', strtotime($result . ' +1 day')) . ' 8:00-12:00' : get_option('trade_fair_2nddismantlday'); ?>" 
                />
                <p><?php echo $pwe_date_end_available ? "Dane pobrane z CAP DB" : 'wartość domyślna -> ' . date('d.m.Y', strtotime($result . ' +1 day')) . ' 8:00-12:00' ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_actualyear() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_actualyear" id="trade_fair_actualyear" value="<?php echo date('Y') ?>" disabled/>
                <p>"Automatycznie pobierany aktulny rok"</p>
            </div>
        <?php
    }

    public function display_trade_fair_branzowy_field($lang = "pl") {
        $pwe_shortcodes_available = empty(get_option("pwe_general_options", [])["pwe_dp_shortcodes_unactive"]);
        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon";

        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available) = $this->get_trade_fair_dates();

        $current_time = strtotime("now");
        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon";

        $option_name = ($lang === "pl") ? "trade_fair_branzowy" : "trade_fair_branzowy_eng";
        $placeholder = ($lang === "pl") ? "np. 15 grudnia 2020" : "e.g. December 15, 2020";

        // no dates → immediate message
        if (empty($start_date)) {
            $industry_day = $new_date_comming_soon;
        } else if (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time) {
            $industry_day = $new_date_comming_soon;
        } else {
            if ($lang === "pl") {
                setlocale(LC_TIME, "pl_PL.UTF-8");
                $industry_day = strftime("%e %B %Y", strtotime($start_date));
            } else {
                $industry_day = date("F j, Y", strtotime($start_date)); // US format
            }
        }

        ?>
        <div class="form-field">
            <input
                <?php echo $pwe_shortcodes_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>
                type="text"
                name="<?php echo $option_name; ?>"
                id="<?php echo $option_name; ?>"
                placeholder="<?php echo $pwe_shortcodes_available ? $industry_day : "" ?>"
                value="<?php echo $pwe_shortcodes_available ? $industry_day : get_option($option_name) ?>"
            />
            <p>
                <?php
                echo $pwe_shortcodes_available ? "Dane pobrane z CAP DB" : $placeholder;
                ?>
            </p>
        </div>
        <?php
    }

    public function display_trade_fair_branzowy() {
        $this->display_trade_fair_branzowy_field("pl");
    }

    public function display_trade_fair_branzowy_eng() {
        $this->display_trade_fair_branzowy_field("en");
    }

    public function display_trade_fair_edition() {
        $pwe_edition = shortcode_exists("pwe_edition") ? do_shortcode('[pwe_edition]') : "";
        $pwe_edition_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_edition) && $pwe_edition !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_edition_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_edition" 
                    id="trade_fair_edition" 
                    value="<?php echo $pwe_edition_available ? $pwe_edition : get_option('trade_fair_edition'); ?>" 
                />
                <p><?php echo $pwe_edition_available ? "Dane pobrane z CAP DB" : "np -> 2"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_accent() {
        $pwe_color_accent = shortcode_exists("pwe_color_accent") ? do_shortcode('[pwe_color_accent]') : "";
        $pwe_color_accent_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_color_accent) && $pwe_color_accent !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_color_accent_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>   
                    type="text" 
                    name="trade_fair_accent" 
                    id="trade_fair_accent" 
                    value="<?php echo $pwe_color_accent_available ? $pwe_color_accent : get_option('trade_fair_accent'); ?>" 
                />
                <p><?php echo $pwe_color_accent_available ? "Dane pobrane z CAP DB" : "np -> #84gj64"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_main2() {
        $pwe_color_main2 = shortcode_exists("pwe_color_main2") ? do_shortcode('[pwe_color_main2]') : "";
        $pwe_color_main2_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_color_main2) && $pwe_color_main2 !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_color_main2_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text" 
                    name="trade_fair_main2" 
                    id="trade_fair_main2" 
                    value="<?php echo $pwe_color_main2_available ? $pwe_color_main2 : get_option('trade_fair_main2'); ?>" />
                <p><?php echo $pwe_color_main2_available ? "Dane pobrane z CAP DB" : "np -> #84gj64"; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_badge() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_badge" id="trade_fair_badge" value="<?php echo get_option('trade_fair_badge'); ?>" />
                <p>"Początek nazwy badge -> ..._gosc_a6 "</p>
            </div>
        <?php
    }

    public function display_trade_fair_opisbranzy() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_opisbranzy" id="trade_fair_opisbranzy" value="<?php echo get_option('trade_fair_opisbranzy'); ?>" />
                <p>"np. uprawa i przetwórstwo warzyw"</p>
            </div>
        <?php
    }

    public function display_trade_fair_opisbranzy_eng() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_opisbranzy_eng" id="trade_fair_opisbranzy_eng" value="<?php echo get_option('trade_fair_opisbranzy_eng'); ?>" />
                <p>"np. cultivation and processing of vegetables"</p>
            </div>
        <?php
    }

    public function display_trade_fair_domainadress() {
        ?>
            <div class="form-field">
                <input type="text" name="trade_fair_domainadress" id="trade_fair_domainadress" value="<?php echo str_replace('https://', '', home_url()); ?>" disabled/>
                <p>"Automatycznie pobierany adres strony"</p>
            </div>
        <?php
    }

    public function display_trade_fair_facebook() {
        $pwe_facebook = shortcode_exists("pwe_facebook") ? do_shortcode('[pwe_facebook]') : "";
        $pwe_facebook_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_facebook) && $pwe_facebook !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_facebook_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text" 
                    name="trade_fair_facebook" 
                    id="trade_fair_facebook" 
                    value="<?php echo $pwe_facebook_available ? $pwe_facebook : get_option('trade_fair_facebook'); ?>"
                />
                <p><?php echo $pwe_facebook_available ? "Dane pobrane z CAP DB" : "https://facebook/..."; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_instagram() {
        $pwe_instagram = shortcode_exists("pwe_instagram") ? do_shortcode('[pwe_instagram]') : "";
        $pwe_instagram_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_instagram) && $pwe_instagram !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_instagram_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_instagram" 
                    id="trade_fair_instagram" 
                    value="<?php echo $pwe_instagram_available ? $pwe_instagram : get_option('trade_fair_instagram'); ?>"
                />
                <p><?php echo $pwe_instagram_available ? "Dane pobrane z CAP DB" : "https://instagram/..."; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_linkedin() {
        $pwe_linkedin = shortcode_exists("pwe_linkedin") ? do_shortcode('[pwe_linkedin]') : "";
        $pwe_linkedin_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_linkedin) && $pwe_linkedin !== "");
        ?>
            <div class="form-field">
                <input
                    <?php echo $pwe_linkedin_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>  
                    type="text" 
                    name="trade_fair_linkedin" 
                    id="trade_fair_linkedin" 
                    value="<?php echo $pwe_linkedin_available ? $pwe_linkedin : get_option('trade_fair_linkedin'); ?>"
                />
                <p><?php echo $pwe_linkedin_available ? "Dane pobrane z CAP DB" : "https://linkedin/..."; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_youtube() {
        $pwe_youtube = shortcode_exists("pwe_youtube") ? do_shortcode('[pwe_youtube]') : "";
        $pwe_youtube_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_youtube) && $pwe_youtube !== "");
        ?>
            <div class="form-field">
                <input 
                    <?php echo $pwe_youtube_available ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?> 
                    type="text" 
                    name="trade_fair_youtube" 
                    id="trade_fair_youtube" 
                    value="<?php echo $pwe_youtube_available ? $pwe_youtube : get_option('trade_fair_youtube'); ?>"
                />
                <p><?php echo $pwe_youtube_available ? "Dane pobrane z CAP DB" : "https://youtube/..."; ?></p>
            </div>
        <?php
    }

    public function display_trade_fair_rejestracja() {
        ?>
            <div class="form-field full-tab-code-system">
                <input type="text" name="trade_fair_rejestracja" id="trade_fair_rejestracja" value="<?php echo get_option('trade_fair_rejestracja'); ?>"/>
                <p>"wartość domyślna -> rejestracja@<?php echo $_SERVER['HTTP_HOST']; ?>"</p>
            </div>
        <?php
    }

    public function display_trade_fair_contact() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 
        $pwe_groups_contacts_data = PWE_Functions::get_database_groups_contacts_data();  

        // Get domain address
        $current_domain = $_SERVER['HTTP_HOST'];

        if (!empty($pwe_groups_data) && !empty($pwe_groups_contacts_data)) {
            foreach ($pwe_groups_data as $group) {
                if ($current_domain == $group->fair_domain) {
                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        if ($group->fair_group == $group_contact->groups_name) {
                            if ($group_contact->groups_slug == "biuro-ob") {
                                $service_contact_data = json_decode($group_contact->groups_data);
                                $service_email = trim($service_contact_data->email);
                            }
                        } 
                    }
                }
            } 
        }

        ?>
            <div class="form-field full-tab-code-system">
                <input 
                    type="text" 
                    name="trade_fair_contact" 
                    id="trade_fair_contact" 
                    value="<?php echo get_option('trade_fair_contact'); ?>"
                />
                <p>"wartość domyślna -> <?php echo !empty($service_email) ? $service_email : ''; ?>"</p>
            </div>
        <?php
    }

    public function display_trade_fair_contact_tech() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 
        $pwe_groups_contacts_data = PWE_Functions::get_database_groups_contacts_data();  

        // Get domain address
        $current_domain = $_SERVER['HTTP_HOST'];

        if (!empty($pwe_groups_data) && !empty($pwe_groups_contacts_data)) {
            foreach ($pwe_groups_data as $group) {
                if ($current_domain == $group->fair_domain) {
                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        if ($group->fair_group == $group_contact->groups_name) {
                            if ($group_contact->groups_slug == "ob-tech-wyst") {
                                $tech_contact_data = json_decode($group_contact->groups_data);
                                $tech_email = trim($tech_contact_data->email);
                            }
                        } 
                    }
                }
            } 
        }

        ?>
            <div class="form-field full-tab-code-system">
                <input 
                    type="text" 
                    name="trade_fair_contact_tech" 
                    id="trade_fair_contact_tech" 
                    value="<?php echo get_option('trade_fair_contact_tech'); ?>"
                />
                <p>"wartość domyślna -> <?php echo !empty($tech_email) ? $tech_email : ''; ?>"</p>
            </div>
        <?php
    }

    public function display_trade_fair_contact_media() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 
        $pwe_groups_contacts_data = PWE_Functions::get_database_groups_contacts_data();  

        // Get domain address
        $current_domain = $_SERVER['HTTP_HOST'];

        if (!empty($pwe_groups_data) && !empty($pwe_groups_contacts_data)) {
            foreach ($pwe_groups_data as $group) {
                if ($current_domain == $group->fair_domain) {
                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        if ($group->fair_group == $group_contact->groups_name) {
                            if ($group_contact->groups_slug == "ob-marketing-media") {
                                $media_contact_data = json_decode($group_contact->groups_data);
                                $media_email = trim($media_contact_data->email);
                            }
                        } 
                    }
                }
            } 
        }

        ?>
            <div class="form-field full-tab-code-system">
                <input 
                    type="text" 
                    name="trade_fair_contact_media" 
                    id="trade_fair_contact_media" 
                    value="<?php echo get_option('trade_fair_contact_media'); ?>"
                />
                <p>"wartość domyślna -> <?php echo !empty($media_email) ? $media_email : ''; ?>"</p>
            </div>
        <?php
    }

    public function display_trade_fair_lidy() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 
        $pwe_groups_contacts_data = PWE_Functions::get_database_groups_contacts_data();  

        // Get domain address
        $current_domain = $_SERVER['HTTP_HOST'];

        if (!empty($pwe_groups_data) && !empty($pwe_groups_contacts_data)) {
            foreach ($pwe_groups_data as $group) {
                if ($current_domain == $group->fair_domain) {
                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        if ($group->fair_group == $group_contact->groups_name) {
                            if ($group_contact->groups_slug == "lidy") {
                                $lidy_contact_data = json_decode($group_contact->groups_data);
                                $lidy_email = trim($lidy_contact_data->email);
                            }
                        } 
                    }
                }
            }
        }

        ?>
            <div class="form-field full-tab-code-system">
                <input 
                    type="text" 
                    name="trade_fair_lidy" 
                    id="trade_fair_lidy" 
                    value="<?php echo get_option('trade_fair_lidy'); ?>"
                />
                <p>"wartość domyślna -> <?php echo !empty($lidy_email) ? $lidy_email : ''; ?>"</p>
            </div>
        <?php
    }

    public function display_first_day() {
        ?>
            <div class="form-field">
                <input type="text" name="first_day" id="first_day" value="<?php echo get_option('first_day'); ?>" />
                <p>"np. 1 marca (piątek) 15:00 - 16:00"</p>
            </div>
        <?php
    }

    public function display_first_day_eng() {
        ?>
            <div class="form-field">
                <input type="text" name="first_day_eng" id="first_day_eng" value="<?php echo get_option('first_day_eng'); ?>" />
                <p>"np. March 1 (friday) 15:00 - 16:00"</p>
            </div>
        <?php
    }

    public function display_second_day() {
        ?>
            <div class="form-field">
                <input type="text" name="second_day" id="second_day" value="<?php echo get_option('second_day'); ?>" />
                <p>"np. 2 marca (sobota) 15:00 - 16:00"</p>
            </div>
        <?php
    }

    public function display_second_day_eng() {
        ?>
            <div class="form-field">
                <input type="text" name="second_day_eng" id="second_day_eng" value="<?php echo get_option('second_day_eng'); ?>" />
                <p>"np. March 2 (friday) 15:00 - 16:00"</p>
            </div>
        <?php
    }

    public function display_third_day() {
        ?>
            <div class="form-field">
                <input type="text" name="third_day" id="third_day" value="<?php echo get_option('third_day'); ?>" />
                <p>"np. 3 marca (sobota) 15:00 - 15:30"</p>
            </div>
        <?php
    }

    public function display_third_day_eng() {
        ?>
            <div class="form-field">
                <input type="text" name="third_day_eng" id="third_day_eng" value="<?php echo get_option('third_day_eng'); ?>" />
                <p>"np. March 3 (friday) 15:00 - 15:30"</p>
            </div>
        <?php
    }

    public function display_super_shortcode_1() {
        ?>
            <div class="form-field">
                <input type="text" name="super_shortcode_1" id="super_shortcode_1" value="<?php echo get_option('super_shortcode_1'); ?>" />
                <p>"np. cokolwiek"</p>
            </div>
        <?php
    }

    public function display_super_shortcode_2() {
        ?>
            <div class="form-field">
                <input type="text" name="super_shortcode_2" id="super_shortcode_2" value="<?php echo get_option('super_shortcode_2'); ?>" />
                <p>"np. cokolwiek"</p>
            </div>
        <?php
    }

    public function days_difference() {
        $trade_fair_date = do_shortcode('[trade_fair_date_custom_format]');
        
        if (preg_match('/(\d{2})-(\d{2})\|(\d{2})\|(\d{4})/', $trade_fair_date, $matches)) {
            // $matches[1] = starting day
            // $matches[2] = end day
            // $matches[3] = month
            // $matches[4] = year
            $start_date = DateTime::createFromFormat('d-m-Y', $matches[1] . '-' . $matches[3] . '-' . $matches[4]);
            $end_date = DateTime::createFromFormat('d-m-Y', $matches[2] . '-' . $matches[3] . '-' . $matches[4]);
            
            // Calculate the difference in days
            $interval = $start_date->diff($end_date);
            $days_difference = $interval->days + 1;
        } else {
            $days_difference = 3;
        }

        return $days_difference;
    }

    public function display_trade_fair_registration_benefits_pl() {
        if (empty(get_option('trade_fair_registration_benefits_pl'))) {
            $html_code = '
            <ul>
                <li><strong>wejścia na targi po rejestracji przez '. $this->days_difference() .' dni</strong></li>
                <li><strong>możliwość udziału w konferencjach</strong> lub warsztatach na zasadzie “wolnego słuchacza”</li>
                <li>darmowy parking</li>
            </ul>';
        } else {
            $html_code = get_option('trade_fair_registration_benefits_pl');
        }
        ?>
            <div class="form-field">
                <textarea id="trade_fair_registration_benefits_pl" name="trade_fair_registration_benefits_pl" rows="5" cols="100"><?php echo $html_code; ?></textarea>
            </div>
        <?php
    }

    public function display_trade_fair_registration_benefits_en() {
        if (empty(get_option('trade_fair_registration_benefits_en'))) {
            $html_code = '
            <ul>
                <li><strong>access to the trade fair for all '. $this->days_difference() .' days upon registration</strong></li>
                <li><strong>the chance to join conferences</strong> or workshops as a listener</li>
                <li>free parking</li>
            </ul>';
        } else {
            $html_code = get_option('trade_fair_registration_benefits_en');
        }
        ?>
            <div class="form-field">
                <textarea id="trade_fair_registration_benefits_en" name="trade_fair_registration_benefits_en" rows="5" cols="100"><?php echo $html_code; ?></textarea>
            </div>
        <?php
    }

    public function display_trade_fair_ticket_benefits_pl() {
        if (empty(get_option('trade_fair_ticket_benefits_pl'))) {
            $html_code = '
            <ul>
                <li><strong>fast track</strong> - szybkie wejście na targi dedykowaną bramką przez '. $this->days_difference() .' dni</li>
                <li><strong>imienny pakiet</strong> - targowy przesyłany kurierem przed wydarzeniem</li>
                <li><strong>welcome pack</strong> - przygotowany specjalnie przez wystawców</li>
                <li>obsługa concierge</li>
                <li>możliwość udziału w konferencjach i&nbsp; warsztatach</li>
                <li>darmowy parking</li>
            </ul>';
        } else {
            $html_code = get_option('trade_fair_ticket_benefits_pl');
        }
        ?>
            <div class="form-field">
                <textarea id="trade_fair_ticket_benefits_pl" name="trade_fair_ticket_benefits_pl" rows="5" cols="100"><?php echo $html_code; ?></textarea>
            </div>
        <?php
    }

    public function display_trade_fair_ticket_benefits_en() {
        if (empty(get_option('trade_fair_ticket_benefits_en'))) {
            $html_code = '
            <ul>
                <li><strong>fast track access</strong> – skip the line and enter the trade fair through a dedicated priority gate for all '. $this->days_difference() .' days</li>
                <li><strong>Personalized trade fair package</strong> - delivered by courier to your address before the event</li>
                <li><strong>welcome pack</strong> - a special set of materials and gifts prepared by exhibitors</li>
                <li>Concierge service</li>
                <li>Access to conferences and workshops</li>
                <li>Free parking</li>
            </ul>';
        } else {
            $html_code = get_option('trade_fair_ticket_benefits_en');
        }
        ?>
            <div class="form-field">
                <textarea id="trade_fair_ticket_benefits_en" name="trade_fair_ticket_benefits_en" rows="5" cols="100"><?php echo $html_code; ?></textarea>
            </div>
        <?php
    }

    public function display_trade_fair_group() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 

        foreach ($pwe_groups_data as $group) {
            if ($_SERVER['HTTP_HOST'] == $group->fair_domain) {
                $current_group = $group->fair_group;
            }
        }  
        ?>
            <div class="form-field">
                <input 
                    <?php echo !empty($current_group) ? "style='pointer-events: none; opacity: 0.5;'" : ""; ?>   
                    type="text" 
                    name="trade_fair_accent" 
                    id="trade_fair_accent" 
                    value="<?php echo !empty($current_group) ? $current_group : get_option('trade_fair_group'); ?>" 
                />
                <p><?php echo !empty($current_group) ? "Dane pobrane z CAP DB" : "np -> gr2"; ?></p>
            </div>
        <?php
    }





    // DISPLAYING THE SHORTCODES <----------------------------------------------------------------------<

    public function show_trade_fair_name() {
        $pwe_name_pl = shortcode_exists("pwe_name_pl") ? do_shortcode('[pwe_name_pl]') : "";
        $pwe_name_pl_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_name_pl) && $pwe_name_pl !== "");
        $result = $pwe_name_pl_available ? $pwe_name_pl : get_option('trade_fair_name');

        $result = html_entity_decode($result, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return $result;
    }

    public function show_trade_fair_name_eng() {
        $pwe_name_pl = shortcode_exists("pwe_name_pl") ? do_shortcode('[pwe_name_pl]') : "";
        $pwe_name_en = shortcode_exists("pwe_name_en") ? do_shortcode('[pwe_name_en]') : "";
        $pwe_name_en = !empty($pwe_name_en) ? $pwe_name_en : $pwe_name_pl; 
        $pwe_name_en_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_name_en) && $pwe_name_en !== "");
        $result = $pwe_name_en_available ? $pwe_name_pl : get_option('trade_fair_name_eng');

        $result = html_entity_decode($result, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $result;
    }    

    public function show_trade_fair_desc() {
        $pwe_desc_pl = shortcode_exists("pwe_desc_pl") ? do_shortcode('[pwe_desc_pl]') : "";
        $pwe_desc_pl_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_desc_pl) && $pwe_desc_pl !== "");
        $result = $pwe_desc_pl_available ? $pwe_desc_pl : get_option('trade_fair_desc');
        return $result;
    }    

    public function show_trade_fair_desc_eng() {
        $pwe_desc_en = shortcode_exists("pwe_desc_en") ? do_shortcode('[pwe_desc_en]') : "";
        $pwe_desc_en_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_desc_en) && $pwe_desc_en !== "");
        $result = $pwe_desc_en_available ? $pwe_desc_en : get_option('trade_fair_desc_eng'); 
        return $result;
    }    

    public function show_trade_fair_desc_short() {
        $result = get_option('trade_fair_desc_short');
        if (empty($result)) {
            return  get_option('trade_fair_desc');
        }
        return $result;
    }    

    public function show_trade_fair_desc_short_eng() {
        $result = get_option('trade_fair_desc_short_eng');
        if (empty($result)) {
            return  get_option('trade_fair_desc_eng');
        }
        return $result;
    }    

    public function show_trade_fair_datetotimer() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $lang = strtolower(ICL_LANGUAGE_CODE);
        $current_time = strtotime("now");
        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon";

        $date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? ($pwe_shortcodes_available ? "" : get_option('trade_fair_datetotimer'))
            : $start_date;

        // Check if the result is in YYYY/MM/DD format (10 characters)
        if (is_string($date) && preg_match('/^\d{4}[\/-]\d{2}[\/-]\d{2}$/', $date)) {
            $date .= " 10:00"; // Add hour 10:00
        }

        return $date;
    }    

    public function show_trade_fair_enddata() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $lang = strtolower(ICL_LANGUAGE_CODE);
        $current_time = strtotime("now");
        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon";

        $date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? ($pwe_shortcodes_available ? "" : get_option('trade_fair_enddata'))
            : $end_date;

        // Check if the result is in YYYY/MM/DD format (10 characters)
        if (is_string($date) && preg_match('/^\d{4}[\/-]\d{2}[\/-]\d{2}$/', $date)) {
            $date .= " 17:00"; // Add hour 10:00
        }

        return $date;
    }    

    public function show_trade_fair_date_custom_format() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $lang = strtolower(ICL_LANGUAGE_CODE);
        $current_time = strtotime("now");
        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon";

        $date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? ($pwe_shortcodes_available ? $new_date_comming_soon : get_option('trade_fair_date_custom_format'))
            :  PWE_Functions::transform_dates($start_date, $end_date, false);

        return $date;
    }    

    public function show_trade_fair_catalog() {
        $pwe_catalog = shortcode_exists("pwe_catalog") ? do_shortcode('[pwe_catalog]') : "";
        $pwe_catalog_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_catalog) && $pwe_catalog !== "");
        $result = $pwe_catalog_available ? $pwe_catalog : get_option('trade_fair_catalog');
        return $result;
    }    

    public function show_trade_fair_catalog_id() {
        $pwe_catalog_id = shortcode_exists("pwe_catalog_id") ? do_shortcode('[pwe_catalog_id]') : "";
        $pwe_catalog_id_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_catalog_id) && $pwe_catalog_id !== "");
        $result = $pwe_catalog_id_available ? $pwe_catalog_id : get_option('trade_fair_catalog_id');
        return $result;
    }    

    public function show_trade_fair_catalog_year() {
        $pwe_date_start = shortcode_exists("pwe_date_start") ? do_shortcode('[pwe_date_start]') : "";
        $pwe_date_start_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_start));
        $result = $pwe_date_start_available ? date('Y', strtotime($pwe_date_start)) : get_option('trade_fair_catalog_year');
        return $result;
    }
    
    public function show_trade_fair_conferance() {
        $result = get_option('trade_fair_conferance');
        if (empty($result)) {
            return  get_option('trade_fair_desc');
        }
        return $result;
    }
    
    public function show_trade_fair_conferance_eng() {
        $result = get_option('trade_fair_conferance_eng');
        if (empty($result)) {
            return  get_option('trade_fair_desc_eng');
        }
        return $result;
    }
    
    public function show_trade_fair_1stbuildday() {
        $pwe_date_start = shortcode_exists("pwe_date_start") ? do_shortcode('[pwe_date_start]') : "";
        $pwe_date_start_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_start));
        $result = $pwe_date_start_available ? (date('d.m.Y', strtotime($pwe_date_start . ' -2 day')) . ' 8:00-18:00') : get_option('trade_fair_1stbuildday');	
        
        return $result;
    }	
    
    public function show_trade_fair_2ndbuildday() {
        $pwe_date_start = shortcode_exists("pwe_date_start") ? do_shortcode('[pwe_date_start]') : "";
        $pwe_date_start_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_start));
        $result = $pwe_date_start_available ? (date('d.m.Y', strtotime($pwe_date_start . ' -1 day')) . ' 8:00-20:00') : get_option('trade_fair_2ndbuildday');	
        
        return $result;
    }

    public function show_trade_fair_1stdismantlday() {
        $pwe_date_end = shortcode_exists("pwe_date_end") ? do_shortcode('[pwe_date_end]') : "";
        $pwe_date_end_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_end));
        // if (!empty(get_option('trade_fair_1nddismantlday'))) {
        // 	$result = get_option('trade_fair_1nddismantlday');
        // } else {
            $result = date('d.m.Y', strtotime($pwe_date_end_available ? $pwe_date_end : get_option('trade_fair_enddata'))) . ' 17:00-24:00';
        // }
        return $result;
    }

    public function show_trade_fair_2nddismantlday() {
        $pwe_date_end = shortcode_exists("pwe_date_end") ? do_shortcode('[pwe_date_end]') : "";
        $pwe_date_end_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_date_end));
        // if (!empty(get_option('trade_fair_2nddismantlday'))) {
        // 	$result = get_option('trade_fair_2nddismantlday');
        // } else {
            $result = date('d.m.Y', strtotime(($pwe_date_end_available ? $pwe_date_end : get_option('trade_fair_enddata')) . ' +1 day')) . ' 8:00-12:00';
        // }
        return $result;
    }
    
    public function show_trade_fair_date() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $lang = strtolower(ICL_LANGUAGE_CODE);
        $current_time = strtotime("now");
        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon";

        $date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? ($pwe_shortcodes_available ? $new_date_comming_soon : get_option('trade_fair_date'))
            :  $this->format_trade_fair_date($start_date, $end_date, "pl");

        return $date;
    }
    
    public function show_trade_fair_date_eng() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $lang = strtolower(ICL_LANGUAGE_CODE);
        $current_time = strtotime("now");
        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon";

        $date = (empty($start_date) || (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time))
            ? ($pwe_shortcodes_available ? $new_date_comming_soon : get_option('trade_fair_date_eng'))
            :  $this->format_trade_fair_date($start_date, $end_date, "en");

        return $date;
    }
    
    public function show_trade_fair_date_multilang() {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        // WPML language e.g. pl, en etc.
        $lang = strtolower(ICL_LANGUAGE_CODE);

        $current_time = strtotime("now");

        // translations "nowa data wkrótce"
        switch ($lang) {
            case "pl": $new_date_coming_soon = "Nowa data wkrótce"; break;
            case "en": $new_date_coming_soon = "New date coming soon"; break;
            case "de": $new_date_coming_soon = "Neuer Termin folgt in Kürze"; break;
            case "lt": $new_date_coming_soon = "Nauja data netrukus"; break;
            case "lv": $new_date_coming_soon = "Jauns datums drīzumā"; break;
            case "uk": $new_date_coming_soon = "Нова дата незабаром"; break;
            case "cs": $new_date_coming_soon = "Nový termín již brzy"; break;
            case "sk": $new_date_coming_soon = "Nový termín už čoskoro"; break;
            case "ru": $new_date_coming_soon = "Новая дата скоро"; break;
            default: $new_date_coming_soon = "New date coming soon"; break;
        }

        $date =
            (empty($start_date) || (!empty($end_date) && strtotime($end_date . " +20 hours") < $current_time))
                ? ($pwe_shortcodes_available ? $new_date_coming_soon : get_option('trade_fair_date_'.$lang))
                : $this->format_trade_fair_date($start_date, $end_date, $lang);

        return $date;
    }

    public function show_trade_fair_edition($entry = null, $fields = null) {
        $result = '';

        $pwe_edition = shortcode_exists("pwe_edition") ? do_shortcode('[pwe_edition]') : "";
        $pwe_edition_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_edition) && $pwe_edition !== "");
        
        $trade_fair_edition = $pwe_edition_available ? $pwe_edition : get_option('trade_fair_edition');

        // Sprawdzenie wartości i ustawienie wyniku
        if ($trade_fair_edition === '1') {
            $result = get_locale() === "pl_PL" ? 'Premierowa' : 'Premier';
        } else {
            $result = $trade_fair_edition . '.';
        }

        return $result;
    }	

    public function show_trade_fair_accent() {
        $pwe_color_accent = shortcode_exists("pwe_color_accent") ? do_shortcode('[pwe_color_accent]') : "";
        $pwe_color_accent_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_color_accent) && $pwe_color_accent !== "");
        $result = $pwe_color_accent_available ? $pwe_color_accent : get_option('trade_fair_accent');
        return $result;
    }
    
    public function show_trade_fair_main2() {
        $pwe_color_main2 = shortcode_exists("pwe_color_main2") ? do_shortcode('[pwe_color_main2]') : "";
        $pwe_color_main2_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_color_main2) && $pwe_color_main2 !== "");
        $result = $pwe_color_main2_available ? $pwe_color_main2 : get_option('trade_fair_main2');
        return $result;
    }
    
    public function trade_fair_branzowy_result($lang = "pl") {
        list($start_date, $end_date, $pwe_date_start_available, $pwe_date_end_available, $pwe_shortcodes_available) = $this->get_trade_fair_dates();

        $new_date_comming_soon = ($lang === "pl") ? "Nowa data wkrótce" : "New date comming soon"; 
        $current_time = strtotime("now");

        // no dates → immediate message
        if (empty($start_date)) {
            return $new_date_comming_soon;
        }

        // end date expired → message
        if (!empty($end_date) && (strtotime($end_date . " +20 hours")) < $current_time) {
            return $new_date_comming_soon;
        }

        // correct date → we format only the first day
        if ($lang === "pl") {
            setlocale(LC_TIME, "pl_PL.UTF-8");
            $industry_day = strftime("%e %B %Y", strtotime($start_date));
        } else {
            $industry_day = date("F j, Y", strtotime($start_date)); // US format
        }

        return $pwe_shortcodes_available ? $industry_day : (($lang === "pl") ? get_option('trade_fair_branzowy') : get_option('trade_fair_branzowy_eng'));
    }

    public function show_trade_fair_branzowy() {
        $result = $this->trade_fair_branzowy_result("pl");

        if (empty($result)) {
            return get_option('trade_fair_date');
        }
        return $result;
    }
    

    public function show_trade_fair_branzowy_eng() {
        $result = $this->trade_fair_branzowy_result("en");

        if (empty($result)) {
            return get_option('trade_fair_date_eng');
        }
        return $result;
    }
    
    public function show_trade_fair_badge() {
        $result = get_option('trade_fair_badge');
        return $result;
    }
    
    public function show_trade_fair_opisbranzy() {
        $result = get_option('trade_fair_opisbranzy');
        return $result;
    }
    
    public function show_trade_fair_opisbranzy_eng() {
        $result = get_option('trade_fair_opisbranzy_eng');
        return $result;
    }
    
    public function show_trade_fair_facebook() {
        $pwe_facebook = shortcode_exists("pwe_facebook") ? do_shortcode('[pwe_facebook]') : "";
        $pwe_facebook_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_facebook) && $pwe_facebook !== "");
        $result = $pwe_facebook_available ? $pwe_facebook : get_option('trade_fair_facebook');
        if (empty($result)) {
            return "https://warsawexpo.eu";
        }
        return $result;
    }
    
    public function show_trade_fair_instagram() {
        $pwe_instagram = shortcode_exists("pwe_instagram") ? do_shortcode('[pwe_instagram]') : "";
        $pwe_instagram_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_instagram) && $pwe_instagram !== "");
        $result = $pwe_instagram_available ? $pwe_instagram : get_option('trade_fair_instagram');
        if (empty($result)) {
            return "https://warsawexpo.eu";
        }
        return $result;
    }
    
    public function show_trade_fair_linkedin() {
        $pwe_linkedin = shortcode_exists("pwe_linkedin") ? do_shortcode('[pwe_linkedin]') : "";
        $pwe_linkedin_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_linkedin) && $pwe_linkedin !== "");
        $result = $pwe_linkedin_available ? $pwe_linkedin : get_option('trade_fair_linkedin');
        if (empty($result)) {
            return "https://warsawexpo.eu";
        }
        return $result;
    }
    
    public function show_trade_fair_youtube() {
        $pwe_youtube = shortcode_exists("pwe_youtube") ? do_shortcode('[pwe_youtube]') : "";
        $pwe_youtube_available = (empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']) && !empty($pwe_youtube) && $pwe_youtube !== "");
        $result = $pwe_youtube_available ? $pwe_youtube : get_option('trade_fair_youtube');
        if (empty($result)) {
            return "https://warsawexpo.eu";
        }
        return $result;
    }
    
    public function show_first_day() {
        $result = get_option('first_day');
        return $result;
    }
    
    public function show_first_day_eng() {
        $result = get_option('first_day_eng');
        return $result;
    }
    
    public function show_second_day() {
        $result = get_option('second_day');
        return $result;
    }
    
    public function show_second_day_eng() {
        $result = get_option('second_day_eng');
        return $result;
    }
   
    public function show_third_day() {
        $result = get_option('third_day');
        return $result;
    }
    
    public function show_third_day_eng() {
        $result = get_option('third_day_eng');
        return $result;
    }
    
    public function show_super_shortcode_1() {
        $result = get_option('super_shortcode_1');
        return $result;
    }
    
    public function show_super_shortcode_2() {
        $result = get_option('super_shortcode_2');
        return $result;
    }
    
    public function show_trade_fair_domainadress() {
        $result = $_SERVER['HTTP_HOST'];
        if(empty($result)){
            return str_replace('https://', '', home_url());
        }
        return $result;
    }
    
    public function show_trade_fair_actualyear() {
        $result = date('Y');
        return $result;
    }
    
    public function show_trade_fair_rejestracja() {
        if (empty($result)) {
            return 'rejestracja@' . $_SERVER['HTTP_HOST'];
        }
        return $result;
    }
    
    public function show_trade_fair_contact() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 
        $pwe_groups_contacts_data = PWE_Functions::get_database_groups_contacts_data();  

        // Get domain address
        $current_domain = $_SERVER['HTTP_HOST'];
        $result = '';

        if (!empty($pwe_groups_data) && !empty($pwe_groups_contacts_data)) {
            foreach ($pwe_groups_data as $group) {
                if ($current_domain == $group->fair_domain) {
                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        if ($group->fair_group == $group_contact->groups_name) {
                            if ($group_contact->groups_slug == "biuro-ob") {
                                $service_contact_data = json_decode($group_contact->groups_data);
                                $service_email = trim($service_contact_data->email);
                            }
                        } 
                    }
                }
            }
        }

        $result = !empty($service_email) ? $service_email : get_option('trade_fair_contact');

        return $result;
    }
    
    public function show_trade_fair_contact_tech() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 
        $pwe_groups_contacts_data = PWE_Functions::get_database_groups_contacts_data();  

        // Get domain address
        $current_domain = $_SERVER['HTTP_HOST'];
        $result = '';

        if (!empty($pwe_groups_data) && !empty($pwe_groups_contacts_data)) {
            foreach ($pwe_groups_data as $group) {
                if ($current_domain == $group->fair_domain) {
                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        if ($group->fair_group == $group_contact->groups_name) {
                            if ($group_contact->groups_slug == "ob-tech-wyst") {
                                $tech_contact_data = json_decode($group_contact->groups_data);
                                $tech_email = trim($tech_contact_data->email);
                            }
                        } 
                    }
                }
            }
        }

        $result = !empty($tech_email) ? $tech_email : get_option('trade_fair_contact_tech');

        return $result;
    }
    
    public function show_trade_fair_contact_media() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 
        $pwe_groups_contacts_data = PWE_Functions::get_database_groups_contacts_data();  

        // Get domain address
        $current_domain = $_SERVER['HTTP_HOST'];
        $result = '';

        if (!empty($pwe_groups_data) && !empty($pwe_groups_contacts_data)) {
            foreach ($pwe_groups_data as $group) {
                if ($current_domain == $group->fair_domain) {
                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        if ($group->fair_group == $group_contact->groups_name) {
                            if ($group_contact->groups_slug == "ob-marketing-media") {
                                $media_contact_data = json_decode($group_contact->groups_data);
                                $media_email = trim($media_contact_data->email);
                            }
                        } 
                    }
                }
            }
        }

        $result = !empty($media_email) ? $media_email : get_option('trade_fair_contact_media');

        return $result;
    }    

    public function show_trade_fair_lidy() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 
        $pwe_groups_contacts_data = PWE_Functions::get_database_groups_contacts_data();  

        // Get domain address
        $current_domain = $_SERVER['HTTP_HOST'];
        $result = '';

        if (!empty($pwe_groups_data) && !empty($pwe_groups_contacts_data)) {
            foreach ($pwe_groups_data as $group) {
                if ($current_domain == $group->fair_domain) {
                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        if ($group->fair_group == $group_contact->groups_name) {
                            if ($group_contact->groups_slug == "lidy") {
                                $lidy_contact_data = json_decode($group_contact->groups_data);
                                $lidy_email = trim($lidy_contact_data->email);
                            }
                        } 
                    }
                }
            }
        }

        $result = !empty($lidy_email) ? $lidy_email : get_option('trade_fair_lidy');

        return $result;
    }    

    public function show_trade_fair_group() {
        $pwe_groups_data = PWE_Functions::get_database_groups_data(); 

        foreach ($pwe_groups_data as $group) {
            if ($_SERVER['HTTP_HOST'] == $group->fair_domain) {
                $current_group = $group->fair_group;
            }
        }  

        return $current_group;
    }
    
    public function show_trade_fair_registration_benefits_pl() {
        if (empty(get_option('trade_fair_registration_benefits_pl'))) {
            $result = '
            <ul>
                <li><strong>wejścia na targi po rejestracji przez '. $this->days_difference() .' dni</strong></li>
                <li><strong>możliwość udziału w konferencjach</strong> lub warsztatach na zasadzie “wolnego słuchacza”</li>
                <li>darmowy parking</li>
            </ul>';
        } else {
            $result = get_option('trade_fair_registration_benefits_pl');
        }
        return $result;
    }
    
    public function show_trade_fair_registration_benefits_en() {
        if (empty(get_option('trade_fair_registration_benefits_en'))) {
            $result = '
            <ul>
                <li><strong>access to the trade fair for all '. $this->days_difference() .' days upon registration</strong></li>
                <li><strong>the chance to join conferences</strong> or workshops as a listener</li>
                <li>free parking</li>
            </ul>';
        } else {
            $result = get_option('trade_fair_registration_benefits_en');
        }
        return $result;
    }
    
    public function show_trade_fair_ticket_benefits_pl() {
        if (empty(get_option('trade_fair_ticket_benefits_pl'))) {
            $result = '
            <ul>
                <li><strong>fast track</strong> - szybkie wejście na targi dedykowaną bramką przez '. $this->days_difference() .' dni</li>
                <li><strong>imienny pakiet</strong> - targowy przesyłany kurierem przed wydarzeniem</li>
                <li><strong>welcome pack</strong> - przygotowany specjalnie przez wystawców</li>
                <li>obsługa concierge</li>
                <li>możliwość udziału w konferencjach i&nbsp; warsztatach</li>
                <li>darmowy parking</li>
            </ul>';
        } else {
            $result = get_option('trade_fair_ticket_benefits_pl');
        }
        return $result;
    }
    
    public function show_trade_fair_ticket_benefits_en() {
        if (empty(get_option('trade_fair_ticket_benefits_en'))) {
            $result = '
            <ul>
                <li><strong>fast track access</strong> – skip the line and enter the trade fair through a dedicated priority gate for all '. $this->days_difference() .' days</li>
                <li><strong>Personalized trade fair package</strong> - delivered by courier to your address before the event</li>
                <li><strong>welcome pack</strong> - a special set of materials and gifts prepared by exhibitors</li>
                <li>Concierge service</li>
                <li>Access to conferences and workshops</li>
                <li>Free parking</li>
            </ul>';
        } else {
            $result = get_option('trade_fair_ticket_benefits_en');
        }
        return $result;
    }
    

    // FOR YOAST SEO START <----------------------------------------------------------------------<

    public function sc_pwe_trade_fair_full_desc() {
        $domain = $_SERVER['HTTP_HOST'];
        $shortcodes_active = empty(get_option('pwe_general_options', [])['pwe_dp_shortcodes_unactive']);
        $lang = strtolower(ICL_LANGUAGE_CODE);

        if (!function_exists('get_translated_field')) {
            function get_translated_field($fair, $field_base_name) {
                // Get the language in the format e.g. "de", "pl"
                $lang = strtolower(ICL_LANGUAGE_CODE); // "de"

                // Check if a specific translation exists (e.g. fair_name_{lang})
                $field_with_lang = "{$field_base_name}_{$lang}";

                if (!empty($fair[$field_with_lang])) {
                    return $fair[$field_with_lang];
                }

                // Fallback to English
                $fallback = "{$field_base_name}_en";
                return $fair[$fallback] ?? '';
            }
        }

        if (!function_exists('get_pwe_shortcode')) {
            function get_pwe_shortcode($shortcode, $domain) {
                return shortcode_exists($shortcode) ? do_shortcode('[' . $shortcode . ' domain="' . $domain . '"]') : "";
            }
        }

        if (!function_exists('check_available_pwe_shortcode')) {
            function check_available_pwe_shortcode($shortcodes_active, $shortcode) {
                return $shortcodes_active && !empty($shortcode);
            }
        }

        $translates = PWE_Functions::get_database_translations_data($domain);

        $shortcode_full_desc = get_pwe_shortcode("pwe_full_desc_$lang", $domain);
        $shortcode_full_desc_available = check_available_pwe_shortcode($shortcodes_active, $shortcode_full_desc);
        $fair_full_desc = $shortcode_full_desc_available ? get_translated_field($translates[0], 'fair_full_desc') : '';

        $description = '';
        if (!empty($fair_full_desc)) {
            $description = strstr($fair_full_desc, '<br>', true);
            if ($description === false) {
                $description = $fair_full_desc;
            }
        }
        
        return $description;
    }

    public function sc_pwe_text_news() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Bądź na bieżąco z wydarzeniami i nowościami związanymi z '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        } else {
            return 'Stay up to date with events and news related to '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        }
    }
    
    public function sc_pwe_text_for_visitors() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Sprawdź, dlaczego warto odwiedzić '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .' – znajdziesz tu najnowsze trendy, innowacje i inspirujące rozwiązania.';
        } else {
            return 'Check out why you should visit '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .' – discover the latest trends, innovations, and inspiring solutions.';
        }
    }
    
    public function sc_pwe_text_for_exhibitors() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Zdobądź nowych klientów i pokaż swoją markę na '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        } else {
            return 'Gain new customers and showcase your brand at '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        }
    }
    
    public function sc_pwe_text_add_calendar() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Nie przegap '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]').'! Dodaj wydarzenie do swojego kalendarza.';
        } else {
            return 'Don\'t miss '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]').'! Add the event to your calendar.';
        }
    }
    
    public function sc_pwe_text_gallery() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Zobacz galerię '. do_shortcode('[trade_fair_name]') .' – sprawdź jak wyglądają targi z perspektywy obiektywu.';
        } else {
            return 'See the gallery of '. do_shortcode('[trade_fair_name_eng]') .' – check out the fair through the lens of the camera.';
        }
    }
    
    public function sc_pwe_text_org_info() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Wszystkie niezbędne informacje organizacyjne dla wystawców '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        } else {
            return 'All necessary organizational information for exhibitors at '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        }
    }
    
    public function sc_pwe_text_exh_catalog() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Poznaj firmy i marki obecne na '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        } else {
            return 'Get to know the companies and brands present at '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        }
    }
    
    public function sc_pwe_text_events() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Sprawdź wydarzenia towarzyszące '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .' – konferencje, prelekcje, spotkania.';
        } else {
            return 'Check out the events accompanying '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .' – conferences, lectures, meetings.';
        }
    }
    
    public function sc_pwe_text_contact() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Skontaktuj się z organizatorami '. do_shortcode('[trade_fair_name]') .' i uzyskaj potrzebne informacje o wydarzeniu.';
        } else {
            return 'Contact the organizers of '. do_shortcode('[trade_fair_name_eng]') .' to get the information you need about the event.';
        }
    }
    
    public function sc_pwe_text_fair_plan() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Zobacz plan stoisk i atrakcji '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        } else {
            return 'See the booth and attraction plan for '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        }
    }
    
    public function sc_pwe_text_registration() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Zarejestruj się na '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .' i odbierz swój bilet na targi.';
        } else {
            return 'Register for '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .' and get your ticket to the fair.';
        }
    }
    
    public function sc_pwe_text_promote_yourself() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Zwiększ rozpoznawalność swojej marki – wypromuj się na '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        } else {
            return 'Increase your brand visibility – promote yourself at '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .'.';
        }
    }
    
    public function sc_pwe_text_become_an_exhibitor() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Dołącz do grona wystawców '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .' i zaprezentuj swoją ofertę.';
        } else {
            return 'Join the exhibitors at '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .' and present your offer.';
        }
    }
    
    public function sc_pwe_text_store() {
        if (ICL_LANGUAGE_CODE == "pl") {
            return 'Zamów bilety lub pakiety promocyjne związane z '. do_shortcode('[trade_fair_name]') .' '. do_shortcode('[trade_fair_catalog_year]') .' w naszym sklepie online.';
        } else {
            return 'Order tickets or promotional packages related to '. do_shortcode('[trade_fair_name_eng]') .' '. do_shortcode('[trade_fair_catalog_year]') .' in our online store.';
        }
    }
    
    public function wpseo_register_extra_replacements() {
        $shortcode_map = $this->get_yoast_shortcodes_map();
        $keys = array_map(function($key) {
            return '%%' . $key . '%%';
        }, array_keys($shortcode_map));
        return $keys;
    }

    public function wpseo_replacements($replacements) {
        $shortcode_map = $this->get_yoast_shortcodes_map();

        foreach ($shortcode_map as $yoast_key => $callback) {
            $value = '';

            // Wywołaj metodę klasy
            if (is_callable([$this, $callback])) {
                $value = call_user_func([$this, $callback]);
            }

            $replacements['%%' . $yoast_key . '%%'] = $value;
        }

        return $replacements;
    }

    // FOR YOAST SEO END <----------------------------------------------------------------------<

    public function replace_gf_merge_tags($text, $form, $entry, $url_encode, $esc_html, $nl2br, $format) {
        $map = $this->get_gf_shortcodes_map();

        foreach ($map as $tag => $function_name) {

            if (!function_exists($function_name)) {
                continue;
            }

            $value = call_user_func($function_name);

            $text = str_replace(
                ['{' . $tag . '}', '[' . $tag . ']'],
                $value,
                $text
            );
        }

        return $text;
    }
}

PWE_Shortcodes::init();