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
                'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
                'week' => plugin_dir_path(__FILE__) . 'presets/week/preset.php',
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
