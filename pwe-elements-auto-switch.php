<?php
/*
 * Plugin Name: PWE Elements AutoSwitch
 * Plugin URI: https://github.com/ptak-warsaw-expo-dev/pwe-elements-auto-switch
 * Description: Elements that dynamically adapt to groups.
 * Version: 1.1.2
 * Author: Anton Melnychuk
 * Co-author: Piotr Krupniewski, Marek Rumianek, Jakub Choła
 * Author URI: https://github.com/antonmelnychuk1
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://github.com/ptak-warsaw-expo-dev/pwe-elements-auto-switch/releases/latest
 * Text Domain: pwe-elements-auto-switch
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('PWE_PLUGIN_FILE', __FILE__);

// Load all classes
require_once plugin_dir_path( __FILE__ ) . 'includes/class-groups.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-elements-data.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-elements.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-hooks.php';

require_once plugin_dir_path( __FILE__ ) . 'components/menu/menu.php';

if ( ! class_exists( 'PWE_Elements_AutoSwitch' ) ) {

    final class PWE_Elements_AutoSwitch {

        /**
         * Singleton instance
         *
         * @var PWE_Elements_AutoSwitch|null
         */
        private static $instance = null;

        /**
         * Singleton
         */
        public static function get_instance() {
            if ( self::$instance === null ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Private constructor
         */
        private function __construct() {
            // Autoupdate
            $this->setup_updater();

            // PWE_Groups::init();
            PWE_Elements::init();
        }

        /**
         * Retrieving the GitHub key from the database
         *
         * @return string|null
         */
        private function get_github_key() {
            global $wpdb;

            $table_name = $wpdb->prefix . 'custom_klavio_setup';
            if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) != $table_name ) {
                return null;
            }

            $github_pre = $wpdb->prepare(
                "SELECT klavio_list_id FROM $table_name WHERE klavio_list_name = %s",
                'github_secret_2'
            );
            $github_result = $wpdb->get_results( $github_pre );

            if ( ! empty( $github_result ) ) {
                return $github_result[0]->klavio_list_id;
            }

            return null;
        }

        /**
         * Setting the auto-update mechanism
         */
        private function setup_updater() {
            $checker_file = plugin_dir_path( __FILE__ ) . 'plugin-update-checker/plugin-update-checker.php';

            if ( file_exists( $checker_file ) ) {
                require_once $checker_file;

                $updateChecker = Puc_v4_Factory::buildUpdateChecker(
                    'https://github.com/ptak-warsaw-expo-dev/pwe-elements-auto-switch',
                    __FILE__,
                    'pwe-elements-auto-switch'
                );

                // Key from the database
                $githubKey = $this->get_github_key();
                if ( $githubKey ) {
                    $updateChecker->setAuthentication( $githubKey );
                }

                // Downloading assets (release zips)
                $updateChecker->getVcsApi()->enableReleaseAssets();
            }
        }

    }

    // Plugin start
    PWE_Elements_AutoSwitch::get_instance();
}
