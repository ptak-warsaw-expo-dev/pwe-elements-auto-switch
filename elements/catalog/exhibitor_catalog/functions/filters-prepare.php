<?php
if (!defined('ABSPATH')) exit;

/**
 * Prepare all filter-related datasets for the exhibitor catalog.
 *
 * Builds:
 *  - Unique list of halls
 *  - Unique list of sectors (catalog_tags)
 *  - Unique list of brands
 *  - Unique list of normalized product tags
 *  - Unified item list ($all_items) for filter counting
 *
 * The $all_items array contains:
 *   [
 *     ['type' => 'exhibitor', 'data' => exhibitorArray],
 *     ['type' => 'product',   'data' => ['product' => productArray, 'exhibitor' => exhibitorArray]],
 *     ['type' => 'brand',     'data' => ['brand' => brandName,     'exhibitor' => exhibitorArray]]
 *   ]
 *
 * @param array $exhibitors
 * @return array
 */
function ec_prepare_filters($exhibitors) {

    $halls   = [];
    $sectors = [];
    $brands  = [];
    $tags    = [];

    $all_items = [];

    foreach ($exhibitors as $ex) {

        /** -------------------------------
         *  HALLS
         * -------------------------------- */
        if (!empty($ex['hall_name'])) {
            $halls[$ex['hall_name']] = true;
        }

        /** -------------------------------
         *  SECTORS (catalog_tags)
         * -------------------------------- */
        if (!empty($ex['catalog_tags']) && is_array($ex['catalog_tags'])) {
            foreach ($ex['catalog_tags'] as $tag) {
                if ($tag !== '') {
                    $sectors[$tag] = true;
                }
            }
        }

        /** -------------------------------
         *  BRANDS
         * -------------------------------- */
        if (!empty($ex['brands']) && is_array($ex['brands'])) {
            foreach ($ex['brands'] as $brand) {
                if ($brand !== '') {
                    $brands[$brand] = true;
                }
            }
        }

        /** -------------------------------
         *  PRODUCTS → product tags
         * -------------------------------- */
        if (!empty($ex['products']) && is_array($ex['products'])) {
            foreach ($ex['products'] as $product) {

                $raw_tags   = $product['tags'] ?? [];
                $clean_tags = ec_normalize_product_tags($raw_tags);

                foreach ($clean_tags as $t) {
                    $tags[$t] = true;
                }

                // Add product to unified item list
                $all_items[] = [
                    'type' => 'product',
                    'data' => [
                        'product'   => $product,
                        'exhibitor' => $ex
                    ]
                ];
            }
        }

        /** -------------------------------
         *  Add exhibitor entry
         * -------------------------------- */
        $all_items[] = [
            'type' => 'exhibitor',
            'data' => $ex
        ];

        /** -------------------------------
         *  Add brand entries for filter counts
         * -------------------------------- */
        if (!empty($ex['brands'])) {
            foreach ($ex['brands'] as $brand) {
                $all_items[] = [
                    'type' => 'brand',
                    'data' => [
                        'brand'     => $brand,
                        'exhibitor' => $ex
                    ]
                ];
            }
        }
    }

    /** ----------------------------------------
     *  Sorting each filter list alphabetically
     * ---------------------------------------- */
    ksort($halls,   SORT_NATURAL | SORT_FLAG_CASE);
    ksort($sectors, SORT_NATURAL | SORT_FLAG_CASE);
    ksort($brands,  SORT_NATURAL | SORT_FLAG_CASE);
    ksort($tags,    SORT_NATURAL | SORT_FLAG_CASE);

    /** ----------------------------------------
     *  Count items per filter
     * ---------------------------------------- */
    $counts = [
        'halls'   => exhibitor_catalog_count_filters($all_items, 'hall'),
        'sectors' => exhibitor_catalog_count_filters($all_items, 'sector'),
        'brands'  => exhibitor_catalog_count_filters($all_items, 'brand'),
        'tags'    => exhibitor_catalog_count_filters($all_items, 'tag'),
    ];

    return [
        'halls'      => array_keys($halls),
        'sectors'    => array_keys($sectors),
        'brands'     => array_keys($brands),
        'tags'       => array_keys($tags),
        'all_items'  => $all_items,
        'counts'     => $counts,
    ];
}
