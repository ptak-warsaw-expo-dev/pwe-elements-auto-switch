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

        $messages = self::get_messages();
        $language = self::get_language();
        $current_messages = $messages[$language] ?? $messages['en'];

        wp_localize_script('pwe-phone-validator', 'pwePhoneValidatorConfig', [
            'invalidMessage'      => $current_messages['invalid'] ?? $messages['en']['invalid'],
            'requiredMessage'     => $current_messages['required'] ?? $messages['en']['required'],
            'libraryErrorMessage' => $current_messages['library_error'] ?? $messages['en']['library_error'],
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

    /** @return array<string,array<string,string>> */
    private static function get_messages(): array
    {
        return [
            'pl' => [
                'invalid'       => 'Wpisz prawidłowy numer telefonu.',
                'required'      => 'Numer telefonu jest wymagany.',
                'library_error' => 'Nie udało się załadować walidatora telefonu.',
            ],
            'en' => [
                'invalid'       => 'Enter a valid phone number.',
                'required'      => 'Phone number is required.',
                'library_error' => 'Failed to load the phone validator.',
            ],
            'cs' => [
                'invalid'       => 'Zadejte platné telefonní číslo.',
                'required'      => 'Telefonní číslo je povinné.',
                'library_error' => 'Nepodařilo se načíst validátor telefonního čísla.',
            ],
            'de' => [
                'invalid'       => 'Geben Sie eine gültige Telefonnummer ein.',
                'required'      => 'Die Telefonnummer ist erforderlich.',
                'library_error' => 'Der Telefonnummern-Validator konnte nicht geladen werden.',
            ],
            'it' => [
                'invalid'       => 'Inserisci un numero di telefono valido.',
                'required'      => 'Il numero di telefono è obbligatorio.',
                'library_error' => 'Impossibile caricare il validatore del numero di telefono.',
            ],
            'lt' => [
                'invalid'       => 'Įveskite galiojantį telefono numerį.',
                'required'      => 'Telefono numeris yra privalomas.',
                'library_error' => 'Nepavyko įkelti telefono numerio tikrinimo priemonės.',
            ],
            'lv' => [
                'invalid'       => 'Ievadiet derīgu tālruņa numuru.',
                'required'      => 'Tālruņa numurs ir obligāts.',
                'library_error' => 'Neizdevās ielādēt tālruņa numura validatoru.',
            ],
            'sk' => [
                'invalid'       => 'Zadajte platné telefónne číslo.',
                'required'      => 'Telefónne číslo je povinné.',
                'library_error' => 'Nepodarilo sa načítať validátor telefónneho čísla.',
            ],
            'uk' => [
                'invalid'       => 'Введіть правильний номер телефону.',
                'required'      => 'Номер телефону є обов’язковим.',
                'library_error' => 'Не вдалося завантажити валідатор номера телефону.',
            ],
            'ro' => [
                'invalid'       => 'Introduceți un număr de telefon valid.',
                'required'      => 'Numărul de telefon este obligatoriu.',
                'library_error' => 'Validatorul numărului de telefon nu a putut fi încărcat.',
            ],
            'et' => [
                'invalid'       => 'Sisestage kehtiv telefoninumber.',
                'required'      => 'Telefoninumber on kohustuslik.',
                'library_error' => 'Telefoninumbri valideerija laadimine ebaõnnestus.',
            ],
            'hu' => [
                'invalid'       => 'Adjon meg egy érvényes telefonszámot.',
                'required'      => 'A telefonszám megadása kötelező.',
                'library_error' => 'A telefonszám-ellenőrző betöltése sikertelen.',
            ],
            'fr' => [
                'invalid'       => 'Saisissez un numéro de téléphone valide.',
                'required'      => 'Le numéro de téléphone est obligatoire.',
                'library_error' => 'Impossible de charger le validateur de numéro de téléphone.',
            ],
            'es' => [
                'invalid'       => 'Introduce un número de teléfono válido.',
                'required'      => 'El número de teléfono es obligatorio.',
                'library_error' => 'No se ha podido cargar el validador del número de teléfono.',
            ],
        ];
    }

    private static function get_language(): string
    {
        $allowed = ['pl', 'en', 'cs', 'de', 'it', 'lt', 'lv', 'sk', 'uk', 'ro', 'et', 'hu', 'fr', 'es'];
        $locale = function_exists('determine_locale') ? determine_locale() : get_locale();
        $language = strtolower(substr(str_replace('_', '-', (string) $locale), 0, 2));

        return in_array($language, $allowed, true) ? $language : 'en';
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
            $messages = self::get_messages();
            $language = self::get_language();
            $current_messages = $messages[$language] ?? $messages['en'];

            $result['is_valid'] = false;
            $result['message'] = $current_messages['invalid'] ?? $messages['en']['invalid'];
        }

        return $result;
    }
}

PWE_Phone_Validator_Addon::init();