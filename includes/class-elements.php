<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PWE_Elements {

    // Register all shortcodes and WPBakery
    public static function init() {
        // Array of shortcodes and configuration of their "types"
        $shortcodes = [
            'main'  => ['shortcode' => 'pwe-elements-auto-switch-page-main',  'title' => 'Main'],
            'catalog' => ['shortcode' => 'pwe-elements-auto-switch-page-catalog', 'title' => 'Catalog'],
            'flip-book' => ['shortcode' => 'pwe-elements-auto-switch-page-flip-book', 'title' => 'Flip Book'],
        ];

        add_action('wp_enqueue_scripts', [__CLASS__, 'adding_styles']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'adding_scripts']);

        // Shortcodes and VC for page elements <-----------------------------------------------------------------<
        foreach ($shortcodes as $key => $data) {
            // Shortcode registration for page elements
            add_shortcode($data['shortcode'], function($atts = [], $content = null) use ($key) {
                return self::render_elements($key, $atts);
            });

            // Registering the item in WPBakery
            add_action('vc_before_init', function() use ($data) {
                if (!function_exists('vc_map')) return;

                $params = [];

                if ($data['shortcode'] === 'pwe-elements-auto-switch-page-catalog') {
                    $params = [
                        [
                            'type' => 'textfield',
                            'heading' => __('Archive ids', 'auto_pwe_katalog'),
                            'param_name' => 'archive_catalog_id',
                            'group' => 'Custom Settings',
                            'save_always' => true,
                        ],
                        [
                            'type' => 'textfield',
                            'heading' => __('Exhibitor changer', 'auto_pwe_katalog'),
                            'param_name' => 'exhibitor_changer',
                            'group' => 'Custom Settings',
                            'description' => __(
                                'Changer for exhibitors divided by ";;". Try to put names.<br>
                                Change places "name<=>name or position,<br>
                                Move to position "name=>>name or position,
                                Do not use exhibitor ID',
                                'auto_pwe_katalog'
                            ),
                            'save_always' => true,
                        ],
                    ];
                }
                if ($data['shortcode'] === 'pwe-elements-auto-switch-page-flip-book') {
                    $params = [
                        [
                            'type' => 'textfield',
                            'heading' => __('PDF URL', 'pwe-elements-auto-switch'),
                            'param_name' => 'pdf_url',
                            'group' => 'Custom Settings',
                            'description' => __('URL to the PDF file for the flip book.', 'pwe-elements-auto-switch'),
                            'save_always' => true,
                        ],
                    ];
                }

                vc_map([
                    'name'     => __('PWE Elements AutoSwitch: ' . $data['title'], 'pwe-elements-auto-switch'),
                    'base'     => $data['shortcode'],
                    'category' => __('PWE Elements Auto Switch', 'pwe-elements-auto-switch'),
                    'icon'     => 'icon-wpb-layer-shape',
                    'params'   => $params,
                ]);
            });
        }

        // Shortcodes and VC for single elements <---------------------------------------------------------------<
        $all_elements = PWE_Elements_Data::get_all_elements();
        $registered_classes = [];

        foreach ($all_elements as $type => $elements) {
            foreach ($elements as $el) {
                $class = $el['class'];

                // Avoid duplicates
                if (isset($registered_classes[$class])) {
                    continue;
                }

                $registered_classes[$class] = true;

                $shortcode = 'pwe-elements-auto-switch-' . strtolower(str_replace('_', '-', $class));

                // Shortcode
                add_shortcode($shortcode, function($atts = [], $content = null) use ($class) {
                    return PWE_Elements::render_single_element($class, $atts);
                });

                // WPBakery
                add_action('vc_before_init', function() use ($shortcode, $class, $all_elements) {
                    if (!function_exists('vc_map')) return;

                    $params = [];
                    $slug_options = [];

                    // Create a list of available slugs once per class
                    foreach ($all_elements as $type => $elements) {
                        foreach ($elements as $el) {
                            if ($el['class'] === $class && !empty($el['params']['slug'])) {
                                $slug_value = $el['params']['slug'];
                                $slug_options[$slug_value] = $slug_value;
                            }
                        }
                    }

                    if (!empty($slug_options)) {
                        $params[] = [
                            'type' => 'dropdown',
                            'heading' => __('Slug', 'pwe-elements-auto-switch'),
                            'param_name' => 'slug',
                            'group' => 'Custom Settings',
                            'value' => $slug_options,
                            'save_always' => true,
                            'description' => __('Select a slug for this element', 'pwe-elements-auto-switch'),
                        ];
                    }

                    if ($class === 'PWE_Flipbook') {
                        $params[] = [
                            'type' => 'textfield',
                            'heading' => __('Link do PDF', 'pwe-elements-auto-switch'),
                            'param_name' => 'pdf_url',
                            'value' => '',
                            'description' => __('Wklej tutaj pełny link do pliku PDF.', 'pwe-elements-auto-switch'),
                            'group' => 'Custom Settings',
                            'save_always' => true,
                        ];
                    }

                    vc_map([
                        'name'     => __('PWE Single AutoSwitch: ' . $class, 'pwe-elements-auto-switch'),
                        'base'     => $shortcode,
                        'category' => __('PWE Elements Auto Switch', 'pwe-elements-auto-switch'),
                        'icon'     => 'icon-wpb-layer-shape',
                        'params'   => $params,
                    ]);
                });
            }
        }

        // Shortcodes and VC for components <---------------------------------------------------------------------<
        $all_components = PWE_Elements_Data::get_all_components();

        foreach ($all_components as $component_key => $comp) {
            $class = $comp['class'];
            $shortcode = 'pwe-elements-component-' . strtolower(str_replace('_', '-', $class));

            // Shortcode registration
            add_shortcode($shortcode, function($atts = [], $content = null) use ($class) {
                return PWE_Elements::render_single_element($class, $atts);
            });

            // Register at WPBakery
            add_action('vc_before_init', function() use ($shortcode, $class) {
                if (!function_exists('vc_map')) return;

                $params = [];

                // // If the component have a slug, add the dropdown/text param
                // $params[] = [
                //     'type' => 'textfield',
                //     'heading' => __('Slug', 'pwe-elements-auto-switch'),
                //     'param_name' => 'slug',
                //     'group' => 'Custom Settings',
                //     'description' => __('Optional slug for this component', 'pwe-elements-auto-switch'),
                //     'save_always' => true,
                // ];

                vc_map([
                    'name'     => __('PWE Component AutoSwitch: ' . $class, 'pwe-elements-auto-switch'),
                    'base'     => $shortcode,
                    'category' => __('PWE Elements Auto Switch', 'pwe-elements-auto-switch'),
                    'icon'     => 'icon-wpb-layer-shape',
                    'params'   => $params,
                ]);
            });
        }

    }

    // Adding Styles
    public static function adding_styles() {
        $group = PWE_Groups::get_current_group();

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

    // Render single elements for shortcodes
    public static function render_single_element($class_name, $atts = []) {
        $group = PWE_Groups::get_current_group();

        $all_elements = PWE_Elements_Data::get_all_elements();

        foreach ($all_elements as $type => $elements) {
            foreach ($elements as $el) {

                if ($el['class'] !== $class_name) {
                    continue;
                }

                $order = $el['order'][$group] ?? 0;
                if ($order <= 0) {
                    return '';
                }

                // Load element files
                PWE_Elements_Data::require_elements($type);

                // Load Swiper
                $swiper_file = plugin_dir_path(__DIR__) . 'assets/' . $group . '/swiper.php';
                if (!class_exists('PWE_Swiper') && file_exists($swiper_file)) {
                    require_once $swiper_file;
                }

                if (!class_exists($class_name)) {
                    return '';
                }

                // Params: defaults + shortcode
                $default_params = $el['params'] ?? [];
                $params = array_merge($default_params, $atts);

                $el_slug  = $params['slug'] ?? '';
                $camel_id = ucfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $el_slug))));

                $base_class = lcfirst(str_replace('_', '-', strtolower($class_name)));
                $base_id    = lcfirst(str_replace('_', '', $class_name));

                ob_start();
                echo '<div 
                        id="' . $base_id . $camel_id . ucfirst($group) . '" 
                        class="' . $base_class . '-' . $group . ' ' . $base_class . ' pwe-element-auto-switch pwe-limit-width">';
                        $class_name::render($group, $params, $atts);
                echo '</div>';

                return ob_get_clean();
            }
        }

        return '';
    }

    // Render elements depending on type (shortcode key)
    public static function render_elements($type, $atts = []) {
        $group        = PWE_Groups::get_current_group();

        $all_elements = PWE_Elements_Data::get_all_elements();
        $elements     = [];

        $elements_for_type = $all_elements[$type] ?? [];

        $swiper_file = plugin_dir_path(__DIR__) . 'assets/' . $group . '/swiper.php';
        if (!class_exists('PWE_Swiper') && file_exists($swiper_file)) {
            require_once $swiper_file;
        }

        // Load element files for type
        PWE_Elements_Data::require_elements($type, $group);

        foreach ($elements_for_type as $el) {
            $class  = $el['class'];
            $order  = $el['order'][$group] ?? 999;

            if ($order <= 0) continue;

            $params = $el['params'] ?? [];

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

            echo '<div 
                    id="'. lcfirst(str_replace('_', '', $el['class'])) . $camel_id . ucfirst($group) .'" 
                    class="pwe-element-auto-switch '. lcfirst(str_replace('_', '-', strtolower($el['class']))) . '-' . $group . ' ' . lcfirst(str_replace('_', '-', strtolower($el['class']))) .' pwe-limit-width">';
                    $el['class']::render($group, $el['params'], $atts);
            echo '</div>';
        }
        echo '</div>';

        return ob_get_clean();
    }

}