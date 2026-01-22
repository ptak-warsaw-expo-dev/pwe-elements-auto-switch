<?php
if (!defined('ABSPATH')) exit;

class Statistics {

    public static function get_data() {
        return [
            'types' => ['main'],
            'order' => [
                'gr1' => 999,
                'gr2' => 999,
            ],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/preset-gr1/preset-gr1.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/preset-gr2/preset-gr2.php',
            ],
        ];
    }

    public static function render($group) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(__CLASS__);

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
            /* <-------------> General code start <-------------> */
           
                // 1 GR
                if ($group === 'gr1') {
                    $icon_color = '#FFFFFF';
                } else {
                    $icon_color = 'var(--accent-color)';
                }

                $svg_icon_visitors = '<svg class="pwe-statistics__icon pwe-statistics__icon-exhibitor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="' . $icon_color . '"><path d="M40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm720 0v-120q0-44-24.5-84.5T666-434q51 6 96 20.5t84 35.5q36 20 55 44.5t19 53.5v120H760ZM360-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm400-160q0 66-47 113t-113 47q-11 0-28-2.5t-28-5.5q27-32 41.5-71t14.5-81q0-42-14.5-81T544-792q14-5 28-6.5t28-1.5q66 0 113 47t47 113ZM120-240h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T440-640q0-33-23.5-56.5T360-720q-33 0-56.5 23.5T280-640q0 33 23.5 56.5T360-560Zm0 320Zm0-400Z"></path></svg>';

                $svg_icon_exhibitors = '<svg class="pwe-statistics__icon pwe-statistics__icon-visitor" xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" fill="' . $icon_color . '"><path d="M360-80v-529q-91-24-145.5-100.5T160-880h80q0 83 53.5 141.5T430-680h100q30 0 56 11t47 32l181 181-56 56-158-158v478h-80v-240h-80v240h-80Zm120-640q-33 0-56.5-23.5T400-800q0-33 23.5-56.5T480-880q33 0 56.5 23.5T560-800q0 33-23.5 56.5T480-720Z"></path></svg>';

                $svg_icon_area = '<svg class="pwe-statistics__icon pwe-statistics__icon-area" viewBox="4 4 16 16" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 19.475L6 16.6C5.68333 16.4167 5.4375 16.175 5.2625 15.875C5.0875 15.575 5 15.2417 5 14.875V9.125C5 8.75833 5.0875 8.425 5.2625 8.125C5.4375 7.825 5.68333 7.58333 6 7.4L11 4.525C11.3167 4.34167 11.65 4.25 12 4.25C12.35 4.25 12.6833 4.34167 13 4.525L18 7.4C18.3167 7.58333 18.5625 7.825 18.7375 8.125C18.9125 8.425 19 8.75833 19 9.125V14.875C19 15.2417 18.9125 15.575 18.7375 15.875C18.5625 16.175 18.3167 16.4167 18 16.6L13 19.475C12.6833 19.6583 12.35 19.75 12 19.75C11.65 19.75 11.3167 19.6583 11 19.475ZM11 17.175V12.575L7 10.25V14.875L11 17.175ZM13 17.175L17 14.875V10.25L13 12.575V17.175ZM12 10.85L15.95 8.525L12 6.25L8.05 8.525L12 10.85Z" fill="' . $icon_color . '"></path>
                </svg>';

                $visitors_total = (int) do_shortcode('[pwe_visitors]');
                $visitors_abroad = (int) do_shortcode('[pwe_visitors_foreign]');

                $visitors_percent = 0;
                if ($visitors_total > 0) {
                    $visitors_percent = round(($visitors_abroad / $visitors_total) * 100);
                }



                // 2 GR
                function ordinal_suffix($n) {
                    if (!in_array(($n % 100), [11, 12, 13])) {
                        switch ($n % 10) {
                            case 1:  return $n . 'st';
                            case 2:  return $n . 'nd';
                            case 3:  return $n . 'rd';
                        }
                    }
                    return $n . 'th';
                }

                function adapting_word($edition) {

                    // 1st edition (special case)
                    if ($edition == 1) {
                        return PWE_Functions::multi_translation("estimates_1st");
                    }

                    // Other editions (2, 3, 4…)
                    $locale = get_locale();

                    if ($locale === "en_US") {
                        // English needs correct ordinal form
                        if ($edition == 2) return PWE_Functions::multi_translation("industry_visitors_1st");
                        if ($edition == 3) return PWE_Functions::multi_translation("industry_visitors_2nd");
                        if ($edition == 4) return PWE_Functions::multi_translation("industry_visitors_3rd");

                        // For 4+ use generic + ordinal_suffix
                        $number = $edition - 1;
                        $translation = PWE_Functions::multi_translation("industry_visitors_nth");

                        // If translation already have correct ordinal placeholder, just replace
                        if (   strpos($translation, '{number}th') !== false
                            || strpos($translation, '{number}st') !== false
                            || strpos($translation, '{number}nd') !== false
                            || strpos($translation, '{number}rd') !== false) {

                            return str_replace('{number}', $number, $translation);
                        }

                        // in other cases, generate ordinal suffix
                        $ordinal = ordinal_suffix($number);
                        return str_replace('{number}', $ordinal, $translation);

                    }

                    // PL, DE — normal placeholder
                    return str_replace(
                        "{number}",
                        $edition - 1,
                        PWE_Functions::multi_translation("industry_visitors_nth")
                    );
                }

                // Taking a shortcode and returning a positive integer or default.
                function sc_int(string $shortcode, int $default = 0): int {
                    $value = (int) do_shortcode("[$shortcode]");
                    return $value > 0 ? $value : $default;
                }

                // Comparing current values ​​with previous ones and calculating percentages.
                function compare_values(int $current, int $previous): array {
                    if ($previous <= 0 && $current <= 0) {
                        return [
                            'current'  => 0,
                            'previous' => 0,
                            'increase' => 0,
                        ];
                    }

                    $max = max($current, $previous, 1);

                    return [
                        'current'  => ($current / $max) * 100,
                        'previous' => ($previous / $max) * 100,
                        'increase' => round(100 - (($previous / $max) * 100)),
                    ];
                }

                // Shortcodes
                $pwe_visitors         = sc_int('pwe_visitors');
                $pwe_visitors_foreign = sc_int('pwe_visitors_foreign');
                $pwe_exhibitors       = sc_int('pwe_exhibitors');
                $pwe_countries        = sc_int('pwe_countries', 15);
                $pwe_area             = sc_int('pwe_area');
                $pwe_statistics_year_curr = sc_int('pwe_statistics_year_curr');

                $pwe_visitors_prev    = sc_int('pwe_visitors_prev');
                $pwe_visitors_foreign_prev = sc_int('pwe_visitors_foreign_prev');
                $pwe_exhibitors_prev  = sc_int('pwe_exhibitors_prev');
                $pwe_countries_prev        = sc_int('pwe_countries_prev');
                $pwe_area_prev        = sc_int('pwe_area_prev');
                $pwe_statistics_year_prev = sc_int('pwe_statistics_year_prev');

                // Visitor calculations
                $polish_visitors = max(0, $pwe_visitors - $pwe_visitors_foreign);

                $percent_polish = $pwe_visitors > 0
                    ? round(($polish_visitors / $pwe_visitors) * 100)
                    : 0;

                $percent_abroad = 100 - $percent_polish;

                // Comparisons
                $visitors_stats   = compare_values($pwe_visitors, $pwe_visitors_prev);
                $exhibitors_stats = compare_values($pwe_exhibitors, $pwe_exhibitors_prev);
                $area_stats       = compare_values($pwe_area, $pwe_area_prev);

                // Visitors
                $number_visitors_percentage           = $visitors_stats['current'];
                $number_visitors_previous_percentage  = $visitors_stats['previous'];
                $number_visitors_increase             = $visitors_stats['increase'];

                // Exhibitors
                $number_exhibitors_percentage          = $exhibitors_stats['current'];
                $number_exhibitors_previous_percentage = $exhibitors_stats['previous'];
                $number_exhibitors_increase            = $exhibitors_stats['increase'];

                // Area
                $exhibition_space_percentage           = $area_stats['current'];
                $exhibition_space_previous_percentage  = $area_stats['previous'];
                $exhibition_space_increase             = $area_stats['increase'];

                // Edition
                $pwe_edition = max(1, sc_int('trade_fair_edition'));
                $adapting_word = adapting_word($pwe_edition);

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
