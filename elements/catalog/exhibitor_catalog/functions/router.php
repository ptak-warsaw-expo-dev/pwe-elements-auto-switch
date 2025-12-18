<?php
if (!defined('ABSPATH')) exit;

/**
 * -----------------------------------------------------------------------------
 * Helpers: project paths and URLs
 * -----------------------------------------------------------------------------
 */

function ec_path($append = '') {
    return EX_PATH . ($append ? '/' . ltrim($append, '/') : '');
}

function ec_url($append = '') {
    return EX_URL . ($append ? '/' . ltrim($append, '/') : '');
}

function ec_view_path($view, $file = '') {
    $base = EX_PATH . "/views/{$view}";
    return $file ? $base . '/' . ltrim($file, '/') : $base;
}


/**
 * Device detection → select view type
 */
function ec_get_view_type() {
    return ec_is_mobile() ? 'mobile' : 'desktop';
}


/**
 * -----------------------------------------------------------------------------
 * Router: determine which page type is currently requested
 * -----------------------------------------------------------------------------
 *
 * main               → default directory view
 * single-exhibitor   → exhibitor detail page
 * single-product     → exhibitor product detail
 */
function ec_router() {

    if (isset($_GET['exhibitor_id']) && is_numeric($_GET['exhibitor_id'])) {

        if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
            return 'single-product';
        }

        return 'single-exhibitor';
    }

    return 'main';
}


/**
 * Load view component (CSS, JS, PHP) with fallback: mobile → desktop
 */
function ec_load_view($folder, $file = null, $context = []) {

    global $ec_current_page_view;

    if (!$ec_current_page_view) {
        $ec_current_page_view = ec_router();
    }

    $view = ec_get_view_type();
    $base = $folder;

    $dir_mobile  = EX_PATH . "/views/mobile/{$ec_current_page_view}/{$folder}";
    $dir_desktop = EX_PATH . "/views/desktop/{$ec_current_page_view}/{$folder}";

    $url_mobile  = EX_URL . "/views/mobile/{$ec_current_page_view}/{$folder}";
    $url_desktop = EX_URL . "/views/desktop/{$ec_current_page_view}/{$folder}";

    // CSS
    $css_file = null;
    $css_url  = null;

    if ($view === 'mobile' && file_exists($dir_mobile . "/{$base}-style.css")) {
        $css_file = $dir_mobile . "/{$base}-style.css";
        $css_url  = $url_mobile . "/{$base}-style.css";

    } elseif (file_exists($dir_desktop . "/{$base}-style.css")) {
        $css_file = $dir_desktop . "/{$base}-style.css";
        $css_url  = $url_desktop . "/{$base}-style.css";
    }

    if ($css_file) {
        wp_enqueue_style(
            "ec-style-{$ec_current_page_view}-{$folder}",
            $css_url,
            [],
            filemtime($css_file)
        );
    }

    // JS
    $js_file = null;
    $js_url  = null;

    if ($view === 'mobile' && file_exists($dir_mobile . "/{$base}-script.js")) {
        $js_file = $dir_mobile . "/{$base}-script.js";
        $js_url  = $url_mobile . "/{$base}-script.js";

    } elseif (file_exists($dir_desktop . "/{$base}-script.js")) {
        $js_file = $dir_desktop . "/{$base}-script.js";
        $js_url  = $url_desktop . "/{$base}-script.js";
    }

    if ($js_file) {
        wp_enqueue_script(
            "ec-script-{$ec_current_page_view}-{$folder}",
            $js_url,
            [],
            filemtime($js_file),
            true
        );
    }

    // PHP fallback loader
    $php_file = null;

    if ($view === 'mobile') {

        if (file_exists($dir_mobile . "/{$base}.php")) {
            $php_file = $dir_mobile . "/{$base}.php";

        } elseif (file_exists($dir_desktop . "/{$base}.php")) {
            $php_file = $dir_desktop . "/{$base}.php";
        }

    } else {

        if (file_exists($dir_desktop . "/{$base}.php")) {
            $php_file = $dir_desktop . "/{$base}.php";
        }
    }

    if (!$php_file) {
        return "<!-- Missing view: {$ec_current_page_view}/{$folder}/{$base}.php -->";
    }

    return ec_render($php_file, $context);
}


/**
 * -----------------------------------------------------------------------------
 * Load a single component from its folder
 * -----------------------------------------------------------------------------
 */
function ec_load_components($component, $context = []) {

    global $ec_current_page_view;

    if (!$ec_current_page_view) {
        $ec_current_page_view = ec_router();
    }

    $view = ec_get_view_type();

    $dir      = EX_PATH . "/views/{$view}/{$ec_current_page_view}/{$component}";
    $fallback = EX_PATH . "/views/desktop/{$ec_current_page_view}/{$component}";

    $dir_final = is_dir($dir) ? $dir : $fallback;

    if (!is_dir($dir_final)) {
        return "<!-- Missing component folder: {$dir_final} -->";
    }

    $php_file = $dir_final . "/{$component}.php";

    if (!file_exists($php_file)) {
        return "<!-- Missing component file: {$php_file} -->";
    }

    return ec_render($php_file, $context);
}


/**
 * -----------------------------------------------------------------------------
 * Render main view
 * -----------------------------------------------------------------------------
 */
function ec_render_main_view($context) {

    ec_auto_enqueue_assets();
    
    global $ec_current_page_view;

    if (!$ec_current_page_view) {
        $ec_current_page_view = ec_router();
    }

    $view_type = ec_get_view_type();

    // MAIN (old structure)
    if ($ec_current_page_view === 'main') {

        $file = EX_PATH . 'main/main.php';

    } else {

        $file = EX_PATH . "/views/{$view_type}/{$ec_current_page_view}/{$ec_current_page_view}.php";

        if (!file_exists($file) && $view_type !== 'desktop') {
            $file = EX_PATH . "/views/desktop/{$ec_current_page_view}/{$ec_current_page_view}.php";
        }

        /**
         * Attach CSS/JS for single-exhibitor / single-product
         */
        $dir      = EX_PATH . "/views/{$view_type}/{$ec_current_page_view}";
        $fallback = EX_PATH . "/views/desktop/{$ec_current_page_view}";

        $dir_final = is_dir($dir) ? $dir : $fallback;
        $url_final = is_dir($dir)
            ? EX_URL . "/views/{$view_type}/{$ec_current_page_view}"
            : EX_URL . "/views/desktop/{$ec_current_page_view}";

        $base = $ec_current_page_view;

        $css_file = $dir_final . "/{$base}-style.css";
        if (file_exists($css_file)) {
            wp_enqueue_style(
                "ec-style-{$base}",
                $url_final . "/{$base}-style.css",
                [],
                filemtime($css_file)
            );
        }

        $js_file = $dir_final . "/{$base}-script.js";
        if (file_exists($js_file)) {
            wp_enqueue_script(
                "ec-script-{$base}",
                $url_final . "/{$base}-script.js",
                [],
                filemtime($js_file),
                true
            );
        }
    }

    if (!file_exists($file)) {
        return "<!-- Missing main view file: {$file} -->";
    }

    return ec_render($file, $context);
}