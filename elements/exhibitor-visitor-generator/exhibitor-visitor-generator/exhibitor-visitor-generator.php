<?php
if (!defined('ABSPATH')) exit;

class Exhibitor_Visitor_Generator {

    public static function get_data() {
        return [
            'types' => ['exhibitor-visitor-generator'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
                'single' => plugin_dir_path(__FILE__) . 'presets/single/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'exhibitor-visitor-generator';

        $generator_company = isset($_GET['generator'])
            ? sanitize_text_field(wp_unslash($_GET['generator']))
            : '';

        $field_values = http_build_query([
            'company' => $generator_company,
        ], '', '&', PHP_QUERY_RFC3986);

        $group = $generator_company !== ''
            ? 'single'
            : 'all';

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

            $form_id = PWE_Functions::get_gf_form_id('Rejestracja gości wystawców');

            if (!$form_id) {
                return;
            }

            $form = do_shortcode(
                '[gravityform id="' . absint($form_id) . '"
                    title="false"
                    description="false"
                    ajax="false"
                    field_values="' . esc_attr($field_values) . '"]'
            );

            $badge_path = '/doc/badgevip.webp';

            $badge_file = ABSPATH . ltrim($badge_path, '/');

            if (!file_exists($badge_file)) {
                $badge_path = '/wp-content/plugins/pwe-media/media/generator-gosci-wystawcow-auto-switch/badgevip.webp';
            }

            $badge = 'url("' . esc_url($badge_path) . '")';

            $fair_group = do_shortcode('[trade_fair_group]');

            $icons_path = '/wp-content/plugins/pwe-media/media/generator-gosci-wystawcow-auto-switch/icons/';

            $icons = [
                'fast_track' => [
                    'image'       => 'fast-track-icon.png',
                    'translation' => 'fast_track',
                ],
                'vip_room' => [
                    'image'       => 'vip-room-icon.png',
                    'translation' => 'vip_room',
                ],
                'concierge' => [
                    'image'       => 'concierge-icon.png',
                    'translation' => 'concierge',
                ],
                'conferences' => [
                    'image'       => 'conferences-icon.png',
                    'translation' => 'konferencje',
                ],
                'parking' => [
                    'image'       => 'parking-icon.png',
                    'translation' => 'parking',
                ],
            ];

            $group_icons = [
                'gr1' => [
                    'concierge',
                    'conferences',
                    'parking',
                ],
                'gr2' => [
                    'fast_track',
                    'vip_room',
                    'concierge',
                    'conferences',
                    'parking',
                ],
                'gr3' => [
                    'vip_room',
                    'concierge',
                    'conferences',
                    'parking',
                ],
            ];

            $selected_icons = $group_icons[$fair_group] ?? $group_icons['gr1'];

            if ($fair_group === 'gr3') {
                $email = 'media3@warsawexpo.eu';
            } else {
                $email = 'generator.wystawcow@warsawexpo.eu';
            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
