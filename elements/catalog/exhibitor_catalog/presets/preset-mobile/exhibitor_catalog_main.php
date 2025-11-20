<?php
if (!defined('ABSPATH')) exit;

require_once __DIR__ . '/exhibitor_catalog_main_exhibitor_card.php';
require_once __DIR__ . '/exhibitor_catalog_main_brand_card.php';
require_once __DIR__ . '/exhibitor_catalog_main_product_card.php';
require_once __DIR__ . '/exhibitor_catalog_main_filters.php';

// --- USTAWIENIA PAGINACJI ---
$per_page = 30;
$current_page = isset($_GET['exh-page']) ? max(1, (int)$_GET['exh-page']) : 1;

// --- 1) ZBUDUJ JEDNĄ LISTĘ wg wybranych filtrów ---
$all_items = [];

$filter_result = exhibitor_catalog_apply_filters($exhibitors_prepared);
$exhibitors_filtered = $filter_result['filtered'];
$types_for_display   = $filter_result['types_for_display'];
$visible_filters     = $filter_result['visible_filters'];

$selected_types = $types_for_display;

// a) wystawcy
if (in_array('exhibitor', $selected_types, true)) {
    foreach ($exhibitors_filtered as $exhibitor) {

        // --- AKTYWNY FILTR MAREK ---
        if (!empty($selected_brands) && !empty($exhibitor['brands']) && is_array($exhibitor['brands'])) {

            // przefiltruj marki, żeby zostały tylko te z wybranych
            $filtered_brands = array_values(array_filter($exhibitor['brands'], function($brand) use ($selected_brands) {
                return in_array($brand, $selected_brands, true);
            }));

            // nadpisz listę marek tylko tymi, które użytkownik wybrał
            $exhibitor['brands'] = $filtered_brands;

            // jeśli po odfiltrowaniu nie ma żadnej pasującej marki — pomiń
            if (empty($filtered_brands)) {
                continue;
            }
        }

        $all_items[] = [
            'type' => 'exhibitor',
            'data' => $exhibitor,
        ];
    }
}

// b) marki — tylko jeśli wybrano filtr „brand”
if (in_array('brand', $selected_types, true)) {
    foreach ($exhibitors_filtered as $exhibitor) {

        if (empty($exhibitor['brands']) || !is_array($exhibitor['brands'])) {
            continue;
        }

        // jeśli jest aktywny filtr marek, pokaż tylko te, które użytkownik wybrał
        $brands_to_show = !empty($selected_brands)
            ? array_values(array_filter($exhibitor['brands'], function($brand) use ($selected_brands) {
                return in_array($brand, $selected_brands, true);
            }))
            : $exhibitor['brands'];

        if (empty($brands_to_show)) {
            continue;
        }

        // minimalne dane o wystawcy
        $exhibitor_info = [
            'exhibitor_id'           => $exhibitor['exhibitor_id'] ?? '',
            'exhibitor_name'         => $exhibitor['name'] ?? '',
            'exhibitor_stand_number' => $exhibitor['stand_number'] ?? '',
            'hall_name'              => $exhibitor['hall_name'] ?? '',
        ];

        // dodaj do listy tylko pasujące marki
        foreach ($brands_to_show as $brand) {
            $all_items[] = [
                'type' => 'brand',
                'data' => [
                    'brand'     => $brand,
                    'exhibitor' => $exhibitor_info,
                ],
            ];
        }
    }
}

// c) produkty — tylko jeśli wybrano „product”
if (in_array('product', $selected_types, true)) {
    foreach ($exhibitors_filtered as $exhibitor) {
        if (empty($exhibitor['products']) || !is_array($exhibitor['products'])) {
            continue;
        }

        $exhibitor_info = exhibitor_catalog_min_info($exhibitor);

        foreach ($exhibitor['products'] as $product) {
            // 🔸 jeśli są wybrane kategorie, filtruj po tagach
            if (!empty($selected_categories)) {
                $tags = array_map('trim', $product['tags'] ?? []);
                $matches = false;

                foreach ($tags as $tag) {
                    if (in_array($tag, $selected_categories, true)) {
                        $matches = true;
                        break;
                    }
                }

                if (!$matches) {
                    continue; // pomiń produkt spoza wybranych kategorii
                }
            }

            $all_items[] = [
                'type' => 'product',
                'data' => [
                    'product'   => $product,
                    'exhibitor' => $exhibitor_info,
                ],
            ];
        }
    }
}

// --- 2) Paginacja i liczenie filtrów ---
// UWAGA: liczymy tylko faktycznie przefiltrowane elementy, które mają być widoczne (zgodne z wybranymi typami)
$visible_items = array_filter($all_items, function($item) use ($types_for_display) {
    return in_array($item['type'], $types_for_display, true);
});

$total_items_count = count($visible_items);
$total_pages = max(1, (int)ceil($total_items_count / $per_page));
$offset = ($current_page - 1) * $per_page;
$page_items = array_slice(array_values($visible_items), $offset, $per_page);


