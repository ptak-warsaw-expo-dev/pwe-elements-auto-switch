
<?php
if (!defined('ABSPATH')) exit;

class Statistics {

    private const MAX_COMBINED_AREA = 153000;

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1'     => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                'gr2'     => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
                'b2c-new' => plugin_dir_path(__FILE__) . 'presets/b2c-new/preset.php',
                'week'    => plugin_dir_path(__FILE__) . 'presets/week/preset.php',
            ],
        ];
    }

    // Pobieranie statystyki z shortcode.
    private static function stat($key, $domain = '', $default = 0) {
        $atts = $domain ? ' domain="' . esc_attr($domain) . '"' : '';
        $value = trim((string) do_shortcode("[$key$atts]"));
        return ctype_digit($value) && (int) $value > 0 ? (int) $value : $default;
    }

    // Normalizacja domeny.
    private static function normalize_domain($domain) {
        $domain = strtolower(trim((string) $domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = explode('/', $domain)[0];
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '.');

        return preg_match('/^[a-z0-9-]+(?:\.[a-z0-9-]+)+$/', $domain) ? $domain : '';
    }

    // Lista domen powiązanych, bez domen wskazanych jako main_fair_domain.
    private static function associate_domains($events) {
        $domains = [];
        $excluded = [];

        foreach ($events as $event) {
            if (!is_object($event)) continue;

            $main = self::normalize_domain($event->main_fair_domain ?? '');
            if ($main !== '') $excluded[] = $main;

            foreach (explode(',', (string) ($event->fair_associates ?? '')) as $domain) {
                $domain = self::normalize_domain($domain);
                if ($domain !== '') $domains[] = $domain;
            }
        }

        return array_values(array_diff(
            array_unique($domains),
            array_unique($excluded)
        ));
    }

    // Sumowanie statystyk wydarzeń połączonych.
    private static function combined_stats($events) {
        $sum = [
            'pwe_visitors', 'pwe_visitors_foreign', 'pwe_exhibitors', 'pwe_area',
            'pwe_visitors_prev', 'pwe_visitors_foreign_prev', 'pwe_exhibitors_prev', 'pwe_area_prev',
        ];

        $max = ['pwe_countries', 'pwe_countries_prev'];
        $stats = array_fill_keys(array_merge($sum, $max), 0);

        foreach (self::associate_domains($events) as $domain) {
            foreach ($sum as $key) {
                $stats[$key] += self::stat($key, $domain);
            }

            foreach ($max as $key) {
                $stats[$key] = max($stats[$key], self::stat($key, $domain));
            }
        }

        $stats['pwe_area'] = min(self::MAX_COMBINED_AREA, $stats['pwe_area']);
        $stats['pwe_area_prev'] = min(self::MAX_COMBINED_AREA, $stats['pwe_area_prev']);

        return $stats;
    }

    public static function render($group = '', $params = [], $atts = []) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(__CLASS__);

        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        PWE_Functions::assets_per_element($element_slug, $element_type);
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $host = $_SERVER['HTTP_HOST'] ?? '';
        $combined_events = PWE_Functions::get_database_associates_data($host, true);

        if (empty($combined_events)) {
            $combined_events = PWE_Functions::get_database_associates_data($host, false);
        }

        // CSS dla wydarzeń połączonych.
        if (!empty($combined_events)) {
            $css = 'presets/gr2/assets/style-combined.css';
            $path = plugin_dir_path(__FILE__) . $css;

            wp_enqueue_style(
                'pwe-statistics-combined',
                plugin_dir_url(__FILE__) . $css,
                ['pwe-statistics-style'],
                file_exists($path) ? filemtime($path) : '1.0.0'
            );
        }

        $preset_file = $data['presets'][$group] ?? null;
        if (!$preset_file || !file_exists($preset_file)) return;

        /* <-------------> General code start <-------------> */

        $b2c_option = $atts['b2c'] ?? false;
        $b2c = PWE_Groups::is_b2c() ?: $b2c_option;
        $edition = do_shortcode('[pwe_edition]');

        if (!function_exists('ordinal_suffix')) {
            function ordinal_suffix($n) {
                if (!in_array($n % 100, [11, 12, 13])) {
                    switch ($n % 10) {
                        case 1: return $n . 'st';
                        case 2: return $n . 'nd';
                        case 3: return $n . 'rd';
                    }
                }
                return $n . 'th';
            }
        }

        if (!function_exists('adapting_word')) {
            function adapting_word($edition) {
                if ($edition == 1) return PWE_Functions::multi_translation('estimates_1st');

                $locale = get_locale();
                $number = $edition - 1;

                if ($locale === 'en_US') {
                    if ($edition == 2) return PWE_Functions::multi_translation('industry_visitors_1st');
                    if ($edition == 3) return PWE_Functions::multi_translation('industry_visitors_2nd');
                    if ($edition == 4) return PWE_Functions::multi_translation('industry_visitors_3rd');

                    $translation = PWE_Functions::multi_translation('industry_visitors_nth');

                    foreach (['th', 'st', 'nd', 'rd'] as $suffix) {
                        if (strpos($translation, '{number}' . $suffix) !== false) {
                            return str_replace('{number}', $number, $translation);
                        }
                    }

                    return str_replace('{number}', ordinal_suffix($number), $translation);
                }

                return str_replace(
                    '{number}',
                    $number,
                    PWE_Functions::multi_translation('industry_visitors_nth')
                );
            }
        }

        if (!function_exists('sc_int')) {
            function sc_int(string $shortcode, int $default = 0): int {
                $value = (int) do_shortcode("[$shortcode]");
                return $value > 0 ? $value : $default;
            }
        }

        if (!function_exists('compare_values')) {
            function compare_values(int $current, int $previous): array {
                if ($previous <= 0 && $current <= 0) {
                    return ['current' => 0, 'previous' => 0, 'increase' => 0];
                }

                $max = max($current, $previous, 1);

                return [
                    'current'  => ($current / $max) * 100,
                    'previous' => ($previous / $max) * 100,
                    'increase' => round(100 - (($previous / $max) * 100)),
                ];
            }
        }

        // Pobieranie statystyk.
        $keys = [
            'pwe_visitors', 'pwe_visitors_foreign', 'pwe_exhibitors', 'pwe_countries', 'pwe_area',
            'pwe_visitors_prev', 'pwe_visitors_foreign_prev', 'pwe_exhibitors_prev',
            'pwe_countries_prev', 'pwe_area_prev',
        ];

        if (!empty($combined_events)) {
            $stats = self::combined_stats($combined_events);
        } else {
            $stats = [];

            foreach ($keys as $key) {
                $stats[$key] = self::stat($key, '', $key === 'pwe_countries' ? 15 : 0);
            }
        }

        // Zmienne wykorzystywane przez presety.
        $pwe_visitors              = $stats['pwe_visitors'];
        $pwe_visitors_foreign      = $stats['pwe_visitors_foreign'];
        $pwe_exhibitors            = $stats['pwe_exhibitors'];
        $pwe_countries             = $stats['pwe_countries'];
        $pwe_area                  = $stats['pwe_area'];

        $pwe_visitors_prev         = $stats['pwe_visitors_prev'];
        $pwe_visitors_foreign_prev = $stats['pwe_visitors_foreign_prev'];
        $pwe_exhibitors_prev       = $stats['pwe_exhibitors_prev'];
        $pwe_countries_prev        = $stats['pwe_countries_prev'];
        $pwe_area_prev             = $stats['pwe_area_prev'];

        $pwe_statistics_year_curr = sc_int('pwe_statistics_year_curr');
        $pwe_statistics_year_prev = sc_int('pwe_statistics_year_prev');

        // Odwiedzający i procenty.
        $visitors_total = $pwe_visitors;
        $visitors_abroad = $pwe_visitors_foreign;
        $visitors_percent = $visitors_total > 0
            ? round($visitors_abroad / $visitors_total * 100)
            : 0;

        $polish_visitors = max(0, $pwe_visitors - $pwe_visitors_foreign);
        $percent_polish = $pwe_visitors > 0
            ? round($polish_visitors / $pwe_visitors * 100)
            : 0;
        $percent_abroad = 100 - $percent_polish;

        // Porównania statystyk.
        $visitors_stats = compare_values($pwe_visitors, $pwe_visitors_prev);
        $exhibitors_stats = compare_values($pwe_exhibitors, $pwe_exhibitors_prev);
        $area_stats = compare_values($pwe_area, $pwe_area_prev);

        $number_visitors_percentage = $visitors_stats['current'];
        $number_visitors_previous_percentage = $visitors_stats['previous'];
        $number_visitors_increase = $visitors_stats['increase'];

        $number_exhibitors_percentage = $exhibitors_stats['current'];
        $number_exhibitors_previous_percentage = $exhibitors_stats['previous'];
        $number_exhibitors_increase = $exhibitors_stats['increase'];

        $exhibition_space_percentage = $area_stats['current'];
        $exhibition_space_previous_percentage = $area_stats['previous'];
        $exhibition_space_increase = $area_stats['increase'];

        // Edycja targów.
        $pwe_edition = max(1, sc_int('trade_fair_edition'));
        $adapting_word = adapting_word($pwe_edition);

        /* <-------------> General code end <-------------> */

        $output = include $preset_file;

        if ($output) {
            echo do_shortcode($output);
        }
    }
}