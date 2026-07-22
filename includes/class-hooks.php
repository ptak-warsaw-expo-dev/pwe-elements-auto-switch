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

// Save Gravity Forms entries used by registration flows in the PHP session.
// add_action('gform_after_submission', function ($entry, $form) {
//     if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
//         session_start();
//     }

//     if (session_status() !== PHP_SESSION_ACTIVE) {
//         return;
//     }

//     $request_path = isset($_SERVER['REQUEST_URI'])
//         ? (string) parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH)
//         : '';
//     $current_path = trim(strtolower($request_path), '/');

//     $paths = [
//         'exhibitor' => ['zostan-wystawca', 'krok2', 'en/become-an-exhibitor', 'en/step2'],
//         'visitor'   => ['rejestracja', 'registration', 'en/registration'],
//     ];

//     $translations_file = WP_PLUGIN_DIR . '/pwe-multilang/website-translation.json';
//     if (is_readable($translations_file)) {
//         $translations = json_decode((string) file_get_contents($translations_file), true);
//         if (is_array($translations)) {
//             $translation_keys = [
//                 'exhibitor' => ['zostan_wystawca', 'krok2'],
//                 'visitor'   => ['rejestracja'],
//             ];

//             foreach ($translation_keys as $type => $keys) {
//                 foreach ($keys as $key) {
//                     if (empty($translations[$key]) || !is_array($translations[$key])) {
//                         continue;
//                     }
//                     foreach ($translations[$key] as $lang => $data) {
//                         if (empty($data['url'])) {
//                             continue;
//                         }
//                         $url = trim((string) $data['url'], '/');
//                         $paths[$type][] = $lang === 'pl' ? $url : trim($lang . '/' . $url, '/');
//                     }
//                 }
//             }
//         }
//     }

//     $paths = array_map(static function ($items) {
//         return array_values(array_unique(array_filter(array_map(static function ($path) {
//             return trim(strtolower((string) $path), '/');
//         }, $items))));
//     }, $paths);

//     $session_key = null;
//     if (in_array($current_path, $paths['exhibitor'], true)) {
//         $session_key = 'pwe_exhibitor_entry';
//     } elseif (in_array($current_path, $paths['visitor'], true)) {
//         $session_key = 'pwe_reg_entry';
//     }

//     if ($session_key === null) {
//         return;
//     }

//     $_SESSION[$session_key] = [
//         'entry_id' => isset($entry['id']) ? absint($entry['id']) : 0,
//     ];

//     if ($session_key === 'pwe_exhibitor_entry') {
//         $_SESSION[$session_key]['current_url'] = '/' . $current_path . '/';
//     }

//     foreach (($form['fields'] ?? []) as $field) {
//         $type = is_object($field) ? ($field->type ?? '') : ($field['type'] ?? '');
//         $id   = is_object($field) ? ($field->id ?? null) : ($field['id'] ?? null);
//         if (!$id || !in_array($type, ['email', 'phone'], true)) {
//             continue;
//         }
//         $value = isset($entry[(string) $id]) ? sanitize_text_field((string) $entry[(string) $id]) : '';
//         if ($value !== '') {
//             $_SESSION[$session_key][$type] = $value;
//         }
//     }
// }, 10, 2);