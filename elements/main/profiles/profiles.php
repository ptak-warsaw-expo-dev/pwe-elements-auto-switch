<?php
if (!defined('ABSPATH')) exit;

class Profiles {

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
           
            $data = PWE_Functions::get_database_fairs_data_profiles();  

            $validate_img_url = function(?string $url, string $default): string {
                if (empty($url)) {
                    return $default;
                }

                $response = wp_remote_head($url, ['timeout' => 3]);

                if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                    return $default;
                }

                return $url;
            };
         
            $default_visitors_img   = '/wp-content/plugins/pwe-media/media/default-profiles/visitors.webp';
            $default_exhibitors_img = '/wp-content/plugins/pwe-media/media/default-profiles/exhibitors.webp';
            $default_industry_img   = '/wp-content/plugins/pwe-media/media/default-profiles/industry.webp';

            if (!empty($data)) {
                $decoded_data = json_decode($data[0]->data, true);

                $profile_for_visitors_img = $validate_img_url(
                    $decoded_data['profile_for_visitors_img'] ?? null,
                    $default_visitors_img
                );

                $profile_for_exhibitors_img = $validate_img_url(
                    $decoded_data['profile_for_exhibitors_img'] ?? null,
                    $default_exhibitors_img
                );

                $profile_industry_scope_img = $validate_img_url(
                    $decoded_data['profile_industry_scope_img'] ?? null,
                    $default_industry_img
                );

                $profile_for_visitors_pl   = $decoded_data['profile_for_visitors_pl'] ?? null;
                $profile_for_exhibitors_pl = $decoded_data['profile_for_exhibitors_pl'] ?? null;
                $profile_industry_scope_pl = $decoded_data['profile_industry_scope_pl'] ?? null;

                $profile_for_visitors_en   = $decoded_data['profile_for_visitors_en'] ?? null;
                $profile_for_exhibitors_en = $decoded_data['profile_for_exhibitors_en'] ?? null;
                $profile_industry_scope_en = $decoded_data['profile_industry_scope_en'] ?? null;
            }

            if (PWE_Functions::lang_pl() && (empty($profile_for_visitors_pl) || empty($profile_for_exhibitors_pl) || empty($profile_industry_scope_pl))) {
                return;
            } else if (!PWE_Functions::lang_pl() && (empty($profile_for_visitors_en) || empty($profile_for_exhibitors_en) || empty($profile_industry_scope_en))) {
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
