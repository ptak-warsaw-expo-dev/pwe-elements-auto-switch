<?php
if (!defined('ABSPATH')) exit;

/**
 * Load all MAIN view components (JS + CSS + PHP)
 */
$view_type = ec_get_view_type();
$main_dir  = EX_PATH . "/views/{$view_type}/main/";

$components = glob($main_dir . '/*', GLOB_ONLYDIR);
if (!empty($components)) {
    foreach ($components as $component_path) {
        $component = basename($component_path);
        ec_load_view($component, 'index.php', $context);
    }
}

/**
 * 1) Prepare base data
 */
$exhibitors = $context['exhibitors'] ?? [];
$atts       = $context['atts']       ?? [];
$view       = $context['view']       ?? 'desktop';

$per_page     = 10;
$current_page = isset($_GET['exh-page']) ? max(1, (int)$_GET['exh-page']) : 1;

/**
 * 2) Raw filter structures
 */
$filters_raw = ec_prepare_filters($exhibitors);
$all_items   = $filters_raw['all_items'];

/**
 * 3) Apply filters
 */
$filter_result       = ec_apply_filters($exhibitors, $all_items);
$exhibitors_filtered = $filter_result['filtered'];
$types_for_display   = $filter_result['types_for_display'];
$visible_filters     = $filter_result['visible_filters'];

/**
 * 4) Sorting
 */
$sort_mode         = $_GET['sort'] ?? ($atts['sort'] ?? 'default');
$exhibitors_sorted = ec_sort_exhibitors($exhibitors_filtered, $sort_mode);
if ($sort_mode === 'default' && !empty($atts['exhibitor_changer']) && is_string($atts['exhibitor_changer']) && is_array($exhibitors_sorted)) {
    $exhibitors_sorted = ec_apply_manual_order_changes(
        $atts['exhibitor_changer'],
        $exhibitors_sorted
    );
}

/**
 * 5) Build unified dataset (exhibitor -> brand -> product)
 */
$block_exhibitors = [];
$block_brands     = [];
$block_products   = [];

foreach ($exhibitors_sorted as $ex) {

    $ex_id    = (string)($ex['exhibitor_id'] ?? uniqid('ex_', true));
    $hall     = trim((string)($ex['hall_name'] ?? ''));
    $sectors  = !empty($ex['catalog_tags']) && is_array($ex['catalog_tags'])
        ? array_values(array_filter(array_map('trim', $ex['catalog_tags'])))
        : [];
    $brands   = $ex['brands']   ?? [];
    $products = $ex['products'] ?? [];

    // normalized category set
    $ex_categories      = [];
    $ex_category_labels = [];

    foreach ($products as $p) {
        if (!empty($p['tags']) && is_array($p['tags'])) {
            foreach ($p['tags'] as $t) {
                $key = ec_normalize_tag_key($t);
                if ($key === '') continue;
                $ex_categories[] = $key;
                $ex_category_labels[$key] = trim($t);
            }
        }
    }

    $ex_categories = array_values(array_unique($ex_categories));

    // exhibitor entry
    $block_exhibitors[] = [
        'id'              => $ex_id,
        'type'            => 'exhibitor',
        'hall'            => $hall,
        'sectors'         => $sectors,
        'categories'      => $ex_categories,
        'category_labels' => $ex_category_labels,
        'data'            => $ex,
    ];

    // brand entries
    if (!empty($brands)) {
        foreach ($brands as $brand) {
            $block_brands[] = [
                'id'              => $ex_id . '::brand::' . $brand,
                'type'            => 'brand',
                'hall'            => $hall,
                'sectors'         => $sectors,
                'categories'      => $ex_categories,
                'category_labels' => $ex_category_labels,
                'data'            => [
                    'brand'     => $brand,
                    'exhibitor' => exhibitor_catalog_min_info($ex),
                ],
            ];
        }
    }

    // product entries
    if (!empty($products)) {
        foreach ($products as $i => $product) {

            $pid = $ex_id . '::prod::' . ($product['name'] ?? md5(json_encode($product)));
            $product_tags   = [];
            $product_labels = [];

            if (!empty($product['tags']) && is_array($product['tags'])) {
                foreach ($product['tags'] as $t) {
                    $key = ec_normalize_tag_key($t);
                    if ($key === '') continue;
                    $product_tags[]        = $key;
                    $product_labels[$key]  = trim($t);
                }
            }

            $product_tags = array_values(array_unique($product_tags));

            $block_products[] = [
                'id'              => $pid,
                'product_id'      => $i,
                'exhibitor_id'    => $ex_id,
                'type'            => 'product',
                'hall'            => $hall,
                'sectors'         => $sectors,
                'categories'      => $product_tags,
                'category_labels' => $product_labels,
                'data'            => [
                    'product'   => $product,
                    'exhibitor' => exhibitor_catalog_min_info($ex),
                ],
            ];
        }
    }
}

/**
 * 6) Merge all blocks
 */
