<?php
ob_start();

$js_base_url  = plugin_dir_url(__FILE__)  . 'assets/js/';
$js_base_path = plugin_dir_path(__FILE__) . 'assets/js/';

$css_base_url  = plugin_dir_url(__FILE__)  . 'assets/css/';
$css_base_path = plugin_dir_path(__FILE__) . 'assets/css/';

$ver = static function($p){ return file_exists($p) ? filemtime($p) : null; };

wp_enqueue_style(
    'exhibitor-catalog-main-css',
    $css_base_url . 'exhibitor_catalog_main.css',
    [],
    $ver($css_base_path . 'exhibitor_catalog_main.css'),
    'all'
);

wp_enqueue_style(
    'exhibitor-catalog-single-css',
    $css_base_url . 'exhibitor_catalog_single.css',
    [],
    $ver($css_base_path . 'exhibitor_catalog_single.css'),
    'all'
);

wp_enqueue_style(
    'product-modal-css',
    $css_base_url . 'exhibitors-product-modal.css',
    [],
    $ver($css_base_path . 'exhibitors-product-modal.css'),
    'all'
);

wp_enqueue_style(
    'exhibitors-documents-modal-css',
    $css_base_url . 'exhibitor-documents-modal.css',
    [],
    $ver($css_base_path . 'exhibitor-documents-modal.css'),
    'all'
);

wp_enqueue_script(
    'product-modal',
    $js_base_url . 'exhibitors-product-modal.js',
    [],
    $ver($js_base_path . 'exhibitors-product-modal.js'),
    true
);

wp_enqueue_script(
  'exhibitors-documents-modal',
  $js_base_url . 'exhibitor-documents-modal.js',
  [],
  $ver($js_base_path . 'exhibitor-documents-modal.js'),
  true
);

$requested_id = isset($_GET['exhibitor_id']) ? (int)$_GET['exhibitor_id'] : 0;

if ($requested_id > 0) {

    $single_exhibitor = $exhibitors_by_id[$requested_id] ?? null;

    include plugin_dir_path(__FILE__) . 'exhibitor_catalog_single.php';

    $output = ob_get_clean();
    return $output;
}

wp_enqueue_script(
  'exhibitors-map-polish',
  $js_base_url . 'exhibitors-map-polish.js',
  [],
  $ver($js_base_path . 'exhibitors-map-polish.js'),
  true
);

// 1) CORE (utils + stan + renderCard)
wp_enqueue_script(
  'exhibitors-core',
  $js_base_url . 'exhibitors-core.js',
  [],
  $ver($js_base_path . 'exhibitors-core.js'),
  true
);

// 2) FILTRY
wp_enqueue_script(
  'exhibitors-filters',
  $js_base_url . 'exhibitors-filters.js',
  ['exhibitors-core'],
  $ver($js_base_path . 'exhibitors-filters.js'),
  true
);

// 3) SORT
wp_enqueue_script(
  'exhibitors-sort',
  $js_base_url . 'exhibitors-sort.js',
  ['exhibitors-core'],
  $ver($js_base_path . 'exhibitors-sort.js'),
  true
);

// 4) PAGINACJA
wp_enqueue_script(
  'exhibitors-pagination',
  $js_base_url . 'exhibitors-pagination.js',
  ['exhibitors-core','exhibitors-sort'],
  $ver($js_base_path . 'exhibitors-pagination.js'),
  true
);

// 5) WYSZUKIWANIE
wp_enqueue_script(
  'exhibitors-search',
  $js_base_url . 'exhibitors-search.js',
  ['exhibitors-core','exhibitors-pagination'],
  $ver($js_base_path . 'exhibitors-search.js'),
  true
);

// 6) INIT — Twój obecny script.js
wp_enqueue_script(
  'exhibitors-init',
  $js_base_url . 'script.js',
  ['exhibitors-core','exhibitors-filters','exhibitors-sort','exhibitors-pagination','exhibitors-search'],
  $ver($js_base_path . 'script.js'),
  true
);

include  plugin_dir_path(__FILE__) . 'exhibitor_catalog_main.php';

$output = ob_get_clean();

return $output;