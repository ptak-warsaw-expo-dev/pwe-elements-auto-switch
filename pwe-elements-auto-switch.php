<?php
/*
 * Plugin Name: PWE Elements AutoSwitch
 * Plugin URI: https://github.com/ptak-warsaw-expo-dev/pwe-elements-auto-switch
 * Description: Elements that dynamically adapt to groups.
 * Version: 1.4.7
 * Author: Anton Melnychuk
 * Co-author: Piotr Krupniewski, Marek Rumianek, Jakub Choła
 * Author URI: https://github.com/antonmelnychuk1
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI: https://github.com/ptak-warsaw-expo-dev/pwe-elements-auto-switch/releases/latest
 * Text Domain: pwe-elements-auto-switch
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define('PWE_PLUGIN_FILE', __FILE__); // Main plugin file path
define('PWE_PLUGIN_PATH', plugin_dir_path(__FILE__)); // Main plugin directory path
define('PWE_LANG', substr(determine_locale(), 0, 2)); // Get the current locale (pl/en/de/...)

date_default_timezone_set('Europe/Warsaw');

// Load all classes
require_once PWE_PLUGIN_PATH . 'includes/class-groups.php';
require_once PWE_PLUGIN_PATH . 'includes/class-elements-data.php';
require_once PWE_PLUGIN_PATH . 'includes/class-elements.php';
require_once PWE_PLUGIN_PATH . 'includes/class-functions.php';
require_once PWE_PLUGIN_PATH . 'includes/class-hooks.php';
require_once PWE_PLUGIN_PATH . 'includes/class-shortcodes.php';
require_once PWE_PLUGIN_PATH . 'includes/class-clear-transients.php';
require_once PWE_PLUGIN_PATH . 'includes/class-updater.php';

require_once PWE_PLUGIN_PATH . 'components/menu/menu.php';

if (!class_exists('Flip_Book')){
    require_once PWE_PLUGIN_PATH . 'elements/flip-book/flip-book.php';
}

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
            add_action('init', function() {
                new PWE_Updater();
            });

            // Initialize elements
            PWE_Elements::init(); 
        }
    }

    // Plugin start
    PWE_Elements_AutoSwitch::get_instance();
}
