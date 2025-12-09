<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PWE_Functions {

    private static $translation_context = [
        'element_slug' => null,
        'group' => null,
        'element_type' => 'main'
    ];

    public static function set_translation_context($element_slug, $group, $element_type = 'main') {
        self::$translation_context['element_slug'] = $element_slug;
        self::$translation_context['group'] = $group;
        self::$translation_context['element_type'] = $element_type;
    }

    public static function multi_translation($key) {
        $ctx = self::$translation_context;

        if (!$ctx['element_slug'] || !$ctx['group']) {
            return $key;
        }

        $locale = get_locale();

        $translations_file = plugin_dir_url(__DIR__) .
            'translations/elements/' . $ctx['element_type'] . '/' .
            $ctx['element_slug'] . '/' .
            $ctx['element_slug'] . '-' . $ctx['group'] . '.json';

        $translations_data = json_decode(file_get_contents($translations_file), true);

        $map = $translations_data[$locale]
            ?? $translations_data['en_US']
            ?? [];

        return $map[$key] ?? $key;
    }

    // Assets per element
    public static function assets_per_element($element_slug, $element_type = 'main', $folder = 'elements') {
        $src = $folder .'/'. (!empty($element_type) ? $element_type .'/' : '') . $element_slug . '/assets/';

        $base_dir = plugin_dir_path(__DIR__) . $src;
        $base_url = plugin_dir_url(__DIR__) . $src;

        $el_name = 'el=' . urlencode($element_slug);

        // CSS
        if (file_exists($base_dir . 'style.css')) {
            $version = filemtime($base_dir . 'style.css');
            wp_enqueue_style(
                'pwe-' . $element_slug . '-style',
                $base_url . 'style.css?' . $el_name,
                [],
                $version
            );
        }

        // JS
        if (file_exists($base_dir . 'script.js')) {
            $version = filemtime($base_dir . 'script.js');
            wp_enqueue_script(
                'pwe-' . $element_slug . '-script',
                $base_url . 'script.js?' . $el_name,
                ['jquery'],
                $version,
                true
            );
        }
    }

    // Assets per group
    public static function assets_per_group($element_slug, $group, $element_type = 'main', $folder = 'elements', $atts = null) {
        $src = $folder .'/'. (!empty($element_type) ? $element_type .'/' : '') . $element_slug . '/presets/preset-' . $group . '/assets/';
        
        $base_dir = plugin_dir_path(__DIR__) . $src;
        $base_url = plugin_dir_url(__DIR__) . $src;

        $el_name = 'el=' . urlencode($element_slug);
        $el_group = 'gr=' . urlencode($group);

        // CSS
        if (file_exists($base_dir . 'style.css')) {
            $version = filemtime($base_dir . 'style.css');
            wp_enqueue_style(
                'pwe-' . $element_slug . '-' . $group . '-style',
                $base_url . 'style.css?' . $el_name .'&'. $el_group,
                [],
                $version
            );
        }

        // JS
        if (file_exists($base_dir . 'script.js')) {
            $handle = 'pwe-' . $element_slug . '-' . $group . '-script';
            $version = filemtime($base_dir . 'script.js');

            wp_enqueue_script(
                $handle,
                $base_url . 'script.js?' . $el_name .'&'. $el_group,
                ['jquery'],
                $version,
                true
            );
            if (!empty($atts)) {
                wp_localize_script($handle, 'pwe_element_atts', $atts);
            }
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
    public static function exhibitor_logos($catalog_id, $count = null, $shuffle = true) {
        $catalog_id = do_shortcode('[trade_fair_catalog]');
        $catalog_ids = do_shortcode('[trade_fair_catalog_id]');
        $fair_date = do_shortcode('[trade_fair_date]'); 

        if (!empty($catalog_ids) && stripos($fair_date, 'nowa data') === false) {

            try {

                // String or array handling
                if (is_string($catalog_ids)) {
                    $catalog_array = array_map('intval', array_map('trim', explode(',', $catalog_ids)));
                } elseif (is_array($catalog_ids)) {
                    $catalog_array = array_map('intval', $catalog_ids);
                } else {
                    throw new Exception("Incorrect catalog_ids format");
                }

                $all_exhibitors = [];

                foreach ($catalog_array as $catalog_id_single) {

                    $exh_catalog_address = PWECommonFunctions::get_database_meta_data('exh_catalog_address_2');
                    $catalog_url = "{$exh_catalog_address}{$catalog_id_single}/exhibitors.json";

                    $res = wp_remote_get($catalog_url, [
                        'timeout' => 10,
                        'headers' => ['Accept' => 'application/json'],
                    ]);

                    if (is_wp_error($res)) {
                        throw new Exception("Błąd połączenia: " . $res->get_error_message());
                    }

                    if (wp_remote_retrieve_response_code($res) !== 200) {
                        throw new Exception("HTTP != 200 dla URL: {$catalog_url}");
                    }

                    $body = wp_remote_retrieve_body($res);
                    if (empty($body)) {
                        throw new Exception("Pusty JSON: {$catalog_url}");
                    }

                    $json = json_decode($body, true);
                    if (!is_array($json) || empty($json['success']) || empty($json['exhibitors'])) {
                        throw new Exception("Nieprawidłowy JSON w {$catalog_url}");
                    }

                    $all_exhibitors = array_merge($all_exhibitors, $json['exhibitors']);
                }

                // Saving full data to a file
                $file = $_SERVER['DOCUMENT_ROOT'] . '/doc/pwe-exhibitors-data.json';
                file_put_contents($file, json_encode($all_exhibitors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);

                // Data mapping + filters + dupe removal
                $mapped = [];
                $usedNames = [];

                foreach ($all_exhibitors as $item) {

                    $name    = trim(($item['companyInfo']['displayName'] ?? $item['companyInfo']['name'] ?? ''));
                    $nameKey = mb_strtolower($name);
                    $stand   = $item['stand']['standNumber'] ?? '';
                    $logo    = $item['companyInfo']['logoUrl'] ?? '';
                    $www     = $item['companyInfo']['website'] ?? '';

                    // Skip without logo
                    if (empty($logo)) continue;

                    // Skip duplicates by name
                    if (isset($usedNames[$nameKey])) continue;
                    $usedNames[$nameKey] = true;

                    $mapped[] = [
                        'name'  => $name,
                        'stand' => $stand,
                        'logo'  => $logo,
                        'www'   => $www
                    ];
                }

                // Shuffle
                if ($shuffle) {
                    shuffle($mapped);
                }

                // Limit
                if (!empty($count)) {
                    $mapped = array_slice($mapped, 0, $count);
                }

                return $mapped;

            } catch (Throwable $e) {

                error_log("[exhibitor_logos] " . $e->getMessage());

                if (current_user_can('administrator')) {
                    echo '<script>console.error("exhibitor_logos ERROR: ' . htmlentities($e->getMessage()) . '")</script>';
                }

                return [];
            }
        } else if (!empty($catalog_id) && stripos($fair_date, 'nowa data') === false) {

            $basic_exhibitors = [];
            $data = [];

            $today = new DateTime();
            $token = md5("#22targiexpo22@@@#" . $today->format('Y-m-d'));
            $exh_catalog_address = PWECommonFunctions::get_database_meta_data('exh_catalog_address');
            $can_url = $exh_catalog_address . $token . '&id_targow=' . intval($catalog_id);

            try {

                // Attempting to read from a local file
                $local_file = $_SERVER['DOCUMENT_ROOT'] . '/doc/pwe-exhibitors.json';

                if (file_exists($local_file)) {

                    $json = file_get_contents($local_file);
                    $data = json_decode($json, true);

                    if (!empty($data[$catalog_id]['Wystawcy'])) {
                        $basic_exhibitors = $data[$catalog_id]['Wystawcy'];
                    }
                }

                // No local data → we download from API
                if (empty($basic_exhibitors)) {

                    $context = stream_context_create([
                        'http' => ['timeout' => 10]
                    ]);

                    $json = @file_get_contents($can_url, false, $context);
                    if ($json === false) {
                        throw new Exception("Nie można pobrać danych z API: {$can_url}");
                    }

                    $data = json_decode($json, true);
                    if (!is_array($data)) {
                        throw new Exception("Błąd dekodowania JSON z {$can_url}");
                    }

                    $first = reset($data);
                    $basic_exhibitors = $first['Wystawcy'] ?? [];
                }

                // Data mapping + filters + dupe removal
                $mapped = [];
                $usedNames = [];

                foreach ($basic_exhibitors as $item) {

                    $name    = trim($item['Nazwa_wystawcy'] ?? '');
                    $nameKey = mb_strtolower($name);
                    $stand   = $item['Numer_stoiska'] ?? '';
                    $logo    = $item['URL_logo_wystawcy'] ?? '';
                    $www     = $item['www'] ?? '';

                    if (empty($logo)) continue;

                    if (isset($usedNames[$nameKey])) continue;
                    $usedNames[$nameKey] = true;

                    $mapped[] = [
                        'name'  => $name,
                        'stand' => $stand,
                        'logo'  => $logo,
                        'www'   => $www
                    ];
                }

                // Shuffle
                if ($shuffle) {
                    shuffle($mapped);
                }

                // Limit
                if (!empty($count)) {
                    $mapped = array_slice($mapped, 0, $count);
                }

                return $mapped;

            } catch (Throwable $e) {

                error_log("[exhibitor_logos] " . $e->getMessage());

                if (current_user_can('administrator')) {
                    echo '<script>console.error("exhibitor_logos ERROR: ' . htmlentities($e->getMessage()) . '")</script>';
                }

                return [];
            }
        }
    }

}