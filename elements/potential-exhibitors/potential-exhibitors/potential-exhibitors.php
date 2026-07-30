<?php
if (!defined('ABSPATH')) exit;

class Potential_Exhibitors {

    public static function get_data() {
        return [
            'types' => ['potential-exhibitors'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'potential-exhibitors';

        $group = 'all';

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

            $form_id = PWE_Functions::get_gf_form_id('Potencjalny wystawca - aktywacja');

            if (!$form_id) {
                return;
            }

            // Processing edition shortcode
            $trade_fair_edition = do_shortcode('[trade_fair_edition]');

            if ($trade_fair_edition === 1) {
                $trade_fair_edition = ($lang === 'pl') ? 'Premierowa Edycja' : 'Premier Edition';
            } else {
                $trade_fair_edition = $trade_fair_edition . ($lang === 'pl' ? ' edycja' : ' edition');
            }

            $trade_fair_date = do_shortcode('[trade_fair_date_custom_format]');
           
            if (class_exists('GFAPI')) {
                $all_forms = GFAPI::get_forms();

                foreach($all_forms as $single_form) {
                    if (stripos($single_form['title'], 'potencjalny wystawca') !== false) {
                        foreach($single_form['fields'] as $single_field){

                            $label = strtolower($single_field['label']);

                            switch (true) {

                                case (stripos($label, 'nazwisk') !== false || stripos($label, 'imie') !== false || stripos($label, 'imię') !== false || stripos($label, 'imiĘ') !== false || stripos($label, 'name') !== false) && stripos($label, 'id') === false:
                                    $input_name = $label;
                                    continue 2;

                                case stripos($label, 'mail') !== false && stripos($label, 'id') === false:
                                    $input_email = $label;
                                    continue 2;

                                case (stripos($label, 'tel') !== false || stripos($label, 'phone') !== false) && stripos($label, 'id') === false:
                                    $input_phone = $label;
                                    continue 2;

                                case stripos($label, 'firma') !== false || stripos($label, 'company') !== false:
                                    $input_company = $label;
                                    continue 2;

                                case stripos($label, 'kanał') !== false || stripos($label, 'kanal') !== false:
                                    $input_channel = $label;
                                    continue 2;

                                case stripos($label, 'badge') !== false:
                                    $input_badge = $label;
                                    continue 2;

                                case stripos($label, 'id') !== false && stripos($label, 'name') === false && stripos($label, 'mail') === false && stripos($label, 'phone') === false:
                                    $input_id = $label;
                                    continue 2;

                                case stripos($label, 'idname') !== false:
                                    $input_idname = $label;
                                    continue 2;

                                case stripos($label, 'idemail') !== false:
                                    $input_idemail = $label;
                                    continue 2;

                                case stripos($label, 'idphone') !== false:
                                    $input_idphone = $label;
                                    continue 2;
                            }
                        }
                    }
                }
            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }
}
