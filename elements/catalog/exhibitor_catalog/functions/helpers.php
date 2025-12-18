<?php
if (!defined('ABSPATH')) exit;

/**
 * Detects whether the visitor is on a mobile device based on User-Agent.
 */
function ec_is_mobile() {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    return preg_match(
        '/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',
        $_SERVER['HTTP_USER_AGENT']
    );
}


/**
 * Renders a PHP template and injects variables from $context.
 */
function ec_render($path, $context = []) {
    if (!file_exists($path)) {
        return '';
    }

    if (!empty($context) && is_array($context)) {
        extract($context, EXTR_SKIP); // inject variables safely
    }

    ob_start();
    include $path;
    return ob_get_clean();
}


/**
 * Reads fair theme colors (accent + secondary) from DB.
 */
function ec_get_catalog_colors() {
    $domain = do_shortcode('[trade_fair_domainadress]');
    $db     = PWECommonFunctions::connect_database();

    $sql = $db->prepare("
        SELECT
            MAX(CASE WHEN fa.slug = 'fair_color_accent_catlog' THEN fa.data END) AS accent,
            MAX(CASE WHEN fa.slug = 'fair_color_main2_catlog' THEN fa.data END) AS main2
        FROM fair_adds fa
        JOIN fairs f ON f.id = fa.fair_id
        WHERE f.fair_domain = %s
    ", $domain);

    $res = $db->get_results($sql, ARRAY_A);

    return [
        'accent' => $res[0]['accent'] ?: 'var(--accent-color)',
        'main2'  => $res[0]['main2']  ?: 'var(--main2-color)',
    ];
}


/**
 * Returns only the essential exhibitor information.
 * Used for brand and product associations.
 */
function exhibitor_catalog_min_info($exhibitor) {
    return [
        'exhibitor_id'           => $exhibitor['exhibitor_id'] ?? '',
        'exhibitor_name'         => $exhibitor['name'] ?? '',
        'exhibitor_stand_number' => $exhibitor['stand_number'] ?? '',
        'hall_name'              => $exhibitor['hall_name'] ?? '',
    ];
}


/**
 * PAGINATION: BUILD PAGE-LINK WITH QUERY ARGS
 */
function ec_paginate_build_link($page, $params) {
    $params['exh-page'] = $page;
    return add_query_arg($params, get_permalink());
}