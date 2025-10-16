<?php
if (!defined('ABSPATH')) exit;

class Halls {

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
           
            // Get current domain
            $current_domain = do_shortcode('[trade_fair_domainadress]');

            // Fair dates
            $trade_fair_start = do_shortcode('[trade_fair_datetotimer]');
            $trade_fair_end = do_shortcode('[trade_fair_enddata]');

            // Converting dates to timestamps
            $trade_fair_start_timestamp = strtotime($trade_fair_start);
            $trade_fair_end_timestamp = strtotime($trade_fair_end);

            // Get JSON
            $fairs_json = PWECommonFunctions::json_fairs();

            $fair_items_json = [];

            foreach ($fairs_json as $fair) {
                // Getting start and end dates
                $date_start = isset($fair['date_start']) ? strtotime($fair['date_start']) : null;
                $date_end = isset($fair['date_end']) ? strtotime($fair['date_end']) : null;

                // Checking if the date is in the range
                if ($date_start && $date_end) {
                    if (($date_start >= $trade_fair_start_timestamp && $date_start <= $trade_fair_end_timestamp) ||
                        ($date_end >= $trade_fair_start_timestamp && $date_end <= $trade_fair_end_timestamp)) {
                        $fair_items_json[] = [
                            "domain" => $fair["domain"],
                            "halls" => $fair["hall"],
                            "color" => $fair["color_accent"]
                        ];
                    }
                }
            }

            $all_halls = '';

            $json_data_all = [];
            $json_data_active = [];

            foreach ($fair_items_json as $item) {
                $halls = array_map('trim', explode(',', $item['halls']));
                foreach ($halls as $hall) {
                    if (strpos($item['domain'], "mr.glasstec.pl") === false) {
                        $json_data_all[] = [
                            "id" => $hall,
                            "domain" => $item['domain'],
                            "color" => $item['color']
                        ];
                    }
                }

                if ($item['domain'] === $current_domain) {
                    foreach ($halls as $hall) {
                        $json_data_active[] = [
                            "id" => $hall,
                            "color" => $item['color']
                        ];

                        // Adding halls to $all_halls without numbers
                        $clean_hall = preg_replace('/\d/', '', $hall);
                        if (!str_contains($all_halls, $clean_hall)) {
                            $all_halls .= $clean_hall . ', ';
                        }
                    }
                }
            }

            $all_halls = rtrim($all_halls, ', ');

            $halls_word = (count(array_filter(array_map('trim', explode(',', $all_halls)))) > 1)
                ? PWECommonFunctions::languageChecker('Hale', 'Halls')
                : PWECommonFunctions::languageChecker('Hala', 'Hall');


            // $current_day_timestamp = time();

            // $days_to_event = (($trade_fair_end_timestamp - $current_day_timestamp) / (60 * 60 * 24));
            // $days_after_event = ($current_day_timestamp - $trade_fair_end_timestamp) / (60 * 60 * 24);

            // $less_2_month_before = ($trade_fair_start_timestamp != false || !empty($trade_fair_start)) && $days_to_event > 0 && $days_to_event < 63;
            // $less_1_day_after = ($trade_fair_start_timestamp != false || !empty($trade_fair_end)) && $days_after_event > 0 && $days_after_event < 1;

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
