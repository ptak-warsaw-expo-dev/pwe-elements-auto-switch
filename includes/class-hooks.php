<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add filter to override menu output
add_action('plugins_loaded', function() {
    add_filter('pwe_override_menu_output', function($html) {
        ob_start();
        Menu::render('all');
        return ob_get_clean();
    });
});

// Loading registration classes (for AJAX and specific pages)
add_action('wp', function() {
    if (wp_doing_ajax()) {
        require_once PWE_PLUGIN_PATH . 'elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php';
        require_once PWE_PLUGIN_PATH . 'elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php';

        if (class_exists('Confirmation_Visitors_Registration')) {
            Confirmation_Visitors_Registration::init();
        }
        if (class_exists('Confirmation_Exhibitors_Registration')) {
            Confirmation_Exhibitors_Registration::init();
        }
        return;
    }

    $allowed_pages = ['potwierdzenie-rejestracji', 'potwierdzenie-rejestracji-wystawcy'];

    if (is_page($allowed_pages)) {
        require_once PWE_PLUGIN_PATH . 'elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php';
        require_once PWE_PLUGIN_PATH . 'elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php';

        if (class_exists('Confirmation_Visitors_Registration')) {
            Confirmation_Visitors_Registration::init();
        }
        if (class_exists('Confirmation_Exhibitors_Registration')) {
            Confirmation_Exhibitors_Registration::init();
        }
    }
});

// Redirects for trade fair plan and post-show reports
add_action('template_redirect', function () {

    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    if (!class_exists('PWE_Functions')) {
        return;
    }

    $cache_key = $_SERVER['HTTP_HOST'] ?? 'default';
    $transient_key = 'pwe_fairs_redirects_' . md5($cache_key);

    $redirects = get_transient($transient_key);

    if ($redirects === false) {

        $files = PWE_Functions::get_database_fairs_data_files();

        if (empty($files) || !is_array($files)) {
            return;
        }

        $redirects = [];
        $fair_plan_years = [];

        foreach ($files as $file) {
            if (($file->category_slug ?? '') === 'trade-fair-plan' && !empty($file->year)) {
                $fair_plan_years[] = (int) $file->year;
            }
        }

        if (!empty($fair_plan_years)) {
            $latest_year = max($fair_plan_years);

            $redirects['plan-targow-' . $latest_year] = home_url('/plan-targow/');
            $redirects['en/fair-plan-' . $latest_year] = home_url('/en/fair-plan/');
        }

        foreach ($files as $file) {

            if (($file->category_slug ?? '') !== 'post-show-report') {
                continue;
            }

            if (empty($file->year) || empty($file->language) || empty($file->file_path)) {
                continue;
            }

            $year = (string) $file->year;
            $lang = (string) $file->language;

            if ($lang === 'pl') {
                $redirects['post-show-' . $year] = 'https://cap.warsawexpo.eu' . $file->file_path;
            }

            if ($lang === 'en') {
                $redirects['en/post-show-' . $year] = 'https://cap.warsawexpo.eu' . $file->file_path;
            }
        }

        set_transient($transient_key, $redirects, 600);
    }

    if (!empty($redirects[$path])) {
        wp_redirect($redirects[$path], 301);
        exit;
    }
});
