<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/presets/exhibitor_catalog_functions.php';
require_once __DIR__ . '/presets/svg-icon.php';

class Exhibitor_Catalog {

    public static function get_data() {
        return [
            'types' => ['catalog'],
            'presets' => [
                'all'    => plugin_dir_path(__FILE__) . 'presets/preset-all/preset-all.php',
                'mobile' => plugin_dir_path(__FILE__) . 'presets/preset-mobile/preset-mobile.php',
            ],
        ];
    }

    public static function render($group, $params, $atts) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(__CLASS__);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $data = self::get_data();

        $preset_file = is_mobile()
            ? $data['presets']['mobile']
            : $data['presets']['all'];

        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

                /* --- POBRANIE I PRZYGOTOWANIE DANYCH --- */
                $exhibitors_prepared = get_all_exhibitors($atts);

                $exhibitors_count = count($exhibitors_prepared);

                $exhibitors_per_page = 20;

                $exhibitors_by_id = [];
                foreach ($exhibitors_prepared as $exhibitor) {
                    $exhibitors_by_id[(int)$exhibitor['id_numeric']] = $exhibitor;
                }

                $halls = [];
                $sectors = [];
                $brands = [];
                $products_tags =[];

                foreach ($exhibitors_prepared as $exhibitor_row) {
                    if (!empty($exhibitor_row['hall_name'])) {
                        $halls[$exhibitor_row['hall_name']] = true;
                    }
                    if (!empty($exhibitor_row['catalog_tags']) && is_array($exhibitor_row['catalog_tags'])) {
                        foreach ($exhibitor_row['catalog_tags'] as $tag) {
                            if (is_string($tag)) {
                                $tag = trim($tag);
                                if ($tag !== '') $sectors[$tag] = true;
                            }
                        }
                    }
                    if (!empty($exhibitor_row['brands']) && is_array($exhibitor_row['brands'])) {
                        foreach ($exhibitor_row['brands'] as $brand) {
                            if (is_string($brand)) {
                                $brand = trim($brand);
                                if ($brand !== '') $brands[$brand] = true;
                            }
                        }
                    }
                    if (!empty($exhibitor_row['products']) && is_array($exhibitor_row['products'])) {
                        foreach ($exhibitor_row['products'] as $p) {
                            $raw = $p['tags'] ?? [];

                            // ujednolicenie do tablicy
                            if (is_string($raw))       $candidates = [$raw];
                            elseif (is_array($raw))    $candidates = $raw;
                            else                       $candidates = [];

                            foreach ($candidates as $cand) {
                                if (!is_string($cand) || $cand === '') continue;

                                // dziel po przecinku LUB po >=2 spacjach
                                $parts = preg_split('/\s*,\s*|\s{2,}/u', $cand, -1, PREG_SPLIT_NO_EMPTY);
                                foreach ($parts as $t) {
                                    $t = trim($t);
                                    if ($t !== '') $products_tags[$t] = true;
                                }
                            }
                        }
                    }
                }
                ksort($halls, SORT_NATURAL | SORT_FLAG_CASE);
                ksort($sectors, SORT_NATURAL | SORT_FLAG_CASE);
                ksort($brands, SORT_NATURAL | SORT_FLAG_CASE);
                ksort($products_tags, SORT_NATURAL | SORT_FLAG_CASE);

                $halls   = array_keys($halls);
                $sectors = array_keys($sectors);
                $brands = array_keys($brands);
                $products_tags = array_keys($products_tags);

                $domain = do_shortcode('[trade_fair_domainadress]');
                $cap_db = PWECommonFunctions::connect_database();

                $sql = $cap_db->prepare("
                    SELECT 
                        MAX(CASE WHEN fa.slug = 'fair_color_accent_catlog' THEN fa.data END) AS accent,
                        MAX(CASE WHEN fa.slug = 'fair_color_main2_catlog' THEN fa.data END) AS main2
                    FROM fair_adds fa
                    JOIN fairs f ON f.id = fa.fair_id
                    WHERE f.fair_domain = %s
                ", $domain);

                $colors = $cap_db->get_results($sql, ARRAY_A);

                $output = '
                <style>
                    #exhibitorCatalog, #exhibitorPage, .exhibitor-product-modal {
                        --catalog-accent-color: ' . (empty($colors[0]['accent']) ? 'var(--accent-color)' : $colors[0]['accent']) . ';
                        --catalog-main2-color: ' . (empty($colors[0]['main2']) ? 'var(--main2-color)' : $colors[0]['main2']) . ';

                        --accent_lighter_color: color-mix(in srgb, var(--catalog-accent-color) 5%, #ffffff 95%);
                        --main2_lighter_color: color-mix(in srgb, var(--catalog-main2-color) 5%, #ffffff 95%);

                        --hover_main2_color: color-mix(in srgb, var(--catalog-main2-color) 80%, #ffffff 20%);
                    }
                </style>';

            /* <-------------> General code end <-------------> */ 

            $startMemory = memory_get_usage();

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }

            $endMemory = memory_get_usage();

            if (current_user_can('administrator')) {
                echo '<script>console.log("Catalog memory size used - '. ($endMemory - $startMemory) / 1024 .'kb")</script>';
            }
        }
    }
}
