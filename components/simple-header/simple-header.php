<?php
if (!defined('ABSPATH')) exit;

class Simple_Header {

    private static $rendered = false;

    public static function get_data() {
        return [
            'types' => ['speakers'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group) {

        // Locking the element if the footer has been rendered
        if (self::$rendered) {
            return;
        }

        self::$rendered = true;

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        $group = 'all';

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, '');
        // Global assets
        PWE_Functions::assets_per_element($element_slug, '', 'components');
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, '', 'components');
        
        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $trade_fair_name = (PWE_Functions::lang_pl()) ? do_shortcode('[trade_fair_name]') : do_shortcode('[trade_fair_name_eng]');
            $trade_fair_date = do_shortcode('[trade_fair_date_multilang]');

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
