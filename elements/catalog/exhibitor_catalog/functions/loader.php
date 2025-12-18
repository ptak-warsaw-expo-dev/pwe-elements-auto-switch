<?php
if (!defined('ABSPATH')) exit;

/**
 * -----------------------------------------------------------------------------
 * Function: ec_load_functions
 * -----------------------------------------------------------------------------
 * Automatically loads ALL PHP files from the current /functions directory,
 * including subdirectories.
 *
 * Loader excludes itself (loader.php) to prevent accidental recursion.
 *
 * This function should be executed once during plugin/theme initialization.
 * -----------------------------------------------------------------------------
 */
function ec_load_functions() {

    $base = __DIR__;

    // Iterator scans recursively through folders
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {

        // Load only PHP files except this loader
        if (
            $file->getExtension() === 'php'
            && $file->getFilename() !== 'loader.php'
        ) {
            require_once $file->getPathname();
        }
    }
}

/**
 * Automatyczne ładowanie wszystkich plików CSS i JS
 * z katalogów /assets/css/ oraz /assets/js/ wewnątrz wtyczki.
 */
function ec_auto_enqueue_assets() {

    // Ścieżki fizyczne wtyczki
    $css_dir = EX_PATH . 'assets/css/';
    $js_dir  = EX_PATH . 'assets/js/';

    // URL-e do wtyczki
    $css_url_base = EX_URL . 'assets/css/';
    $js_url_base  = EX_URL . 'assets/js/';

    // Wczytywanie CSS
    if (is_dir($css_dir)) {
        foreach (glob($css_dir . '*.css') as $style) {
            $handle = 'ec-' . basename($style, '.css');

            wp_enqueue_style(
                $handle,
                $css_url_base . basename($style),
                array(),
                filemtime($style)
            );
        }
    }

    // Wczytywanie JS
    if (is_dir($js_dir)) {
        foreach (glob($js_dir . '*.js') as $script) {
            $handle = 'ec-' . basename($script, '.js');

            wp_enqueue_script(
                $handle,
                $js_url_base . basename($script),
                array('jquery'),     // jeżeli niepotrzebne, możesz dać array()
                filemtime($script),
                true                 // ładowanie w stopce
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'ec_auto_enqueue_assets');