<?php
if (!defined('ABSPATH')) exit;

class Menu {

    public static function get_data() {
        return [
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group) {
        $data = self::get_data();
        $element_type = $data['types'][0] ?? '';
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        $group = 'all';

        $atts = [
            'menu_transparent'       => !empty(get_option('pwe_menu_options', [])['pwe_menu_transparent']) ? "true" : "false",
            'trade_fair_datetotimer' => do_shortcode('[trade_fair_datetotimer]'),
            'trade_fair_enddata'     => do_shortcode('[trade_fair_enddata]'),
        ];

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type = '', $folder = 'components');
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type = '', $folder = 'components', $atts);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
            /* <-------------> General code start <-------------> */

            

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}