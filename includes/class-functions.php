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
    private static $translation_cache = [];
    public static function multi_translation($key) {
        $ctx = self::$translation_context;

        if (empty($ctx['element_slug']) || empty($ctx['group'])) {
            return $key;
        }

        $locale = get_locale();

        // Cache key for this file
        $cache_key = $ctx['element_type'] . '/' . $ctx['element_slug'] . '/' . $ctx['group'] . '.json';

        // If not loaded yet, load it
        if (!isset(self::$translation_cache[$cache_key])) {

            $translations_file = plugin_dir_path(__DIR__) .
                'translations/elements/' . $cache_key;

            if (file_exists($translations_file)) {
                $json = file_get_contents($translations_file);
                $data = json_decode($json, true);
                self::$translation_cache[$cache_key] = is_array($data) ? $data : [];
            } else {
                // If the file is missing, the cache is empty
                self::$translation_cache[$cache_key] = [];
            }
        }

        // Download from the cache
        $translations_data = self::$translation_cache[$cache_key];

        // Choosing the right map
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
    // Functions from plugin PWElements 3.3.0 <========================================================>
    // <============================================================================================>


    /**
     * Random number
     */
    public static function id_rnd() {
        $id_rnd = rand(10000, 99999);
        return $id_rnd;
    }

    /**
     * Add logs to uploads/logs/{$filename}.log
     */
    public static function add_log($message, $filename = 'logs') {
        $upload_dir = wp_upload_dir();
        $dir = $upload_dir['basedir'] . '/logs';
        $file = $dir . '/'. $filename .'.log';

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $line = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;

        file_put_contents($file, $line, FILE_APPEND);
    }

    /**
     * Collecting all logs
     */
    private static $debug_logs = [];
    private static function debug_log($message, $type = 'log') {

        if (!function_exists('wp_get_current_user')) {
            return;
        }

        if (!current_user_can('administrator') || is_admin()) {
            return;
        }

        self::$debug_logs[] = [
            'type' => $type,
            'message' => $message
        ];
    }

    /**
     * Output console logs 
     */
    public static function output_db_connection_logs() {

        if (empty(self::$debug_logs)) {
            return;
        }

        echo '<script>';
        echo 'console.groupCollapsed("DB CONNECTIONS ('. self::class .')");';

        foreach (self::$debug_logs as $log) {
            $msg = addslashes($log['message']);
            echo "console.{$log['type']}('{$msg}');";
        }

        echo 'console.groupEnd();';
        echo '</script>';
    }

    /**
     * Returning server hosts
     */
    private static function resolve_server_addr_fallback() {
        $host = php_uname('n');
            switch ($host) {
            case 'dedyk93.cyber-folks.pl': return '94.152.206.93';
            case 'dedyk180.cyber-folks.pl': return '94.152.207.180';
            case 'dedyk239.cyber-folks.pl': return '91.225.28.47';
            case 'dedyk1072.cyber-folks.pl': return '91.225.28.72';
            default: return '';
        }
    }

    /**
     * List of DB servers
     */
    private static function get_database_servers() {

        // All PWE servers
        $servers = [
            [
                'host'=>'dedyk93.cyber-folks.pl',
                'name'=> defined('PWE_DB_NAME_93') ? PWE_DB_NAME_93 : null,
                'user'=> defined('PWE_DB_USER_93') ? PWE_DB_USER_93 : null,
                'pass'=> defined('PWE_DB_PASSWORD_93') ? PWE_DB_PASSWORD_93 : null,
                'ip'  => '94.152.206.93'
            ],
            [
                'host'=>'dedyk180.cyber-folks.pl',
                'name'=> defined('PWE_DB_NAME_180') ? PWE_DB_NAME_180 : null,
                'user'=> defined('PWE_DB_USER_180') ? PWE_DB_USER_180 : null,
                'pass'=> defined('PWE_DB_PASSWORD_180') ? PWE_DB_PASSWORD_180 : null,
                'ip'  => '94.152.207.180'
            ],
            [
                'host'=>'dedyk239.cyber-folks.pl',
                'name'=> defined('PWE_DB_NAME_239') ? PWE_DB_NAME_239 : null,
                'user'=> defined('PWE_DB_USER_239') ? PWE_DB_USER_239 : null,
                'pass'=> defined('PWE_DB_PASSWORD_239') ? PWE_DB_PASSWORD_239 : null,
                'ip'  => '91.225.28.47'
            ],
            [
                'host'=>'dedyk1072.cyber-folks.pl',
                'name'=> defined('PWE_DB_NAME_1072') ? PWE_DB_NAME_1072 : null,
                'user'=> defined('PWE_DB_USER_1072') ? PWE_DB_USER_1072 : null,
                'pass'=> defined('PWE_DB_PASSWORD_1072') ? PWE_DB_PASSWORD_1072 : null,
                'ip'  => '91.225.28.72'
            ],
        ];

        // CRON fallback
        if (empty($_SERVER['SERVER_ADDR'])) {
            $_SERVER['SERVER_ADDR'] = self::resolve_server_addr_fallback();
        }

        $current_ip = $_SERVER['SERVER_ADDR'];

        // Finding which host is localhost
        foreach ($servers as &$server) {
            if ($server['ip'] === $current_ip) {
                $server['host'] = 'localhost';
                $server['is_local'] = true;
            } else {
                $server['is_local'] = false;
            }
        }
        unset($server);

        return $servers;
    }

    /**
     * Connecting to CAP database
     */
    private static $cached_db_connection = null;
    public static function connect_database() {

        // Return cached connection if already connected
        if (self::$cached_db_connection !== null) {
            if (self::$cached_db_connection->dbh) {
                return self::$cached_db_connection;
            }
        }

        $servers = self::get_database_servers();

        // Timeout filter (only once)
        static $timeout_filter_added = false;
        if (!$timeout_filter_added) {
            add_filter('wpdb_connect_timeout', [self::class, 'set_db_timeout']);
            $timeout_filter_added = true;
        }

        // Find local server
        $is_local_server = false;
        foreach ($servers as $s) {
            if (!empty($s['is_local'])) {
                $is_local_server = true;
                break;
            }
        }

        // If running on local server, only use that one
        if ($is_local_server) {
            $servers = array_values(array_filter($servers, function ($s) {
                return !empty($s['is_local']);
            }));
        }

        // Local in-request blocking (no transient)
        static $blocked_hosts = [];

        foreach ($servers as $server) {

            if (empty($server['user']) || empty($server['pass']) || empty($server['name'])) {
                continue;
            }

            $host = $server['host'] ?? 'localhost';

            // Skip if already marked as blocked in THIS request
            if (!empty($blocked_hosts[$host])) {
                continue;
            }

            // Try connecting
            $wpdb = new wpdb($server['user'], $server['pass'], $server['name'], $host);

            if (!$wpdb->dbh) {

                error_log("PWE DB: Cannot connect to $host (immediate block).");
                error_log(json_encode([
                    'error' => mysqli_connect_error(),
                    'host'  => $host,
                    'db'    => $server['name']
                ], JSON_UNESCAPED_UNICODE));

                self::add_log('[PWE_Functions] DB CONNECTION FAILED: Host: ' . $host . ' User: ' . $server['name'] . ' Error: ' . mysqli_connect_error(), 'db-connections');

                // Mark blocked for this request so next iterations skip it
                $blocked_hosts[$host] = true;
                continue;
            }

            // Test query
            $test = $wpdb->get_var("SELECT 1");
            if ((int)$test !== 1) {
                error_log("PWE DB: Test query failed on $host (immediate block).");
                $blocked_hosts[$host] = true;
                continue;
            }

            // Cache connection
            self::$cached_db_connection = $wpdb;
            return $wpdb;
        }

        // No server worked
        error_log('PWE DB: All connection attempts failed.');
        return false;
    }

    /**
     * Timeout filter for wpdb connection
     */
    public static function set_db_timeout() {
        return 2;
    }

    // DATABASE CONNECTIONS START <==================================================================================>

    /**
     * Get fairs data from CAP databases
     *
     * Modes:
     * - 'warsawexpo.eu' OR 'all' → return ALL fairs (no filters)
     * - get_database_fairs_data($domain) → fairs only for that domain
     * - get_database_fairs_data() → fairs within ±17 days window
     */
    private static $fairs_cache = [];
    public static function get_database_fairs_data($fair_domain = null): array {

        // Detect current domain
        $current_domain = $_SERVER['HTTP_HOST'] ?? '';

        // Cache key
        if ($current_domain === 'warsawexpo.eu' || $fair_domain === 'all') {
            $cache_key = 'all_fairs';
        } elseif ($fair_domain !== null) {
            $cache_key = $fair_domain;
        } else {
            $cache_key = 'month';
        }

        // Static cache
        if (isset(self::$fairs_cache[$cache_key])) {
            self::debug_log('get_database_fairs_data: data from STATIC → key=' . $cache_key);
            return self::$fairs_cache[$cache_key];
        }

        // Transient cache
        $transient_key = 'pwe_fairs_' . md5($cache_key);
        $cached = get_transient($transient_key);

        // Log timeout if transient exists
        $timeout = get_option('_transient_timeout_' . $transient_key);
        if ($timeout !== false) {
            $time_left = $timeout - time();
            $time_left_str = gmdate('H:i:s', max($time_left, 0));
        } else {
            $time_left_str = 'unknown';
        }

        if ($cached !== false) {
            self::debug_log('get_database_fairs_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
        }

        // Connect database
        $cap_db = self::connect_database();

        if (!$cap_db) {
            // DB not available → use last transient if exists, else empty
            if ($cached !== false) {

                // Extend transient by 10 minutes in emergency mode
                set_transient($transient_key, $cached, 600);

                self::debug_log('get_database_fairs_data: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');

                self::$fairs_cache[$cache_key] = $cached;
                return $cached;
            }

            // No transient available → return empty
            self::debug_log('get_database_fairs_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            error_log('get_database_fairs_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key);

            // CRON-safe: no wp_die()
            if (defined('DOING_CRON') && DOING_CRON) {
                return [];
            }

            // Frontend fallback → user-friendly 503
            wp_die(
                '<h1>Przepraszamy</h1><p>Trwają prace techniczne. Spróbuj ponownie później.</p>',
                'Strona tymczasowo niedostępna',
                ['response' => 503]
            );

            return [];
        }

        // Base SQL
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

                MAX(CASE WHEN fa.slug = 'category_pl' THEN fa.data END) AS category_pl,
                MAX(CASE WHEN fa.slug = 'category_en' THEN fa.data END) AS category_en,
                MAX(CASE WHEN fa.slug = 'konf_name' THEN fa.data END) AS konf_name,
                MAX(CASE WHEN fa.slug = 'konf_title_pl' THEN fa.data END) AS konf_title_pl,
                MAX(CASE WHEN fa.slug = 'konf_title_en' THEN fa.data END) AS konf_title_en,
                MAX(CASE WHEN fa.slug = 'fair_kw_new' THEN fa.data END) AS fair_kw_new,
                MAX(CASE WHEN fa.slug = 'fair_kw_old_arch' THEN fa.data END) AS fair_kw_old_arch,
                MAX(CASE WHEN fa.slug = 'fair_kw_new_arch' THEN fa.data END) AS fair_kw_new_arch,
                MAX(CASE WHEN fa.slug = 'fair_entrance' THEN fa.data END) AS fair_entrance

            FROM fairs f
            LEFT JOIN fair_adds fa
                ON fa.fair_id = f.id
                AND fa.slug IN (
                    'category_pl',
                    'category_en',
                    'konf_name',
                    'konf_title_pl',
                    'konf_title_en',
                    'fair_kw_new',
                    'fair_kw_old_arch',
                    'fair_kw_new_arch',
                    'fair_entrance'
                )
        ";

        // WHERE conditions
        $params = [];

        if ($current_domain === 'warsawexpo.eu' || $fair_domain === 'all') {
            // no WHERE
        } elseif ($fair_domain !== null) {
            $sql .= " WHERE f.fair_domain = %s ";
            $params[] = $fair_domain;
        } else {
            $current_fair = $cap_db->get_row(
                $cap_db->prepare(
                    "SELECT fair_date_start, fair_date_end
                    FROM fairs
                    WHERE fair_domain = %s
                    LIMIT 1",
                    $current_domain
                )
            );

            if ($current_fair && !empty($current_fair->fair_date_start) && !empty($current_fair->fair_date_end)) {
                $start = date('Y/m/d', strtotime($current_fair->fair_date_start . ' -17 days'));
                $end   = date('Y/m/d', strtotime($current_fair->fair_date_end . ' +17 days'));

                $sql .= "
                    WHERE f.fair_date_start >= %s
                    AND f.fair_date_end <= %s
                ";
                $params[] = $start;
                $params[] = $end;
            }
            // else → no WHERE
        }

        $sql .= " GROUP BY f.id ";

        $start_time = microtime(true);

        // Execute query
        if (!empty($params)) {
            $query = call_user_func_array([$cap_db, 'prepare'], array_merge([$sql], $params));
            $results = $cap_db->get_results($query);
        } else {
            $results = $cap_db->get_results($sql);
        }

        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_fairs_data: SQL error → '. addslashes($cap_db->last_error), 'error');
            // Use last transient if available
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$fairs_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$fairs_cache[$cache_key] = [];
            return [];
        }

        // Cache results for 10 minutes
        set_transient($transient_key, $results, 600);

        self::$fairs_cache[$cache_key] = $results;
        self::debug_log('get_database_fairs_data: data from database DIRECTLY (SQL time ' . $time . 'ms) → key=' . $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get fairs additional data from CAP databases
     */
    private static $fairs_adds_cache = [];
    public static function get_database_fairs_data_adds($fair_domain = null): array {

        // Resolve current domain
        $fair_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        $cache_key = $fair_domain;

        // STATIC cache
        if (isset(self::$fairs_adds_cache[$cache_key])) {
            self::debug_log('get_database_fairs_data_adds: data from STATIC → key='. $cache_key);
            return self::$fairs_adds_cache[$cache_key];
        }

        // Transient
        $transient_key = 'pwe_fairs_adds_' . md5($cache_key);
        $cached = get_transient($transient_key);

        // Log transient timeout
        $timeout = get_option('_transient_timeout_' . $transient_key);
        if ($timeout !== false) {
            $time_left = $timeout - time();
            $time_left_str = gmdate('H:i:s', max($time_left, 0));
        } else {
            $time_left_str = 'unknown';
        }

        if ($cached !== false) {
            self::debug_log('get_database_fairs_data_adds: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$fairs_adds_cache[$cache_key] = $cached;
            return $cached;
        }

        // Connect DB
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_fairs_data_adds: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$fairs_adds_cache[$cache_key] = $cached;
                return $cached;
            }
            self::debug_log('get_database_fairs_data_adds: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$fairs_adds_cache[$cache_key] = [];
            return [];
        }

        // SQL query
        $sql = "
            SELECT
                f.id,
                f.fair_domain,
                MAX(CASE WHEN fa.slug = 'konf_name' THEN fa.data END)       AS konf_name,
                MAX(CASE WHEN fa.slug = 'konf_title_pl' THEN fa.data END)   AS konf_title_pl,
                MAX(CASE WHEN fa.slug = 'konf_title_en' THEN fa.data END)   AS konf_title_en,
                MAX(CASE WHEN fa.slug = 'konf_desc_pl' THEN fa.data END)    AS konf_desc_pl,
                MAX(CASE WHEN fa.slug = 'konf_desc_en' THEN fa.data END)    AS konf_desc_en,
                MAX(CASE WHEN fa.slug = 'about_title_pl' THEN fa.data END)  AS about_title_pl,
                MAX(CASE WHEN fa.slug = 'about_title_en' THEN fa.data END)  AS about_title_en,
                MAX(CASE WHEN fa.slug = 'about_desc_pl' THEN fa.data END)   AS about_desc_pl,
                MAX(CASE WHEN fa.slug = 'about_desc_en' THEN fa.data END)   AS about_desc_en
            FROM fairs f
            LEFT JOIN fair_adds fa
                ON fa.fair_id = f.id
                AND fa.slug IN (
                    'konf_name','konf_title_pl','konf_title_en',
                    'konf_desc_pl','konf_desc_en','about_title_pl',
                    'about_title_en','about_desc_pl','about_desc_en'
                )
            WHERE f.fair_domain = %s
            GROUP BY f.id
        ";

        $start_time = microtime(true);

        $results = $cap_db->get_results($cap_db->prepare($sql, $fair_domain));

        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_fairs_data_adds: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$fairs_adds_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$fairs_adds_cache[$cache_key] = [];
            return [];
        }

        // Save transient + STATIC cache
        set_transient($transient_key, $results, 600);
        self::$fairs_adds_cache[$cache_key] = $results;
        self::debug_log('get_database_fairs_data_adds: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get translations data from CAP databases
     */
    private static $translations_cache = [];
    public static function get_database_translations_data($fair_domain = null): array {

        // Cache key
        $cache_key = $fair_domain ?? 'all';

        // STATIC cache
        if (isset(self::$translations_cache[$cache_key])) {
            self::debug_log('get_database_translations_data: STATIC → key='. $cache_key);
            return self::$translations_cache[$cache_key];
        }

        // Transient
        $transient_key = 'pwe_translations_' . md5($cache_key);
        $cached = get_transient($transient_key);

        // Log transient timeout
        $timeout = get_option('_transient_timeout_' . $transient_key);
        if ($timeout !== false) {
            $time_left = $timeout - time();
            $time_left_str = gmdate('H:i:s', max($time_left, 0));
        } else {
            $time_left_str = 'unknown';
        }

        if ($cached !== false) {
            self::debug_log('get_database_translations_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$translations_cache[$cache_key] = $cached;
            return $cached;
        }

        // Connect DB
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_translations_data: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$translations_cache[$cache_key] = $cached;
                return $cached;
            }
            self::debug_log('get_database_translations_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$translations_cache[$cache_key] = [];
            return [];
        }

        // SQL
        $sql = "
            SELECT
                f.id,
                f.fair_domain,
                f.fair_name_pl,
                f.fair_name_en,
                f.fair_desc_pl,
                f.fair_desc_en,
                f.fair_short_desc_pl,
                f.fair_short_desc_en,
                f.fair_full_desc_pl,
                f.fair_full_desc_en,
                t.language,
                t.translation
            FROM fairs f
            LEFT JOIN translations t
                ON t.fair_id = f.id
        ";

        $start_time = microtime(true);

        if ($fair_domain !== null) {
            $sql .= " WHERE f.fair_domain = %s";
            $rows = $cap_db->get_results($cap_db->prepare($sql, $fair_domain));
        } else {
            $rows = $cap_db->get_results($sql);
        }

        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_translations_data: SQL error: ' . addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$translations_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$translations_cache[$cache_key] = [];
            return [];
        }

        // Map results
        $results = [];
        foreach ($rows as $row) {
            $fair_id = $row->id;
            if (!isset($results[$fair_id])) {
                $results[$fair_id] = [
                    'fair_domain' => $row->fair_domain,
                    'fair_name_pl' => $row->fair_name_pl,
                    'fair_name_en' => $row->fair_name_en,
                    'fair_desc_pl' => $row->fair_desc_pl,
                    'fair_desc_en' => $row->fair_desc_en,
                    'fair_short_desc_pl' => $row->fair_short_desc_pl,
                    'fair_short_desc_en' => $row->fair_short_desc_en,
                    'fair_full_desc_pl' => $row->fair_full_desc_pl,
                    'fair_full_desc_en' => $row->fair_full_desc_en,
                ];
            }
            if (!empty($row->translation)) {
                $lang = strtolower($row->language);
                $data = json_decode($row->translation, true);
                if ($data) {
                    foreach (['fair_name','fair_desc','fair_short_desc','fair_full_desc'] as $field) {
                        if (isset($data[$field])) {
                            $results[$fair_id]["{$field}_$lang"] = $data[$field];
                        }
                    }
                }
            }
        }

        $results = array_values($results);

        // Save transient + STATIC cache
        set_transient($transient_key, $results, 600);
        self::$translations_cache[$cache_key] = $results;
        self::debug_log('get_database_translations_data: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get associates data from CAP databases
     */
    private static $associates_cache = [];
    public static function get_database_associates_data($fair_domain = null): array {

        // Resolve domain
        $fair_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        $cache_key = $fair_domain;

        // STATIC cache
        if (isset(self::$associates_cache[$cache_key])) {
            self::debug_log('get_database_associates_data: data from STATIC → key='. $cache_key);
            return self::$associates_cache[$cache_key];
        }

        // Transient
        $transient_key = 'pwe_associates_' . md5($cache_key);
        $cached = get_transient($transient_key);

        // Log transient timeout
        $timeout = get_option('_transient_timeout_' . $transient_key);
        if ($timeout !== false) {
            $time_left = $timeout - time();
            $time_left_str = gmdate('H:i:s', max($time_left, 0));
        } else {
            $time_left_str = 'unknown';
        }

        if ($cached !== false) {
            self::debug_log('get_database_associates_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$associates_cache[$cache_key] = $cached;
            return $cached;
        }

        // Connect DB
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_associates_data: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$associates_cache[$cache_key] = $cached;
                return $cached;
            }
            self::debug_log('get_database_associates_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$associates_cache[$cache_key] = [];
            return [];
        }

        // SQL query
        $query = $cap_db->prepare("SELECT * FROM associates WHERE FIND_IN_SET(%s, fair_associates)", $fair_domain);

        $start_time = microtime(true);
        $results = $cap_db->get_results($query);
        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_associates_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$associates_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$associates_cache[$cache_key] = [];
            return [];
        }

        // Save transient + STATIC cache
        set_transient($transient_key, $results, 600);
        self::$associates_cache[$cache_key] = $results;
        self::debug_log('get_database_associates_data: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get store data from CAP databases
     */
    private static $store_cache = null;
    public static function get_database_store_data(): array {

        $cache_key = 'store';

        // STATIC cache
        if (self::$store_cache !== null) {
            self::debug_log('get_database_store_data: data from STATIC memory');
            return self::$store_cache;
        }

        // Transient
        $transient_key = 'pwe_store_data';
        $cached = get_transient($transient_key);

        // Log transient timeout
        $timeout = get_option('_transient_timeout_' . $transient_key);
        if ($timeout !== false) {
            $time_left = $timeout - time();
            $time_left_str = gmdate('H:i:s', max($time_left, 0));
        } else {
            $time_left_str = 'unknown';
        }

        if ($cached !== false) {
            self::debug_log('get_database_store_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$store_cache = $cached;
            return $cached;
        }

        // Connect DB
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_store_data: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$store_cache = $cached;
                return $cached;
            }
            self::debug_log('get_database_store_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$store_cache = [];
            return [];
        }

        // SQL query
        $sql = "SELECT * FROM shop";
        $start_time = microtime(true);
        $results = $cap_db->get_results($sql);
        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_store_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$store_cache = $cached;
                return $cached;
            }
            self::$store_cache = [];
            return [];
        }

        // Save transient + STATIC cache
        set_transient($transient_key, $results, 600);
        self::$store_cache = $results;
        self::debug_log('get_database_store_data: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get store packages data from CAP databases
     */
    private static $store_packages_cache = null;
    public static function get_database_store_packages_data(): array {

        $cache_key = 'store_packages';

        // STATIC cache
        if (self::$store_packages_cache !== null) {
            self::debug_log('get_database_store_packages_data: data from STATIC memory');
            return self::$store_packages_cache;
        }

        // Transient
        $transient_key = 'pwe_store_packages';
        $cached = get_transient($transient_key);

        // Log transient timeout
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_store_packages_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$store_packages_cache = $cached;
            return $cached;
        }

        // Connect DB
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_store_packages_data: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$store_packages_cache = $cached;
                return $cached;
            }
            self::debug_log('get_database_store_packages_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$store_packages_cache = [];
            return [];
        }

        // SQL query
        $sql = "SELECT * FROM shop_packs";
        $start_time = microtime(true);
        $results = $cap_db->get_results($sql);
        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_store_packages_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$store_packages_cache = $cached;
                return $cached;
            }
            self::$store_packages_cache = [];
            return [];
        }

        // Save transient + STATIC cache
        set_transient($transient_key, $results, 600);
        self::$store_packages_cache = $results;
        self::debug_log('get_database_store_packages_data: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get meta data from CAP databases
     */
    private static $meta_cache = [];
    public static function get_database_meta_data($data_id = null, $domain = null) {

        $current_domain = $_SERVER['HTTP_HOST'] ?? '';
        $current_domain = preg_replace('/:\d+$/', '', $current_domain);
        $cache_key = $data_id . '_' . $current_domain;

        // STATIC cache
        if (isset(self::$meta_cache[$cache_key])) {
            self::debug_log('get_database_meta_data: data from STATIC → key='. $cache_key);
            return self::$meta_cache[$cache_key];
        }

        // Transient
        $transient_key = 'pwe_meta_' . md5($cache_key);
        $cached = get_transient($transient_key);

        // Log transient timeout
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_meta_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$meta_cache[$cache_key] = $cached;
            return $cached;
        }

        // Connect DB
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_meta_data: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$meta_cache[$cache_key] = $cached;
                return $cached;
            }
            self::debug_log('get_database_meta_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$meta_cache[$cache_key] = [];
            return [];
        }

        $start_time = microtime(true);

        // SQL query
        if ($data_id === null) {
            $results = $cap_db->get_results("SELECT * FROM meta_data");
        } elseif ($domain !== null) {
            $query = "
                SELECT m.meta_data
                FROM meta_data AS m
                INNER JOIN fairs AS f ON m.rights = f.id
                WHERE m.slug = %s
                AND f.fair_domain = %s
            ";
            $results = $cap_db->get_results(
                $cap_db->prepare($query, $data_id, $domain)
            );
        } else {
            $results = $cap_db->get_var(
                $cap_db->prepare("SELECT meta_data FROM meta_data WHERE slug = %s", $data_id)
            );
        }

        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_meta_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$meta_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$meta_cache[$cache_key] = [];
            return [];
        }

        // Save transient + STATIC cache
        set_transient($transient_key, $results, 600);
        self::$meta_cache[$cache_key] = $results;
        self::debug_log('get_database_meta_data: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get group contacts data from CAP databases
     */
    private static $groups_contacts_cache = null;
    public static function get_database_groups_contacts_data(): array {

        $cache_key = 'groups_contacts';

        // STATIC cache
        if (self::$groups_contacts_cache !== null) {
            self::debug_log('get_database_groups_contacts_data: data from STATIC memory');
            return self::$groups_contacts_cache;
        }

        $transient_key = 'pwe_groups_contacts';
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_groups_contacts_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$groups_contacts_cache = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_groups_contacts_data: NO DB → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$groups_contacts_cache = $cached;
                return $cached;
            }
            self::debug_log('get_database_groups_contacts_data: NO DB and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$groups_contacts_cache = [];
            return [];
        }

        $start_time = microtime(true);
        $results = $cap_db->get_results("SELECT * FROM groups");
        $time = round((microtime(true) - $start_time) * 1000, 2);

        if ($cap_db->last_error) {
            self::debug_log('get_database_groups_contacts_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$groups_contacts_cache = $cached;
                return $cached;
            }
            self::$groups_contacts_cache = [];
            return [];
        }

        set_transient($transient_key, $results, 600);
        self::$groups_contacts_cache = $results;
        self::debug_log('get_database_groups_contacts_data: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get group callcenter data from CAP databases
     */
    private static $groups_callcenter_cache = null;
    public static function get_database_groups_callcenter_data(): array {

        $cache_key = 'groups_callcenter';

        if (self::$groups_callcenter_cache !== null) {
            self::debug_log('get_database_groups_callcenter_data: data from STATIC memory');
            return self::$groups_callcenter_cache;
        }

        $transient_key = 'pwe_groups_callcenter';
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_groups_callcenter_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$groups_callcenter_cache = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_groups_callcenter_data: NO DB → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$groups_callcenter_cache = $cached;
                return $cached;
            }
            self::debug_log('get_database_groups_callcenter_data: NO DB and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$groups_callcenter_cache = [];
            return [];
        }

        $start_time = microtime(true);
        $results = $cap_db->get_results("SELECT * FROM form_senders");
        $time = round((microtime(true) - $start_time) * 1000, 2);

        if ($cap_db->last_error) {
            self::debug_log('get_database_groups_callcenter_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$groups_callcenter_cache = $cached;
                return $cached;
            }
            self::$groups_callcenter_cache = [];
            return [];
        }

        set_transient($transient_key, $results, 600);
        self::$groups_callcenter_cache = $results;
        self::debug_log('get_database_groups_callcenter_data: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get groups data from CAP databases
     */
    private static $groups_cache = null;
    public static function get_database_groups_data(): array {

        $cache_key = 'groups';

        if (self::$groups_cache !== null) {
            self::debug_log('get_database_groups_data: data from STATIC memory');
            return self::$groups_cache;
        }

        $transient_key = 'pwe_groups';
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_groups_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$groups_cache = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_groups_data: NO DB → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$groups_cache = $cached;
                return $cached;
            }
            self::debug_log('get_database_groups_data: NO DB and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$groups_cache = [];
            return [];
        }

        $start_time = microtime(true);
        $results = $cap_db->get_results("SELECT fair_domain, fair_group FROM fairs");
        $time = round((microtime(true) - $start_time) * 1000, 2);

        if ($cap_db->last_error) {
            self::debug_log('get_database_groups_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$groups_cache = $cached;
                return $cached;
            }
            self::$groups_cache = [];
            return [];
        }

        set_transient($transient_key, $results, 600);
        self::$groups_cache = $results;
        self::debug_log('get_database_groups_data: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get week data from CAP databases
     */
    private static $week_data_cache = [];
    public static function get_database_week_data($fair_domain = null): array {

        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        $cache_key = $current_domain;

        if (isset(self::$week_data_cache[$cache_key])) {
            self::debug_log('get_database_week_data: data from STATIC → key='. $cache_key);
            return self::$week_data_cache[$cache_key];
        }

        $transient_key = 'pwe_week_data_' . md5($cache_key);
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_week_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$week_data_cache[$cache_key] = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_week_data: NO DB → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$week_data_cache[$cache_key] = $cached;
                return $cached;
            }
            self::debug_log('get_database_week_data: NO DB and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$week_data_cache[$cache_key] = [];
            return [];
        }

        $week = $cap_db->get_row(
            $cap_db->prepare("SELECT fairs_domains FROM fair_weeks WHERE week_domain = %s LIMIT 1", $current_domain)
        );

        if ($cap_db->last_error) {
            self::debug_log('get_database_week_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$week_data_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$week_data_cache[$cache_key] = [];
            return [];
        }

        $start_time = microtime(true);
        $results = [];
        if ($week && !empty($week->fairs_domains)) {
            $results = array_map('trim', explode(',', $week->fairs_domains));
        }
        $time = round((microtime(true) - $start_time) * 1000, 2);

        set_transient($transient_key, $results, 600);
        self::$week_data_cache[$cache_key] = $results;
        self::debug_log('get_database_week_data: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get full week data from CAP databases
     */
    private static $week_all_cache = [];
    public static function get_database_week_all($fair_domain = null) {

        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        $cache_key = $current_domain;

        if (isset(self::$week_all_cache[$cache_key])) {
            self::debug_log('get_database_week_all: data from STATIC → key='. $cache_key);
            return self::$week_all_cache[$cache_key];
        }

        $transient_key = 'pwe_week_all_' . md5($cache_key);
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_week_all: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$week_all_cache[$cache_key] = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_week_all: NO DB → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$week_all_cache[$cache_key] = $cached;
                return $cached;
            }
            self::debug_log('get_database_week_all: NO DB and no TRANSIENT → returning null → key='. $cache_key, 'error');
            self::$week_all_cache[$cache_key] = null;
            return null;
        }

        $week = $cap_db->get_row(
            $cap_db->prepare("SELECT week_data FROM fair_weeks WHERE week_domain = %s LIMIT 1", $current_domain)
        );

        if ($cap_db->last_error) {
            self::debug_log('get_database_week_all: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$week_all_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$week_all_cache[$cache_key] = null;
            return null;
        }

        $start_time = microtime(true);
        $results = null;
        if ($week && !empty($week->week_data)) {
            $decoded = json_decode($week->week_data, true);
            $results = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $week->week_data;
        }
        $time = round((microtime(true) - $start_time) * 1000, 2);

        set_transient($transient_key, $results, 600);
        self::$week_all_cache[$cache_key] = $results;
        self::debug_log('get_database_week_all: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get all week domains from CAP databases
     */
    private static $all_week_domains_cache = null;
    public static function get_all_week_domains(): array {

        $cache_key = 'all_week_domains';

        if (self::$all_week_domains_cache !== null) {
            self::debug_log('get_all_week_domains: data from STATIC memory');
            return self::$all_week_domains_cache;
        }

        $transient_key = 'pwe_all_week_domains';
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_all_week_domains: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$all_week_domains_cache = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_all_week_domains: NO DB → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$all_week_domains_cache = $cached;
                return $cached;
            }
            self::debug_log('get_all_week_domains: NO DB and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$all_week_domains_cache = [];
            return [];
        }

        $rows = $cap_db->get_results("SELECT week_domain FROM fair_weeks");

        if ($cap_db->last_error) {
            self::debug_log('get_all_week_domains: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$all_week_domains_cache = $cached;
                return $cached;
            }
            self::$all_week_domains_cache = [];
            return [];
        }

        $start_time = microtime(true);
        $domains = [];
        foreach ($rows as $row) {
            if (!empty($row->week_domain)) {
                $domains[] = trim($row->week_domain);
            }
        }
        $results = array_values(array_unique($domains));
        $time = round((microtime(true) - $start_time) * 1000, 2);

        set_transient($transient_key, $results, 600);
        self::$all_week_domains_cache = $results;
        self::debug_log('get_all_week_domains: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get logotypes data from CAP databases
     */
    private static $logotypes_cache = [];
    public static function get_database_logotypes_data($fair_domain = null): array {

        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];
        $cache_key = $current_domain;

        if (isset(self::$logotypes_cache[$cache_key])) {
            self::debug_log('get_database_logotypes_data: data from STATIC → key='. $cache_key);
            return self::$logotypes_cache[$cache_key];
        }

        $transient_key = 'pwe_logotypes_' . md5($cache_key);
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_logotypes_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$logotypes_cache[$cache_key] = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_logotypes_data: NO DB → using last TRANSIENT → key='. $cache_key, 'error');
                self::$logotypes_cache[$cache_key] = $cached;
                return $cached;
            }
            self::debug_log('get_database_logotypes_data: NO DB and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$logotypes_cache[$cache_key] = [];
            return [];
        }

        $start_time = microtime(true);
        $results = [];
        $week = $cap_db->get_row($cap_db->prepare(
            "SELECT fairs_domains FROM fair_weeks WHERE week_domain = %s LIMIT 1", 
            $current_domain
        ));

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
            self::debug_log('get_database_logotypes_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$logotypes_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$logotypes_cache[$cache_key] = [];
            return [];
        }

        $results = self::remove_logo_duplicates($results);
        $time = round((microtime(true) - $start_time) * 1000, 2);

        set_transient($transient_key, $results, 600);
        self::$logotypes_cache[$cache_key] = $results;
        self::debug_log('get_database_logotypes_data: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get conferences data from CAP databases
     */
    private static $conferences_cache = [];
    public static function get_database_conferences_data($domain = null): array {

        $domain = $domain ?? $_SERVER['HTTP_HOST'];
        $cache_key = $domain;

        if (isset(self::$conferences_cache[$cache_key])) {
            self::debug_log('get_database_conferences_data: data from STATIC → key='. $cache_key);
            return self::$conferences_cache[$cache_key];
        }

        $transient_key = 'pwe_conferences_' . md5($cache_key);
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_conferences_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$conferences_cache[$cache_key] = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_conferences_data: NO DB → using last TRANSIENT → key='. $cache_key, 'error');
                self::$conferences_cache[$cache_key] = $cached;
                return $cached;
            }
            self::debug_log('get_database_conferences_data: NO DB and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$conferences_cache[$cache_key] = [];
            return [];
        }

        $start_time = microtime(true);
        $results = $cap_db->get_results(
            $cap_db->prepare(
                "SELECT * FROM conferences WHERE conf_site_link LIKE %s AND deleted_at IS NULL",
                '%' . $domain . '%'
            )
        );

        if ($cap_db->last_error) {
            self::debug_log('get_database_conferences_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$conferences_cache[$cache_key] = $cached;
                return $cached;
            }
            self::$conferences_cache[$cache_key] = [];
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

        $time = round((microtime(true) - $start_time) * 1000, 2);
        set_transient($transient_key, $results, 600);
        self::$conferences_cache[$cache_key] = $results;
        self::debug_log('get_database_conferences_data: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get fairs profiles data from CAP databases
     */
    private static $fairs_profiles_cache = [];
    public static function get_database_fairs_data_profiles($fair_domain = null): array {

        $fair_domain = $fair_domain ?? $_SERVER['HTTP_HOST'] ?? '';
        $cache_key = $fair_domain;

        if (isset(self::$fairs_profiles_cache[$cache_key])) {
            self::debug_log('get_database_fairs_data_profiles: data from STATIC → key='. $cache_key);
            return self::$fairs_profiles_cache[$cache_key];
        }

        $transient_key = 'pwe_fairs_profiles_' . md5($cache_key);
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_fairs_data_profiles: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$fairs_profiles_cache[$cache_key] = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::debug_log('get_database_fairs_data_profiles: no database connection.', 'error');
            self::$fairs_profiles_cache[$cache_key] = [];
            return [];
        }

        $sql = "
            SELECT f.id, f.fair_domain, fp.data
            FROM fairs f
            LEFT JOIN fair_profiles fp ON fp.fair_id = f.id AND fp.slug = f.fair_domain
            WHERE f.fair_domain = %s
        ";

        $start_time = microtime(true);
        $results = $cap_db->get_results($cap_db->prepare($sql, $fair_domain));
        $time = round((microtime(true) - $start_time) * 1000, 2);

        if ($cap_db->last_error) {
            self::debug_log('get_database_fairs_data_profiles: SQL error: '. addslashes($cap_db->last_error), 'error');
            $results = [];
        }

        set_transient($transient_key, $results, 600);
        self::$fairs_profiles_cache[$cache_key] = $results;
        self::debug_log('get_database_fairs_data_profiles: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get premieres data from CAP databases
     */
    private static $premieres_cache = [];
    public static function get_database_premieres_data($fair_domain = null): array {

        $cache_key = $fair_domain ?? 'all';

        if (isset(self::$premieres_cache[$cache_key])) {
            self::debug_log('get_database_premieres_data: data from STATIC → key='. $cache_key);
            return self::$premieres_cache[$cache_key];
        }

        $transient_key = 'pwe_premieres_' . md5($cache_key);
        $cached = get_transient($transient_key);
        $timeout = get_option('_transient_timeout_' . $transient_key);
        $time_left_str = ($timeout !== false) ? gmdate('H:i:s', max($timeout - time(),0)) : 'unknown';

        if ($cached !== false) {
            self::debug_log('get_database_premieres_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$premieres_cache[$cache_key] = $cached;
            return $cached;
        }

        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::debug_log('get_database_premieres_data: no database connection.', 'error');
            self::$premieres_cache[$cache_key] = [];
            return [];
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

        $start_time = microtime(true);
        $results = !empty($params)
            ? $cap_db->get_results($cap_db->prepare($sql, $params))
            : $cap_db->get_results($sql);

        $time = round((microtime(true) - $start_time) * 1000, 2);

        if ($cap_db->last_error) {
            self::debug_log('get_database_premieres_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            self::$premieres_cache[$cache_key] = [];
            return [];
        }

        set_transient($transient_key, $results, 600);
        self::$premieres_cache[$cache_key] = $results;
        self::debug_log('get_database_premieres_data: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get fairs opinions data from CAP databases
     */
    private static $fairs_opinions_cache = [];
    public static function get_database_fairs_data_opinions($fair_domain = null): array {

        $fair_domain = $fair_domain ?? $_SERVER['HTTP_HOST'] ?? '';
        $cache_key = $fair_domain;

        // Check runtime cache first
        if (isset(self::$fairs_opinions_cache[$cache_key])) {
            self::debug_log('get_database_fairs_data_opinions: data from STATIC → key='. $cache_key);
            return self::$fairs_opinions_cache[$cache_key];
        }

        // Transient key
        $transient_key = 'pwe_fairs_opinions_' . md5($cache_key);

        // Try transient
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            $timeout = get_option('_transient_timeout_' . $transient_key);
            $time_left_str = $timeout !== false ? gmdate('H:i:s', max($timeout - time(), 0)) : 'unknown';

            self::debug_log('get_database_fairs_data_opinions: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$fairs_opinions_cache[$cache_key] = $cached;
            return $cached;
        }

        // Connect to database
        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::debug_log('get_database_fairs_data_opinions: no database connection.', 'error');
            self::$fairs_opinions_cache[$cache_key] = [];
            return [];
        }

        // SQL query
        $sql = "
            SELECT f.id, f.fair_domain, fp.data, fp.slug, fp.order
            FROM fairs f
            LEFT JOIN fair_opinions fp ON fp.fair_id = f.id
        ";
        $params = [];
        if ($fair_domain !== null) {
            $sql .= " WHERE f.fair_domain = %s";
            $params[] = $fair_domain;
        }
        $sql .= " ORDER BY fp.order ASC";

        $start_time = microtime(true);

        // Execute query
        $results = !empty($params) ? $cap_db->get_results($cap_db->prepare($sql, $params)) : $cap_db->get_results($sql);
        $time = round((microtime(true) - $start_time) * 1000, 2);

        // Handle SQL errors
        if ($cap_db->last_error) {
            self::debug_log('get_database_fairs_data_opinions: SQL error: ' . addslashes($cap_db->last_error), 'error');
            $results = [];
        }

        // Save to transient for 10 minutes
        set_transient($transient_key, $results, 600);

        // Save to runtime cache
        self::$fairs_opinions_cache[$cache_key] = $results;
        self::debug_log('get_database_fairs_data_opinions: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get fairs speakers data from CAP databases
     */
    private static $fairs_speakers_cache = [];
    public static function get_database_fairs_data_speakers($fair_domain = null): array {

        $fair_domain = $fair_domain ?? $_SERVER['HTTP_HOST'] ?? '';
        $cache_key = $fair_domain;

        // Check runtime cache first
        if (isset(self::$fairs_speakers_cache[$cache_key])) {
            self::debug_log('get_database_fairs_data_speakers: data from STATIC → key='. $cache_key);
            return self::$fairs_speakers_cache[$cache_key];
        }

        // Transient key
        $transient_key = 'pwe_fairs_speakers_' . md5($cache_key);

        // Try transient
        $cached = get_transient($transient_key);
        if ($cached !== false) {
            $timeout = get_option('_transient_timeout_' . $transient_key);
            $time_left_str = $timeout !== false ? gmdate('H:i:s', max($timeout - time(), 0)) : 'unknown';

            self::debug_log('get_database_fairs_data_speakers: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$fairs_speakers_cache[$cache_key] = $cached;
            return $cached;
        }

        // Connect to database
        $cap_db = self::connect_database();
        if (!$cap_db) {
            self::debug_log('get_database_fairs_data_speakers: no database connection.', 'error');
            self::$fairs_speakers_cache[$cache_key] = [];
            return [];
        }

        // SQL query
        $sql = "
            SELECT f.id, f.fair_domain, fp.data, fp.slug, fp.order
            FROM fairs f
            LEFT JOIN prelegents fp ON fp.fair_id = f.id
        ";
        $params = [];
        if ($fair_domain !== null) {
            $sql .= " WHERE f.fair_domain = %s";
            $params[] = $fair_domain;
        }
        $sql .= " ORDER BY fp.order ASC";

        $start_time = microtime(true);

        // Execute query
        $results = !empty($params) ? $cap_db->get_results($cap_db->prepare($sql, $params)) : $cap_db->get_results($sql);
        $time = round((microtime(true) - $start_time) * 1000, 2);

        // Handle SQL errors
        if ($cap_db->last_error) {
            self::debug_log('get_database_fairs_data_speakers: SQL error: ' . addslashes($cap_db->last_error), 'error');
            $results = [];
        }

        // Save to transient for 10 minutes
        set_transient($transient_key, $results, 600);

        // Save to runtime cache
        self::$fairs_speakers_cache[$cache_key] = $results;
        self::debug_log('get_database_fairs_data_speakers: data from database DIRECTLY (SQL time ' . $time . 'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get elements data from CAP databases
     */
    private static $elements_cache = null;
    public static function get_database_elements_data(): array {

        $cache_key = 'elements';

        // STATIC cache
        if (self::$elements_cache !== null) {
            self::debug_log('get_database_elements_data: data from STATIC memory');
            return self::$elements_cache;
        }

        // Transient
        $transient_key = 'pwe_elements_data';
        $cached = get_transient($transient_key);

        // Log transient timeout
        $timeout = get_option('_transient_timeout_' . $transient_key);
        if ($timeout !== false) {
            $time_left = $timeout - time();
            $time_left_str = gmdate('H:i:s', max($time_left, 0));
        } else {
            $time_left_str = 'unknown';
        }

        if ($cached !== false) {
            self::debug_log('get_database_elements_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$elements_cache = $cached;
            return $cached;
        }

        // Connect DB
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_elements_data: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$elements_cache = $cached;
                return $cached;
            }
            self::debug_log('get_database_elements_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$elements_cache = [];
            return [];
        }

        // SQL query
        $sql = "SELECT * FROM pwelements";
        $start_time = microtime(true);
        $results = $cap_db->get_results($sql);
        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_elements_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$elements_cache = $cached;
                return $cached;
            }
            self::$elements_cache = [];
            return [];
        }

        // Save transient + STATIC cache
        set_transient($transient_key, $results, 600);
        self::$elements_cache = $results;
        self::debug_log('get_database_elements_data: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    /**
     * Get elements order data from CAP databases
     */
    private static $elements_order_cache = null;
    public static function get_database_elements_order_data(): array {

        $cache_key = 'elements_order';

        // STATIC cache
        if (self::$elements_order_cache !== null) {
            self::debug_log('get_database_elements_order_data: data from STATIC memory');
            return self::$elements_order_cache;
        }

        // Transient
        $transient_key = 'pwe_elements_order_data';
        $cached = get_transient($transient_key);

        // Log transient timeout
        $timeout = get_option('_transient_timeout_' . $transient_key);
        if ($timeout !== false) {
            $time_left = $timeout - time();
            $time_left_str = gmdate('H:i:s', max($time_left, 0));
        } else {
            $time_left_str = 'unknown';
        }

        if ($cached !== false) {
            self::debug_log('get_database_elements_order_data: data from TRANSIENT → key='. $cache_key .', expires in '. $time_left_str);
            self::$elements_order_cache = $cached;
            return $cached;
        }

        // Connect DB
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::debug_log('get_database_elements_order_data: NO DB connection → using last TRANSIENT and extending 10min → key='. $cache_key, 'error');
                self::$elements_order_cache = $cached;
                return $cached;
            }
            self::debug_log('get_database_elements_order_data: NO DB connection and no TRANSIENT → returning empty → key='. $cache_key, 'error');
            self::$elements_order_cache = [];
            return [];
        }

        // SQL query
        $sql = "SELECT * FROM pwe_order";
        $start_time = microtime(true);
        $results = $cap_db->get_results($sql);
        $time = round((microtime(true) - $start_time) * 1000, 2);

        // SQL error
        if ($cap_db->last_error) {
            self::debug_log('get_database_elements_order_data: SQL error: '. addslashes($cap_db->last_error), 'error');
            if ($cached !== false) {
                set_transient($transient_key, $cached, 600);
                self::$elements_order_cache = $cached;
                return $cached;
            }
            self::$elements_order_cache = [];
            return [];
        }

        // Save transient + STATIC cache
        set_transient($transient_key, $results, 600);
        self::$elements_order_cache = $results;
        self::debug_log('get_database_elements_order_data: data from database DIRECTLY (SQL time '.$time.'ms) → key='. $cache_key .', host='. $cap_db->dbhost .' ['. gethostname() .'] and saved to TRANSIENT.');

        return $results;
    }

    // DATABASE CONNECTIONS END <==================================================================================>


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
            "hall_entrance" => $fair->fair_entrance ?? "",
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

add_action('wp_footer', ['PWE_Functions', 'output_db_connection_logs']);