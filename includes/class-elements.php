<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PWE_Elements {

    // Register all shortcodes and WPBakery
    public static function init() {
        // Array of shortcodes and configuration of their "types"
        $shortcodes = [
            'main'  => ['shortcode' => 'pwe-elements-auto-switch-page-main',  'title' => 'Main'],
            'catalog' => ['shortcode' => 'pwe-elements-auto-switch-page-catalog', 'title' => 'Catalog'],
        ];

        add_action('wp_enqueue_scripts', [__CLASS__, 'adding_styles']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'adding_scripts']);

        foreach ($shortcodes as $key => $data) {
            // Shortcode registration
            add_shortcode($data['shortcode'], function($atts = [], $content = null) use ($key) {
                return self::render_elements($key);
            });

            // Registering the item in WPBakery
            add_action('vc_before_init', function() use ($data) {
                if (!function_exists('vc_map')) return;

                vc_map([
                    'name'     => __('PWE Elements AutoSwitch: ' . $data['title'], 'pwe-elements-auto-switch'),
                    'base'     => $data['shortcode'],
                    'category' => __('PWE Elements', 'pwe-elements-auto-switch'),
                    'icon'     => 'icon-wpb-layer-shape',
                    'params'   => [],
                ]);
            });
        }
    }

    // Adding Styles
    public static function adding_styles() {
        // $group = PWE_Groups::get_current_group();
        $group        = 'gr2'; // <-------------------------------------- Temporary solution ---------------------------------------<

        // Common CSS for all elements
        $css_file    = plugins_url('assets/style.css', dirname(__FILE__));
        $css_version = filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/style.css');
        wp_enqueue_style('pwe-style-css', $css_file, [], $css_version);

        // Group-specific CSS
        if ($group) {
            $group_css_file = plugin_dir_path(dirname(__FILE__)) . "assets/style-{$group}.css";
            if (file_exists($group_css_file)) {
                wp_enqueue_style(
                    "pwe-style-{$group}-css",
                    plugins_url("assets/style-{$group}.css", dirname(__FILE__)),
                    [],
                    filemtime($group_css_file)
                );
            }
        }
    }

    // Adding Scripts
    public static function adding_scripts() {
        $js_file    = plugins_url('assets/script.js', dirname(__FILE__));
        $js_version = filemtime(plugin_dir_path(dirname(__FILE__)) . 'assets/script.js');
        wp_enqueue_script('pwe-script-js', $js_file, array('jquery'), $js_version, true);
    }

    // Render elements depending on type (shortcode key)
    public static function render_elements($type) {
        // $group        = PWE_Groups::get_current_group();
        $group        = 'gr2'; // <-------------------------------------- Temporary solution ---------------------------------------<
        $all_elements = PWE_Elements_Data::get_all_elements();
        $elements     = [];

        $elements_for_type = $all_elements[$type] ?? [];

        $swiper_file = plugin_dir_path(__DIR__) . 'assets/' . $group . '/swiper.php';
        if (!class_exists('PWE_Swiper') && file_exists($swiper_file)) {
            require_once $swiper_file;
        }

        // Load element files for type
        PWE_Elements_Data::require_elements($type);

        foreach ($elements_for_type as $el_conf) {
            $class  = $el_conf['class'];
            $order  = $el_conf['order'][$group] ?? 999;
            $params = $el_conf['params'] ?? [];

            if (class_exists($class)) {
                $el_data = $class::get_data();

                if (isset($el_data['types']) && in_array($type, $el_data['types'])) {
                    $elements[] = [
                        'class'  => $class,
                        'order'  => $order,
                        'params' => $params,
                    ];
                }
            }
        }

        if (empty($elements)) return '';

        // Sorting by order
        usort($elements, function($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        // Render
        ob_start();
        echo '<div id="pweElementsAutoSwitch">';
        foreach ($elements as $el) {
            $el_slug = $el['params']['slug'] ?? '';
            $camel_id = ucfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $el_slug))));

            echo '<div id="'. lcfirst(str_replace('_', '', $el['class'])) . $camel_id . ucfirst($group) .'" class="'. lcfirst(str_replace('_', '-', strtolower($el['class']))) . '-' . $group . ' ' . lcfirst(str_replace('_', '-', strtolower($el['class']))) .' pwe-limit-width">';
                $el['class']::render($group, $el['params']);
            echo '</div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

}
