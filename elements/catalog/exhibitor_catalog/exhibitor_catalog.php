<?php
if (!defined('ABSPATH')) exit;

class Exhibitor_Catalog {

    public static function get_data() {
        return [
            'types' => ['catalog'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/preset-all/preset-all.php'
            ],
        ];
    }

    public static function render($group) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(__CLASS__);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets']['all'] ?? null;



        if ($preset_file && file_exists($preset_file)) {

            $output = include $preset_file;


            /* <-------------> General code start <-------------> */




            /* <-------------> General code end <-------------> */


            if ($output) {
                echo $output;
            }
        }
    }
}
