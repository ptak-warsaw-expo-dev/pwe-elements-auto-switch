<?php
if (!defined('ABSPATH')) {
    exit;
}

class Confirmation_Exhibitors_Registration {

    private static $ajax_registered = false;
    private static $filters_registered = false;

    public static function init() {
        self::register_ajax_handlers();
        self::register_gravity_filters();
    }

    public static function get_data() {
        return [
            'types' => [
                'confirmation-exhibitors-registration'
            ],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php'
            ]
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'confirmation-exhibitors-registration';
        $group = 'all';

        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        PWE_Functions::assets_per_element($element_slug, $element_type);
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = $data['presets'][$group] ?? '';

        if (!$preset_file || !file_exists($preset_file)) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $form_id_step2 = PWE_Functions::get_gf_form_id('Zostań wystawcą (krok2)');
        $form_id_main  = PWE_Functions::get_gf_form_id('Zostań wystawcą');

        if (!$form_id_step2 && !$form_id_main) {
            return;
        }

        $is_exhibitor = !empty($_SESSION['pwe_exhibitor_entry']['entry_id']);
        $is_visitor   = !empty($_SESSION['pwe_reg_entry']['entry_id']);

        if ($is_exhibitor) {
            $form_id = $form_id_main;
        } elseif ($is_visitor) {
            $form_id = $form_id_step2;
        } else {
            $form_id = $form_id_step2 ?: $form_id_main;
        }

        $form_id = absint($form_id);

        $gravity_form = do_shortcode(
            '[gravityform id="' . $form_id . '" title="false" description="false" ajax="true"]'
        );

        $output = include $preset_file;

        if ($output) {
            echo do_shortcode($output);
        }
    }

    private static function register_gravity_filters() {
        if (self::$filters_registered) {
            return;
        }

        self::$filters_registered = true;

        add_filter('gform_pre_render', [__CLASS__, 'prepare_form']);
        add_filter('gform_pre_validation', [__CLASS__, 'prepare_form']);
        add_filter('gform_validation', [__CLASS__, 'fix_validation_and_inject'], 5);
        add_filter('gform_save_field_value', [__CLASS__, 'override_saved_field_value'], 10, 5);
        add_action('gform_pre_submission', [__CLASS__, 'inject_session_data']);

        add_action('gform_after_submission', [__CLASS__, 'clear_session_after_submission'], 20, 2);
        add_filter('gform_confirmation', [__CLASS__, 'clear_session_on_confirmation'], 10, 4);
    }

    private static function get_session_data() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $email = $_SESSION['pwe_exhibitor_entry']['email'] ?? $_SESSION['pwe_reg_entry']['email'] ?? '';
        $phone = $_SESSION['pwe_exhibitor_entry']['phone'] ?? $_SESSION['pwe_reg_entry']['phone'] ?? '';

