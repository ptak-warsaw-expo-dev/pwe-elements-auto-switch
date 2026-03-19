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
            $deleted = self::clear_all_transients();
            wp_die('Dane są zaktualizowane. Zaktualizowanych wpisów: ' . $deleted . '.');
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