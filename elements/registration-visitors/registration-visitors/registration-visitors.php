<?php
if (!defined('ABSPATH')) exit;

class Registration_Visitors {

    private static $filters_registered = false;
    private static $session_registered = false;

    public static function get_data() {
        return [
            'types' => ['registration-visitors'],
            'presets' => [
                'standard' => plugin_dir_path(__FILE__) . 'presets/standard/preset.php',
                'premium'  => plugin_dir_path(__FILE__) . 'presets/premium/preset.php',
                'byli'     => plugin_dir_path(__FILE__) . 'presets/byli/preset.php',
                'platyna'  => plugin_dir_path(__FILE__) . 'presets/platyna/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'registration-visitors';

        $source_utm = isset($_GET['utm_source'])
            ? sanitize_key(wp_unslash($_GET['utm_source']))
            : '';

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (in_array($source_utm, ['byli', 'premium', 'platyna'], true)) {
            $_SESSION['pwe_registration_utm_source'] = $source_utm;
        }

        if ($source_utm === 'premium') {
            $group = 'premium';
            $badgevipmockup = self::get_existing_document('/doc/badgevipmockup.webp');
        } elseif ($source_utm === 'byli') {
            $group = 'byli';
            $badgevipmockup = self::get_vip_badge_mockup();
        } elseif ($source_utm === 'platyna') {
            $group = 'platyna';
            $badgevipmockup = '';
        } else {
            $group = 'standard';
            $badgevipmockup = '';
        }

        self::register_gravity_forms_filters();
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

            $form_id = PWE_Functions::get_gf_form_id('Rejestracja');

            if (!$form_id) {
                return;
            }

            $fair_group = do_shortcode('[trade_fair_group]');

            $gravity_form = do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="false"]');

            $exhibitors = PWE_Functions::exhibitor_logos(12);

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }

    private static function get_existing_document($path) {
        if (empty($_SERVER['DOCUMENT_ROOT'])) {
            return '';
        }

        return file_exists($_SERVER['DOCUMENT_ROOT'] . $path) ? $path : '';
    }

    private static function get_vip_badge_mockup() {
        if (PWE_Functions::lang() === 'pl') {
            return self::get_existing_document('/doc/badgevipmockup.webp');
        }

        $english_mockup = self::get_existing_document('/doc/badgevipmockup-en.webp');

        if ($english_mockup) {
            return $english_mockup;
        }

        return self::get_existing_document('/doc/badgevipmockup.webp');
    }

    private static function register_gravity_forms_filters() {
        if (self::$filters_registered) {
            return;
        }

        self::$filters_registered = true;

        add_filter('gform_pre_render', [__CLASS__, 'hide_registration_fields']);

        add_filter(
            'gform_confirmation',
            [__CLASS__, 'add_utm_to_confirmation_redirect'],
            10,
            4
        );
    }

    private static function register_session_handler() {

        if (self::$session_registered) {
            return;
        }

        self::$session_registered = true;

        add_action('gform_after_submission', [__CLASS__, 'entry_to_session'], 10, 2);

    }

    public static function entry_to_session($entry, $form) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $utm_source = sanitize_key(
            $_SESSION['pwe_registration_utm_source'] ?? ''
        );

        $_SESSION['pwe_reg_entry'] = [
            'entry_id'   => absint($entry['id']),
            'utm_source' => $utm_source,
        ];

        if (empty($form['fields'])) {
            return;
        }

        foreach ($form['fields'] as $field) {
            if (!is_object($field)) {
                continue;
            }

            if ($field->type === 'email') {
                $_SESSION['pwe_reg_entry']['email'] = sanitize_email(
                    rgar($entry, $field->id)
                );
            }

            if ($field->type === 'phone') {
                $_SESSION['pwe_reg_entry']['phone'] = sanitize_text_field(
                    rgar($entry, $field->id)
                );
            }

            $admin_label = (string) ($field->adminLabel ?? '');

            if ($admin_label === 'utm_source') {
                $entry_utm_source = sanitize_key(
                    rgar($entry, $field->id)
                );

                if (in_array($entry_utm_source, ['byli', 'premium', 'platyna'], true)) {
                    $_SESSION['pwe_reg_entry']['utm_source'] = $entry_utm_source;
                    $_SESSION['pwe_registration_utm_source'] = $entry_utm_source;
                }
            }
        }
    }

    public static function add_utm_to_confirmation_redirect(
        $confirmation,
        $form,
        $entry,
        $ajax
    ) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $utm_source = sanitize_key(
            $_SESSION['pwe_reg_entry']['utm_source']
            ?? $_SESSION['pwe_registration_utm_source']
            ?? ''
        );

        if (!in_array($utm_source, ['byli', 'premium', 'platyna'], true)) {
            return $confirmation;
        }

        if (!is_array($confirmation) || empty($confirmation['redirect'])) {
            return $confirmation;
        }

        $confirmation['redirect'] = add_query_arg(
            'utm_source',
            $utm_source,
            $confirmation['redirect']
        );

        return $confirmation;
    }

    public static function hide_registration_fields($form) {

        foreach (($form['fields'] ?? []) as $field) {
            if (
                is_object($field)
                && in_array((string) $field->adminLabel, ['name', 'street', 'house', 'post', 'city'], true)
            ) {
                $field->visibility = 'hidden';
            }
        }

        return $form;
    }
}
