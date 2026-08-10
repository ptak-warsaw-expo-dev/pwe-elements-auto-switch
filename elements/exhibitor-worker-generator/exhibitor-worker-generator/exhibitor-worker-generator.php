<?php
if (!defined('ABSPATH')) exit;

class Exhibitor_Worker_Generator {

    public static function get_data() {
        return [
            'types' => ['exhibitor-worker-generator'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
                'single' => plugin_dir_path(__FILE__) . 'presets/single/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'exhibitor-worker-generator';

        $generator_company = isset($_GET['generator'])
            ? sanitize_text_field(wp_unslash($_GET['generator']))
            : '';

        $field_values = http_build_query([
            'company' => $generator_company,
        ], '', '&', PHP_QUERY_RFC3986);

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

            $lang = PWE_Functions::lang();

            $form_id = PWE_Functions::get_gf_form_id('Rejestracja wystawców (badge)');

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

            $badge_path = '/wp-content/plugins/pwe-media/media/generator-wystawcow/badgevipmockup-wys.webp';

            $badge_file = ABSPATH . ltrim($badge_path, '/');

            $badge = 'url("' . esc_url($badge_path) . '")';

            $fair_group = do_shortcode('[trade_fair_group]');

            if ($fair_group === 'gr3') {
                $email = 'biuro.podawcze3@<wbr>warsawexpo.eu';
            } else {
                $email = 'info@<wbr>warsawexpo.eu';
            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
