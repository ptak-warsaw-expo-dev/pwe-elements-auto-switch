<?php
/**
 * PWE Phone Validator add-on.
 *
 * In the main plugin file add:
 * require_once plugin_dir_path(__FILE__) . 'addons/phone-validator/phone-validator.php';
 */

defined('ABSPATH') || exit;

final class PWE_Phone_Validator_Addon
{
    private const VERSION = '1.2.0';
    private const INTL_TEL_INPUT_VERSION = '29.1.2';

    public static function init(): void
    {
        add_action('wp_enqueue_scripts', [self::class, 'enqueue_assets'], 20);
        add_filter('gform_field_validation', [self::class, 'validate_phone_field'], 10, 4);
    }

    public static function enqueue_assets(): void
    {
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
            'invalidMessage' => __('Wpisz prawidłowy numer telefonu.', 'pwe-elements-auto-switch'),
            'requiredMessage' => __('Numer telefonu jest wymagany.', 'pwe-elements-auto-switch'),
            'libraryErrorMessage' => __('Nie udało się załadować walidatora telefonu.', 'pwe-elements-auto-switch'),
        ]);
    }

    /**
     * Server-side safety net for Gravity Forms.
     *
     * The browser validator writes a per-field validity marker immediately
     * before submission. Gravity Forms then verifies that marker and the E.164
     * value. This prevents AJAX submission paths from accepting a number that
     * intl-tel-input has already marked as invalid.
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
            // Required-field handling remains Gravity Forms' responsibility.
            return $result;
        }

        $field_id = (string) $field->id;
        $marker_key = 'pwe_phone_valid_' . $field_id;
        $client_valid = isset($_POST[$marker_key])
            ? sanitize_text_field(wp_unslash($_POST[$marker_key]))
            : '';

        // JS normalizes valid values to E.164 before submission.
        $e164_ok = (bool) preg_match('/^\\+[1-9][0-9]{7,14}$/', $phone);

        if ($client_valid !== '1' || !$e164_ok) {
            $result['is_valid'] = false;
            $result['message'] = __('Wpisz prawidłowy numer telefonu.', 'pwe-elements-auto-switch');
        }

        return $result;
    }

}

PWE_Phone_Validator_Addon::init();
