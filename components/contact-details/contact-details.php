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

            foreach ($pwe_groups_data as $group) {

                if ($current_domain != $group->fair_domain) {
                    continue;
                }

                $current_group = $group->fair_group;

                $is_b2c_special = (
                    ($current_group === "b2c" || $current_group === "b2c-new")
                    && $current_edition
                );

                foreach ($pwe_groups_contacts_data as $group_contact) {

                    $contact_group_name = $group_contact->groups_name;
                    $slug = $group_contact->groups_slug;
                    $data = json_decode($group_contact->groups_data);

                    /*
                    =================================================
                    MODE 1: B2C SPECIAL → GR1 ONLY
                    =================================================
                    */
                    
                    if ($is_b2c_special && $contact_group_name === "gr1") {

                        $data = json_decode($group_contact->groups_data);

                        if ($slug === "biuro-ob") {

                            $service_emails = array_map(
                                'trim',
                                preg_split('/[,;]+/', trim($data->email))
                            );

                            $service_phone = trim($data->tel);

                            $locked_service = true;
                        }

                        if ($slug === "ob-marketing-media") {

                            $marketing_emails = array_map(
                                'trim',
                                preg_split('/[,;]+/', trim($data->email))
                            );

                            $locked_marketing = true;
                        }

                        continue;
                    }

                    /*
                    =================================================
                    MODE 2: NORMAL → ALL
                    =================================================
                    */
                    
                    if ($group->fair_group === $contact_group_name) {

                        $data = json_decode($group_contact->groups_data);

                        if ($slug === "biuro-ob" && !$locked_service) {

                            $service_emails = array_map(
                                'trim',
                                preg_split('/[,;]+/', trim($data->email))
                            );

                            $service_phone = trim($data->tel);
                        }

                        if ($slug === "ob-marketing-media" && !$locked_marketing) {

                            $marketing_emails = array_map(
                                'trim',
                                preg_split('/[,;]+/', trim($data->email))
                            );
                        }

                        if ($slug === "ob-tech-wyst") {
                            $consultant_email = trim($data->email);
                        }

                        if ($slug === "osoba-kontakt") {
                            $contact_person_name  = trim($data->name);
                            $contact_person_email = trim($data->email);
                            $contact_person_phone = trim($data->tel);
                        }
                    }
                }
            }

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}