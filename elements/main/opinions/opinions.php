<?php
if (!defined('ABSPATH')) exit;

class Opinions {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/preset-gr1/preset-gr1.php',
                // 'gr2' => plugin_dir_path(__FILE__) . 'presets/preset-gr2/preset-gr2.php',
            ],
        ];
    }

    public static function render($group) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
            /* <-------------> General code start <-------------> */
            
            $edition = do_shortcode('[trade_fair_edition]');

            // Loading JSON with default opinions
            $opinions_file = ABSPATH . 'doc/pwe-opinions.json';
            $opinions_data = json_decode(file_get_contents($opinions_file), true);

            $default_opinions = $opinions_data['default'] ?? [];

            if (strpos(strtolower($edition), "premier") !== false) {
                $default_opinions = array_merge($default_opinions, $opinions_data['premiere'] ?? []);
            } else {
                $default_opinions = array_merge($default_opinions, $opinions_data['no_premiere'] ?? []);
            }

            // Index the default opinions_order
            $opinions_indexed = [];
            foreach ($default_opinions as $opinion) {
                $order = $opinion['opinions_order'] ?? null;
                if ($order) {
                    $opinions_indexed[$order] = $opinion;
                }
            }

            // Get opinions from the database
            $data = PWECommonFunctions::get_database_fairs_data_opinions(); 
            if (!empty($data)) {
                // If there are 2 opinions in the summary – overwrite
                if (count($data) >= 2) {
                    $opinions_indexed = []; 
                }

                foreach ($data as $row) {
                    if (!empty($row->data)) {
                        $decoded = json_decode($row->data, true);

                        if ($decoded) {
                            $opinion = [
                                'opinions_slug'              => $row->slug ?? '',
                                'opinion_person_img'         => !empty($decoded['opinion_person_img'])
                                    ? 'https://cap.warsawexpo.eu/public/uploads/domains/' . str_replace('.', '-', $_SERVER['HTTP_HOST']) . '/opinions/' . $row->slug . '/' . $decoded['opinion_person_img']
                                    : '',
                                'opinion_company_img'        => !empty($decoded['opinion_company_img'])
                                    ? 'https://cap.warsawexpo.eu/public/uploads/domains/' . str_replace('.', '-', $_SERVER['HTTP_HOST']) . '/opinions/' . $row->slug . '/' . $decoded['opinion_company_img']
                                    : '',
                                'opinion_company_name_pl'    => $decoded['opinion_company_name_pl'] ?? '',
                                'opinion_company_name_en'    => $decoded['opinion_company_name_en'] ?? '',
                                'opinion_person_name'        => $decoded['opinion_person_name'] ?? '',
                                'opinion_person_position_pl' => $decoded['opinion_person_position_pl'] ?? '',
                                'opinion_person_position_en' => $decoded['opinion_person_position_en'] ?? '',
                                'opinion_text_pl'            => $decoded['opinion_text_pl'] ?? '',
                                'opinion_text_en'            => $decoded['opinion_text_en'] ?? '',
                                'opinions_order'             => $row->order ?? ''
                            ];

                            $order = $opinion['opinions_order'];
                            if (!empty($order)) {
                                if ($order == 99) {
                                    // dodaj na koniec tablicy (nie indeksowane po order)
                                    $opinions_indexed[] = $opinion;
                                } else {
                                    // normalnie indeksujemy po order
                                    $opinions_indexed[$order] = $opinion;
                                }
                            }
                        }
                    }
                }
            }

            // Final list for rendering – sorted by opinions_order (bez tych 99)
            ksort($opinions_indexed);

            // Build a score
            $opinions_to_render = [];

            // First, these are sorted out
            foreach ($opinions_indexed as $k => $op) {
                if (isset($op['opinions_order']) && $op['opinions_order'] != 99) {
                    $opinions_to_render[] = $op;
                }
            }

            // At the end all with order = 99
            foreach ($opinions_indexed as $k => $op) {
                if (isset($op['opinions_order']) && $op['opinions_order'] == 99) {
                    $opinions_to_render[] = $op;
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
