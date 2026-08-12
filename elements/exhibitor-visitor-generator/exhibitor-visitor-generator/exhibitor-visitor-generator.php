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

            if ($lang === 'en') {
                $translations = [
                    'Gość - Imię i Nazwisko'     => 'Guest - Full Name',
                    'Firma Zapraszająca'         => 'Inviting Company',
                    'E-mail osoby zapraszanej'   => 'Guest E-mail',
                    'Wyślij'                      => 'Send',
                ];

                $form = strtr($form, $translations);
            }

            $badge_path = '/doc/badgevip.webp';

            $badge_file = ABSPATH . ltrim($badge_path, '/');

            if (!file_exists($badge_file)) {
                $badge_path = '/wp-content/plugins/pwe-media/media/generator-gosci-wystawcow-auto-switch/badgevip.webp';
            }

            $badge = 'url("' . do_shortcode('[trade_fair_exhibitor_generator_badge_url]') . '")';

            $fair_group = do_shortcode('[trade_fair_group]');

            $domain = $_SERVER['HTTP_HOST'];

            if ($domain === 'campercaravanshow.com') {
                $email = 'biuro.podawcze3@warsawexpo.eu';
            }
            else if ($fair_group === 'b2c' || $fair_group === 'b2c-new') {
                $email = 'biuro.podawcze2@warsawexpo.eu';
            } else {
                $email = do_shortcode('[trade_fair_contact]');
            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
