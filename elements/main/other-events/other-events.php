<?php
if (!defined('ABSPATH')) exit;

class Other_Events {

    public static function get_data() {
        return [
            'types' => ['main'],
            'order' => [
                'gr1' => 999,
                'gr2' => 999,
            ],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/preset-gr1/preset-gr1.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/preset-gr2/preset-gr2.php',
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

            $fairs_json = PWECommonFunctions::json_fairs();

            // Check if there is any data entered into the element
            if (!empty($fairs_json)) {
                $other_events_items_json = [];

                foreach ($fairs_json as $fair) {
                    // Getting start and end dates
                    $date_start = isset($fair['date_start']) ? strtotime($fair['date_start']) : null;
                    $date_end = isset($fair['date_end']) ? strtotime($fair['date_end']) : null;

                    // Checking if the date is in the range
                    if ($date_start && $date_end) {
                        if ((($date_start >= $trade_fair_start_timestamp && $date_start <= $trade_fair_end_timestamp) ||
                            ($date_end >= $trade_fair_start_timestamp && $date_end <= $trade_fair_end_timestamp)) &&
                            strpos($fair['domain'], $current_domain) === false && (strpos($fair['domain'], "fasttextile.com") === false && strpos($fair['domain'], "expotrends.eu") === false && strpos($fair['domain'], "fabrics-expo.eu") === false)) {
                            $other_events_items_json[] = [
                                "other_events_domain" => $fair["domain"],
                                "other_events_text" => PWECommonFunctions::languageChecker($fair["desc_pl"], $fair["desc_en"])
                            ];
                        }
                    }
                }
            }

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output && count($other_events_items_json) > 0) {
                echo $output;         
            }
        }
    }
}
