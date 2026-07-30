<?php
if (!defined('ABSPATH')) exit;

class Step2 {

    public static function get_data() {
        return [
            'types' => ['step2'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'step2';

        $group = 'all';

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $lang = PWE_Functions::lang();

            $logo_src = $lang === 'pl' ? '/doc/logo-color.webp' : '/doc/logo-color-en.webp';

            $trade_fair_date = do_shortcode('[trade_fair_enddata]');

            $trade_fair_timestamp = strtotime($trade_fair_date);
            $current_timestamp = time();

            $days_difference = ($trade_fair_timestamp - $current_timestamp) / (60 * 60 * 24);

            $pwe_groups_data = do_shortcode('[pwe_group]');

            $current_domain = trim(do_shortcode('[trade_fair_domainadress]'));

            $day_limit = in_array($pwe_groups_data, ["gr1", "gr2"]) ? 21 : 17;

            $is_invalid_date = ($trade_fair_timestamp === false || empty($trade_fair_date));
            $is_past_or_far  = ($days_difference > $day_limit || $trade_fair_timestamp < $current_timestamp);
            $is_pl           = (get_locale() === "pl_PL");

            $step2_link_exhibitor_no = '/potwierdzenie-rejestracji/';

            if ($current_domain === "gunshootingexpo.com") {
                $link = '[url_home]';
            } else {
                $link = (($is_invalid_date || $is_past_or_far) && $is_pl)
                    ? $step2_link_exhibitor_no
                    : '[url_home]';
            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }
}
