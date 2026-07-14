<?php
if (!defined('ABSPATH')) exit;

class Menu {

    public static function get_data() {
        return [
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {
        $data = self::get_data();
        $element_type = $data['types'][0] ?? '';
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        $group = 'all';

        $atts = [
            'menu_transparent'       => !empty(get_option('pwe_menu_options', [])['pwe_menu_transparent']) ? "true" : "false",
            'trade_fair_datetotimer' => do_shortcode('[trade_fair_datetotimer]'),
            'trade_fair_enddata'     => do_shortcode('[trade_fair_enddata]'),
        ];

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type = '', $folder = 'components');
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type = '', $folder = 'components', $atts);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
            /* <-------------> General code start <-------------> */

            $lang = PWE_Functions::lang();
            $current_lang = ($lang === 'pl') ? 'pl' : 'en';

            $cap_files = PWE_Functions::get_database_fairs_data_files();
            $fair_current_year = do_shortcode('[trade_fair_catalog_year]');

            $active_current_fair_plan = null;
            $active_current_post_show = null;
            $active_current_fair_offer = null;

            $all_fair_plans = [];
            $all_post_shows = [];

            $latest_post_show_year = 0;
            $latest_fair_offer_year = 0;


            /**
             * ALL TRADE FAIR PLANS
             */
            foreach ($cap_files as $item) {

                if (
                    $item->category_slug === 'trade-fair-plan' &&
                    $item->is_active == '1'
                ) {

                    $all_fair_plans[] = $item;

                    // aktualny rok do głównego menu
                    if ($item->year == $fair_current_year) {
                        $active_current_fair_plan = $item;
                    }
                }
            }


            /**
             * ALL POST SHOW REPORTS + CURRENT
             */
            foreach ($cap_files as $item) {

                if (
                    $item->category_slug === 'post-show-report' &&
                    $item->is_active == '1' &&
                    $item->language === $current_lang
                ) {

                    $all_post_shows[] = $item;

                    $year = (int)$item->year;

                    if ($year > $latest_post_show_year) {
                        $latest_post_show_year = $year;
                        $active_current_post_show = $item;
                    }
                }
            }


            /**
             * TRADE FAIR OFFER – the biggest year
             */
            foreach ($cap_files as $item) {

                if (
                    $item->category_slug === 'trade-fair-offer' &&
                    $item->is_active == '1' &&
                    $item->language === $current_lang
                ) {

                    $year = (int)$item->year;

                    if ($year > $latest_fair_offer_year) {
                        $latest_fair_offer_year = $year;
                        $active_current_fair_offer = $item;
                    }
                }
            }


            // sortowanie od najnowszych edycji
            usort($all_fair_plans, function($a, $b) {
                return (int)$b->year <=> (int)$a->year;
            });

            usort($all_post_shows, function($a, $b) {
                return (int)$b->year <=> (int)$a->year;
            });


            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}