$hall_counts    = exhibitor_catalog_count_filters($all_items, 'hall');
$sectors_counts = exhibitor_catalog_count_filters($all_items, 'sector');
$brands_counts  = exhibitor_catalog_count_filters($all_items, 'brand');
$tags_counts    = exhibitor_catalog_count_filters($all_items, 'tag');


// --- 2) ROZDZIEL TYLKO TE ELEMENTY Z BIEŻĄCEJ STRONY DO DWÓCH KONTENERÓW ---
$page_exhibitors = [];
$page_brands = [];
$page_products   = [];

foreach ($page_items as $item) {
    if ($item['type'] === 'exhibitor') {
        $page_exhibitors[] = $item['data'];
    } elseif ($item['type'] === 'brand') {
        $page_brands[] = $item['data'];
    } elseif ($item['type'] === 'product') {
        $page_products[] = $item['data']; // ['product'=>..., 'exhibitor'=>...]
    }
}

// --- 3) RENDER POMOCNICZY: linki paginacji (zachowujemy pozostałe query paramy) ---
function ec_build_page_url($page) {
    // Zachowaj istniejące parametry zapytania, ale podmień 'page'
    $params = $_GET;
    $params['exh-page'] = max(1, (int)$page);
    $base = strtok($_SERVER['REQUEST_URI'], '?');
    return esc_url( $base . '?' . http_build_query($params) );
}


$output .= '
<div id="exhibitorCatalog" class="exhibitor-catalog" style="visibility: hidden; opacity: 0; transition: .3s ease; transition-delay: 400ms;">

    <div class="exhibitor-catalog__header">

        <h1 class="exhibitor-catalog__header-title">Katalog wystawców</h1>

    </div>

    <div class="exhibitor-catalog__content">

        <div class="catalog-mobile-panel '. ($domain !== "warsawexpo.eu" ? "sticky-element" : "") .'">
            <div class="catalog-mobile-panel__wrapper">
                <div class="catalog-mobile-panel__search">
                    <form method="get" action="" class="catalog-mobile-panel__search-form">
                        <input type="text" 
                            class="catalog-mobile-panel__search-input" 
                            name="search"
                            value="'. (isset($_GET['search']) ? esc_attr($_GET['search']) : '') .'"
                            placeholder="Wyszukaj wystawcę" />
                    </form>
                </div>
                <div class="catalog-mobile-panel__results-wrapper">
                    <div class="catalog-mobile-panel__results">
                        <h2 class="catalog-mobile-panel__results-title">
                            Wyniki
                            <span class="catalog-mobile-panel__results-count">(' . $total_items_count . ')</span>
                        </h2>
                        <div class="catalog-mobile-panel__buttons">';
                            $current_sort = $_GET['sort_mode'] ?? 'standard';
                            $output .= exhibitor_catalog_render_sort_select($current_sort);
                            $output .= '
                            <button class="catalog-mobile-panel__filters-btn" id="filterMenu">
                                ' . pwe_svg_icon('filter') . '
                                Filtry
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        $output .= exhibitor_catalog_render_filters($all_items, $halls, $hall_counts, $sectors, $sectors_counts, $brands, $brands_counts, $products_tags, $tags_counts, $visible_filters);

        $output .= '
        <div class="exhibitor-catalog__main-columns">';
            // --- 4) RENDER HTML: przed Twoim JS, na Twoim $output ---
            $output .= '
            <div class="exhibitor-catalog__pagination-container">

                <div class="exhibitor-catalog__items-container">';

                    // ===== WYSTAWCY Z TEJ STRONY =====
                    if (!empty($page_exhibitors)) {

                        $output .= '
                        <div class="exhibitor-catalog__exhibitors-container">';

                        foreach ($page_exhibitors as $exhibitor) {

                            $output .= exhibitor_catalog_render_exhibitor_card($exhibitor);

                        }

                        $output .= '
                        </div>';

                    }

                    // ===== MAKI Z TEJ STRONY =====
                    if (!empty($page_brands)) {

                        $output .= '
                        <div class="catalog-mobile-brands-container">';

                        foreach ($page_brands as $brand) {

                            $output .= exhibitor_catalog_render_brand_card($brand);
                            
                        }

                        $output .= '
                        </div>';

                    }

                    // ===== PRODUKTY Z TEJ STRONY =====
                    if (!empty($page_products)) {

                        $output .= '
                        <div class="catalog-mobile-products-container">';

                        foreach ($page_products as $product) {

                            $output .= exhibitor_catalog_render_product_card($product);
                            
                        }

                        $output .= '
                        </div>';
                    }

                $output .= '
                </div><!-- /.exhibitor-catalog__items-container --> 
                ';

                // --- 5) PASEK NAWIGACJI PO STRONACH ---

                if ($current_page < $total_pages) {
                    $output .= '
                    <button id="exhibitorLoadMore" class="exhibitor-catalog__load-more">
                        Załaduj więcej
                    </button>';
                }

            $output .= '
            </div>

        </div>

    </div>

    <div class="exhibitor-catalog__spinner" style="display:none;">
        <div class="exhibitor-catalog__spinner-inner"></div>
    </div>

</div>';

echo $output;