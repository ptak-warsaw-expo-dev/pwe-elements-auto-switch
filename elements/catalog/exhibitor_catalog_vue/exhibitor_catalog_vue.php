<?php
if (!defined('ABSPATH')) exit;

define('EX_PATH', plugin_dir_path(__FILE__));
define('EX_URL', plugin_dir_url(__FILE__));

class Exhibitor_Catalog {

    public static function get_data() {
            return [
                'types'   => ['catalog'],
                'presets' => [],
            ];
        }

    public static function get_info() {
        return [
            'type' => 'catalog',
            'slug' => strtolower(__CLASS__),
        ];
    }

    public static function render($group, $params, $atts) {

        self::enqueue_assets();
        self::enqueue_feedback_assets();
        self::inject_config($atts);
        self::sync_archive_catalog_entry($atts);

        echo '
        <style>
            .catalog__loading {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 24px;
            }
            .catalog__loading-spinner {
                --uib-size: 60px;
                --uib-speed: .9s;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                height: var(--uib-size);
                width: var(--uib-size);
            }

            .spinner__dot {
                position: absolute;
                top: 0;
                left: 0;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                height: 100%;
                width: 100%;
            }

            .spinner__dot::before {
                content: "";
                height: 20%;
                width: 20%;
                border-radius: 50%;
                background-color: var(--main2-color);
                transform: scale(0);
                opacity: 0.5;
                animation: pulse0112 calc(var(--uib-speed) * 1.111) ease-in-out infinite;
                box-shadow: 0 0 20px rgba(18, 31, 53, 0.3);
            }

            .spinner__dot:nth-child(2) {
                transform: rotate(45deg);
            }

            .spinner__dot:nth-child(2)::before {
                animation-delay: calc(var(--uib-speed) * -0.875);
            }

            .spinner__dot:nth-child(3) {
                transform: rotate(90deg);
            }

            .spinner__dot:nth-child(3)::before {
                animation-delay: calc(var(--uib-speed) * -0.75);
            }

            .spinner__dot:nth-child(4) {
                transform: rotate(135deg);
            }

            .spinner__dot:nth-child(4)::before {
                animation-delay: calc(var(--uib-speed) * -0.625);
            }

            .spinner__dot:nth-child(5) {
                transform: rotate(180deg);
            }

            .spinner__dot:nth-child(5)::before {
                animation-delay: calc(var(--uib-speed) * -0.5);
            }

            .spinner__dot:nth-child(6) {
                transform: rotate(225deg);
            }

            .spinner__dot:nth-child(6)::before {
                animation-delay: calc(var(--uib-speed) * -0.375);
            }

            .spinner__dot:nth-child(7) {
                transform: rotate(270deg);
            }

            .spinner__dot:nth-child(7)::before {
                animation-delay: calc(var(--uib-speed) * -0.25);
            }

            .spinner__dot:nth-child(8) {
                transform: rotate(315deg);
            }

            .spinner__dot:nth-child(8)::before {
                animation-delay: calc(var(--uib-speed) * -0.125);
            }

            @keyframes pulse0112 {
            0%,
            100% {
                transform: scale(0);
                opacity: 0.5;
            }

            50% {
                transform: scale(1);
                opacity: 1;
            }
            }
        </style>
        <div class="catalog__loading">
            <h2 class="catalog__loading-title">Katalog Wystawców</h2>
            <div class="catalog__loading-spinner">
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
            </div>
        </div>
        <div id="vue-catalog" style="min-height: 130vh;"></div>';

        if (current_user_can('administrator')) {
            echo '<script>console.log("Link od odświeżenia/pobrania katalogu: https://'. $_SERVER['HTTP_HOST'] .'/wp-content/plugins/custom-element/other/cron_catalog.php?pass=iR8gCdZlITxRvVBS")</script>';
        }

        $feedback = EX_PATH . 'addons/feedback/feedback.php';
        if (file_exists($feedback)) {
            require_once $feedback;
        }

        $translates = EX_PATH . 'addons/translates.php';
        if (file_exists($translates)) {
            require_once $translates;
        }
    }

