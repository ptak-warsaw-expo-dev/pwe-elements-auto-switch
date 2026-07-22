<?php
if (!defined('ABSPATH')) exit;

class Contact_Details {

    public static function get_data() {
        return [
            'types' => ['contact-details'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    /**
     * Clean scalar value.
     *
     * @param mixed $value
     * @return string
     */
    private static function pwe_clean_value($value) {
        if (is_array($value) || is_object($value)) {
            return '';
        }

        return trim((string) $value);
    }

    /**
     * Get manual option value from WordPress options table.
     * Manual value is treated as the highest-priority source.
     *
     * @param string $option_name
     * @return string
     */
    private static function pwe_option_value($option_name) {
        return self::pwe_clean_value(get_option($option_name, ''));
    }

    /**
     * Return manual value if it exists, otherwise return default value.
     *
     * Priority: manual field > CAP fallback > empty.
     *
     * @param mixed $manual_value
     * @param mixed $default_value
     * @return string
     */
    private static function pwe_first_not_empty($manual_value, $default_value = '') {
        $manual_value = self::pwe_clean_value($manual_value);

        if ($manual_value !== '') {
            return $manual_value;
        }

        return self::pwe_clean_value($default_value);
    }

    /**
     * Split one or many emails separated by comma or semicolon.
     *
     * @param mixed $value
     * @return array
     */
    private static function pwe_split_emails($value) {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        $value = self::pwe_clean_value($value);

        if ($value === '') {
            return [];
        }

        $emails = preg_split('/[,;]+/', $value);
        $emails = array_map('trim', $emails);
        $emails = array_filter($emails);

        return array_values($emails);
    }

    /**
     * Prepare phone number for tel: href.
     *
     * @param mixed $phone
     * @return string
     */
    private static function pwe_phone_href($phone) {
        return preg_replace('/[^0-9+]/', '', self::pwe_clean_value($phone));
    }

    /**
     * Read selected field from decoded CAP contact data.
     *
     * @param mixed  $data
     * @param string $field
     * @return string
     */
    private static function pwe_data_value($data, $field) {
        if (!is_object($data)) {
            return '';
        }

        switch ($field) {
            case 'name':
                return self::pwe_clean_value($data->name ?? '');

            case 'phone':
                return self::pwe_clean_value($data->tel ?? '');

            case 'email':
                return self::pwe_clean_value($data->email ?? '');

            default:
                return '';
        }
    }

    /**
     * Render mailto links from one email string or array of email strings.
     *
     * @param mixed $emails
     * @return string
     */
    private static function pwe_render_email_links($emails) {
        $output = '';

        foreach (self::pwe_split_emails($emails) as $email) {
            $email = sanitize_email($email);

            if (empty($email)) {
                continue;
            }

            $domain = '@warsawexpo.eu';

            if (substr($email, -strlen($domain)) === $domain) {
                $display = '<span>' . esc_html(str_replace($domain, '', $email)) . '</span><span>' . esc_html($domain) . '</span>';
            } else {
                $display = '<span>' . esc_html($email) . '</span>';
            }

            $output .= '
            <a href="' . esc_url('mailto:' . $email) . '">
                ' . $display . '
            </a>';
        }

        return $output;
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        $group = 'all';

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, 'components');
        // Global assets
        PWE_Functions::assets_per_element($element_slug, '', 'components');
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, '', 'components');

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
            /* <-------------> General code start <-------------> */

            $pwe_groups_data = PWECommonFunctions::get_database_groups_data();
            $pwe_groups_contacts_data = PWECommonFunctions::get_database_groups_contacts_data();

            $source_utm = (isset($_SERVER['argv'][0])) ? $_SERVER['argv'][0] : '';

            $current_domain = $_SERVER['HTTP_HOST'];
            
            $current_edition = trim(do_shortcode('[pwe_edition]')) === '1';

            $locked_service = false;
            $locked_marketing = false;

            $service_name = '';
            $service_emails = [];
            $service_phone = '';

            $marketing_media_name = '';
            $marketing_emails = [];
            $marketing_media_phone = '';

            $consultant_email = '';

            $contact_person_name = '';
            $contact_person_email = '';
            $contact_person_phone = '';

            $contact_person_name_2 = '';
            $contact_person_email_2 = '';
            $contact_person_phone_2 = '';

            $contact_person_name_3 = '';
            $contact_person_email_3 = '';
            $contact_person_phone_3 = '';


            if (is_iterable($pwe_groups_data) && is_iterable($pwe_groups_contacts_data)) {
                foreach ($pwe_groups_data as $group) {
                    if (empty($group->fair_domain) || $current_domain !== $group->fair_domain) {
                        continue;
                    }

                    $current_group = self::pwe_clean_value($group->fair_group ?? '');

                    $is_b2c_special = (
                        ($current_group === 'b2c' || $current_group === 'b2c-new')
                        && $current_edition
                    );

                    foreach ($pwe_groups_contacts_data as $group_contact) {
                        $contact_group_name = self::pwe_clean_value($group_contact->groups_name ?? '');
                        $slug = self::pwe_clean_value($group_contact->groups_slug ?? '');
                        $data = json_decode($group_contact->groups_data ?? '');

                        /*
                        =================================================
                        MODE 1: B2C SPECIAL → GR1 as CAP fallback
                        =================================================
                        */
                        if ($is_b2c_special && $contact_group_name === 'gr1') {
                            if ($slug === 'biuro-ob') {
                                $service_name = self::pwe_data_value($data, 'name');
                                $service_emails = self::pwe_split_emails(self::pwe_data_value($data, 'email'));
                                $service_phone = self::pwe_data_value($data, 'phone');

                                $locked_service = true;
                            }

                            if ($slug === 'ob-marketing-media') {
                                $marketing_media_name = self::pwe_data_value($data, 'name');
                                $marketing_emails = self::pwe_split_emails(self::pwe_data_value($data, 'email'));
                                $marketing_media_phone = self::pwe_data_value($data, 'phone');

                                $locked_marketing = true;
                            }

                            continue;
                        }

                        /*
                        =================================================
                        MODE 2: NORMAL → current group as CAP fallback
                        =================================================
                        */
                        if ($current_group !== $contact_group_name) {
                            continue;
                        }

                        if ($slug === 'biuro-ob' && !$locked_service) {
                            $service_name = self::pwe_data_value($data, 'name');
                            $service_emails = self::pwe_split_emails(self::pwe_data_value($data, 'email'));
                            $service_phone = self::pwe_data_value($data, 'phone');
                        }

                        if ($slug === 'ob-marketing-media' && !$locked_marketing) {
                            $marketing_media_name = self::pwe_data_value($data, 'name');
                            $marketing_emails = self::pwe_split_emails(self::pwe_data_value($data, 'email'));
                            $marketing_media_phone = self::pwe_data_value($data, 'phone');
                        }

                        if ($slug === 'ob-tech-wyst') {
                            $consultant_email = self::pwe_data_value($data, 'email');
                        }

                        if ($slug === 'osoba-kontakt') {
                            $contact_person_name = self::pwe_data_value($data, 'name');
                            $contact_person_email = self::pwe_data_value($data, 'email');
                            $contact_person_phone = self::pwe_data_value($data, 'phone');
                        }

                        if ($slug === 'osoba-kontakt-2') {
                            $contact_person_name_2 = self::pwe_data_value($data, 'name');
                            $contact_person_email_2 = self::pwe_data_value($data, 'email');
                            $contact_person_phone_2 = self::pwe_data_value($data, 'phone');
                        }

                        if ($slug === 'osoba-kontakt-3') {
                            $contact_person_name_3 = self::pwe_data_value($data, 'name');
                            $contact_person_email_3 = self::pwe_data_value($data, 'email');
                            $contact_person_phone_3 = self::pwe_data_value($data, 'phone');
                        }
                    }
                }
            }

            /*
            =================================================
            Manual fields from panel always have priority.
            Priority: manual field > CAP fallback > empty.
            =================================================
            */
            $service_name = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_service_name'),
                $service_name
            );

            $service_phone = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_service_phone'),
                $service_phone
            );

