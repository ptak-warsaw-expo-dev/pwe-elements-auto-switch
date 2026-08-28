<?php
if (!defined('ABSPATH')) exit;

class Side_Events {

    public static function get_data() {
        return [
            'types' => ['conferences'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'side-events';

        $group = 'all';

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $group = do_shortcode('[trade_fair_group]');

            $side_events = [
                'gr1' => [
                    'medal_img' => '/wp-content/plugins/pwe-media/media/conferences/side-events/medals_1_pl.webp',
                    'categories' => [
                        [
                            'title' => 'medal_gr1_category_1_title',
                            'description' => 'medal_gr1_category_1_description',
                        ],
                        [
                            'title' => 'medal_gr1_category_2_title',
                            'description' => 'medal_gr1_category_2_description',
                        ],
                        [
                            'title' => 'medal_gr1_category_3_title',
                            'description' => 'medal_gr1_category_3_description',
                        ],
                        [
                            'title' => 'medal_gr1_category_4_title',
                            'description' => 'medal_gr1_category_4_description',
                        ],
                    ],
                ],

                'default' => [
                    'medal_img' => '/wp-content/plugins/pwe-media/media/conferences/side-events/medals_all.webp',
                    'categories' => [
                        [
                            'title' => 'medal_default_category_1_title',
                            'description' => 'medal_default_category_1_description',
                        ],
                        [
                            'title' => 'medal_default_category_2_title',
                            'description' => 'medal_default_category_2_description',
                        ],
                        [
                            'title' => 'medal_default_category_3_title',
                            'description' => 'medal_default_category_3_description',
                        ],
                        [
                            'title' => 'medal_default_category_4_title',
                            'description' => 'medal_default_category_4_description',
                        ],
                    ],
                ],
            ];

            $data = $side_events[$group] ?? $side_events['default'];

            $medal_img = $data['medal_img'];
            $categories = $data['categories'];

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }
}
