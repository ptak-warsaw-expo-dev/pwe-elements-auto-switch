<?php
if (!defined('ABSPATH')) exit;

class Header {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/preset-gr1/preset-gr1.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/preset-gr2/preset-gr2.php',
            ],
        ];
    }

    public static function render($group) {
        $data = self::get_data();
        $element_slug = strtolower(__CLASS__);
        $element_type = $data['types'][0];

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group);
        
        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */
            
            $el_id = PWECommonFunctions::id_rnd();

            $trade_fair_name = (PWECommonFunctions::lang_pl()) ? do_shortcode('[trade_fair_name]') : do_shortcode('[trade_fair_name_eng]');
            $trade_fair_desc = (PWECommonFunctions::lang_pl()) ? do_shortcode('[trade_fair_desc]') : do_shortcode('[trade_fair_desc_eng]');
            $trade_fair_date = do_shortcode('[trade_fair_date_multilang]');

            $trade_fair_dates_custom_format = do_shortcode('[trade_fair_date_custom_format]');

            // Processing edition shortcode
            $trade_fair_edition_shortcode = do_shortcode('[trade_fair_edition]');
            $trade_fair_edition_text = PWE_Functions::multi_translation('edition');
            $trade_fair_edition_first = PWE_Functions::multi_translation('premier_edition');
            $trade_fair_edition = (!is_numeric($trade_fair_edition_shortcode) || $trade_fair_edition_shortcode == 1) ? $trade_fair_edition_first : $trade_fair_edition_shortcode . $trade_fair_edition_text;

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
