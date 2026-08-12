<?php
/**
 * PWE Phone Validator add-on.
 */

defined('ABSPATH') || exit;

final class PWE_Phone_Validator_Addon
{
    private const VERSION = '1.3.0';
    private const INTL_TEL_INPUT_VERSION = '29.1.2';

    private static bool $initialized = false;
    private static bool $assets_loaded = false;

    public static function init(): void
    {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        add_filter('gform_pre_render', [self::class, 'maybe_enqueue_assets_for_form'], 5);
        add_filter('gform_field_validation', [self::class, 'validate_phone_field'], 10, 4);
    }

    /**
     * Load the frontend phone library only when the Gravity Form being rendered
     * actually contains a field marked with the pwe-phone-validate CSS class.
     * This keeps the newer intl-tel-input isolated from legacy PWElements forms.
     */
    public static function maybe_enqueue_assets_for_form($form)
    {
        if (is_admin() || !is_array($form) || empty($form['fields'])) {
            return $form;
        }

        foreach ($form['fields'] as $field) {
            if (!$field) {
                continue;
            }

            $css_class = isset($field->cssClass) ? (string) $field->cssClass : '';
            $css_classes = preg_split('/\s+/', trim($css_class)) ?: [];

            if (in_array('pwe-phone-validate', $css_classes, true)) {
                self::enqueue_assets();
                break;
            }
        }

        return $form;
    }

    public static function enqueue_assets(): void
    {
        if (self::$assets_loaded || is_admin()) {
            return;
        }

        self::$assets_loaded = true;

        $base_url = plugin_dir_url(__FILE__);

        wp_enqueue_style(
            'pwe-intl-tel-input',
            'https://cdn.jsdelivr.net/npm/intl-tel-input@' . self::INTL_TEL_INPUT_VERSION . '/dist/css/intlTelInput.min.css',
            [],
            self::INTL_TEL_INPUT_VERSION
        );

        wp_enqueue_style(
            'pwe-phone-validator',
            $base_url . 'assets/css/phone-validator.css',
            ['pwe-intl-tel-input'],
            self::VERSION
        );

        wp_enqueue_script(
            'pwe-intl-tel-input',
            'https://cdn.jsdelivr.net/npm/intl-tel-input@' . self::INTL_TEL_INPUT_VERSION . '/dist/js/intlTelInputWithUtils.min.js',
            [],
            self::INTL_TEL_INPUT_VERSION,
            true
        );

        wp_enqueue_script(
            'pwe-phone-validator',
            $base_url . 'assets/js/phone-validator.js',
            ['pwe-intl-tel-input'],
            self::VERSION,
            true
        );

        wp_localize_script('pwe-phone-validator', 'pwePhoneValidatorConfig', [
            'invalidMessage'      => __('Wpisz prawidłowy numer telefonu.', 'pwe-elements-auto-switch'),
            'requiredMessage'     => __('Numer telefonu jest wymagany.', 'pwe-elements-auto-switch'),
            'libraryErrorMessage' => __('Nie udało się załadować walidatora telefonu.', 'pwe-elements-auto-switch'),
        ]);

        self::print_late_styles([
            'pwe-intl-tel-input',
            'pwe-phone-validator',
        ]);
    }

    /**
     * If render_elements() runs after wp_head, styles enqueued at that moment
     * would normally miss the head output. Print only these newly queued styles
     * immediately. Footer scripts remain handled by WordPress normally.
     *
     * @param string[] $handles
     */
    private static function print_late_styles(array $handles): void
    {
        if (!did_action('wp_head') || !function_exists('wp_print_styles')) {
            return;
        }

        $pending = array_values(array_filter($handles, static function ($handle) {
            return !wp_style_is($handle, 'done');
        }));

        if ($pending) {
            wp_print_styles($pending);
        }
    }

    /**
     * Server-side safety net for Gravity Forms.
     */
    public static function validate_phone_field($result, $value, $form, $field)
    {
        if (!$field || !$result['is_valid']) {
            return $result;
        }

        $css_classes = preg_split('/\\s+/', trim((string) $field->cssClass)) ?: [];
        if (!in_array('pwe-phone-validate', $css_classes, true)) {
            return $result;
        }

        $phone = is_string($value) ? trim($value) : '';
        if ($phone === '') {
            return $result;
        }

        $field_id = (string) $field->id;
        $marker_key = 'pwe_phone_valid_' . $field_id;
        $client_valid = isset($_POST[$marker_key])
            ? sanitize_text_field(wp_unslash($_POST[$marker_key]))
            : '';

        $e164_ok = (bool) preg_match('/^\\+[1-9][0-9]{7,14}$/', $phone);

        if ($client_valid !== '1' || !$e164_ok) {
            $result['is_valid'] = false;
            $result['message'] = __('Wpisz prawidłowy numer telefonu.', 'pwe-elements-auto-switch');
        }

        return $result;
    }
}

PWE_Phone_Validator_Addon::init();
