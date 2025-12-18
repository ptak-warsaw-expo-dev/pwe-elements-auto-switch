<?php
if (!defined('ABSPATH')) exit;

class Profiles {

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
           
            $data = PWECommonFunctions::get_database_fairs_data_profiles();  
            
            if (!empty($data)) {
                $decoded_data = json_decode($data[0]->data, true);

                $profile_for_visitors_img       = $decoded_data['profile_for_visitors_img'] ?? null;
                $profile_for_exhibitors_img     = $decoded_data['profile_for_exhibitors_img'] ?? null;
                $profile_industry_scope_img     = $decoded_data['profile_industry_scope_img'] ?? null;

                $profile_for_visitors_pl        = $decoded_data['profile_for_visitors_pl'] ?? null;
                $profile_for_exhibitors_pl      = $decoded_data['profile_for_exhibitors_pl'] ?? null;
                $profile_industry_scope_pl      = $decoded_data['profile_industry_scope_pl'] ?? null;

                $profile_for_visitors_en        = $decoded_data['profile_for_visitors_en'] ?? null;
                $profile_for_exhibitors_en      = $decoded_data['profile_for_exhibitors_en'] ?? null;
                $profile_industry_scope_en      = $decoded_data['profile_industry_scope_en'] ?? null;
            }

            if (PWECommonFunctions::lang_pl() && (empty($profile_for_visitors_pl) || empty($profile_for_exhibitors_pl) || empty($profile_industry_scope_pl))) {
                return;
            } else if (!PWECommonFunctions::lang_pl() && (empty($profile_for_visitors_en) || empty($profile_for_exhibitors_en) || empty($profile_industry_scope_en))) {
                return;
            }

            /* <-------------> General code end <-------------> */
            
            $output = include $preset_file;
            
            if ($output && !empty($decoded_data)) {
                echo $output;         
            }
        }
    }
}
