<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define('PWE_CLEAR_TOKEN', 'h00T8hbDdv45K3V');

class PWE_Clear_Transients {

    public static function init() {
        add_action('wp_loaded', [self::class, 'handle_request'], 99);
    }

    public static function handle_request() {
        if (
            isset($_GET['pwe_clear_transients']) &&
            is_string($_GET['pwe_clear_transients']) &&
            hash_equals(PWE_CLEAR_TOKEN, $_GET['pwe_clear_transients'])
        ) {

            if (!defined('DONOTCACHEPAGE')) {
                define('DONOTCACHEPAGE', true);
            }

            nocache_headers();

            // Refresh database json files cache
            if (class_exists('PWE_Functions')) {
                PWE_Functions::refresh_database_json_cache();
            }

            if (class_exists('PWECommonFunctions')) {
                PWECommonFunctions::refresh_database_json_cache();
            }

            $deleted = self::clear_all_transients();

            self::create_plans_news();

            // Clear WP Rocket after all data and page updates.
            $rocket_status = self::clear_wp_rocket_cache();

            header('Content-Type: text/html; charset=utf-8');
            echo '<div style="display:flex;flex-direction:column;text-align:center;align-items:center;">';
            echo '<h1>Dane są zaktualizowane!</h1>';
            foreach ($rocket_status as $message) {
                echo '<p style="margin:0;">' . esc_html($message) . '</p>';
            }
            echo '<p style="margin:0;">Zaktualizowanych wpisów: ' . (int)$deleted . '</p>';

            echo '<a style="font-size:24px;" href="/">Na stronę główną 🡒</a>';

            echo '<p id="countdown">Zamknięcie karty za 10 sekund...</p>
                  <script>
                    let time = 10;
                    const el = document.getElementById("countdown");
                    
                    const timer = setInterval(() => {
                        time--;
                        el.textContent = "Zamknięcie karty za " + time + " sekund...";
                        
                        if (time <= 0) {
                            clearInterval(timer);
                            window.close();
                        }
                    }, 1000);
                  </script>';
                  
            echo '</div>';

            exit;
        }
    }

    /**
     * Clear optimized assets before clearing the page cache.
     */
    private static function clear_wp_rocket_cache(): array {

        $operations = [
            'rocket_clean_minify' => 'CSS/JS (minify)',
            'rocket_clean_cache_busting' => 'Cache busting',
            'rocket_clean_domain' => 'Cache stron',
        ];

        $status = [];

        foreach ($operations as $function => $label) {
            if (!function_exists($function)) {
                $status[] = 'WP Rocket: ' . $label . ' - funkcja niedostępna.';
                error_log('[PWE Cache] ' . $function . ' not available');
                continue;
            }

            try {
                $function();
                $status[] = 'WP Rocket: ' . $label . ' - wykonano czyszczenie.';
            } catch (\Throwable $e) {
                $status[] = 'WP Rocket: ' . $label . ' - błąd czyszczenia.';
                error_log('[PWE Cache] ' . $function . ': ' . $e->getMessage());
            }
        }

        return $status;
    }

    /**
     * Clear all PWE transients from database
     */
    public static function clear_all_transients(): int {

        global $wpdb;

        $deleted = $wpdb->query(
            "DELETE FROM {$wpdb->options}
            WHERE option_name LIKE '\_transient\_pwe\_%'
                OR option_name LIKE '\_transient\_timeout\_pwe\_%'"
        );

        return (int)$deleted;
    }

    /**
     * Create news posts for all fair plans based on the data files
     */
    public static function create_plans_news(): void {

        require_once plugin_dir_path(dirname(__FILE__))
            . 'elements/fair-plan/fair-plan/fair-plan.php';

        /*
        * Strony główne:
        * /plan-targow/
        * /fair-plan/
        */
        Fair_Plan::create_or_update_fair_plan_pages();

        /*
        * Wpisy roczne:
        * /plan-targow-{rok}/
        * /fair-plan-{rok}/
        * itd.
        */
        $files = PWE_Functions::get_database_fairs_data_files();

        Fair_Plan::create_missing_news_for_files($files);
    }

}

PWE_Clear_Transients::init();