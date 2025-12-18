<?php
if (!defined('ABSPATH')) exit;

/**
 * Extract multiple filter values from GET parameter.
 * Supports comma-separated values and nested arrays.
 */
function ec_get_param_array($key) {
    if (!isset($_GET[$key])) {
        return [];
    }

    $raw = $_GET[$key];

    // Normalize array input → flatten → string
    if (is_array($raw)) {
        $flat = [];
        array_walk_recursive($raw, function($v) use (&$flat) {
            if ($v !== '' && $v !== null) {
                $flat[] = trim((string)$v);
            }
        });
        $raw = implode(',', $flat);
    }

    $parts = array_map('trim', explode(',', (string)$raw));
    $parts = array_filter($parts, fn($v) => $v !== '');

    return array_values(array_unique($parts));
}

/**
 * Build a clean query string leaving only allowed filter parameters.
 */
function ec_build_clear_filters_query() {
    $allowed = ['search', 'sort_mode'];
    $result  = [];

    foreach ($_GET as $key => $value) {
        if (in_array($key, $allowed, true)) {
            $result[$key] = $value;
        }
    }

    return http_build_query($result);
}


/**
 * Check whether unified item matches filters (type, hall, sectors, categories).
 */
function ec_item_matches_filters($item) {
    $sel_types      = ec_get_param_array('type');
    $sel_halls      = ec_get_param_array('hall');
    $sel_sectors    = ec_get_param_array('sector');
    $sel_categories = ec_get_param_array('category');

    // TYPE → OR
    if (!empty($sel_types) && !in_array($item['type'], $sel_types, true)) {
        return false;
    }

    // HALL → OR
    if (!empty($sel_halls) && !in_array($item['hall'] ?? '', $sel_halls, true)) {
        return false;
    }

    // SECTORS → OR
    if (!empty($sel_sectors)) {
        $item_sectors = $item['sectors'] ?? [];
        if (empty($item_sectors) || !array_intersect($item_sectors, $sel_sectors)) {
            return false;
        }
    }

    // CATEGORIES → OR
    if (!empty($sel_categories)) {
        $item_cats = $item['categories'] ?? [];
        if (empty($item_cats) || !array_intersect($item_cats, $sel_categories)) {
            return false;
        }
    }

    return true;
}


/**
 * Count filter values based on unified items produced by ec_prepare_filters().
 * Counts halls, sectors, brands, and product tags.
 */
function exhibitor_catalog_count_filters($all_items, $field_type) {
    $counts = [];

    foreach ($all_items as $row) {
        $type = $row['type'] ?? '';
        $data = $row['data'] ?? [];

        switch ($field_type) {

            // HALLS (exhibitor / brand / product)
            case 'hall':
                if ($type === 'exhibitor') {
                    $hall = trim((string)($data['hall_name'] ?? ''));
                } elseif ($type === 'brand') {
                    $hall = trim((string)($data['exhibitor']['hall_name'] ?? ''));
                } elseif ($type === 'product') {
                    $hall = trim((string)($data['exhibitor']['hall_name'] ?? ''));
                } else {
                    $hall = '';
                }

                if ($hall !== '') {
                    $counts[$hall] = ($counts[$hall] ?? 0) + 1;
                }
                break;

            // SECTORS (only exhibitor)
            case 'sector':
                if ($type === 'exhibitor' &&
                    !empty($data['catalog_tags']) &&
                    is_array($data['catalog_tags'])) {

                    foreach ($data['catalog_tags'] as $tag) {
                        $tag = trim($tag);
                        if ($tag !== '') {
                            $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                        }
                    }
                }
                break;

            // BRANDS (exhibitor + brand item)
            case 'brand':
                if ($type === 'exhibitor' &&
                    !empty($data['brands']) &&
                    is_array($data['brands'])) {

                    foreach ($data['brands'] as $brand) {
                        $brand = trim($brand);
                        if ($brand !== '') {
                            $counts[$brand] = ($counts[$brand] ?? 0) + 1;
                        }
                    }
                }

                if ($type === 'brand' && !empty($data['brand'])) {
                    $brand = trim($data['brand']);
                    if ($brand !== '') {
                        $counts[$brand] = ($counts[$brand] ?? 0) + 1;
                    }
                }
                break;

            // TAGS / CATEGORIES (exhibitor + product)
            case 'tag':

                // Exhibitor-level categories (from all their products)
                if ($type === 'exhibitor' &&
                    !empty($data['products']) &&
                    is_array($data['products'])) {

                    foreach ($data['products'] as $p) {
                        if (!empty($p['tags']) && is_array($p['tags'])) {
                            $clean = array_map(
                                'mb_strtolower',
                                ec_normalize_product_tags($p['tags'])
                            );
                            foreach ($clean as $tag) {
                                if ($tag !== '') {
                                    $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                                }
                            }
                        }
                    }
                }

                // Product-level categories
                if ($type === 'product' &&
                    !empty($data['product']['tags']) &&
                    is_array($data['product']['tags'])) {

                    $clean = array_map(
                        'mb_strtolower',
                        ec_normalize_product_tags($data['product']['tags'])
                    );

                    foreach ($clean as $tag) {
                        if ($tag !== '') {
                            $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                        }
                    }
                }

                break;
        }
    }

    // Sorting rules
    switch ($field_type) {
        case 'hall':
            ksort($counts, SORT_NATURAL | SORT_FLAG_CASE);
            break;

        case 'sector':
        case 'brand':
        case 'tag':
            arsort($counts, SORT_NUMERIC);
            break;

        default:
            break;
    }

    return $counts;
}


