<?php
if (!defined('ABSPATH')) exit;

class Sectors {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {
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

            $lang = PWE_Functions::lang();

            // Get sectors from the database
            $data = PWE_Functions::get_database_fairs_data_sectors(); 

            $sectors = [];

            if (!empty($data)) {
                foreach ($data as $row) {
                    if (!empty($row->data)) {
                        $decoded = json_decode($row->data, true);

                        if (!empty($decoded)) {
                            foreach ($decoded as $sector) {

                                $sectors[] = [
                                    'sector_name' => !empty($sector['name_' . $lang]) 
                                        ? $sector['name_' . $lang] 
                                        : ($sector['name_en'] ?? ''),
                                    'sector_image' => 'https://cap.warsawexpo.eu/public/uploads/domains/' 
                                        . str_replace('.', '-', $_SERVER['HTTP_HOST']) 
                                        . '/sectors/' 
                                        . ($sector['image'] ?? ''),
                                ];
                            }
                        }
                    }
                }
            }

            if (empty($sectors)) {
                echo '<style>.pwe-element-auto-switch.sectors {display:none;}</style>';
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
