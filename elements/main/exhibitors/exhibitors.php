<?php
if (!defined('ABSPATH')) exit;

class Exhibitors {

    public static function get_data() {
        return [
            'types' => ['main'],
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
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
            /* <-------------> General code start <-------------> */
           
            $katalog_id = do_shortcode('[pwe_catalog]');

            if ($group === "gr1") {
                $exhibitors = PWE_Functions::exhibitor_logos($katalog_id, 16);
            } else {
                $exhibitors = PWE_Functions::exhibitor_logos($katalog_id);
            }

            if (!is_array($exhibitors)) {
                $exhibitors = [];
            }
            
            if (count($exhibitors) < 16) {
                echo '<style>.exhibitors-'. $group .'{display:none;}</style>';
                return;
            }

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
