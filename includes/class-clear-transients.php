<?php
if ( ! defined( 'ABSPATH' ) ) exit;

define('PWE_CLEAR_TOKEN', 'h00T8hbDdv45K3V');

class PWE_Clear_Transients {

    public static function init() {
        add_action('template_redirect', [self::class, 'handle_request']);
    }

    public static function handle_request() {
        if (
            isset($_GET['pwe_clear_transients']) &&
            $_GET['pwe_clear_transients'] === PWE_CLEAR_TOKEN
        ) {
            // WP Rocket (clean cache)
            if ( function_exists( 'rocket_clean_domain' ) ) {
                rocket_clean_domain();
            }

            $deleted = self::clear_all_transients();

            header('Content-Type: text/html; charset=utf-8');
            echo '<div style="display:flex;flex-direction:column;text-align:center;align-items:center;">';
            echo '<h1>Dane są zaktualizowane!</h1>';
            echo '<p style="margin:0;">Cache został wyczyszczony.</p>';
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

}

PWE_Clear_Transients::init();