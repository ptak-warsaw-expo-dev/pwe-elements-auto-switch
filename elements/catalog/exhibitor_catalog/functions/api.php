<?php
if (!defined('ABSPATH')) exit;

/**
 * API module: Fetching and preparing exhibitor data.
 *
 * This file provides:
 *  - Fetching exhibitor lists (API + local fallback)
 *  - Normalizing and preparing exhibitor structure
 *  - Fetching single exhibitor or product details
 */


/**
 * Fetch all exhibitors from remote API or local fallback.
 *
 * @param array $atts Optional attributes, e.g. archive_catalog_id.
 * @return array Prepared exhibitors list.
 */
function ec_get_all_exhibitors($atts = []) {

    $catalog_ids_raw = do_shortcode('[trade_fair_catalog_id]');
    $raw_exhibitors  = [];
    $use_fallback    = empty($atts['archive_catalog_id']);
    $catalog_ids_src = $use_fallback ? $catalog_ids_raw : $atts['archive_catalog_id'];

    try {
        /**
         * STEP 1 — Fetch JSON data from remote API
         */
        $catalog_ids = array_filter(array_map('intval',
            array_map('trim', explode(',', (string) $catalog_ids_src))
        ), fn($id) => $id > 0);

        if (empty($catalog_ids)) {
            throw new Exception("No valid catalog IDs provided.");
        }

        foreach ($catalog_ids as $id) {

            $base_address = PWECommonFunctions::get_database_meta_data('exh_catalog_address_2');
            $catalog_url  = "{$base_address}{$id}/exhibitors.json";

            $response = wp_remote_get($catalog_url, [
                'timeout' => 10,
                'headers' => ['Accept' => 'application/json'],
            ]);

            if (is_wp_error($response)) {
                throw new Exception($response->get_error_message());
            }

            if (wp_remote_retrieve_response_code($response) !== 200) {
                throw new Exception("HTTP " . wp_remote_retrieve_response_code($response));
            }

            $body = wp_remote_retrieve_body($response);
            if (!$body) {
                throw new Exception("Empty API response.");
            }

            $data = json_decode($body, true);
            if (!is_array($data) || empty($data['success'])) {
                throw new Exception("Invalid JSON structure.");
            }

            $raw_exhibitors = array_merge($raw_exhibitors, $data['exhibitors']);
        }

    } catch (Exception $e) {

        /**
         * STEP 2 — Local fallback (only if enabled)
         */
        if (!$use_fallback) {
            return [];
        }

        $local_path_meta = PWECommonFunctions::get_database_meta_data('exh_catalog_address_doc');
        $local_path      = $_SERVER['DOCUMENT_ROOT'] . $local_path_meta;

        if (file_exists($local_path)) {
            $json = file_get_contents($local_path);
            $local_data = json_decode($json, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($local_data)) {
                $raw_exhibitors = $local_data;
            }
        }
    }

    if (empty($raw_exhibitors)) {
        return [];
    }

    /**
     * STEP 3 — Prepare normalized exhibitor structure
     */
    $prepared = [];

    foreach ($raw_exhibitors as $exhibitor) {

        $company   = $exhibitor['companyInfo'] ?? [];
        $stand     = $exhibitor['stand'] ?? [];
        $products  = is_array($exhibitor['products'] ?? null)  ? $exhibitor['products']  : [];
        $documents = is_array($exhibitor['documents'] ?? null) ? $exhibitor['documents'] : [];

        // Normalize product tags
        $products = array_map(function ($p) {
            $raw_tags = $p['tags'] ?? [];
            $p['tags'] = ec_normalize_product_tags($raw_tags);
            return $p;
        }, $products);

        $prepared[] = [
            'exhibitor_id' => (string)($exhibitor['exhibitorId'] ?? ''),
            'id_numeric'   => (int)($exhibitor['exhibitorId'] ?? 0),

            'name'         => $company['displayName'] ?? '',
            'logo_url'     => $company['logoUrl'] ?? '',
            'description'  => $company['description'] ?? '',
            'why_visit'    => $company['whyVisit'] ?? '',

            'website'        => $company['website'] ?? '',
            'contact_phone'  => $company['contactPhone'] ?? '',
            'contact_email'  => $company['contactEmail'] ?? '',

            'facebook'       => $company['facebook'] ?? '',
            'instagram'      => $company['instagram'] ?? '',
            'linkedin'       => $company['linkedin'] ?? '',
            'youtube'        => $company['youtube'] ?? '',
            'tiktok'         => $company['tiktok'] ?? '',
            'x'              => $company['x'] ?? '',

            'brands'       => $company['brands'] ?? [],
            'catalog_tags' => $company['catalogTags'] ?? [],
            'industries'   => $company['industries'] ?? '',

            'hall_name'    => $stand['hallName'] ?? '',
            'stand_number' => $stand['standNumber'] ?? '',
            'area'         => (float)($stand['boothArea'] ?? 0),

            'products'        => $products,
            'documents'       => $documents,
            'products_count'  => count($products),
            'documents_count' => count($documents),

            'products_preview'  => array_slice($products, 0, 2),
            'documents_preview' => array_slice($documents, 0, 2),
        ];
    }

    return $prepared;
}


/**
 * Get a single exhibitor by exhibitor_id (from $_GET).
 *
 * @return array|null
 */
function ec_get_single_exhibitor() {

    if (empty($_GET['exhibitor_id'])) {
        return null;
    }

    $id  = trim($_GET['exhibitor_id']);
    $all = ec_get_all_exhibitors();

    foreach ($all as $exh) {
        if ((string)$exh['exhibitor_id'] === (string)$id) {
            return $exh;
        }
    }

    return null;
}


/**
 * Get a single product belonging to a specific exhibitor.
 *
 * @return array|null
 */
function ec_get_single_product() {

    if (!isset($_GET['exhibitor_id'], $_GET['product_id'])) {
        return null;
    }

    $product_id = (int)$_GET['product_id'];
    $exhibitor  = ec_get_single_exhibitor();

    if (!$exhibitor) {
        return null;
    }

    return $exhibitor['products'][$product_id] ?? null;
}
