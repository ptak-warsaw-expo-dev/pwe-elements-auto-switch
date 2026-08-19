<?php
if (!defined('ABSPATH')) exit;

class Registration_Exhibitors {

    private static $session_registered = false;

    public static function get_data() {
        return [
            'types' => ['registration-exhibitors'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'registration-exhibitors';
        $group = 'all';

        self::register_session_handler();

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);

        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);

        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = $data['presets'][$group] ?? null;

        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $form_id = PWE_Functions::get_gf_form_id('Zostań wystawcą');

            if (!$form_id) {
                return;
            }

            $fair_group = do_shortcode('[trade_fair_group]');

            $registration_title = PWE_Functions::multi_translation('exhibitor_registration_title');
            $registration_text = PWE_Functions::multi_translation('exhibitor_registration_text');

            $gravity_form = do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="false"]');

            $exhibitors = PWE_Functions::exhibitor_logos(12);

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }

    private static function register_session_handler() {

        if (self::$session_registered) {
            return;
        }

        self::$session_registered = true;

        add_action('gform_after_submission', [__CLASS__, 'entry_to_session'], 10, 2);
    }

    public static function entry_to_session($entry, $form) {

        $form_id = PWE_Functions::get_gf_form_id('Zostań wystawcą');

        if ((int)$form['id'] !== (int)$form_id) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['pwe_exhibitor_entry'] = [
            'entry_id' => $entry['id'],
        ];

        if (empty($form['fields'])) {
            return;
        }

        foreach ($form['fields'] as $field) {

            if (!is_object($field)) {
                continue;
            }

            if ($field->type === 'email') {
                $_SESSION['pwe_exhibitor_entry']['email'] = rgar($entry, $field->id);
            }

            if ($field->type === 'phone') {
                $_SESSION['pwe_exhibitor_entry']['phone'] = rgar($entry, $field->id);
            }
        }
        session_write_close();
    }
}