    private static function enqueue_assets() {

        $manifest_path = EX_PATH . 'dist/.vite/manifest.json';
        if (!file_exists($manifest_path)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifest_path), true);
        if (!isset($manifest['src/main.js'])) {
            return;
        }

        $entry = $manifest['src/main.js'];

        // CSS
        if (!empty($entry['css'][0])) {
            wp_enqueue_style(
                'vue-catalog',
                EX_URL . 'dist/' . $entry['css'][0],
                [],
                null
            );
        }

        // JS
        wp_enqueue_script(
            'vue-catalog',
            EX_URL . 'dist/' . $entry['file'],
            [],
            null,
            true
        );
    }

    private static function enqueue_feedback_assets() {

        $base_path = EX_PATH . 'addons/feedback/';
        $base_url  = EX_URL  . 'addons/feedback/';

        if (file_exists($base_path . 'feedback-style.css')) {
            wp_enqueue_style(
                'exhibitor-feedback-style',
                $base_url . 'feedback-style.css',
                [],
                filemtime($base_path . 'feedback-style.css')
            );
        }

        if (file_exists($base_path . 'feedback-script.js')) {
            wp_enqueue_script(
                'exhibitor-feedback-script',
                $base_url . 'feedback-script.js',
                [],
                filemtime($base_path . 'feedback-script.js'),
                true
            );

            wp_localize_script(
                'exhibitor-feedback-script',
                'EX_FEEDBACK_CONFIG',
                [
                    'source'  => 'catalog',
                    'version' => self::get_plugin_version(),
                ]
            );
        }
    }

    private static function get_plugin_version() {
        static $version = null;

        if ($version === null) {
            $data = get_file_data(PWE_PLUGIN_FILE, ['Version' => 'Version']);
            $version = $data['Version'] ?: 'unknown';
        }

        return $version;
    }

    private static function sync_archive_catalog_entry($atts) {

        $catalog_id = $atts['archive_catalog_id'];
        $catalog_year = $atts['archive_catalog_year'];

        // --------------------------------------------------
        // Log setup
        // --------------------------------------------------
        $logDir  = $_SERVER["DOCUMENT_ROOT"] . '/wp-content/uploads/exhibitor-catalogs/';
        $logFile = $logDir . 'archive_catalog.log';

        if (!is_dir($logDir)) {
            wp_mkdir_p($logDir);
        }

        $log = function ($msg) use ($logFile) {
            $time = date('Y-m-d H:i:s');
            file_put_contents($logFile, "[$time] $msg\n", FILE_APPEND);
        };

        // --------------------------------------------------
        // Basic validation
        // --------------------------------------------------
        if (empty($catalog_id)) {
            return;
        }

        // Extract only YYYY from catalog_year (e.g. "text 2025")
        $year = null;
        if (!empty($catalog_year) && preg_match('/(19|20)\d{2}/', $catalog_year, $m)) {
            $year = $m[0];
        }

        if (!$year) {
            return;
        }

        $log("START [NEW CATALOG] sync_archive_catalog_entry | catalog_id={$catalog_id}, catalog_year={$year}");

        $domain = $_SERVER['HTTP_HOST'] ?? null;
        if (!$domain) {
            $log("STOP: domain missing");
            return;
        }

        $log("DOMAIN={$domain}, YEAR={$year}");

        // --------------------------------------------------
        // Database configurations
        // --------------------------------------------------
        $databases = [
            ['host'=>'dedyk180.cyber-folks.pl','name'=>PWE_DB_NAME_180,'user'=>PWE_DB_USER_180,'pass'=>PWE_DB_PASSWORD_180],
            ['host'=>'dedyk93.cyber-folks.pl','name'=>PWE_DB_NAME_93,'user'=>PWE_DB_USER_93,'pass'=>PWE_DB_PASSWORD_93],
            ['host'=>'dedyk239.cyber-folks.pl','name'=>PWE_DB_NAME_239,'user'=>PWE_DB_USER_239,'pass'=>PWE_DB_PASSWORD_239],
        ];

        // Use localhost on the current server
        switch ($_SERVER['SERVER_ADDR']) {
            case '94.152.207.180': $databases[0]['host'] = 'localhost'; break;
            case '94.152.206.93':  $databases[1]['host'] = 'localhost'; break;
            case '91.225.28.47':   $databases[2]['host'] = 'localhost'; break;
        }

        // --------------------------------------------------
        // Process each database
        // --------------------------------------------------
        foreach ($databases as $i => $db) {

            $log("DATABASE #" . ($i+1) . " | host={$db['host']} | db={$db['name']}");

            $wpdb = new wpdb($db['user'], $db['pass'], $db['name'], $db['host']);

            if ($wpdb->last_error) {
                $log("DB CONNECTION ERROR: " . $wpdb->last_error);
                continue;
            }

            // --------------------------------------------------
            // Get fair_id
            // --------------------------------------------------
            $fair_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM fairs WHERE fair_domain = %s",
                    $domain
                )
            );

            if (empty($fair_id)) {
                $log("FAIR_ID not found – skip database");
                continue;
            }

            $log("FAIR_ID={$fair_id}");

            // --------------------------------------------------
            // Get existing archive data
            // --------------------------------------------------
            $slug = 'fair_kw_new_arch';
            $existing_data = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT data FROM fair_adds WHERE fair_id = %d AND slug = %s",
                    $fair_id,
                    $slug
                )
            );

            // --------------------------------------------------
            // Rebuild data: ONE entry per year
            // --------------------------------------------------
            $pairs = [];

            if (!empty($existing_data)) {
                $items = array_filter(explode(';', $existing_data));

                foreach ($items as $item) {
                    if (!str_contains($item, '-')) {
                        continue;
                    }

                    [$y, $id] = explode('-', $item, 2);

                    // Keep only entries for other years
                    if ($y !== $year) {
                        $pairs[$y] = $y . '-' . $id;
                    }
                }
            }

            // Overwrite / add current year
            $pairs[$year] = $year . '-' . $catalog_id;

            $new_data = implode(';', $pairs) . ';';

            // --------------------------------------------------
            // Insert or update
            // --------------------------------------------------
            $query = $wpdb->prepare(
                "INSERT INTO fair_adds (fair_id, slug, data)
                VALUES (%d, %s, %s)
                ON DUPLICATE KEY UPDATE data = VALUES(data)",
                $fair_id,
                $slug,
                $new_data
            );

            $result = $wpdb->query($query);

            if ($result === false) {
                $log("DB ERROR: " . $wpdb->last_error);
            } else {
                $log("OK | saved data={$new_data}");
            }
        }

        $log("DONE");
        $log("--------------------------------------------------");
    }

    private static function inject_config($atts) {
        $catalog_year = !empty($atts['archive_catalog_year']) ? trim($atts['archive_catalog_year']) : '';

        $base_path = '/wp-content/uploads/exhibitor-catalogs/';
        $default_file = 'pwe-exhibitors.json';
        $data_url = $base_path . $default_file;

        if (!empty($catalog_year)) {
            $year_file = 'pwe-exhibitors-' . $catalog_year . '.json';
            $year_file_path = ABSPATH . ltrim($base_path, '/') . $year_file;

            if (file_exists($year_file_path)) {
                $data_url = $base_path . $year_file;
            }
        }

        wp_add_inline_script(
            'vue-catalog',
            'window.VUE_CATALOG_CONFIG = ' . json_encode([
                'csrfToken' => wp_create_nonce('catalog_action'),
                'dataUrl' => site_url($data_url),
                'atts'    => $atts,
                'locale'  => get_locale(),
            ]) . ';',
            'before'
        );
    }
}
