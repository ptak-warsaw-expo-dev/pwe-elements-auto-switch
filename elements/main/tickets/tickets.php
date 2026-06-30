<?php
if (!defined('ABSPATH')) exit;

class Tickets {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'b2c-new' => plugin_dir_path(__FILE__) . 'presets/b2c-new/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        PWE_Functions::assets_per_element($element_slug, $element_type);
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            $lang = PWE_Functions::lang();
            $db_data = PWE_Functions::get_database_fairs_data_tickets();

            $tiers = [];

            if (!empty($db_data)) {
                foreach ($db_data as $row) {
                    if (!empty($row->data)) {
                        $decoded = json_decode($row->data, true);

                        if (!empty($decoded['tickets'])) {
                            foreach ($decoded['tickets'] as $ticket) {

                                $name = !empty($ticket['name'][$lang]) ? $ticket['name'][$lang] : ($ticket['name']['en'] ?? '');
                                $desc = !empty($ticket['short_desc'][$lang]) ? $ticket['short_desc'][$lang] : ($ticket['short_desc']['en'] ?? '');
                                $button_text = !empty($ticket['button_text'][$lang]) ? $ticket['button_text'][$lang] : ($ticket['button_text']['en'] ?? '');
                                $button_url = !empty($ticket['button_link'][$lang]) ? $ticket['button_link'][$lang] : ($ticket['button_link']['en'] ?? '#');
                                $features = !empty($ticket['points'][$lang]) ? $ticket['points'][$lang] : ($ticket['points']['en'] ?? []);

                                $tiers[] = [
                                    'name'        => $name,
                                    'price'       => $ticket['price'] ?? null,
                                    'sale_price'  => $ticket['sale_price'] ?? null,
                                    'currency'    => $ticket['currency'] ?? 'PLN',
                                    'desc'        => $desc,
                                    'features'    => $features,
                                    'popular'     => !empty($ticket['highlighted']) ? (bool)$ticket['highlighted'] : false,
                                    'button_text' => $button_text,
                                    'button_url'  => $button_url,
                                    'order'       => $ticket['order'] ?? 99
                                ];
                            }
                        }
                    }
                }
            }

            usort($tiers, function ($a, $b) {
                return ($a['order'] ?? 0) <=> ($b['order'] ?? 0);
            });

            if (empty($tiers)) {
                echo '<style>.tickets-section {display:none;}</style>';
                return;
            }

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}