/**
 * New-architecture count API (kept for compatibility).
 */
function ec_count_filters($items, $field_type) {
    $counts = [];

    foreach ($items as $item) {
        switch ($field_type) {

            case 'hall':
                $hall = trim((string)($item['hall'] ?? ''));
                if ($hall !== '') {
                    $counts[$hall] = ($counts[$hall] ?? 0) + 1;
                }
                break;

            case 'sector':
                $sectors = $item['sectors'] ?? [];
                foreach ($sectors as $s) {
                    $s = trim((string)$s);
                    if ($s !== '') {
                        $counts[$s] = ($counts[$s] ?? 0) + 1;
                    }
                }
                break;

            case 'brand':
                if (($item['type'] ?? '') === 'brand') {
                    $brand = trim((string)($item['data']['brand'] ?? ''));
                    if ($brand !== '') {
                        $counts[$brand] = ($counts[$brand] ?? 0) + 1;
                    }
                }
                break;

            case 'tag':
                $cats = $item['categories'] ?? [];
                foreach ($cats as $c) {
                    $c = trim((string)$c);
                    if ($c !== '') {
                        $counts[$c] = ($counts[$c] ?? 0) + 1;
                    }
                }
                break;
        }
    }

    arsort($counts, SORT_NUMERIC);
    return $counts;
}


/**
 * Main filter logic for catalog exhibitors.
 * Applies hall, sector, brand, product category and search filters.
 */
function exhibitor_catalog_apply_filters($exhibitors_prepared, $all_items) {
    $selected_types      = ec_get_param_array('type');
    $selected_halls      = ec_get_param_array('hall');
    $selected_sectors    = ec_get_param_array('sector');
    $selected_brands     = ec_get_param_array('brand');
    $selected_categories = ec_get_param_array('category');

    $search_query = isset($_GET['search'])
        ? trim(mb_strtolower($_GET['search']))
        : '';

    $filtered = [];

    foreach ($exhibitors_prepared as $ex) {

        $match_hall     = true;
        $match_sector   = true;
        $match_category = true;
        $match_brand    = true;
        $match_search   = true;

        // HALL
        if (!empty($selected_halls)) {
            $match_hall = in_array($ex['hall_name'] ?? '', $selected_halls, true);
        }

        // SECTOR
        if (!empty($selected_sectors)) {
            $match_sector = false;

            if (!empty($ex['catalog_tags'])) {
                foreach ($ex['catalog_tags'] as $tag) {
                    if (in_array($tag, $selected_sectors, true)) {
                        $match_sector = true;
                        break;
                    }
                }
            }
        }

        // BRAND
        if (!empty($selected_brands)) {
            $match_brand = false;

            if (!empty($ex['brands'])) {
                foreach ($ex['brands'] as $brand) {
                    if (in_array($brand, $selected_brands, true)) {
                        $match_brand = true;
                        break;
                    }
                }
            }
        }

        // CATEGORY
        if (!empty($selected_categories)) {
            $match_category = false;

            if (!empty($ex['products'])) {
                foreach ($ex['products'] as $product) {
                    if (empty($product['tags'])) continue;

                    $clean_tags = array_map(
                        'mb_strtolower',
                        ec_normalize_product_tags($product['tags'])
                    );

                    foreach ($clean_tags as $tag) {
                        if (in_array($tag, $selected_categories, true)) {
                            $match_category = true;
                            break 2;
                        }
                    }
                }
            }
        }

        // SEARCH
        if ($search_query !== '') {
            $match_search = false;

            $haystack = [
                mb_strtolower($ex['name'] ?? ''),
                mb_strtolower($ex['description'] ?? ''),
                mb_strtolower($ex['website'] ?? ''),
                mb_strtolower($ex['stand_number'] ?? ''),
            ];

            if (!empty($ex['brands'])) {
                foreach ($ex['brands'] as $brand) {
                    $haystack[] = mb_strtolower($brand);
                }
            }

            if (!empty($ex['products'])) {
                foreach ($ex['products'] as $product) {
                    $haystack[] = mb_strtolower($product['name'] ?? '');
                    $haystack[] = mb_strtolower($product['description'] ?? '');
                }
            }

            foreach ($haystack as $text) {
                if ($text !== '' && strpos($text, $search_query) !== false) {
                    $match_search = true;
                    break;
                }
            }
        }

        // FINAL DECISION
        if ($match_hall && $match_sector && $match_category && $match_brand && $match_search) {
            $filtered[] = $ex;
        }
    }

    // What card types are visible on frontend
    $types_for_display =
        !empty($selected_types)
            ? $selected_types
            : ['exhibitor', 'brand', 'product'];

    return [
        'filtered'          => $filtered,
        'types_for_display' => $types_for_display,
        'visible_filters'   => ['hall', 'sector', 'category', 'brand'],
    ];
}

/**
 * Simple wrapper for old → new architecture.
 */
function ec_apply_filters($exhibitors_prepared, $all_items) {
    return exhibitor_catalog_apply_filters($exhibitors_prepared, $all_items);
}
