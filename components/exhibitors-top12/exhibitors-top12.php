<?php
if (!defined('ABSPATH')) exit;

class Exhibitors_Top12 {

    public static function get_data() {
        return [
            'types' => ['exhibitors-top12'],
            'presets' => [
                'premium' => plugin_dir_path(__FILE__) . 'presets/premium/preset.php',
                'standard' => plugin_dir_path(__FILE__) . 'presets/standard/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        if (isset($_SERVER['argv'][0])) {
            $source_utm = $_SERVER['argv'][0];
        } else {
            $source_utm = '';
        }

        if (strpos($source_utm, 'utm_source=premium') !== false  ) {
            $group = 'premium';
        } else if(strpos($source_utm, 'utm_source=byli') !== false || strpos($source_utm, 'utm_source=platyna') !== false ) {
            $group = 'platyna';
        } else {
            $group = 'standard';
        }

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, 'components');
        // Global assets
        PWE_Functions::assets_per_element($element_slug, '', 'components');
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, '', 'components');

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
            /* <-------------> General code start <-------------> */

            $exhibitors = PWE_Functions::exhibitor_logos(12);

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}