<?php
ob_start();

$css_base_url  = plugin_dir_url(__FILE__)  . 'assets/css/';
$css_base_path = plugin_dir_path(__FILE__) . 'assets/css/';

$js_base_url  = plugin_dir_url(__FILE__)  . 'assets/js/';
$js_base_path = plugin_dir_path(__FILE__) . 'assets/js/';

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
    'exhibitors-product-modal-css',
    $css_base_url . 'exhibitors-product-modal.css',
    [],
    $ver($css_base_path . 'exhibitors-product-modal.css'),
    'all'
);

wp_enqueue_script(
  'exhibitors-product-modal',
  $js_base_url . 'exhibitors-product-modal.js',
  [],
  $ver($js_base_path . 'exhibitors-product-modal.js'),
  true
);

include plugin_dir_path(__FILE__) . 'svg-icon.php';

$requested_id = isset($_GET['exhibitor_id']) ? (int)$_GET['exhibitor_id'] : 0;

if ($requested_id > 0) {

    $single_exhibitor = $exhibitors_by_id[$requested_id] ?? null;

    include plugin_dir_path(__FILE__) . 'exhibitor_catalog_single.php';

    wp_enqueue_script(
        'exhibitor_catalog_single.js',
        $js_base_url . 'exhibitor_catalog_single.js',
        [],
        $ver($js_base_path . 'exhibitor_catalog_single.js'),
        true
    );

    $output = ob_get_clean();
    return $output;
}

wp_enqueue_script(
  'exhibitors-init',
  $js_base_url . 'script.js',
  [],
  $ver($js_base_path . 'script.js'),
  true
);

include  plugin_dir_path(__FILE__) . 'exhibitor_catalog_main.php';

$output = ob_get_clean();

return $output;