<?php
if (!defined('ABSPATH')) exit;

class Exhibitor_Catalog {

    public static function get_data() {
        return [
            'types' => ['catalog'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/preset-all/preset-all.php'
            ],
        ];
    }

    private static function get_all_exhibitors() {
        $exh_catalog_local_file = PWECommonFunctions::get_database_meta_data('exh_catalog_address_doc');

        $local_file = $_SERVER['DOCUMENT_ROOT'] . $exh_catalog_local_file;
        if (!file_exists($local_file) || !is_readable($local_file)) {
            error_log("[get_all_exhibitors] Brak pliku lub brak dostępu: {$local_file}");
            return [];
        }

        $json = file_get_contents($local_file);
        if ($json === false || trim($json) === '') {
            error_log("[get_all_exhibitors] Nie udało się odczytać pliku lub plik jest pusty.");
            return [];
        }

        $catalog_data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($catalog_data)) {
            error_log("[get_all_exhibitors] Błąd JSON: " . json_last_error_msg());
            return [];
        }
        return $catalog_data;
    }

    public static function render($group) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(__CLASS__);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets']['all'] ?? null;



        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

                $exh_catalog_cron_pass = PWECommonFunctions::get_database_meta_data('cron_secret_pass');

                $domain = $_SERVER['HTTP_HOST'];

                if (current_user_can('administrator')) {
                    echo '<script>console.log("Link do odświeżenia katlogu: https://' . $domain . '/wp-content/plugins/custom-element/other/mass_vip_cron.php?pass=' . $exh_catalog_cron_pass . '")</script>';
                };


                /* --- POBRANIE I PRZYGOTOWANIE DANYCH --- */
                $exhibitors_prepared = self::get_all_exhibitors();

                $exhibitors_count = count($exhibitors_prepared);

                $exhibitors_per_page = 20;

                $exhibitors_by_id = [];
                foreach ($exhibitors_prepared as $exhibitor) {
                    $exhibitors_by_id[(int)$exhibitor['id_numeric']] = $exhibitor;
                }

                $halls = [];
                $sectors = [];
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
                ksort($products_tags, SORT_NATURAL | SORT_FLAG_CASE);

                $halls   = array_keys($halls);
                $sectors = array_keys($sectors);
                $products_tags = array_keys($products_tags);


            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
