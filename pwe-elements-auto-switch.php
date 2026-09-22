<?php
/*
 * Plugin Name: PWE Elements AutoSwitch
 * Plugin URI: https://github.com/ptak-warsaw-expo-dev/pwe-elements-auto-switch
 * Description: Elements that dynamically adapt to groups.
 * Version: 1.8.9.1
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
if (!defined('PWE_LANG')) {
    define('PWE_LANG', substr(determine_locale(), 0, 2)); // Get the current locale (pl/en/de/...)
}

date_default_timezone_set('Europe/Warsaw');

/**
 * Migration compatibility.
 *
 * PWE System is optional. This plugin must always work on its own.
 * When PWE System is active and its shared functions module is available,
 * prefer the new implementation. Otherwise load the full local legacy code.
 */
if (!function_exists('pwe_elements_has_pwe_system')) {
    function pwe_elements_has_pwe_system() {
        $plugin = 'pwe-system/pwe-system.php';
        $plugin_file = trailingslashit(WP_PLUGIN_DIR) . $plugin;
        $system_functions = trailingslashit(WP_PLUGIN_DIR) . 'pwe-system/core/class-pwe-system-functions.php';

        if (!is_file($plugin_file) || !is_file($system_functions)) {
            return false;
        }

        if (defined('PWE_SYSTEM_FILE') || class_exists('PWE_System', false)) {
            return true;
        }

        $active_plugins = (array) get_option('active_plugins', []);
        if (in_array($plugin, $active_plugins, true)) {
            return true;
        }

        if (is_multisite()) {
            $network_plugins = (array) get_site_option('active_sitewide_plugins', []);
            if (isset($network_plugins[$plugin])) {
                return true;
            }
        }

        return false;
    }
}

$pwe_use_system = pwe_elements_has_pwe_system();
$pwe_system_path = trailingslashit(WP_PLUGIN_DIR) . 'pwe-system/';

// Load all classes
require_once PWE_PLUGIN_PATH . 'includes/class-groups.php';
require_once PWE_PLUGIN_PATH . 'includes/class-elements-data.php';
require_once PWE_PLUGIN_PATH . 'includes/class-elements.php';

// 1. FUNCTIONS
// Prefer PWE System only when it is active and the module really exists.
if ($pwe_use_system) {
    require_once $pwe_system_path . 'core/class-pwe-system-functions.php';
}

// Hard fallback: the full old implementation stays inside this plugin.
if (!class_exists('PWE_Functions', false)) {
    require_once PWE_PLUGIN_PATH . 'includes/class-functions.php';
}

// 2. HIGH-LEVEL SHORTCODES
// With PWE System active, give it time to load backend [pwe_*] shortcodes first,
// then PWE_Shortcodes. If it does not, fall back to the local class.
if ($pwe_use_system) {
    add_action('plugins_loaded', function () {
        if (!class_exists('PWE_Shortcodes', false)) {
            require_once PWE_PLUGIN_PATH . 'includes/class-shortcodes.php';
        }
    }, 30);
} elseif (!class_exists('PWE_Shortcodes', false)) {
    require_once PWE_PLUGIN_PATH . 'includes/class-shortcodes.php';
}

require_once PWE_PLUGIN_PATH . 'includes/class-hooks.php';

require_once PWE_PLUGIN_PATH . 'includes/class-clear-transients.php';
require_once PWE_PLUGIN_PATH . 'includes/class-updater.php';
require_once PWE_PLUGIN_PATH . 'components/menu/menu.php';

// Load validators for phone and email
require_once PWE_PLUGIN_PATH . 'addons/phone-validator/phone-validator.php';
require_once PWE_PLUGIN_PATH . 'addons/email-validator/email-validator.php';

// Load registration log class
require_once PWE_PLUGIN_PATH . 'includes/class-registration-log.php';

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