        return [$email, $phone];
    }

    public static function prepare_form($form) {
        list($session_email, $session_phone) = self::get_session_data();

        if (empty($session_email) && empty($session_phone)) {
            return $form;
        }

        if (!isset($form['fields']) || !is_array($form['fields'])) {
            return $form;
        }

        $email_labels = ['email', 'mail', 'e-mail'];
        $phone_labels = ['phone', 'number', 'telefon', 'phone_number', 'mobile'];

        foreach ($form['fields'] as &$field) {
            if (!is_object($field)) {
                continue;
            }

            $admin_label = strtolower((string)($field->adminLabel ?? ''));

            $is_phone_field = in_array($admin_label, $phone_labels, true) && !empty($session_phone);
            $is_email_field = in_array($admin_label, $email_labels, true) && !empty($session_email);

            if ($is_phone_field || $is_email_field) {
                $field->defaultValue = $is_phone_field ? $session_phone : $session_email;
                $field->isRequired = false;
                $field->visibility = 'hidden';
                $field->cssClass  .= ' gform_hidden';
            }
        }

        return $form;
    }

    public static function fix_validation_and_inject($validation_result) {
        $form = $validation_result['form'];
        list($email, $phone) = self::get_session_data();

        $phone_labels = ['phone', 'number', 'telefon', 'phone_number', 'mobile'];
        $email_labels = ['email', 'mail', 'e-mail'];

        foreach ($form['fields'] as &$field) {
            $admin_label = strtolower((string)($field->adminLabel ?? ''));

            if (in_array($admin_label, $phone_labels, true) && !empty($phone)) {
                $field->isRequired = false;
                $field->failed_validation = false;
                $field->validation_message = '';
                $_POST['input_' . $field->id] = $phone;
                $_POST['input_' . $field->id . '_1'] = $phone;
            }

            if (in_array($admin_label, $email_labels, true) && !empty($email)) {
                $field->isRequired = false;
                $field->failed_validation = false;
                $field->validation_message = '';
                $_POST['input_' . $field->id] = $email;
            }
        }

        $validation_result['form'] = $form;

        return $validation_result;
    }

    public static function inject_session_data($form) {
        list($email, $phone) = self::get_session_data();

        $phone_labels = ['phone', 'number', 'telefon', 'phone_number', 'mobile'];
        $email_labels = ['email', 'mail', 'e-mail'];

        foreach ($form['fields'] as $field) {
            $admin_label = strtolower((string)($field->adminLabel ?? ''));

            if (in_array($admin_label, $email_labels, true) && !empty($email)) {
                $_POST['input_' . $field->id] = $email;
            }

            if (in_array($admin_label, $phone_labels, true) && !empty($phone)) {
                $_POST['input_' . $field->id] = $phone;
                $_POST['input_' . $field->id . '_1'] = $phone;
            }
        }
    }

    public static function override_saved_field_value($value, $entry, $field, $form, $input_id) {
        list($email, $phone) = self::get_session_data();

        $admin_label = strtolower((string)($field->adminLabel ?? ''));
        $phone_labels = ['phone', 'number', 'telefon', 'phone_number', 'mobile'];
        $email_labels = ['email', 'mail', 'e-mail'];

        if (in_array($admin_label, $phone_labels, true) && !empty($phone)) {
            return $phone;
        }

        if (in_array($admin_label, $email_labels, true) && !empty($email)) {
            return $email;
        }

        return $value;
    }

    private static function register_ajax_handlers() {
        if (self::$ajax_registered) {
            return;
        }

        self::$ajax_registered = true;

        add_action('wp_ajax_update_exhibitor_data', [__CLASS__, 'update_exhibitor_data']);
        add_action('wp_ajax_nopriv_update_exhibitor_data', [__CLASS__, 'update_exhibitor_data']);

        add_action('wp_ajax_clear_pwe_session', [__CLASS__, 'ajax_clear_session']);
        add_action('wp_ajax_nopriv_clear_pwe_session', [__CLASS__, 'ajax_clear_session']);
    }

    public static function ajax_clear_session() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['pwe_exhibitor_entry']);
        unset($_SESSION['pwe_reg_entry']);

        session_write_close();

        wp_send_json_success(['message' => 'Sesja została wyczyszczona']);
    }

    public static function update_exhibitor_data() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['pwe_exhibitor_entry']['entry_id'])) {
            wp_send_json_error(['message' => 'Brak sesji wystawcy']);
        }

        $entry_id = absint($_SESSION['pwe_exhibitor_entry']['entry_id']);
        $form_id  = PWE_Functions::get_gf_form_id('Zostań wystawcą');

        if (!$form_id) {
            wp_send_json_error(['message' => 'Brak formularza']);
        }

        if (!class_exists('GFAPI')) {
            wp_send_json_error(['message' => 'Brak wtyczki Gravity Forms']);
        }

        $form  = GFAPI::get_form($form_id);
        $entry = GFAPI::get_entry($entry_id);

        if (is_wp_error($entry)) {
            wp_send_json_error(['message' => 'Brak wpisu']);
        }

        if (isset($form['fields']) && is_array($form['fields'])) {
            foreach ($form['fields'] as $field) {
                if (!is_object($field)) {
                    continue;
                }

                switch ($field->adminLabel) {
                    case 'name':
                    case 'nip':
                    case 'company':
                    case 'area':
                        $key = $field->adminLabel;
                        $entry[$field->id] = sanitize_text_field($_POST[$key] ?? '');
                        break;
                }
            }
        }

        $updated = GFAPI::update_entry($entry);

        if (is_wp_error($updated)) {
            wp_send_json_error([
                'message' => 'Błąd zapisu'
            ]);
        }

        $entry = GFAPI::get_entry($entry_id);

        $user_lang = strtolower(substr(get_locale(), 0, 2));
        $notification_name = 'Admin Notification Potwierdzenie - ' . strtoupper($user_lang);

        if (!empty($form['notifications'])) {
            foreach ($form['notifications'] as &$notification) {
                $notification['isActive'] = ($notification['name'] === $notification_name);
            }
            unset($notification);

            GFAPI::send_notifications($form, $entry);
        }

        wp_remote_post(
            home_url('/wp-content/plugins/custom-element/action_handler.php'),
            [
                'body' => [
                    'element'  => 'gform_after_submission',
                    'entry_id' => $entry_id,
                    'url'      => null,
                ],
                'timeout'  => 0.01,
                'blocking' => false,
            ]
        );

        unset($_SESSION['pwe_exhibitor_entry']);
        unset($_SESSION['pwe_reg_entry']);
        session_write_close();

        wp_send_json_success([
            'message' => 'Dane zaktualizowane'
        ]);
    }

    public static function clear_session_after_submission($entry, $form) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['pwe_exhibitor_entry']);
        unset($_SESSION['pwe_reg_entry']);

        session_write_close();
    }

    public static function clear_session_on_confirmation($confirmation, $form, $entry, $ajax) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['pwe_exhibitor_entry']);
        unset($_SESSION['pwe_reg_entry']);

        session_write_close();

        return $confirmation;
    }
}

Confirmation_Exhibitors_Registration::init();