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

    // Get translations from JSONs
    public static function multi_translation($key) {
        $ctx = self::$translation_context;

        if (!$ctx['element_slug'] || !$ctx['group']) {
            return $key;
        }

        $locale = get_locale();

        $translations_file = plugin_dir_url(__DIR__) . 'translations/elements/' . $ctx['element_type'] . '/' . $ctx['element_slug'] . '/' . $ctx['group'] . '.json';

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
        $src = $folder .'/'. (!empty($element_type) ? $element_type .'/' : '') . $element_slug . '/presets/' . $group . '/assets/';

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

                    $exh_catalog_address = PWE_Functions::get_database_meta_data('exh_catalog_address_2');
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
            $exh_catalog_address = PWE_Functions::get_database_meta_data('exh_catalog_address');
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



    // <============================================================================================>
    // Functions from plugin PWElements 3.2.2 <========================================================>
    // <============================================================================================>



    private static $cached_db_connection = null;

    /**
     * Random number
     */
    public static function id_rnd() {
        $id_rnd = rand(10000, 99999);
        return $id_rnd;
    }

    private static function resolve_server_addr_fallback() {
        $host = php_uname('n');
            switch ($host) {
            case 'dedyk180.cyber-folks.pl': return '94.152.207.180';
            case 'dedyk93.cyber-folks.pl': return '94.152.206.93';
            case 'dedyk239.cyber-folks.pl': return '91.225.28.47';
            default: return '';
        }
    }

    /**
     * List of DB servers
     */
    private static function get_database_servers() {
        $servers = [
            ['host'=>'dedyk180.cyber-folks.pl',  'name'=>PWE_DB_NAME_180,  'user'=>PWE_DB_USER_180,  'pass'=>PWE_DB_PASSWORD_180],
            ['host'=>'dedyk93.cyber-folks.pl',   'name'=>PWE_DB_NAME_93,   'user'=>PWE_DB_USER_93,   'pass'=>PWE_DB_PASSWORD_93],
            ['host'=>'dedyk239.cyber-folks.pl',  'name'=>PWE_DB_NAME_239,  'user'=>PWE_DB_USER_239,  'pass'=>PWE_DB_PASSWORD_239],
        ];

        // For CRON
        if (empty($_SERVER['SERVER_ADDR'])) {
            $_SERVER['SERVER_ADDR'] = self::resolve_server_addr_fallback();
        }

        // If there is current server — try localhost first
        switch ($_SERVER['SERVER_ADDR']) {
            case '94.152.207.180': $servers[0]['host'] = 'localhost'; break;
            case '94.152.206.93':  $servers[1]['host'] = 'localhost'; break;
            case '91.225.28.47':   $servers[2]['host'] = 'localhost'; break;
        }

        return $servers;
    }

    /**
     * Connecting to CAP database
     */
    public static function connect_database() {

        // Cache within the request
        if (self::$cached_db_connection !== null) {
            return self::$cached_db_connection;
        }

        // If there was a recent failure - do not try again
        if (get_transient('pwe_db_connection_fail')) {
            error_log('PWE DB: Skipping connection attempt due to recent failure.');
            return false;
        }

        $servers = self::get_database_servers();
        if (empty($servers)) {
            error_log('PWE DB: No database servers defined in get_database_servers().');
            set_transient('pwe_db_connection_fail', 1, 60);
            return false;
        }

        foreach ($servers as $server_index => $server) {

            if (empty($server['user']) || empty($server['pass']) || empty($server['name'])) {
                error_log("PWE DB: Server #$server_index skipped - missing user, pass or db name.");
                continue;
            }

            $host = $server['host'] ?? 'localhost';

            // Quick timeout
            add_filter('wpdb_connect_timeout', function() { return 2; });

            $wpdb = @new wpdb(
                $server['user'],
                $server['pass'],
                $server['name'],
                $host
            );

            if (empty($wpdb->dbh)) {
                error_log("PWE DB: Failed to connect to server #$server_index ($host).");
                continue;
            }

            // Test query
            $test = @$wpdb->get_var("SELECT 1");
            if ($test != 1) {
                error_log("PWE DB: Test query failed on server #$server_index ($host).");
                continue;
            }

            // Success
            self::$cached_db_connection = $wpdb;
            // error_log("PWE DB: Connected successfully to server #$server_index ($host).");
            return $wpdb;
        }

        // Cache the fail for 60 seconds to avoid blocking workers
        error_log('PWE DB: All connection attempts failed. Caching failure for 60 seconds.');
        set_transient('pwe_db_connection_fail', 1, 60);

        return false;
    }

    /**
     * Get data from CAP databases
     */
    private static $fairs_cache = [];
    public static function get_database_fairs_data($fair_domain = null): array {
        // Check runtime cache first
        $cache_key = $fair_domain ?? 'all';
        if (isset(self::$fairs_cache[$cache_key])) {
            return self::$fairs_cache[$cache_key];
        }

        // Connect to database
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("No database connection.")</script>';
            }
            self::$fairs_cache[$cache_key] = [];
            return [];
        }

        // Transient key unique for this domain
        $transient_key = 'pwe_fairs_' . md5($cache_key);

        // Try to get cached data from transient
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$fairs_cache[$cache_key] = $cached;
            return $cached;
        }

        // SQL query
        $sql = "
            SELECT
                f.id,
                f.fair_name_pl,
                f.fair_name_en,
                f.fair_desc_pl,
                f.fair_desc_en,
                f.fair_short_desc_pl,
                f.fair_short_desc_en,
                f.fair_full_desc_pl,
                f.fair_full_desc_en,
                f.fair_date_start,
                f.fair_date_start_hour,
                f.fair_date_end,
                f.fair_date_end_hour,
                f.fair_edition,
                f.fair_visitors,
                f.fair_exhibitors,
                f.fair_countries,
                f.estimations,
                f.fair_facebook,
                f.fair_instagram,
                f.fair_linkedin,
                f.fair_youtube,
                f.fair_color_accent,
                f.fair_color_main2,
                f.fair_hall,
                f.fair_area,
                f.fair_kw,
                f.fair_badge,
                f.fair_domain,
                f.fair_shop,
                f.fair_group,
                fa_category_pl.data AS category_pl,
                fa_category_en.data AS category_en,
                fa_konf_name.data AS konf_name,
                fa_konf_title_pl.data AS konf_title_pl,
                fa_konf_title_en.data AS konf_title_en,
                fa_fair_kw_new.data AS fair_kw_new,
                fa_fair_kw_old_arch.data AS fair_kw_old_arch,
                fa_fair_kw_new_arch.data AS fair_kw_new_arch
            FROM fairs f
            LEFT JOIN fair_adds fa_category_pl       ON fa_category_pl.fair_id = f.id AND fa_category_pl.slug = 'category_pl'
            LEFT JOIN fair_adds fa_category_en       ON fa_category_en.fair_id = f.id AND fa_category_en.slug = 'category_en'
            LEFT JOIN fair_adds fa_konf_name         ON fa_konf_name.fair_id = f.id AND fa_konf_name.slug = 'konf_name'
            LEFT JOIN fair_adds fa_konf_title_pl     ON fa_konf_title_pl.fair_id = f.id AND fa_konf_title_pl.slug = 'konf_title_pl'
            LEFT JOIN fair_adds fa_konf_title_en     ON fa_konf_title_en.fair_id = f.id AND fa_konf_title_en.slug = 'konf_title_en'
            LEFT JOIN fair_adds fa_fair_kw_new       ON fa_fair_kw_new.fair_id = f.id AND fa_fair_kw_new.slug = 'fair_kw_new'
            LEFT JOIN fair_adds fa_fair_kw_old_arch  ON fa_fair_kw_old_arch.fair_id = f.id AND fa_fair_kw_old_arch.slug = 'fair_kw_old_arch'
            LEFT JOIN fair_adds fa_fair_kw_new_arch  ON fa_fair_kw_new_arch.fair_id = f.id AND fa_fair_kw_new_arch.slug = 'fair_kw_new_arch'
        ";

        $params = [];
        if ($fair_domain !== null) {
            $sql .= " WHERE f.fair_domain = %s";
            $params[] = $fair_domain;
        }

        $sql .= " GROUP BY f.id";

        // Execute query
        if (!empty($params)) {
            $results = $cap_db->get_results($cap_db->prepare($sql, $params));
        } else {
            $results = $cap_db->get_results($sql);
        }

        // Handle SQL errors
        if ($cap_db->last_error) {
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("SQL error: ' . addslashes($cap_db->last_error) . '")</script>';
            }
            self::$fairs_cache[$cache_key] = [];
            return [];
        }

        // Save to transient for 1 hour
        set_transient($transient_key, $results, 3600);

        // Save to runtime cache
        self::$fairs_cache[$cache_key] = $results;

        return $results;
    }

    /**
     * Get premieres data
     */
    private static $premieres_cache = [];
    public static function get_database_premieres_data($fair_domain = null): array {
        $cache_key = $fair_domain ?? 'all';
        if (isset(self::$premieres_cache[$cache_key])) {
            return self::$premieres_cache[$cache_key];
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::$premieres_cache[$cache_key] = [];
            return [];
        }

        $transient_key = 'pwe_premieres_' . md5($cache_key);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$premieres_cache[$cache_key] = $cached;
            return $cached;
        }

        $sql = "
            SELECT f.id, f.fair_domain, p.slug, p.data
            FROM fairs f
            LEFT JOIN fair_premieres p ON p.fair_id = f.id
        ";

        $params = [];
        if ($fair_domain !== null) {
            $sql .= " WHERE f.fair_domain = %s";
            $params[] = $fair_domain;
        }

        $results = !empty($params)
            ? $cap_db->get_results($cap_db->prepare($sql, $params))
            : $cap_db->get_results($sql);

        if ($cap_db->last_error) {
            self::$premieres_cache[$cache_key] = [];
            return [];
        }

        set_transient($transient_key, $results, 3600);
        self::$premieres_cache[$cache_key] = $results;
        return $results;
    }

    /**
     * Get fairs additional data
     */
    private static $fairs_adds_cache = [];
    public static function get_database_fairs_data_adds($fair_domain = null): array {
        $fair_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        if (isset(self::$fairs_adds_cache[$fair_domain])) {
            return self::$fairs_adds_cache[$fair_domain];
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::$fairs_adds_cache[$fair_domain] = [];
            return [];
        }

        $transient_key = 'pwe_fairs_adds_' . md5($fair_domain);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$fairs_adds_cache[$fair_domain] = $cached;
            return $cached;
        }

        $sql = "
            SELECT
                f.id,
                f.fair_domain,
                fa_konf_name.data AS konf_name,
                fa_konf_title_pl.data AS konf_title_pl,
                fa_konf_title_en.data AS konf_title_en,
                fa_konf_desc_pl.data AS konf_desc_pl,
                fa_konf_desc_en.data AS konf_desc_en,
                fa_about_title_pl.data AS about_title_pl,
                fa_about_title_en.data AS about_title_en,
                fa_about_desc_pl.data AS about_desc_pl,
                fa_about_desc_en.data AS about_desc_en
            FROM fairs f
            LEFT JOIN fair_adds fa_konf_name      ON fa_konf_name.fair_id = f.id AND fa_konf_name.slug = 'konf_name'
            LEFT JOIN fair_adds fa_konf_title_pl  ON fa_konf_title_pl.fair_id = f.id AND fa_konf_title_pl.slug = 'konf_title_pl'
            LEFT JOIN fair_adds fa_konf_title_en  ON fa_konf_title_en.fair_id = f.id AND fa_konf_title_en.slug = 'konf_title_en'
            LEFT JOIN fair_adds fa_konf_desc_pl   ON fa_konf_desc_pl.fair_id = f.id AND fa_konf_desc_pl.slug = 'konf_desc_pl'
            LEFT JOIN fair_adds fa_konf_desc_en   ON fa_konf_desc_en.fair_id = f.id AND fa_konf_desc_en.slug = 'konf_desc_en'
            LEFT JOIN fair_adds fa_about_title_pl ON fa_about_title_pl.fair_id = f.id AND fa_about_title_pl.slug = 'about_title_pl'
            LEFT JOIN fair_adds fa_about_title_en ON fa_about_title_en.fair_id = f.id AND fa_about_title_en.slug = 'about_title_en'
            LEFT JOIN fair_adds fa_about_desc_pl  ON fa_about_desc_pl.fair_id = f.id AND fa_about_desc_pl.slug = 'about_desc_pl'
            LEFT JOIN fair_adds fa_about_desc_en  ON fa_about_desc_en.fair_id = f.id AND fa_about_desc_en.slug = 'about_desc_en'
            WHERE f.fair_domain = %s
        ";

        $results = $cap_db->get_results($cap_db->prepare($sql, $fair_domain));

        if ($cap_db->last_error) {
            self::$fairs_adds_cache[$fair_domain] = [];
            return [];
        }

        set_transient($transient_key, $results, 3600);
        self::$fairs_adds_cache[$fair_domain] = $results;

        return $results;
    }

    /**
     * Get fairs translations data
     */
    private static $translations_cache = [];
    public static function get_database_translations_data($fair_domain = null): array {
        $cache_key = $fair_domain ?? 'all';
        if (isset(self::$translations_cache[$cache_key])) {
            return self::$translations_cache[$cache_key];
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::$translations_cache[$cache_key] = [];
            return [];
        }

        $transient_key = 'pwe_translations_' . md5($cache_key);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$translations_cache[$cache_key] = $cached;
            return $cached;
        }

        $columns = "
            id,
            fair_domain,
            fair_name_pl,
            fair_name_en,
            fair_desc_pl,
            fair_desc_en,
            fair_short_desc_pl,
            fair_short_desc_en,
            fair_full_desc_pl,
            fair_full_desc_en
        ";

        $fairs = $fair_domain === null
            ? $cap_db->get_results("SELECT $columns FROM fairs")
            : $cap_db->get_results($cap_db->prepare("SELECT $columns FROM fairs WHERE fair_domain = %s", $fair_domain));

        $translations = $cap_db->get_results("SELECT * FROM translations");

        // Map translations
        $translations_map = [];
        foreach ($translations as $tr) {
            $fair_id = $tr->fair_id;
            $lang = strtolower($tr->language);
            $data = json_decode($tr->translation, true);
            if ($data) $translations_map[$fair_id][$lang] = $data;
        }

        $results = [];
        foreach ($fairs as $fair) {
            $row = [
                'fair_domain' => $fair->fair_domain,
                'fair_name_pl' => $fair->fair_name_pl,
                'fair_name_en' => $fair->fair_name_en,
                'fair_desc_pl' => $fair->fair_desc_pl,
                'fair_desc_en' => $fair->fair_desc_en,
                'fair_short_desc_pl' => $fair->fair_short_desc_pl,
                'fair_short_desc_en' => $fair->fair_short_desc_en,
                'fair_full_desc_pl' => $fair->fair_full_desc_pl,
                'fair_full_desc_en' => $fair->fair_full_desc_en,
            ];

            if (isset($translations_map[$fair->id])) {
                foreach ($translations_map[$fair->id] as $lang => $fields) {
                    if (isset($fields['fair_name'])) $row["fair_name_$lang"] = $fields['fair_name'];
                    if (isset($fields['fair_desc'])) $row["fair_desc_$lang"] = $fields['fair_desc'];
                    if (isset($fields['fair_short_desc'])) $row["fair_short_desc_$lang"] = $fields['fair_short_desc'];
                    if (isset($fields['fair_full_desc'])) $row["fair_full_desc_$lang"] = $fields['fair_full_desc'];
                }
            }

            $results[] = $row;
        }

        set_transient($transient_key, $results, 3600);
        self::$translations_cache[$cache_key] = $results;

        return $results;
    }

    /**
     * Get associates data
     */
    private static $associates_cache = [];
    public static function get_database_associates_data($fair_domain = null): array {
        $fair_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        if (isset(self::$associates_cache[$fair_domain])) {
            return self::$associates_cache[$fair_domain];
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::$associates_cache[$fair_domain] = [];
            return [];
        }

        $transient_key = 'pwe_associates_' . md5($fair_domain);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$associates_cache[$fair_domain] = $cached;
            return $cached;
        }

        $query = $cap_db->prepare("
            SELECT *
            FROM associates
            WHERE FIND_IN_SET(%s, fair_associates)
        ", $fair_domain);

        $results = $cap_db->get_results($query);

        if ($cap_db->last_error) {
            self::$associates_cache[$fair_domain] = [];
            return [];
        }

        set_transient($transient_key, $results, 3600);
        self::$associates_cache[$fair_domain] = $results;

        return $results;
    }

    /**
     * Get store data
     */
    private static $store_cache = null;
    public static function get_database_store_data(): array {
        if (self::$store_cache !== null) {
            return self::$store_cache;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::$store_cache = [];
            return [];
        }

        $transient_key = 'pwe_store_data';
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$store_cache = $cached;
            return $cached;
        }

        $results = $cap_db->get_results("SELECT * FROM shop");

        if ($cap_db->last_error) {
            self::$store_cache = [];
            return [];
        }

        set_transient($transient_key, $results, 3600);
        self::$store_cache = $results;

        return $results;
    }

    /**
     * Get store packages data
     */
    private static $store_packages_cache = null;
    public static function get_database_store_packages_data(): array {
        if (self::$store_packages_cache !== null) {
            return self::$store_packages_cache;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::$store_packages_cache = [];
            return [];
        }

        $transient_key = 'pwe_store_packages';
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$store_packages_cache = $cached;
            return $cached;
        }

        $results = $cap_db->get_results("SELECT * FROM shop_packs");

        if ($cap_db->last_error) {
            self::$store_packages_cache = [];
            return [];
        }

        set_transient($transient_key, $results, 3600);
        self::$store_packages_cache = $results;

        return $results;
    }

    /**
     * Get meta data
     */
    private static $meta_cache = [];
    public static function get_database_meta_data($data_id = null) {
        $domain = $_SERVER['HTTP_HOST'] ?? '';
        $domain = preg_replace('/:\d+$/', '', $domain);
        $cache_key = $data_id . '_' . $domain;

        if (isset(self::$meta_cache[$cache_key])) {
            return self::$meta_cache[$cache_key];
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::$meta_cache[$cache_key] = [];
            return [];
        }

        $transient_key = 'pwe_meta_' . md5($cache_key);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$meta_cache[$cache_key] = $cached;
            return $cached;
        }

        if ($data_id === null) {
            $results = $cap_db->get_results("SELECT * FROM meta_data");
        } elseif ($data_id === 'header_order') {
            $query = "
                SELECT m.meta_data
                FROM meta_data AS m
                INNER JOIN fairs AS f ON m.rights = f.id
                WHERE m.slug = 'header_order'
                AND f.fair_domain = %s
            ";
            $results = $cap_db->get_results($cap_db->prepare($query, $domain));
        } else {
            $results = $cap_db->get_var(
                $cap_db->prepare("SELECT meta_data FROM meta_data WHERE slug = %s", $data_id)
            );
        }

        if ($cap_db->last_error) {
            self::$meta_cache[$cache_key] = [];
            return [];
        }

        set_transient($transient_key, $results, 3600);
        self::$meta_cache[$cache_key] = $results;

        return $results;
    }

    /**
     * Get group contacts data
     */
    private static $groups_contacts_cache = null;
    public static function get_database_groups_contacts_data() {

        if (self::$groups_contacts_cache !== null) {
            return self::$groups_contacts_cache;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) return [];

        $transient_key = 'pwe_groups_contacts';
        $cached = get_transient($transient_key);

        if ($cached !== false) {
            self::$groups_contacts_cache = $cached;
            return $cached;
        }

        $results = $cap_db->get_results("SELECT * FROM groups");

        if ($cap_db->last_error) return [];

        set_transient($transient_key, $results, 3600);
        self::$groups_contacts_cache = $results;

        return $results;
    }

    /**
     * Get group callcenter data
     */
    private static $groups_callcenter_cache = null;
    public static function get_database_groups_callcenter_data(): array {
        if (self::$groups_callcenter_cache !== null) {
            return self::$groups_callcenter_cache;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::$groups_callcenter_cache = [];
            return [];
        }

        $transient_key = 'pwe_groups_callcenter';
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$groups_callcenter_cache = $cached;
            return $cached;
        }

        $results = $cap_db->get_results("SELECT * FROM form_senders");

        if ($cap_db->last_error) {
            self::$groups_callcenter_cache = [];
            return [];
        }

        set_transient($transient_key, $results, 3600);
        self::$groups_callcenter_cache = $results;

        return $results;
    }

    /**
     * Get groups data
     */
    private static $groups_cache = null;
    public static function get_database_groups_data() {

        if (self::$groups_cache !== null) {
            return self::$groups_cache;
        }

        $transient_key = 'pwe_groups';
        $cached = get_transient($transient_key);

        if ($cached !== false) {
            self::$groups_cache = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) return [];

        $results = $cap_db->get_results("SELECT fair_domain, fair_group FROM fairs");

        if ($cap_db->last_error) return [];

        set_transient($transient_key, $results, 3600);

        self::$groups_cache = $results;

        return $results;
    }

    /**
     * Get weeks data
     */
    private static $week_data_cache = [];
    public static function get_database_week_data($fair_domain = null): array {
        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        if (isset(self::$week_data_cache[$current_domain])) {
            return self::$week_data_cache[$current_domain];
        }

        $cap_db = self::connect_database();
        if (!$cap_db) return [];

        $transient_key = 'pwe_week_data_' . md5($current_domain);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$week_data_cache[$current_domain] = $cached;
            return $cached;
        }

        $week = $cap_db->get_row(
            $cap_db->prepare("SELECT fairs_domains FROM fair_weeks WHERE week_domain = %s LIMIT 1", $current_domain)
        );

        $results = [];
        if ($week && !empty($week->fairs_domains)) {
            $results = array_map('trim', explode(',', $week->fairs_domains));
        }

        set_transient($transient_key, $results, 3600);
        self::$week_data_cache[$current_domain] = $results;
        return $results;
    }

    /**
     * Get all weeks data
     */
    private static $week_all_cache = [];
    public static function get_database_week_all($fair_domain = null) {
        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        if (isset(self::$week_all_cache[$current_domain])) {
            return self::$week_all_cache[$current_domain];
        }

        $cap_db = self::connect_database();
        if (!$cap_db) return null;

        $transient_key = 'pwe_week_all_' . md5($current_domain);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$week_all_cache[$current_domain] = $cached;
            return $cached;
        }

        $week = $cap_db->get_row(
            $cap_db->prepare("SELECT week_data FROM fair_weeks WHERE week_domain = %s LIMIT 1", $current_domain)
        );

        $results = null;
        if ($week && !empty($week->week_data)) {
            $decoded = json_decode($week->week_data, true);
            $results = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $week->week_data;
        }

        set_transient($transient_key, $results, 3600);
        self::$week_all_cache[$current_domain] = $results;
        return $results;
    }

    /**
     * Get all week domains data
     */
    private static $all_week_domains_cache = null;
    public static function get_all_week_domains(): array {
        if (self::$all_week_domains_cache !== null) return self::$all_week_domains_cache;

        $cap_db = self::connect_database();
        if (!$cap_db) return [];

        $transient_key = 'pwe_all_week_domains';
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$all_week_domains_cache = $cached;
            return $cached;
        }

        $rows = $cap_db->get_results("SELECT week_domain FROM fair_weeks");
        $domains = [];
        if (!empty($rows)) {
            foreach ($rows as $row) {
                if (!empty($row->week_domain)) $domains[] = trim($row->week_domain);
            }
        }

        $results = array_values(array_unique($domains));
        set_transient($transient_key, $results, 3600);
        self::$all_week_domains_cache = $results;
        return $results;
    }

    /**
     * Get logotypes data
     */
    private static $logotypes_cache = [];
    public static function get_database_logotypes_data($fair_domain = null): array {
        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        if (isset(self::$logotypes_cache[$current_domain])) {
            return self::$logotypes_cache[$current_domain];
        }

        $cap_db = self::connect_database();
        if (!$cap_db) return [];

        $transient_key = 'pwe_logotypes_' . md5($current_domain);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$logotypes_cache[$current_domain] = $cached;
            return $cached;
        }

        $week = $cap_db->get_row(
            $cap_db->prepare("SELECT fairs_domains FROM fair_weeks WHERE week_domain = %s LIMIT 1", $current_domain)
        );

        $results = [];
        if ($week && !empty($week->fairs_domains)) {
            $domains = json_decode($week->fairs_domains, true);
            if (!is_array($domains)) $domains = [];
            $domains = array_values(array_filter(array_map('trim', $domains)));

            if (!empty($domains)) {
                $placeholders = implode(',', array_fill(0, count($domains), '%s'));
                $query = "
                    SELECT DISTINCT logos.*, meta_data.meta_data AS meta_data
                    FROM logos
                    INNER JOIN fairs ON logos.fair_id = fairs.id
                    LEFT JOIN meta_data ON meta_data.slug = 'patrons'
                        AND JSON_UNQUOTE(JSON_EXTRACT(meta_data.meta_data, '$.slug')) = logos.logos_type
                    WHERE fairs.fair_domain IN ($placeholders)
                ";
                $results = $cap_db->get_results($cap_db->prepare($query, $domains));
            }
        } else {
            $query = "
                SELECT logos.*, meta_data.meta_data AS meta_data
                FROM logos
                INNER JOIN fairs ON logos.fair_id = fairs.id
                LEFT JOIN meta_data ON meta_data.slug = 'patrons'
                    AND JSON_UNQUOTE(JSON_EXTRACT(meta_data.meta_data, '$.slug')) = logos.logos_type
                WHERE fairs.fair_domain = %s
            ";
            $results = $cap_db->get_results($cap_db->prepare($query, $current_domain));
        }

        if ($cap_db->last_error) {
            self::$logotypes_cache[$current_domain] = [];
            return [];
        }

        $results = self::remove_logo_duplicates($results);
        set_transient($transient_key, $results, 3600);
        self::$logotypes_cache[$current_domain] = $results;
        return $results;
    }

    /**
     * Get conferences data
     */
    private static $conferences_cache = [];
    public static function get_database_conferences_data($domain = null): array {
        $domain = $domain ?? $_SERVER['HTTP_HOST'];
        if (isset(self::$conferences_cache[$domain])) return self::$conferences_cache[$domain];

        $cap_db = self::connect_database();
        if (!$cap_db) return [];

        $transient_key = 'pwe_conferences_' . md5($domain);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$conferences_cache[$domain] = $cached;
            return $cached;
        }

        $results = $cap_db->get_results(
            $cap_db->prepare(
                "SELECT * FROM conferences WHERE conf_site_link LIKE %s AND deleted_at IS NULL",
                '%' . $domain . '%'
            )
        );

        if ($cap_db->last_error) {
            self::$conferences_cache[$domain] = [];
            return [];
        }

        foreach ($results as &$row) {
            if (!empty($row->conf_data)) {
                $decoded = html_entity_decode($row->conf_data);
                $decoded = preg_replace_callback('/style="([^"]+)"/is', function ($match) {
                    $style = $match[1];
                    $style = preg_replace('/font-family\s*:\s*[^;"]+("[^"]+"[, ]*)*[^;"]*;?/i', '', $style);
                    $style = trim(preg_replace('/\s*;\s*/', '; ', $style), '; ');
                    return $style ? 'style="' . $style . '"' : '';
                }, $decoded);

                if (json_decode($decoded, true) !== null) {
                    $row->conf_data = $decoded;
                } else {
                    error_log("Error JSON in conf_data: " . json_last_error_msg());
                }
            }
        }

        set_transient($transient_key, $results, 3600);
        self::$conferences_cache[$domain] = $results;
        return $results;
    }

    /**
     * Get fair profiles data
     */
    private static $fairs_profiles_cache = [];
    public static function get_database_fairs_data_profiles($fair_domain = null): array {
        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        if (isset(self::$fairs_profiles_cache[$current_domain])) return self::$fairs_profiles_cache[$current_domain];

        $cap_db = self::connect_database();
        if (!$cap_db) return [];

        $transient_key = 'pwe_fairs_profiles_' . md5($current_domain);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$fairs_profiles_cache[$current_domain] = $cached;
            return $cached;
        }

        $sql = "
            SELECT f.id, f.fair_domain, fp.data
            FROM fairs f
            LEFT JOIN fair_profiles fp ON fp.fair_id = f.id AND fp.slug = f.fair_domain
        ";

        $results = $fair_domain
            ? $cap_db->get_results($cap_db->prepare($sql . " WHERE f.fair_domain = %s", $fair_domain))
            : $cap_db->get_results($sql);

        if ($cap_db->last_error) $results = [];

        set_transient($transient_key, $results, 3600);
        self::$fairs_profiles_cache[$current_domain] = $results;
        return $results;
    }

    /**
     * Get fair opinions data
     */
    private static $fairs_opinions_cache = [];
    public static function get_database_fairs_data_opinions($fair_domain = null): array {
        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        if (isset(self::$fairs_opinions_cache[$current_domain])) return self::$fairs_opinions_cache[$current_domain];

        $cap_db = self::connect_database();
        if (!$cap_db) return [];

        $transient_key = 'pwe_fairs_opinions_' . md5($current_domain);
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            self::$fairs_opinions_cache[$current_domain] = $cached;
            return $cached;
        }

        $sql = "
            SELECT f.id, f.fair_domain, fp.data, fp.slug, fp.order
            FROM fairs f
            LEFT JOIN fair_opinions fp ON fp.fair_id = f.id
        ";

        $results = $fair_domain
            ? $cap_db->get_results($cap_db->prepare($sql . " WHERE f.fair_domain = %s", $fair_domain))
            : $cap_db->get_results($sql);

        if ($cap_db->last_error) $results = [];

        set_transient($transient_key, $results, 3600);
        self::$fairs_opinions_cache[$current_domain] = $results;
        return $results;
    }

    private static function remove_logo_duplicates(array $logos): array {
        $unique = [];
        $seen = [];

        foreach ($logos as $logo) {
            if (empty($logo->logos_url)) {
                $unique[] = $logo;
                continue;
            }

            if (preg_match('#/partners/([^/]+)/#', $logo->logos_url, $m)) {
                $partner_type = $m[1];
            } else {
                $partner_type = 'unknown';
            }

            $filename = basename($logo->logos_url);
            $key = $partner_type . '|' . $filename;

            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $logo;
            }
        }

        return $unique;
    }

    /**
     * Colors (accent or main2)
     */
    public static function pwe_color($color) {
        $fair_colors = self::findPalletColorsStatic();
        $result_color = null;

        // Handling color 'accent'
        if (strtolower($color) === 'accent' && isset($fair_colors['Accent'])) {
            $result_color = $fair_colors['Accent'];
        }

        // Handling color 'main2'
        if (strtolower($color) === 'main2') {
            foreach ($fair_colors as $color_key => $color_value) {
                if (strpos(strtolower($color_key), 'main2') !== false) {
                    $result_color = $color_value;
                    break;
                }
            }
        }

        return $result_color;
    }

    public static function generate_fair_data($fair) {
        // Decode JSON estimations
        $estimations = !empty($fair->estimations) ? json_decode($fair->estimations, true) : [];

        $data = [
            "domain" => $fair->fair_domain,
            "date_start" => $fair->fair_date_start ?? "",
            "date_start_hour" => $fair->fair_date_start_hour ?? "",
            "date_end" => $fair->fair_date_end ?? "",
            "date_end_hour" => $fair->fair_date_end_hour ?? "",
            "edition" => $fair->fair_edition ?? "",
            "name_pl" => $fair->fair_name_pl ?? "",
            "name_en" => $fair->fair_name_en ?? "",
            "desc_pl" => $fair->fair_desc_pl ?? "",
            "desc_en" => $fair->fair_desc_en ?? "",
            "id" => $fair->id ?? "",
            "short_desc_pl" => $fair->fair_short_desc_pl ?? "",
            "short_desc_en" => $fair->fair_short_desc_en ?? "",
            "full_desc_pl" => $fair->fair_full_desc_pl ?? "",
            "full_desc_en" => $fair->fair_full_desc_en ?? "",
            "visitors" => $fair->fair_visitors ?? "",
            "exhibitors" => $fair->fair_exhibitors ?? "",
            "countries" => $fair->fair_countries ?? "",
            "area" => $fair->fair_area ?? "",
            "color_accent" => $fair->fair_color_accent ?? "",
            "color_main2" => $fair->fair_color_main2 ?? "",
            "hall" => $fair->fair_hall ?? "",
            "facebook" => $fair->fair_facebook ?? "",
            "instagram" => $fair->fair_instagram ?? "",
            "linkedin" => $fair->fair_linkedin ?? "",
            "youtube" => $fair->fair_youtube ?? "",
            "badge" => $fair->fair_badge ?? "",
            "catalog" => $fair->fair_kw ?? "",
            "catalog_id" => $fair->fair_kw_new ?? "",
            "catalog_archive" => $fair->fair_kw_old_arch ?? "",
            "catalog_id_archive" => $fair->fair_kw_new_arch ?? "",
            "shop" => $fair->fair_shop ?? "",
            "category_pl" => $fair->category_pl ?? "",
            "category_en" => $fair->category_en ?? "",
            "conference_name" => $fair->konf_name ?? "",
            "conference_title_pl" => $fair->konf_title_pl ?? "",
            "conference_title_en" => $fair->konf_title_en ?? "",
            "group" => $fair->fair_group ?? "",
        ];

        // Add estimations to data
        if (!empty($estimations)) {
            foreach ($estimations as $key => $val) {
                $data[$key] = $val;
            }
        }

        return $data;
    }

    public static function generate_fair_translation_data($fair) {
        return [
            "domain" => $fair["fair_domain"],
            "name_cs" => $fair["fair_name_cs"] ?? "",
            "desc_cs" => $fair["fair_desc_cs"] ?? "",
            "short_desc_cs" => $fair["fair_short_desc_cs"] ?? "",
            "full_desc_cs" => $fair["fair_full_desc_cs"] ?? "",
            "name_de" => $fair["fair_name_de"] ?? "",
            "desc_de" => $fair["fair_desc_de"] ?? "",
            "short_desc_de" => $fair["fair_short_desc_de"] ?? "",
            "full_desc_de" => $fair["fair_full_desc_de"] ?? "",
            "name_lt" => $fair["fair_name_lt"] ?? "",
            "desc_lt" => $fair["fair_desc_lt"] ?? "",
            "short_desc_lt" => $fair["fair_short_desc_lt"] ?? "",
            "full_desc_lt" => $fair["fair_full_desc_lt"] ?? "",
            "name_lv" => $fair["fair_name_lv"] ?? "",
            "desc_lv" => $fair["fair_desc_lv"] ?? "",
            "short_desc_lv" => $fair["fair_short_desc_lv"] ?? "",
            "full_desc_lv" => $fair["fair_full_desc_lv"] ?? "",
            "name_ru" => $fair["fair_name_ru"] ?? "",
            "desc_ru" => $fair["fair_desc_ru"] ?? "",
            "short_desc_ru" => $fair["fair_short_desc_ru"] ?? "",
            "full_desc_ru" => $fair["fair_full_desc_ru"] ?? "",
            "name_sk" => $fair["fair_name_sk"] ?? "",
            "desc_sk" => $fair["fair_desc_sk"] ?? "",
            "short_desc_sk" => $fair["fair_short_desc_sk"] ?? "",
            "full_desc_sk" => $fair["fair_full_desc_sk"] ?? "",
            "name_uk" => $fair["fair_name_uk"] ?? "",
            "desc_uk" => $fair["fair_desc_uk"] ?? "",
            "short_desc_uk" => $fair["fair_short_desc_uk"] ?? "",
            "full_desc_uk" => $fair["fair_full_desc_uk"] ?? ""
        ];
    }

    /**
     * JSON all trade fairs
     */
    public static function json_fairs() {
        static $runtime_cache = null;

        if ($runtime_cache !== null) {
            return $runtime_cache;
        }

        $pwe_fairs = self::get_database_fairs_data();
        $pwe_fairs_desc_translations = self::get_database_translations_data();

        static $console_logged = false;

        // Check if data is already in global variable
        if (!empty($pwe_fairs) && is_array($pwe_fairs)) {
            global $fairs_data;
            $fairs_data = ["fairs" => []];

            // Add data about the fair
            foreach ($pwe_fairs as $fair) {
                if (!isset($fair->fair_domain) || empty($fair->fair_domain)) {
                    continue;
                }

                $domain = $fair->fair_domain;

                // Save data about the fair in the table
                $fairs_data["fairs"][$domain] = self::generate_fair_data($fair);
            }

            // Add translations to the fair data
            foreach ($pwe_fairs_desc_translations as $translation) {
                if (!isset($translation['fair_domain']) || empty($translation['fair_domain'])) {
                    continue;
                }

                $domain = $translation['fair_domain'];

                $translation_data = self::generate_fair_translation_data($translation);

                // Merge data
                if (isset($fairs_data["fairs"][$domain])) {
                    $fairs_data["fairs"][$domain] = array_merge(
                        $fairs_data["fairs"][$domain],
                        $translation_data
                    );
                }
            }

            // $current_user = wp_get_current_user();
            // if ($current_user && $current_user->user_login === 'Anton') {
            //     var_dump($fairs_data);
            // }
        } else {
            // URL to JSON file
            $json_file = 'https://mr.glasstec.pl/doc/pwe-data.json';

            // Getting data from JSON file
            $json_data = @file_get_contents($json_file); // Use @ to ignore PHP warnings on failure

            // Checking if data has been downloaded
            if ($json_data === false) {
                if (current_user_can("administrator") && !is_admin()) {
                    echo '<script>console.error("Failed to fetch data from JSON file: ' . $json_file . '")</script>';
                }
                return null;
            }

            global $fairs_data;
            // Decoding JSON data
            $fairs_data = json_decode($json_data, true);

            // Checking JSON decoding correctness
            if (json_last_error() !== JSON_ERROR_NONE) {
                if (current_user_can("administrator") && !is_admin()) {
                    echo '<script>console.error("Error decoding JSON: ' . json_last_error_msg() . '")</script>';
                }
                return null;
            }

            // Checking if the data has the correct structure
            if (!isset($fairs_data['fairs']) || !is_array($fairs_data['fairs'])) {
                if (current_user_can("administrator") && !is_admin()) {
                    echo '<script>console.error("Invalid fairs data format in JSON file.")</script>';
                }
                return null;
            }

            if (!$console_logged) {
                if (current_user_can("administrator") && !is_admin()) {
                    echo '<script>console.error("Brak danych o targach w globalnej zmiennej (dane CAP DB), dane są pobrane z pwe-data.json")</script>';
                }
                $console_logged = true;
            }
        }

        $runtime_cache = $fairs_data['fairs'];
        return $runtime_cache;
    }

    /**
     * Function to transform the date
     */
    public static function transform_dates($start_date, $end_date, $include_hours = true) {
        $format = $include_hours ? "Y/m/d H:i" : "Y/m/d";

        // Convert date strings to DateTime objects
        $start_date_obj = DateTime::createFromFormat($format, $start_date);
        $end_date_obj = DateTime::createFromFormat($format, $end_date);

        // Check if the conversion was correct
        if ($start_date_obj && $end_date_obj) {
            // Get the day, month and year from DateTime objects
            $start_day = $start_date_obj->format("d");
            $end_day = $end_date_obj->format("d");
            $start_month = $start_date_obj->format("m");
            $end_month = $end_date_obj->format("m");
            $year = $start_date_obj->format("Y");

            // Check if months are the same
            if ($start_month === $end_month) {
                $formatted_date = "{$start_day}-{$end_day}|{$start_month}|{$year}";
            } else {
                $formatted_date = "{$start_day}|{$start_month}-{$end_day}|{$end_month}|{$year}";
            }

            return $formatted_date;
        } else {
            return "Invalid dates";
        }
    }


    /**
     * Decoding Base64
     * Decoding URL
     * Remowe wpautop
     */
    public static function decode_clean_content($encoded_content) {
        $decoded_content = wpb_js_remove_wpautop(urldecode(base64_decode($encoded_content)), true);
        return $decoded_content;
    }

    /**
     * Decodes URL-encoded string
     * Decodes a JSON string
     */
    public static function json_decode($encoded_variable) {
        $encoded_variable_urldecode = urldecode($encoded_variable);
        $encoded_variable_json = json_decode($encoded_variable_urldecode, true);
        return $encoded_variable_json;
    }

    /**
     * Adding colors
     */
    public static function findColor($primary, $secondary, $default = '') {
        if($primary != '') {
            return $primary;
        } elseif ($secondary != '') {
            return $secondary;
        } else {
            return $default;
        }
    }

    /**
     * Finding preset colors pallet.
     *
     * @return array
     */
    public static function findPalletColorsStatic() {
        $uncode_options = get_option('uncode');
        $accent_uncode_color = $uncode_options["_uncode_accent_color"];
        $custom_element_colors = array();

        if (isset($uncode_options["_uncode_custom_colors_list"]) && is_array($uncode_options["_uncode_custom_colors_list"])) {
            $custom_colors_list = $uncode_options["_uncode_custom_colors_list"];

            foreach ($custom_colors_list as $color) {
                $title = $color['title'];
                $color_value = $color["_uncode_custom_color"];
                $color_id = $color["_uncode_custom_color_unique_id"];

                if ($accent_uncode_color != $color_id) {
                    $custom_element_colors[$title] = $color_value;
                } else {
                    $accent_color_value = $color_value;
                    $custom_element_colors = array_merge(array('Accent' => $accent_color_value), $custom_element_colors);
                }
            }
            $custom_element_colors = array_merge(array('Default' => ''), $custom_element_colors);
        }
        return $custom_element_colors;
    }

    /**
     * Finding preset colors pallet.
     *
     * @return array
     */
    public function findPalletColors() {
        $uncode_options = get_option('uncode');
        $accent_uncode_color = $uncode_options["_uncode_accent_color"];
        $custom_element_colors = array();

        if (isset($uncode_options["_uncode_custom_colors_list"]) && is_array($uncode_options["_uncode_custom_colors_list"])) {
            $custom_colors_list = $uncode_options["_uncode_custom_colors_list"];

            foreach ($custom_colors_list as $color) {
                $title = $color['title'];
                $color_value = $color["_uncode_custom_color"];
                $color_id = $color["_uncode_custom_color_unique_id"];

                if ($accent_uncode_color != $color_id) {
                    $custom_element_colors[$title] = $color_value;
                } else {
                    $accent_color_value = $color_value;
                    $custom_element_colors = array_merge(array('Accent' => $accent_color_value), $custom_element_colors);
                }
            }
            $custom_element_colors = array_merge(array('Default' => ''), $custom_element_colors);
        }
        return $custom_element_colors;
    }

    /**
     * Checking if the location is PL
     *
     * @return bool
     */
    public static function lang_pl() {
        return get_locale() == 'pl_PL';
    }

    /**
     * Laguage check for text
     *
     * @param string $pl text in Polish.
     * @param string $pl text in English.
     * @return string
     */
    public static function languageChecker($pl, $en = '', $de = '') {
        if (get_locale() == 'pl_PL') {
            return $pl;
        } else if (get_locale() == 'en_US') {
            return $en;
        } else {
            return $de;
        }

    }

    /**
     * Function to change color brightness (taking color in hex format)
     *
     * @return array
     */
    public static function adjustBrightness($hex, $steps) {
        // Convert hex to RGB
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        // Shift RGB values
        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        // Convert RGB back to hex
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
                . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
                . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    /**
     * Finding all GF forms
     *
     * @return array
     */
    public function findFormsGF($mode = ''){
        $pwe_forms_array = array();
        if (is_admin()) {
            if (method_exists('GFAPI', 'get_forms')) {
                $pwe_forms = GFAPI::get_forms();
                if($mode == 'id'){
                    foreach ($pwe_forms as $form) {
                        $pwe_forms_array[$form['title']] = $form['id'];
                    }
                } else {
                    foreach ($pwe_forms as $form) {
                        $pwe_forms_array[$form['id']] = $form['title'];
                    }
                }
            }
        }
        return $pwe_forms_array;
    }

    /**
     * Finding all target form id
     *
     * @param string $form_name
     * @return string
     */
    public static function findFormsID($form_name) {
        $pwe_form_id = '';
        if (method_exists('GFAPI', 'get_forms')) {
            $pwe_forms = GFAPI::get_forms();
            foreach ($pwe_forms as $form) {
                if ($form['title'] === $form_name) {
                    $pwe_form_id = $form['id'];
                    break;
                }
            }
        }
        return $pwe_form_id;
    }

    /**
     * Mobile displayer check
     *
     * @return bool
     */
    public static function checkForMobile(){
        return (preg_match('/Mobile|Android|iPhone/i', $_SERVER['HTTP_USER_AGENT']));
    }

    /**
     * Laguage check for text
     *
     * @param bool $logo_color schould logo be in color.
     * @return string
     */
    public static function findBestLogo($logo_color = false) {
        $filePaths = array(
            '/doc/logo-color-en.webp',
            '/doc/logo-color-en.png',
            '/doc/logo-color.webp',
            '/doc/logo-color.png',
            '/doc/logo-en.webp',
            '/doc/logo-en.png',
            '/doc/logo.webp',
            '/doc/logo.png'
        );

        switch (true){
            case(get_locale() == 'pl_PL'):
                if($logo_color){
                    foreach ($filePaths as $path) {
                        if (strpos($path, '-en.') === false && file_exists(ABSPATH . $path)) {
                            return '<img src="' . $path . '"/>';
                        }
                    }
                } else {
                    foreach ($filePaths as $path) {
                        if ( strpos($path, 'color') === false && strpos($path, '-en.') === false && file_exists(ABSPATH . $path)) {
                            return '<img src="' . $path . '"/>';
                        }
                    }
                }
                break;

            case(get_locale() == 'en_US'):
                if($logo_color){
                    foreach ($filePaths as $path) {
                        if (file_exists(ABSPATH . $path)) {
                            return '<img src="' . $path . '"/>';
                        }
                    }
                } else {
                    foreach ($filePaths as $path) {
                        if (strpos($path, 'color') === false && file_exists(ABSPATH . $path)) {
                            return '<img src="' . $path . '"/>';
                        }
                    }
                }
                break;
        }
    }

    /**
     * Finding URL of all images based on katalog
     */
    public static function findAllImages($firstPath, $image_count = false, $secondPath = '/doc/galeria') {
        $firstPath = $_SERVER['DOCUMENT_ROOT'] . $firstPath;

        if (is_dir($firstPath) && !empty(glob($firstPath . '/*.{jpeg,jpg,png,webp,svg,JPEG,JPG,PNG,WEBP}', GLOB_BRACE))) {
            $exhibitorsImages = glob($firstPath . '/*.{jpeg,jpg,png,webp,svg,JPEG,JPG,PNG,WEBP}', GLOB_BRACE);
        } else {
            $secondPath = $_SERVER['DOCUMENT_ROOT'] . $secondPath;
            $exhibitorsImages = glob($secondPath . '/*.{jpeg,jpg,png,webp,svg,JPEG,JPG,PNG,WEBP}', GLOB_BRACE);
        }
        $count = 0;
        foreach($exhibitorsImages as $image){
            if($image_count != false && $count >= $image_count){
                break;
            } else {
                $exhibitors_path[] = substr($image, strpos($image, '/doc/'));
                $count++;
            }
        }

        return $exhibitors_path;
    }

    /**
     * Laguage check for text
     *
     * @param bool $logo_color schould logo be in color.
     * @return string
     */
    public static function findBestFile($file_path) {
        $filePaths = array(
            '.webp',
            '.jpg',
            '.png'
        );

        foreach($filePaths as $com){
            if(file_exists(ABSPATH . $file_path . $com)) {
                return $file_path . $com;
            }
        }
    }

    /**
     * Trade fair date existance check
     *
     * @return bool
     */
    public static function isTradeDateExist() {

        $seasons = ["nowa data", "wiosna", "lato", "jesień", "zima", "new date", "spring", "summer", "autumn", "winter"];
        $trade_date_lower = strtolower(do_shortcode('[trade_fair_date]'));

        // Przeszukiwanie tablicy w poszukiwaniu pasującego sezonu
        foreach ($seasons as $season) {
            if (strpos($trade_date_lower, strtolower($season)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Adding element input[type="range"]
     */
    public static function inputRange() {
        if ( function_exists( 'vc_add_shortcode_param' ) ) {
            vc_add_shortcode_param( 'input_range', array('PWEHeader', 'input_range_field_html') );
        }
    }
    public static function input_range_field_html( $settings, $value ) {
        $id = uniqid('range_');
        return '<div class="pwe-input-range">'
            . '<input type="range" '
            . 'id="' . esc_attr( $id ) . '" '
            . 'name="' . esc_attr( $settings['param_name'] ) . '" '
            . 'class="wpb_vc_param_value ' . esc_attr( $settings['param_name'] ) . ' ' . esc_attr( $settings['type'] ) . '_field" '
            . 'value="' . esc_attr( $value ) . '" '
            . 'min="' . esc_attr( $settings['min'] ) . '" '
            . 'max="' . esc_attr( $settings['max'] ) . '" '
            . 'step="' . esc_attr( $settings['step'] ) . '" '
            . 'oninput="document.getElementById(\'value_' . esc_attr( $id ) . '\').innerHTML = this.value" '
            . '/>'
            . '<span id="value_' . esc_attr( $id ) . '">' . esc_attr( $value ) . '</span>'
            . '</div>';
    }

    /**
     * Adding custom checkbox element
     */
    function pweCheckbox() {
        if (function_exists('vc_add_shortcode_param')) {
            vc_add_shortcode_param('pwe_checkbox', array('PWEHeader', 'pwe_checkbox_html'));
        }
    }
    /**
     * Generate HTML for custom checkbox
     */
    public static function pwe_checkbox_html($settings, $value) {
        $checked = $value === 'true' ? 'checked' : '';
        $id = uniqid('pwe_checkbox_');

        return '<div class="pwe-checkbox">'
            . '<input type="checkbox" '
            . 'id="' . esc_attr($id) . '" '
            . 'name="' . esc_attr($settings['param_name']) . '" '
            . 'class="wpb_vc_param_value ' . esc_attr($settings['param_name']) . ' ' . esc_attr($settings['type']) . '_field" '
            . 'value="'.$value.'" '
            . $checked
            . ' onclick="this.value = this.checked ? \'true\' : \'\';" />'
            . '</div>';
    }

}