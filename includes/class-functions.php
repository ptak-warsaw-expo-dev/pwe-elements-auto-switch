<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PWE_Functions {

    // Assets per element
    public static function assets_per_element($element_slug, $element_type = 'main') {
        $base_dir = plugin_dir_path(__DIR__) . 'elements/'. $element_type .'/'. $element_slug . '/assets/';
        $base_url = plugin_dir_url(__DIR__) . 'elements/'. $element_type .'/'. $element_slug . '/assets/';

        // CSS
        if (file_exists($base_dir . 'style.css')) {
            wp_enqueue_style('pwe-' . $element_slug . '-style', $base_url . 'style.css', [], filemtime($base_dir . 'style.css'));
        }

        // JS
        if (file_exists($base_dir . 'script.js')) {
            wp_enqueue_script('pwe-' . $element_slug . '-script', $base_url . 'script.js', ['jquery'], filemtime($base_dir . 'script.js'), true);
        }
    }
 
    // Assets per group
    public static function assets_per_group($element_slug, $group, $element_type = 'main') {
        $base_dir = plugin_dir_path(__DIR__) . 'elements/'. $element_type .'/'. $element_slug . '/presets/preset-' . $group . '/assets/';
        $base_url = plugin_dir_url(__DIR__) . 'elements/'. $element_type .'/'. $element_slug . '/presets/preset-' . $group . '/assets/';

        // CSS
        if (file_exists($base_dir . 'style.css')) {
            wp_enqueue_style('pwe-' . $element_slug . '-' . $group . '-style', $base_url . 'style.css', [], filemtime($base_dir . 'style.css'));
        }

        // JS
        if (file_exists($base_dir . 'script.js')) {
            wp_enqueue_script('pwe-' . $element_slug . '-' . $group . '-script', $base_url . 'script.js', ['jquery'], filemtime($base_dir . 'script.js'), true);
        }
    }

    /**
     * Downloads exhibitor logos.
     *
     * @param int|string $catalog_id  Catalog ID
     * @param int        $count       Maximum number of logos
     * @param bool       $shuffle     Should logos be randomized (default true)
     * @return array
     */
    public static function exhibitor_logos($catalog_id, $count, $shuffle = true) {
        $basic_exhibitors = [];
        $data = [];

        // Budowanie URL
        $today = new DateTime();
        $formatted_date = $today->format('Y-m-d');
        $token = md5("#22targiexpo22@@@#" . $formatted_date);
        $exh_catalog_address = PWECommonFunctions::get_database_meta_data('exh_catalog_address');
        $can_url = $exh_catalog_address . $token . '&id_targow=' . $catalog_id;

        if (current_user_can('administrator')) {
            if (empty($catalog_id)) {
                echo '<script>console.error("Brak ID katalogu wystawców")</script>';
            }
        }

        // Try local file first
        $local_file = $_SERVER['DOCUMENT_ROOT'] . '/doc/pwe-exhibitors.json';

        if (file_exists($local_file)) {
            $json = file_get_contents($local_file);
            $data = json_decode($json, true);

            if (is_array($data) && isset($data[$catalog_id]['Wystawcy'])) {
                $basic_exhibitors = $data[$catalog_id]['Wystawcy'];

                if (current_user_can('administrator')) {
                    echo '<script>console.log("Dane pobrane z lokalnego pliku (https://'.  $_SERVER['HTTP_HOST'] .'/doc/pwe-exhibitors.json) dla katalogu ' . $catalog_id . '. Link do katalogu expoplanner: '. $can_url .'")</script>';
                };
            }
        } 

        // If local missing/invalid → get external JSON
        if (empty($basic_exhibitors) && !empty($catalog_id)) {
            try {
                $json = @file_get_contents($can_url);

                if ($json === false) {
                    throw new Exception('Nie można pobrać danych JSON z zewnętrznego źródła.');
                }

                $data = json_decode($json, true);
                if (!is_array($data)) {
                    throw new Exception('Błąd dekodowania danych JSON.');
                }

                $basic_exhibitors = reset($data)['Wystawcy'] ?? [];

                if (current_user_can('administrator')) {
                    echo '<script>console.log("Dane pobrane z zewnętrznego API '. $can_url .'")</script>';
                }

            } catch (Exception $e) {
                error_log("[" . date('Y-m-d H:i:s') . "] logosChecker błąd: " . $e->getMessage());
                $basic_exhibitors = [];
            }
        }

        // Filtering only those with logos
        $logotypes_array = array_filter($basic_exhibitors, fn($w) => !empty($w['URL_logo_wystawcy']));

        // Random or natural order
        if ($shuffle) {
            shuffle($logotypes_array);
        }

        // Limit to $count
        $logotypes_array = array_slice($logotypes_array, 0, $count);

        return $logotypes_array;
    }

}