<?php
if (!defined('ABSPATH')) exit;

class Medal_Ceremony {

    public static function get_data() {
        return [
            'types' => ['medal-ceremony'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

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

            $form_id = PWE_Functions::get_gf_form_id('Ceremonia medalowa');

            $ceremony_rules = PWE_Functions::lang_pl() ?
            'https://warsawexpo.eu/docs/Regulamin-Konkursu-Medalowego-Ptak-Warsaw-Expo.pdf' :
            'https://warsawexpo.eu/docs/Rules-of-the-Medal-Competition-Ptak-Warsaw-Expo.pdf';

            $fair_adds = PWE_Functions::get_database_fairs_data_adds();
            $medal_ceremony_data = !empty($fair_adds) && isset($fair_adds[0]->medal_ceremony) ? $fair_adds[0]->medal_ceremony : '';

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }
}
