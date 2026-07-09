<?php
if (!defined('ABSPATH')) exit;

class Registration_Visitors {

    public static function get_data() {
        return [
            'types' => ['registration-visitors'],
            'presets' => [
                'standard' => plugin_dir_path(__FILE__) . 'presets/standard/preset.php',
                'premium' => plugin_dir_path(__FILE__) . 'presets/premium/preset.php',
                // 'platyna' => plugin_dir_path(__FILE__) . 'presets/platyna/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'registration-visitors';

        if (isset($_SERVER['argv'][0])) {
            $source_utm = $_SERVER['argv'][0];
        } else {
            $source_utm = '';
        }

        if (strpos($source_utm, 'utm_source=premium') !== false  ) {
            $group = 'premium';
            $badgevipmockup = (file_exists($_SERVER['DOCUMENT_ROOT'] . '/doc/badge-mockup.webp') ? '/doc/badge-mockup.webp' : '');
        } else if(strpos($source_utm, 'utm_source=byli') !== false || strpos($source_utm, 'utm_source=platyna') !== false ) {
            $group = 'platyna';
            if (PWE_Functions::lang() === 'pl') {
                $badgevipmockup = (file_exists($_SERVER['DOCUMENT_ROOT'] . '/doc/badgevipmockup.webp') ? '/doc/badgevipmockup.webp' : '');
            } else {
                $badgevipmockup = (file_exists($_SERVER['DOCUMENT_ROOT'] . '/doc/badgevipmockup-en.webp') ? '/doc/badgevipmockup-en.webp' : '/doc/badgevipmockup.webp');
            }
        } else {
            $group = 'standard';
        }

        // var_dump($group);

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $form_id_pl = PWE_Functions::get_gf_form_id('Rejestracja PL');
            $form_id_en = PWE_Functions::get_gf_form_id('Rejestracja EN');
            $form_id_multilang = PWE_Functions::get_gf_form_id('Rejestracja Multilang');

            $lang = PWE_Functions::lang();

            if ($lang === 'pl') {

                $form_id = $form_id_pl;

            } elseif ($lang === 'en') {

                $form_id = $form_id_en;

            } else {

                $form_id = $form_id_multilang;

            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
