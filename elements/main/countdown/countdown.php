<?php
if (!defined('ABSPATH')) exit;

class Countdown {

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

            // Get current domain
            $current_domain = do_shortcode('[trade_fair_domainadress]');

            // Getting shortcode values
            $trade_fair_start_date = do_shortcode('[trade_fair_datetotimer]'); // ex. "2027/01/14 10:00"
            $trade_fair_end_date = do_shortcode('[trade_fair_enddata]'); // ex. "2027/01/16 17:00"

            // Convert to timestamp
            $trade_fair_start_date_timestamp = strtotime($trade_fair_start_date);
            $trade_fair_end_date_timestamp = strtotime($trade_fair_end_date);
            $current_timestamp = time();

            // Get the day of the week
            $trade_fair_start_date_week = date('l', $trade_fair_start_date_timestamp);
            $trade_fair_end_date_week = date('l', $trade_fair_end_date_timestamp);
            // Get time
            $trade_fair_start_date_hour = date('H:i', $trade_fair_start_date_timestamp);
            $trade_fair_end_date_hour = date('H:i', $trade_fair_end_date_timestamp);

            // Changing English names of days to Polish ones
            if(get_locale() == 'pl_PL') {
                $days = [
                    'Monday' => 'Poniedziałek',
                    'Tuesday' => 'Wtorek',
                    'Wednesday' => 'Środa',
                    'Thursday' => 'Czwartek',
                    'Friday' => 'Piątek',
                    'Saturday' => 'Sobota',
                    'Sunday' => 'Niedziela'
                ];

                $trade_fair_start_date_week = $days[$trade_fair_start_date_week] ?? $trade_fair_start_date_week;
                $trade_fair_end_date_week = $days[$trade_fair_end_date_week] ?? $trade_fair_end_date_week;
            }

            // Get JSON
            $fairs_json = PWE_Functions::json_fairs();

            $fair_items_json = [];

            foreach ($fairs_json as $fair) {
                // Getting start and end dates
                $date_start = isset($fair['date_start']) ? strtotime($fair['date_start']) : null;
                $date_end = isset($fair['date_end']) ? strtotime($fair['date_end']) : null;

                // Checking if the date is in the range
                if ($date_start && $date_end) {
                    if (($date_start >= $trade_fair_start_date_timestamp && $date_start <= $trade_fair_end_date_timestamp) ||
                        ($date_end >= $trade_fair_start_date_timestamp && $date_end <= $trade_fair_end_date_timestamp)) {
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
                    $json_data_all[] = [
                        "id" => $hall,
                        "domain" => $item['domain']
                    ];
                }

                if ($item['domain'] === $current_domain) {
                    foreach ($halls as $hall) {
                        $json_data_active[] = [
                            "id" => $hall
                        ];

                        // Adding halls to $all_halls without numbers
                        $clean_hall = preg_replace('/\d/', '', $hall);
                        if (!str_contains($all_halls, $clean_hall)) {
                            $all_halls .= $clean_hall . ', ';
                        }
                    }
                }
            }

            // Convert to string
            $all_halls = rtrim($all_halls, ', ');

            // Using the plural or singular form of a word
            $halls_word = (count(array_filter(array_map('trim', explode(',', $all_halls)))) > 1)
                ? PWE_Functions::languageChecker('Hale', 'Halls')
                : PWE_Functions::languageChecker('Hala', 'Hall');


            $all_entries = '';

            // Map assigning halls to their entrances
            $hall_entries = [
                'A' => ['A8'],
                'B' => ['B8', 'B16'],
                'C' => ['C8', 'C16'],
                'D' => ['D8', 'D16'],
                'E' => ['E1', 'E6'],
                'F' => ['F1', 'F7'],
                'A1' => ['A8'], 'A2' => ['A8'],
                'B1' => ['B8'], 'B2' => ['B8'], 'B3' => ['B16'], 'B4' => ['B16'],
                'C1' => ['C8'], 'C2' => ['C8'], 'C3' => ['C16'], 'C4' => ['C16'],
                'D1' => ['D8'], 'D2' => ['D8'], 'D3' => ['D16'], 'D4' => ['D16'],
                'E1' => ['E1'], 'E2' => ['E1'], 'E3' => ['E6'], 'E4' => ['E6'],
                'F1' => ['F7'], 'F2' => ['F7'], 'F3' => ['F1'], 'F4' => ['F1']
            ];

            $matching_entries = [];

            // Iterate through active halls
            foreach ($json_data_active as $item) {
                $hall_id = $item['id'];

                // Check if the hall has an assigned entrance
                if (isset($hall_entries[$hall_id])) {
                    // Adding input to the output list
                    foreach ($hall_entries[$hall_id] as $entry) {
                        $matching_entries[] = $entry;
                    }
                }
            }

            // Remove duplicates and convert to string
            $all_entries = implode(', ', array_unique($matching_entries));

            // Using the plural or singular form of a word
            $entries_word = (count(array_filter(array_map('trim', explode(',', $all_entries)))) > 1)
                ? PWE_Functions::languageChecker('Wejścia', 'Entrances')
                : PWE_Functions::languageChecker('Wejście', 'Entrance');

            $diff_timestamp = ($trade_fair_start_date_timestamp - $current_timestamp);
            $time_to_end_timestamp = ($trade_fair_end_date_timestamp - $current_timestamp);

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