$page_source_items = array_merge(
    $block_exhibitors,
    $block_brands,
    $block_products
);

// collect category label map
$category_labels = [];
foreach ($page_source_items as $it) {
    if (!empty($it['category_labels'])) {
        foreach ($it['category_labels'] as $k => $v) {
            $category_labels[$k] = $v;
        }
    }
}

$page_source_items_filtered = array_values(array_filter(
    $page_source_items,
    'ec_item_matches_filters'
));

/**
 * 7) Pagination
 */
$total_items_count = count($page_source_items_filtered);
$total_pages       = max(1, ceil($total_items_count / $per_page));
$offset            = ($current_page - 1) * $per_page;

$page_items = array_slice($page_source_items_filtered, $offset, $per_page);

/**
 * Filter lists and counters
 */
$halls   = $filters_raw['halls'];
$sectors = $filters_raw['sectors'];
$brands  = $filters_raw['brands'];
$tags    = $filters_raw['tags'];

$hall_counts_filtered    = ec_count_filters($page_source_items_filtered, 'hall');
$sectors_counts_filtered = ec_count_filters($page_source_items_filtered, 'sector');
$brands_counts_filtered  = ec_count_filters($page_source_items_filtered, 'brand');
$tags_counts_filtered    = ec_count_filters($page_source_items_filtered, 'tag');

$hall_counts = [];
foreach ($halls as $h) {
    $hall_counts[$h] = $hall_counts_filtered[$h] ?? 0;
}

$sectors_counts = [];
foreach ($sectors as $s) {
    $sectors_counts[$s] = $sectors_counts_filtered[$s] ?? 0;
}

$brands_counts = [];
foreach ($brands as $b) {
    $brands_counts[$b] = $brands_counts_filtered[$b] ?? 0;
}

$tags_counts = [];
foreach ($tags as $t) {
    $tags_counts[$t] = $tags_counts_filtered[$t] ?? 0;
}

/**
 * 8) Context for views
 */
$context_view = [
    'exhibitors'        => $exhibitors,
    'exhibitors_sorted' => $exhibitors_sorted,

    'all_items'         => $all_items,

    'halls'             => $halls,
    'hall_counts'       => $hall_counts,

    'sectors'           => $sectors,
    'sectors_counts'    => $sectors_counts,

    'brands'            => $brands,
    'brands_counts'     => $brands_counts,

    'tags'              => $tags,
    'tags_counts'       => $tags_counts,
    'category_labels'   => $category_labels,

    'visible_filters'   => $visible_filters,

    'page_items'        => $page_items,
    'total_items'       => $total_items_count,
    'total_pages'       => $total_pages,
    'current_page'      => $current_page,
    'sort'              => $sort_mode,

    'atts'              => $atts,
    'view'              => $view,
];

/**
 * 9) Output (clean, structured HTML)
 */
$output  = '';
$output .= '
<div id="exhibitorCatalog" class="exhibitor-catalog ' . (ec_is_mobile() ? 'exhibitor-catalog-mobile' : '') . '" 
     style="visibility:hidden;opacity:0;transition:.3s ease;transition-delay:400ms;">

    <div class="exhibitor-catalog__header">
        <h1 class="exhibitor-catalog__header-title">Katalog wystawców</h1>
    </div>';

$output .= ec_load_view('panel', 'panel.php', $context_view);

$output .= '
    <div class="exhibitor-catalog__content">

        <div class="exhibitor-catalog__sidebar">
';
$output .= ec_load_view('filters', 'filters.php', $context_view);

$output .= '
        </div>

        <div class="exhibitor-catalog__main">

            <div class="exhibitor-catalog__items">
';

/**
 * Render item cards
 */
foreach ($page_items as $item) {

    if ($item['type'] === 'exhibitor') {
        $output .= ec_load_view('card-exhibitor', 'card-exhibitor.php', [
            'ex' => $item['data']
        ]);
    }

    if ($item['type'] === 'brand') {
        $output .= ec_load_view('card-brand', 'card-brand.php', [
            'item' => $item
        ]);
    }

    if ($item['type'] === 'product') {
        $output .= ec_load_view('card-product', 'card-product.php', [
            'item' => $item
        ]);
    }
}

$output .= '
            </div>
';

$output .= ec_load_view('pagination', 'pagination.php', $context_view);

$output .= '
        </div>

    </div>

    <div class="exhibitor-catalog__spinner" style="display:none;">
        <div class="exhibitor-catalog__spinner-inner"></div>
    </div>';

    $output .= ec_load_view('feedback', 'feedback.php', $context_view);

$output .= '
</div>';

/**
 * Embed JSON dataset
 */
echo '<script id="exhibitorFiltersData" type="application/json">'
     . wp_json_encode(['items' => $page_source_items])
     . '</script>';

/**
 * Translate
 */
echo '<div id="google_translate_element" style="display:none";></div>
<script src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>';

echo $output;
