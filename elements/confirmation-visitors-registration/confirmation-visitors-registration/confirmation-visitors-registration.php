<?php
if (!defined('ABSPATH')) exit;

class Confirmation_Visitors_Registration {

    private static $filters_registered = false;
    private static $ajax_registered = false;

    public static function init() {
        self::register_ajax_handlers();
    }

    public static function get_data() {
        return [
            'types' => ['confirmation-visitors-registration'],
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
        $element_slug = 'confirmation-visitors-registration';

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $source_utm = isset($_GET['utm_source'])
            ? sanitize_key(wp_unslash($_GET['utm_source']))
            : sanitize_key(
                $_SESSION['pwe_reg_entry']['utm_source']
                ?? $_SESSION['pwe_registration_utm_source']
                ?? ''
            );

        if ($source_utm === 'premium') {
            $group = 'premium';
        } elseif ($source_utm === 'byli') {
            $group = 'byli';
        } elseif ($source_utm === 'platyna') {
            $group = 'platyna';
        } else {
            $group = 'standard';
        }

        self::register_gravity_forms_filters();

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = $data['presets'][$group] ?? null;

        if ($preset_file && file_exists($preset_file)) {
            /* <-------------> General code start <-------------> */
            $lang = PWE_Functions::lang();
            $form_id = PWE_Functions::get_gf_form_id('Rejestracja');

            if (!$form_id) {
                return;
            }

            self::add_apartment_field($form_id);

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $registration_session = $_SESSION['pwe_reg_entry'] ?? [];

            if (
                empty($_SESSION['pwe_reg_entry']['entry_id']) &&
                ($reg_form_update_entries === "true") &&
                (!is_user_logged_in() || !current_user_can('administrator'))
            ) {
                header("Location: /rejestracja");
                exit();
            }

            $entry_id = $registration_session['entry_id'] ?? '';
            $fair_group = do_shortcode('[trade_fair_group]');
            $trade_fair_edition = do_shortcode('[trade_fair_edition]');

            if ($trade_fair_edition === 1) {
                $trade_fair_edition = ($lang === 'pl') ? 'Premierowa Edycja' : 'Premier Edition';
            } else {
                $trade_fair_edition = $trade_fair_edition . ($lang === 'pl' ? ' edycja' : ' edition');
            }

            $trade_fair_date = do_shortcode('[trade_fair_date_custom_format]');
            $start_date = do_shortcode('[trade_fair_datetotimer]');
            $end_date = do_shortcode('[trade_fair_enddata]');

            if (!empty($end_date)) {
                $start_date_obj = DateTime::createFromFormat('Y/m/d H:i', $end_date);

                if ($start_date_obj) {
                    $now = new DateTime();
                    $block_start = clone $start_date_obj;
                    $block_start->modify('-3 weeks');

                    if ($now >= $block_start && $now < $start_date_obj) {
                        wp_safe_redirect(home_url('/'));
                        exit();
                    }
                }
            }

            $gravity_form = do_shortcode('[gravityform id="' . $form_id . '" title="false" description="false" ajax="false"]');

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;
            if ($output) {
                echo do_shortcode($output);
            }
        }
    }

    private static function register_gravity_forms_filters() {
        if (self::$filters_registered) {
            return;
        }

        self::$filters_registered = true;

        add_filter('gform_pre_render', [__CLASS__, 'prepare_registration_form']);
        add_filter('gform_pre_validation', [__CLASS__, 'prepare_registration_form']);
    }

    public static function prepare_registration_form($form) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['pwe_reg_entry'])) {
            return $form;
        }

        foreach (($form['fields'] ?? []) as $field) {
            if (!is_object($field)) {
                continue;
            }

            if (in_array((string)($field->adminLabel ?? ''), ['email', 'phone'], true)) {
                $field->visibility = 'hidden';
            }
        }

        return $form;
    }

    private static function register_ajax_handlers() {
        if (self::$ajax_registered) {
            return;
        }

        self::$ajax_registered = true;

        add_action('wp_ajax_update_registration_address', [__CLASS__, 'update_registration_address']);
        add_action('wp_ajax_nopriv_update_registration_address', [__CLASS__, 'update_registration_address']);
    }

    public static function update_registration_address() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['pwe_reg_entry']['entry_id'])) {
            wp_send_json_error(['message' => 'Brak rejestracji w sesji']);
        }

        $entry_id = absint($_SESSION['pwe_reg_entry']['entry_id']);
        $form_id = PWE_Functions::get_gf_form_id('Rejestracja');

        if (!$form_id) {
            wp_send_json_error(['message' => 'Brak formularza w systemie']);
        }

        if (!class_exists('GFAPI')) {
            wp_send_json_error(['message' => 'Gravity Forms jest wyłączone']);
        }

        $form = GFAPI::get_form($form_id);
        if (!$form || is_wp_error($form)) {
            wp_send_json_error(['message' => 'Nie znaleziono konfiguracji formularza']);
        }

        $entry = GFAPI::get_entry($entry_id);
        if (is_wp_error($entry)) {
            wp_send_json_error(['message' => 'Nie znaleziono wpisu rejestracji']);
        }

        foreach ($form['fields'] as $field) {
            if (!is_object($field)) {
                continue;
            }

            $admin_label = (string)($field->adminLabel ?? '');

            switch ($admin_label) {
                case 'name':
                    $entry[$field->id] = sanitize_text_field($_POST['name'] ?? '');
                    break;
                case 'street':
                    $entry[$field->id] = sanitize_text_field($_POST['street'] ?? '');
                    break;
                case 'house':
                    $entry[$field->id] = sanitize_text_field($_POST['house'] ?? '');
                    break;
                case 'apartment':
                case 'local':
                    $entry[$field->id] = sanitize_text_field($_POST['apartment'] ?? '');
                    break;
                case 'post':
                    $entry[$field->id] = sanitize_text_field($_POST['post'] ?? '');
                    break;
                case 'city':
                    $entry[$field->id] = sanitize_text_field($_POST['city'] ?? '');
                    break;
            }
        }

        $updated = GFAPI::update_entry($entry);

        if (is_wp_error($updated)) {
            wp_send_json_error(['message' => 'Błąd podczas zapisu danych']);
        }

        if (!empty($entry_id)) {
            include_once ABSPATH . 'wp-content/plugins/custom-element/gf_integration/gf_integration.php';

            if (class_exists('GF_Integration')) {
                $integration = new GF_Integration($entry_id, '');
                $integration->init();
            } else {
                error_log('Brak klasy GF_Integration podczas aktualizacji adresu');
            }

            $fair_date = do_shortcode('[trade_fair_datetotimer]');
            if (!empty($fair_date)) {
                try {
                    $custom_date = new DateTime($fair_date);
                    $current_date = new DateTime(current_time('mysql'));
                    $current_date->modify('+60 days');

                    if ($current_date > $custom_date) {
                        include_once ABSPATH . 'wp-content/plugins/custom-element/pwe-cdb/activation_db.php';

                        if (class_exists('Activation_DB')) {
                            $cdb_integration = new Activation_DB([$entry_id]);
                            $cdb_integration();
                        }
                    }
                } catch (Exception $e) {
                    error_log('Błąd daty w update_registration_address: ' . $e->getMessage());
                }
            }
        }

        unset(
            $_SESSION['pwe_reg_entry'],
            $_SESSION['pwe_registration_utm_source']
        );

        wp_send_json_success([
            'message' => 'Dane zaktualizowane'
        ]);
    }

    private static function add_apartment_field($form_id) {
        if (!class_exists('GFAPI') || !class_exists('GF_Field_Text')) {
            return;
        }

        $option_key = 'pwe_gf_apartment_added_' . absint($form_id);

        if (get_option($option_key)) {
            return;
        }

        $form = GFAPI::get_form($form_id);

        if (!$form || is_wp_error($form)) {
            return;
        }

        foreach (($form['fields'] ?? []) as $field) {
            if (is_object($field) && in_array((string)($field->adminLabel ?? ''), ['apartment', 'local'], true)) {
                update_option($option_key, 1, false);
                return;
            }
        }

        $field_ids = array_map('intval', wp_list_pluck($form['fields'] ?? [], 'id'));
        $next_id = $field_ids ? max($field_ids) + 1 : 1;

        $new_field = new GF_Field_Text([
            'id'         => $next_id,
            'label'      => PWE_Functions::lang() === 'pl' ? 'Numer lokalu' : 'Apartment number',
            'adminLabel' => 'apartment',
            'visibility' => 'hidden',
            'inputName'  => 'numer_lokalu',
            'isRequired' => false,
            'cssClass'   => 'pwe-field-apartment',
        ]);

        $insert_at = null;

        foreach (($form['fields'] ?? []) as $index => $field) {
            if (is_object($field) && (string)($field->adminLabel ?? '') === 'house') {
                $insert_at = $index + 1;
                break;
            }
        }

        if ($insert_at === null) {
            $form['fields'][] = $new_field;
        } else {
            array_splice($form['fields'], $insert_at, 0, [$new_field]);
        }

        $result = GFAPI::update_form($form);

        if (!is_wp_error($result)) {
            update_option($option_key, 1, false);
        }
    }
}