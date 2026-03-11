<?php
if (!defined('ABSPATH')) exit;

class Speakers {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
                'week' => plugin_dir_path(__FILE__) . 'presets/week/preset.php',
            ],
        ];
    }

    public static function render($group) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            // Get speakers from the database
            $data = PWE_Functions::get_database_fairs_data_speakers(); 
            if (!empty($data)) {

                foreach ($data as $row) {
                    if (!empty($row->data)) {
                        $decoded = json_decode($row->data, true);

                        if ($decoded) {
                            $speaker = [
                                'speakers_slug'         => $row->slug ?? '',
                                'speaker_img'           => !empty($decoded['prelegent_person_img'])
                                    ? 'https://cap.warsawexpo.eu/public/uploads/domains/' . str_replace('.', '-', $_SERVER['HTTP_HOST']) . '/prelegents/' . $row->slug . '/' . $decoded['prelegent_person_img']
                                    : '',
                                'speaker_company_img'   => !empty($decoded['prelegent_company_img'])
                                    ? 'https://cap.warsawexpo.eu/public/uploads/domains/' . str_replace('.', '-', $_SERVER['HTTP_HOST']) . '/prelegents/' . $row->slug . '/' . $decoded['prelegent_company_img']
                                    : '',
                                'speaker_company_name'  => PWE_Functions::lang_pl() ? ($decoded['prelegent_company_name_pl'] ?? '') : ($decoded['prelegent_company_name_en'] ?? ''),
                                'speaker_name'          => $decoded['prelegent_person_name'] ?? '',
                                'speaker_position'      => PWE_Functions::lang_pl() ? ($decoded['prelegent_person_position_pl'] ?? '') : ($decoded['prelegent_person_position_en'] ?? ''),
                                'speaker_text'          => PWE_Functions::lang_pl() ? ($decoded['prelegent_text_pl'] ?? '') : ($decoded['prelegent_text_en'] ?? ''),
                                'speakers_order'        => $row->order ?? ''
                            ];

                            $order = $speaker['speakers_order'];
                            if (!empty($order)) {
                                if ($order == 99) {
                                    // add to the end of the array
                                    $speakers_indexed[99][] = $speaker;
                                } else {
                                    // normal index by order
                                    $speakers_indexed[$order][] = $speaker;
                                }
                            }
                        }
                    }
                }
            }

            if (empty($speakers_indexed)) {
                echo '<style>.pwe-element-auto-switch.speakers {display:none;}</style>';
                return; 
            }

            // Sorted by speakers_order (without 99)
            ksort($speakers_indexed);

            // Build a score
            $speakers = [];

            // First, these are sorted out
            foreach ($speakers_indexed as $order => $items) {
                if ($order != 99) {
                    foreach ($items as $op) {
                        $speakers[] = $op;
                    }
                }
            }

            // At the end all with order = 99
            if (!empty($speakers_indexed[99])) {
                foreach ($speakers_indexed[99] as $op) {
                    $speakers[] = $op;
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
