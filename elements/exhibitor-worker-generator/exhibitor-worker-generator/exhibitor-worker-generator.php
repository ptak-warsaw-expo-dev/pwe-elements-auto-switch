<?php
if (!defined('ABSPATH')) exit;

class Exhibitor_Worker_Generator {

    public static function get_data() {
        return [
            'types' => ['exhibitor-worker-generator'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'exhibitor-worker-generator';

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

            $form_id = PWE_Functions::get_gf_form_id('Rejestracja wystawców (badge)');

            $gravity_form = do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="false"]');

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }
}
