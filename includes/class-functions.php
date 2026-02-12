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
    // Functions from plugin PWElements 3.1.9 <========================================================>
    // <============================================================================================>


    
    private static $cached_db_connection = null;
    private static $connection_attempts = 0;
    private static $max_connection_attempts = 2;

    /**
     * Random number
     */
    public static function id_rnd() {
        $id_rnd = rand(10000, 99999);
        return $id_rnd;
    }

    private static function resolveServerAddrFallback() {
        $host = php_uname('n');
            switch ($host) {
            case 'dedyk180.cyber-folks.pl':
                return '94.152.207.180';
            case 'dedyk93.cyber-folks.pl':
                return '94.152.206.93';
            case 'dedyk239.cyber-folks.pl':
                return '91.225.28.47';
            default:
                return '94.152.207.180';
        }
    }

    /**
     * Connecting to CAP database
     */
    public static function connect_database() {
         // Return cached connection if already established
        if (self::$cached_db_connection !== null) {
            // Return cache if connection already exists
            return self::$cached_db_connection;
        }

        // Limit the number of connection attempts
        if (self::$connection_attempts >= self::$max_connection_attempts) {
            // End attempt after 2 failed attempts
            return false;
        }

        // Initialize connection variables
        $cap_db = null;

        if (!isset($_SERVER['SERVER_ADDR'])) {
            $_SERVER['SERVER_ADDR'] = self::resolveServerAddrFallback();
        }

        $database_host = $database_name = $database_user = $database_password = '';

        // Set connection data depending on the server
        switch ($_SERVER['SERVER_ADDR']) {
            case '94.152.207.180':
                $database_host = 'localhost';
                $database_name = defined('PWE_DB_NAME_180') ? PWE_DB_NAME_180 : '';
                $database_user = defined('PWE_DB_USER_180') ? PWE_DB_USER_180 : '';
                $database_password = defined('PWE_DB_PASSWORD_180') ? PWE_DB_PASSWORD_180 : '';
                break;

            case '94.152.206.93':
                $database_host = 'localhost';
                $database_name = defined('PWE_DB_NAME_93') ? PWE_DB_NAME_93 : '';
                $database_user = defined('PWE_DB_USER_93') ? PWE_DB_USER_93 : '';
                $database_password = defined('PWE_DB_PASSWORD_93') ? PWE_DB_PASSWORD_93 : '';
                break;

            case '91.225.28.47':
                $database_host = 'localhost';
                $database_name = defined('PWE_DB_NAME_239') ? PWE_DB_NAME_239 : '';
                $database_user = defined('PWE_DB_USER_239') ? PWE_DB_USER_239 : '';
                $database_password = defined('PWE_DB_PASSWORD_239') ? PWE_DB_PASSWORD_239 : '';
                break;

            default:
                $database_host = 'dedyk180.cyber-folks.pl';
                $database_name = defined('PWE_DB_NAME_180') ? PWE_DB_NAME_180 : '';
                $database_user = defined('PWE_DB_USER_180') ? PWE_DB_USER_180 : '';
                $database_password = defined('PWE_DB_PASSWORD_180') ? PWE_DB_PASSWORD_180 : '';
        }

        // Check if there is complete data for connection
        if (!empty($database_user) && !empty($database_password) && !empty($database_name) && !empty($database_host)) {
            try {
                $cap_db = new wpdb($database_user, $database_password, $database_name, $database_host);

                // Check if the connection was successful
                if ($cap_db->dbh) {
                    self::$cached_db_connection = $cap_db; // Cache the connection if successful
                } else {
                    throw new Exception('Nie udało się połączyć z bazą danych.');
                }

            } catch (Exception $e) {
                self::$connection_attempts++;
                if (current_user_can("administrator") && !is_admin()) {
                    echo '<script>console.error("Błąd połączenia z bazą danych: '. addslashes($e->getMessage()) .'")</script>';
                }
            }
        } else {
            self::$connection_attempts++;
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Nieprawidłowe dane połączenia z bazą danych.")</script>';
                error_log("Nieprawidłowe dane połączenia z bazą danych.");
            }
        }

        // Check for connection errors
        if (!$cap_db || !$cap_db->dbh || mysqli_connect_errno()) {
            self::$connection_attempts++;
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd połączenia MySQL: '. addslashes(mysqli_connect_error()) .'")</script>';
            }
            return false;
        }

        // error_log("connected to database");
        return $cap_db;
    }

    /**
     * Get data from CAP databases
     */
    public static function get_database_fairs_data($fair_domain = null) {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

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
                MAX(CASE WHEN fa.slug = 'fair_kw_new' THEN fa.data END) AS fair_kw_new,
                MAX(CASE WHEN fa.slug = 'fair_kw_old_arch' THEN fa.data END) AS fair_kw_old_arch,
                MAX(CASE WHEN fa.slug = 'fair_kw_new_arch' THEN fa.data END) AS fair_kw_new_arch

            FROM fairs f
            LEFT JOIN fair_adds fa ON fa.fair_id = f.id
        ";

        $params = [];
        if ($fair_domain !== null) {
            $sql .= " WHERE f.fair_domain = %s";
            $params[] = $fair_domain;
        }

        // Grouping
        $sql .= " GROUP BY f.id";

        // Execution of the query
        if (!empty($params)) {
            $results = $cap_db->get_results($cap_db->prepare($sql, $params));
        } else {
            $results = $cap_db->get_results($sql);
        }


        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        return $results;
    }

    public static function get_database_premieres_data($fair_domain = null) {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

        // Build column list
        $sql = "
            SELECT
                f.id,
                f.fair_domain,
                p.slug,
                p.data
            FROM fairs f
            LEFT JOIN fair_premieres p ON p.fair_id = f.id
        ";

        if (!isset($fair_domain)) {
            $results = $cap_db->get_results($sql);
        } else {
            $sql .= " WHERE f.fair_domain = %s";
            $results = $cap_db->get_results($cap_db->prepare($sql, $fair_domain));
        }


        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        return $results;
    }

    public static function get_database_fairs_data_adds($fair_domain = null) {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

        $fair_domain = ($fair_domain == null) ? $_SERVER['HTTP_HOST'] : $fair_domain;

        // Build column list
        $sql = "
            SELECT
                f.id,
                f.fair_domain,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'konf_name' ORDER BY id ASC LIMIT 1) AS konf_name,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'konf_title_pl' ORDER BY id ASC LIMIT 1) AS konf_title_pl,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'konf_title_en' ORDER BY id ASC LIMIT 1) AS konf_title_en,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'konf_desc_pl' ORDER BY id ASC LIMIT 1) AS konf_desc_pl,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'konf_desc_en' ORDER BY id ASC LIMIT 1) AS konf_desc_en,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'about_title_pl' ORDER BY id ASC LIMIT 1) AS about_title_pl,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'about_title_en' ORDER BY id ASC LIMIT 1) AS about_title_en,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'about_desc_pl' ORDER BY id ASC LIMIT 1) AS about_desc_pl,
                (SELECT data FROM fair_adds WHERE fair_id = f.id AND slug = 'about_desc_en' ORDER BY id ASC LIMIT 1) AS about_desc_en
            FROM fairs f
        ";

        if (!isset($fair_domain)) {
            $results = $cap_db->get_results($sql);
        } else {
            $sql .= " WHERE f.fair_domain = %s";
            $results = $cap_db->get_results($cap_db->prepare($sql, $fair_domain));
        }


        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        return $results;
    }

    public static function get_database_translations_data($fair_domain = null) {
        $cap_db = self::connect_database();
        if (!$cap_db) return [];

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

        // Pobierz wszystkie targi
        if (!isset($fair_domain)){
            // Retrieving all data from the database
            $fairs = $cap_db->get_results("SELECT $columns FROM fairs");
        } else {
            // Retrieving fair data from the database
            $fairs = $cap_db->get_results(
                $cap_db->prepare("SELECT $columns FROM fairs WHERE fair_domain = %s", $fair_domain)
            );
        }

        // Download all translations
        $translations = $cap_db->get_results("SELECT * FROM translations");

        // Map translations by fair_id and language
        $translations_map = [];
        foreach ($translations as $tr) {
            $fair_id = $tr->fair_id;
            $lang = strtolower($tr->language); // ex. de, es
            $data = json_decode($tr->translation, true);
            if ($data) {
                $translations_map[$fair_id][$lang] = $data;
            }
        }

        // Building the final array
        $results = [];
        foreach ($fairs as $fair) {
            $row = [
                'fair_domain'           => $fair->fair_domain,
                'fair_name_pl'          => $fair->fair_name_pl,
                'fair_name_en'          => $fair->fair_name_en,
                'fair_desc_pl'          => $fair->fair_desc_pl,
                'fair_desc_en'          => $fair->fair_desc_en,
                'fair_short_desc_pl'    => $fair->fair_short_desc_pl,
                'fair_short_desc_en'    => $fair->fair_short_desc_en,
                'fair_full_desc_pl'     => $fair->fair_full_desc_pl,
                'fair_full_desc_en'     => $fair->fair_full_desc_en,
            ];

            // If there are translations - add them as additional languages
            if (isset($translations_map[$fair->id])) {
                foreach ($translations_map[$fair->id] as $lang => $fields) {
                    if (isset($fields['fair_name']))         $row["fair_name_$lang"] = $fields['fair_name'];
                    if (isset($fields['fair_desc']))         $row["fair_desc_$lang"] = $fields['fair_desc'];
                    if (isset($fields['fair_short_desc']))   $row["fair_short_desc_$lang"] = $fields['fair_short_desc'];
                    if (isset($fields['fair_full_desc']))    $row["fair_full_desc_$lang"] = $fields['fair_full_desc'];
                }
            }

            $results[] = $row;
        }

        return $results;
    }

    public static function get_database_associates_data($fair_domain = null) {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
            return [];
        }

        $fair_domain = ($fair_domain == null) ? $_SERVER['HTTP_HOST'] : $fair_domain;

        // Retrieving filtered data directly from database
        $query = $cap_db->prepare("
            SELECT *
            FROM associates
            WHERE FIND_IN_SET(%s, fair_associates)
        ", $fair_domain);

        $results = $cap_db->get_results($query);

        // SQL error checking
        if ($cap_db->last_error) {
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
            return [];
        }

        return $results;
    }

    public static function get_database_store_data() {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

        // Retrieving data from the database
        $results = $cap_db->get_results("SELECT * FROM shop");

        // SQL error checking
        if ($cap_db->last_error) {
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
            return [];
        }

        return $results;
    }

    public static function get_database_store_packages_data() {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

        // Retrieving data from the database
        $results = $cap_db->get_results("SELECT * FROM shop_packs");

        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        return $results;
    }

    public static function get_database_meta_data($data_id = null) {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
            return [];
        }

        $domain = $_SERVER['HTTP_HOST'] ?? '';
        $domain = preg_replace('/:\d+$/', '', $domain);

        if($data_id === null){
            // Retrieving data from the database
            $results = $cap_db->get_results("SELECT * FROM meta_data");
        } else {
            if ($data_id === 'header_order') {
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
        }

        // SQL error checking
        if ($cap_db->last_error) {
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
            return [];
        }

        return $results;
    }

    public static function get_database_groups_contacts_data() {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

        // Retrieving data from the database
        $results = $cap_db->get_results("SELECT * FROM groups");

        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        return $results;
    }

    public static function get_database_groups_callcenter_data() {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

        // Retrieving data from the database
        $results = $cap_db->get_results("SELECT * FROM form_senders");

        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        return $results;
    }

    public static function get_database_groups_data() {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

        // Retrieving data from the database
        $results = $cap_db->get_results("SELECT fair_domain, fair_group FROM fairs");

        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        return $results;
    }

    public static function get_database_week_data($fair_domain = null) {

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
            return [];
        }

        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];

        $week = $cap_db->get_row(
            $cap_db->prepare(
                "SELECT fairs_domains
                FROM fair_weeks
                WHERE week_domain = %s
                LIMIT 1",
                $current_domain
            )
        );

        if ($week && !empty($week->fairs_domains)) {
            return array_map('trim', explode(',', $week->fairs_domains));
        }

        return [];
    }

    public static function get_database_week_all($fair_domain = null){
        $cap_db = self::connect_database();
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
            return null;
        }

        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];

        $week = $cap_db->get_row(
            $cap_db->prepare(
                "SELECT week_data
                FROM fair_weeks
                WHERE week_domain = %s
                LIMIT 1",
                $current_domain
            )
        );

        if (!$week || empty($week->week_data)) {
            return null;
        }

        // dekodujemy JSON
        $decoded = json_decode($week->week_data, true);

        // jeśli JSON jest poprawny → zwróć tablicę
        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        // fallback – zwróć surowy string
        return $week->week_data;
    }

    public static function get_all_week_domains() {

        $cap_db = self::connect_database();
        if (!$cap_db) {
            return [];
        }

        $rows = $cap_db->get_results(
            "SELECT week_domain FROM fair_weeks"
        );

        if (empty($rows)) {
            return [];
        }

        $domains = [];

        foreach ($rows as $row) {
            if (!empty($row->week_domain)) {
                $domains[] = trim($row->week_domain);
            }
        }

        return array_values(array_unique($domains));
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

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[] = $logo;
        }

        return $unique;
    }

    public static function get_database_logotypes_data($fair_domain = null) {

        $cap_db = self::connect_database();
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
            return [];
        }

        $current_domain = $fair_domain ?? $_SERVER['HTTP_HOST'];

        $week = $cap_db->get_row(
            $cap_db->prepare(
                "SELECT fairs_domains
                FROM fair_weeks
                WHERE week_domain = %s
                LIMIT 1",
                $current_domain
            )
        );

        if ($week && !empty($week->fairs_domains)) {

            $domains = json_decode($week->fairs_domains, true);

            if (!is_array($domains)) {
                $domains = [];
            }

            $domains = array_values(array_filter(array_map('trim', $domains)));


            if (!empty($domains)) {

                $placeholders = implode(',', array_fill(0, count($domains), '%s'));

                $query = "
                    SELECT DISTINCT logos.*,
                        meta_data.meta_data AS meta_data
                    FROM logos
                    INNER JOIN fairs ON logos.fair_id = fairs.id
                    LEFT JOIN meta_data ON meta_data.slug = 'patrons'
                        AND JSON_UNQUOTE(JSON_EXTRACT(meta_data.meta_data, '$.slug')) = logos.logos_type
                    WHERE fairs.fair_domain IN ($placeholders)
                ";

                $results = $cap_db->get_results(
                    $cap_db->prepare($query, $domains)
                );

            } else {
                $results = [];
            }
        } else {

            $query = "
                SELECT logos.*,
                    meta_data.meta_data AS meta_data
                FROM logos
                INNER JOIN fairs ON logos.fair_id = fairs.id
                LEFT JOIN meta_data ON meta_data.slug = 'patrons'
                    AND JSON_UNQUOTE(JSON_EXTRACT(meta_data.meta_data, '$.slug')) = logos.logos_type
                WHERE fairs.fair_domain = %s
            ";

            $results = $cap_db->get_results(
                $cap_db->prepare($query, $current_domain)
            );
        }

        if ($cap_db->last_error) {
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: ' . addslashes($cap_db->last_error) . '")</script>';
            }
            return [];
        }
        $results = self::remove_logo_duplicates($results);
        return $results;
    }

    public static function get_database_conferences_data($domain = null) {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            return [];
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
        }

        $domain = ($domain == null) ? $_SERVER['HTTP_HOST'] : $domain;

        // Pobieramy dane bez względu na język
        $results = $cap_db->get_results(
            $cap_db->prepare(
                "SELECT * FROM conferences WHERE conf_site_link LIKE %s AND deleted_at IS NULL",
                '%' . $domain . '%'
            )
        );

        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        foreach ($results as &$row) {
            if (!empty($row->conf_data)) {
                $decoded = html_entity_decode($row->conf_data);

                // Czyścimy WSZYSTKIE wystąpienia font-family z atrybutów style (w tym wieloliniowe!)
                $decoded = preg_replace_callback('/style="([^"]+)"/is', function ($match) {
                    $style = $match[1];
                    $style = preg_replace('/font-family\s*:\s*[^;"]+("[^"]+"[, ]*)*[^;"]*;?/i', '', $style);
                    $style = trim(preg_replace('/\s*;\s*/', '; ', $style), '; ');
                    return $style ? 'style="' . $style . '"' : '';
                }, $decoded);

                // Sprawdzamy poprawność JSON
                $test = json_decode($decoded, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row->conf_data = $decoded;
                } else {
                    error_log("❌ Błąd JSON w conf_data: " . json_last_error_msg());
                }
            }
        }

        return $results;
    }

    public static function get_database_fairs_data_profiles($fair_domain = null) {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
            return [];
        }

        $fair_domain = ($fair_domain == null) ? $_SERVER['HTTP_HOST'] : $fair_domain;

        // Build column list
        $sql = "
            SELECT
                f.id,
                f.fair_domain,
                fp.data
            FROM fairs f
            LEFT JOIN fair_profiles fp
                ON fp.fair_id = f.id
            AND fp.slug = f.fair_domain
        ";

        if (!isset($fair_domain)) {
            $results = $cap_db->get_results($sql);
        } else {
            $sql .= " WHERE f.fair_domain = %s";
            $results = $cap_db->get_results($cap_db->prepare($sql, $fair_domain));
        }

        // SQL error checking
        if ($cap_db->last_error) {
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
            return [];
        }

        return $results;
    }

    public static function get_database_fairs_data_opinions($fair_domain = null) {
        // Database connection
        $cap_db = self::connect_database();
        // If connection failed, return empty array
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
            return [];
        }

        $fair_domain = ($fair_domain == null) ? $_SERVER['HTTP_HOST'] : $fair_domain;

        // Build column list
        $sql = "
            SELECT
                f.id,
                f.fair_domain,
                fp.data,
                fp.slug,
                fp.order
            FROM fairs f
            LEFT JOIN fair_opinions fp
                ON fp.fair_id = f.id
        ";

        if (!isset($fair_domain)) {
            $results = $cap_db->get_results($sql);
        } else {
            $sql .= " WHERE f.fair_domain = %s";
            $results = $cap_db->get_results($cap_db->prepare($sql, $fair_domain));
        }

        // SQL error checking
        if ($cap_db->last_error) {
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
            return [];
        }

        return $results;
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

        return $fairs_data['fairs'];
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