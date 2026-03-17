<?php
if (!defined('ABSPATH')) exit;

class Premieres {

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
        $element_slug = strtolower(__CLASS__);

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
        /* <-------------> General code start <-------------> */

        $current_domain = $_SERVER['HTTP_HOST'];
        $premieres = PWE_Functions::get_database_premieres_data($current_domain);

        if (empty($premieres[0]->slug)) {
            echo '<style>.pwe-element-auto-switch.premieres {display:none;}</style>';
            return;
        }

        $slides = [];
        foreach ($premieres as $premiere) {
            $data = json_decode($premiere->data, true);
            if (!isset($data[$premiere->slug])) continue;
            $item = $data[$premiere->slug];

            $slides[] = [
                'name'      => PWE_Functions::lang_pl() ? $item['name_pl'] : ($item['name_en'] ?? $item['name_pl']),
                'desc'      => PWE_Functions::lang_pl() ? $item['desc_pl'] : ($item['desc_en'] ?? $item['desc_pl']),
                'exhibitor' => $item['exhibitor'] ?? '',
                'stand'     => $item['stand'],
                'img'       => $item['background'] ?? '',
                'logo'      => $item['logo'] ?? ''
            ];
        }

        /* <-------------> General code end <-------------> */

            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
