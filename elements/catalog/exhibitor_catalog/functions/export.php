<?php
if (!defined('ABSPATH')) exit;

/**
 * Check whether CSV export is requested via query parameter.
 *
 * @return bool
 */
function ec_export_csv_requested() {
    return isset($_GET['export_exhibitors_csv']) && $_GET['export_exhibitors_csv'] == '1';
}


/**
 * Clear all active output buffers.
 *
 * Prevents BOM or unexpected output from breaking CSV downloads.
 *
 * @return void
 */
function ec_clear_output_buffers() {
    while (ob_get_level()) {
        ob_end_clean();
    }
}


/**
 * Generate and stream a CSV file containing exhibitor data.
 *
 * Expected keys in each exhibitor row:
 *   name, stand_number, contact_phone, contact_email, website, nip
 *
 * @param array $exhibitors
 * @return void
 */
function ec_export_exhibitors_csv($exhibitors) {

    if (empty($exhibitors)) {
        $exhibitors = [];
    }

    ec_clear_output_buffers();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="exhibitors_list.csv"');

    $output = fopen('php://output', 'w');

    // CSV header row
    fputcsv($output, [
        'Name',
        'Stand',
        'Phone',
        'Email',
        'Website',
        'NIP'
    ]);

    foreach ($exhibitors as $row) {
        fputcsv($output, [
            $row['name']          ?? '',
            $row['stand_number']  ?? '',
            $row['contact_phone'] ?? '',
            $row['contact_email'] ?? '',
            $row['website']       ?? '',
            $row['nip']           ?? ''
        ]);
    }

    fclose($output);
    exit;
}


/**
 * Backward-compatibility alias.
 *
 * @param array $exhibitors
 * @return void
 */
function exhibitor_catalog_export_csv($exhibitors) {
    return ec_export_exhibitors_csv($exhibitors);
}