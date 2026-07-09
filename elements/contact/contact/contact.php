<?php
if (!defined('ABSPATH')) exit;

class Contact {

    public static function get_data() {
        return [
            'types' => ['contact'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'contact';

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

            $form_id_pl = PWE_Functions::get_gf_form_id('Napisz do nas');
            $form_id_en = PWE_Functions::get_gf_form_id('Write to us');

            $lang = PWE_Functions::lang();

            if ($lang === 'pl') {

                $form_id = $form_id_pl;

            } else {

                $form_id = $form_id_en;

            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
