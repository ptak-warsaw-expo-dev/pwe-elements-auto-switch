<?php
if (!defined('ABSPATH')) exit;

class About {

    private static function getExhibitorsData(): array {
        $merge_exhibitors = [];
        $logos = [];

        // Pobranie katalogu — dopasuj do swojej implementacji
        $exhibitors = CatalogFunctions::logosChecker(
            do_shortcode('[trade_fair_catalog]'),
            'PWECatalog21',
            false,
            null,
            false
        );

        if (is_array($exhibitors)) {
            foreach ($exhibitors as $exhibitor) {
                $merge_exhibitors[] = $exhibitor;

                // Pola dopasuj do realnej struktury
                $logoName = $exhibitor['Nazwa_wystawcy'] ?? '';
                $logoUrl  = $exhibitor['URL_logo_wystawcy'] ?? '';

                if ($logoUrl && filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                    $logos[] = [
                        'url'  => $logoUrl,
                        'name' => $logoName,
                    ];
                }
            }
        }

        $count = count($merge_exhibitors);

        if (!empty($logos)) {
            shuffle($logos);
            $logos = array_slice($logos, 0, 21);
        }

        return [
            'count'      => $count,
            'has_many'   => $count > 9,
            'logos'      => $logos,
            'exhibitors' => $merge_exhibitors,
        ];
    }

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

        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $selected_lang = PWECommonFunctions::languageChecker('pl', 'en');
            $domain = parse_url(site_url(), PHP_URL_HOST);
            
            $fairs_data_adds = PWECommonFunctions::get_database_fairs_data_adds($domain);
            $fair = $fairs_data_adds[0] ?? null;
            
            $title = $fair->{'about_title_' . $selected_lang} ?? '';
            $desc = $fair->{'about_desc_' . $selected_lang} ?? '';

            $img = '<img class="pwe-about__img" src="' 
                . (file_exists($_SERVER['DOCUMENT_ROOT'] . '/doc/new_template/fair_img.webp') 
                    ? home_url('/doc/new_template/fair_img.webp') 
                    : content_url('plugins/pwe-media/media/main-page/fair_img.webp')) 
                . '" alt="' 
                . PWECommonFunctions::languageChecker(
                    'Odwiedzający na targach ' . do_shortcode('[trade_fair_name]'),
                    'Visitors at the ' . do_shortcode('[trade_fair_name_eng]')
                ) 
                . '">';

            $exhibitorsData = self::getExhibitorsData();

            $hasMany = !empty($exhibitorsData['has_many']);
            $logos   = is_array($exhibitorsData['logos'] ?? null) ? $exhibitorsData['logos'] : [];

            $logos_urls = array_values(array_map(function($l){ return $l['url']; }, $logos));
            $logos_json = esc_attr( wp_json_encode($logos_urls) ); 

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
