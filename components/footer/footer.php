<?php
if (!defined('ABSPATH')) exit;

class Footer {

    private static $rendered = false;

    public static function get_data() {
        return [
            'types' => ['main', 'catalog', 'flip-book'],
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

            $menus = wp_get_nav_menus();

            foreach ($menus as $menu) {
                $menu_name_lower = strtolower($menu->name);
                $patterns = ['1 pl', '1 en', '2 pl', '2 en', '3 pl', '3 en'];
                foreach ($patterns as $pattern) {
                    if (strpos($menu_name_lower, $pattern) !== false) {
                        $varName = 'menu_' . str_replace(' ', '_', $pattern);
                        // $menu_1_pl, $menu_2_pl ...
                        $$varName = $menu->name;
                        break;
                    }
                }
            } 

            $base_url = ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http' ) . '' . $_SERVER['HTTP_HOST'];
            $page_url = 'https://' . $_SERVER['HTTP_HOST'] . PWE_Functions::languageChecker('', '/en/', '/de/');

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}