            $service_emails = self::pwe_split_emails(
                self::pwe_first_not_empty(
                    self::pwe_option_value('trade_fair_contact_service_email'),
                    implode(',', $service_emails)
                )
            );

            // -------------

            if (empty($service_emails)) {
                $service_emails = ['zgloszenia@warsawexpo.eu'];
            }

            // -------------

            $marketing_media_name = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_name'),
                $marketing_media_name
            );

            $marketing_media_phone = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_phone'),
                $marketing_media_phone
            );

            $marketing_emails = self::pwe_split_emails(
                self::pwe_first_not_empty(
                    self::pwe_option_value('trade_fair_contact_media'),
                    implode(',', $marketing_emails)
                )
            );

            // -------------

            $consultant_email = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_tech'),
                $consultant_email
            );

            // -------------

            $contact_person_name = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_name'),
                $contact_person_name
            );

            $contact_person_email = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_email'),
                $contact_person_email
            );

            $contact_person_phone = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_phone'),
                $contact_person_phone
            );

            // -------------

            $contact_person_name_2 = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_name_2'),
                $contact_person_name_2
            );

            $contact_person_email_2 = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_email_2'),
                $contact_person_email_2
            );

            $contact_person_phone_2 = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_phone_2'),
                $contact_person_phone_2
            );

            // -------------

            $contact_person_name_3 = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_name_3'),
                $contact_person_name_3
            );

            $contact_person_email_3 = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_email_3'),
                $contact_person_email_3
            );

            $contact_person_phone_3 = self::pwe_first_not_empty(
                self::pwe_option_value('trade_fair_contact_media_person_phone_3'),
                $contact_person_phone_3
            );

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}