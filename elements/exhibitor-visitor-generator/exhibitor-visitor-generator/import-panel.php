<?php
if (!defined('ABSPATH')) exit;

// Render and enqueue the importer only when CAP explicitly enables it for this fair.
$import_enabled = (static function () {
    $domain = $_SERVER['HTTP_HOST'] ?? '';
    if (!is_string($domain) || $domain === '' || !is_callable(['PWE_Functions', 'connect_database'])) return false;

    try {
        $cap_db = PWE_Functions::connect_database();
        if (!$cap_db) return false;

        $errors_suppressed = $cap_db->suppress_errors(true);
        try {
            $enabled = $cap_db->get_var($cap_db->prepare(
                "SELECT fa.data FROM fairs f
                 INNER JOIN fair_adds fa ON fa.fair_id = f.id
                 WHERE f.fair_domain = %s AND fa.slug = %s LIMIT 1",
                $domain,
                'exhibitor_visitor_bulk_send_enabled'
            ));
            return empty($cap_db->last_error) && (string) $enabled === '1';
        } finally {
            $cap_db->suppress_errors($errors_suppressed);
        }
    } catch (Throwable $error) {
        return false;
    }
})();

if (!$import_enabled) return '';

// Adres odbioru JSON: tutaj zmień przypisanie na docelowy endpoint, np. '/api/odbior-gosci.php'.
$import_endpoint = wp_make_link_relative(plugin_dir_url(__FILE__) . 'assets/test_get_data.php');
$import_id = wp_unique_id('pwe-visitor-import-');
$import_en = $lang === 'en';
$import_text = static function ($pl, $en) use ($import_en) { return esc_html($import_en ? $en : $pl); };
$import_base_url = plugin_dir_url(__FILE__);

// Own URLs ensure that the optional PWE System asset helper cannot redirect these files.
wp_enqueue_style('pwe-visitor-import', $import_base_url . 'assets/import.css', [], filemtime(__DIR__ . '/assets/import.css'));
wp_enqueue_script('pwe-visitor-import', $import_base_url . 'assets/import.js', [], filemtime(__DIR__ . '/assets/import.js'), true);
$import_worker_version = max(filemtime(__DIR__ . '/assets/import-worker.js'), filemtime(__DIR__ . '/assets/import-parser.js'));
$import_worker_url = wp_make_link_relative($import_base_url . 'assets/import-worker.js') . '?ver=' . $import_worker_version;

$output = '
<section class="pwe-visitor-import" data-pwe-visitor-import
    data-endpoint="' . esc_attr((string) $import_endpoint) . '"
    data-worker-url="' . esc_attr($import_worker_url) . '"
    data-language="' . ($import_en ? 'en' : 'pl') . '"
    data-guest-language="' . (((string) $lang === 'pl' || (string) $lang === '') ? 'pl' : 'en') . '">
    <button type="button" class="pwe-visitor-import__toggle" data-import-toggle
        aria-haspopup="dialog" aria-expanded="false" aria-controls="' . esc_attr($import_id) . '">
        ' . $import_text('Wysyłka masowa', 'Bulk Shipping') . '
    </button>
    <div class="pwe-visitor-import__backdrop" data-import-backdrop aria-hidden="true" hidden></div>
    <dialog id="' . esc_attr($import_id) . '" class="pwe-visitor-import pwe-visitor-import__dialog" data-import-panel
        role="dialog" aria-modal="true" aria-labelledby="' . esc_attr($import_id . '-title') . '" tabindex="-1" hidden>
        <div class="pwe-visitor-import__panel">
            <div class="pwe-visitor-import__header">
                <h4 id="' . esc_attr($import_id . '-title') . '" data-import-title tabindex="-1">' . $import_text('Dodaj swoich gości do pliku', 'Add your guests to the file') . '</h4>
                <button type="button" class="pwe-visitor-import__close" data-import-close aria-label="' . $import_text('Zamknij okno importu', 'Close the import window') . '"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="pwe-visitor-import__body">
        <p>' . $import_text('Pobierz wzór, zastąp przykładowe osoby swoimi danymi i wybierz plik. Wszystkie trzy kolumny są wymagane.', 'Download a template, replace the sample guests with your data and select the file. All three columns are required.') . '</p>
        <div class="pwe-visitor-import__templates">
            <a href="' . esc_url($import_base_url . 'templates/goscie-wystawcow-wzor.csv') . '" download>' . $import_text('Pobierz wzór CSV', 'Download CSV template') . '</a>
            <a href="' . esc_url($import_base_url . 'templates/goscie-wystawcow-wzor.xlsx') . '" download>' . $import_text('Pobierz wzór XLSX', 'Download XLSX template') . '</a>
        </div>
        <label for="' . esc_attr($import_id . '-file') . '">' . $import_text('Lista gości', 'Guest List') . '</label>
        <input type="file" id="' . esc_attr($import_id . '-file') . '" data-import-file
            accept=".csv,.xlsx,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
            aria-describedby="' . esc_attr($import_id . '-help') . '">
        <p id="' . esc_attr($import_id . '-help') . '" class="pwe-visitor-import__hint">' . $import_text('Do 20 000 osób, maks. 5 MB. XLSX: jeden arkusz, same wartości bez formuł. CSV: separator średnik, przecinek lub tabulator. Nagłówki: Imię i nazwisko, Firma zapraszająca, Adres e-mail.', 'Up to 20,000 guests, max. 5 MB. XLSX: one worksheet, values only, no formulas. CSV: semicolon, comma or tab separator. Headers: Full name, Inviting company, Email (Polish template headers are also accepted).') . '</p>';

if ($generator_company !== '') {
    $output .= '
        <p class="pwe-visitor-import__hint">' . $import_text('Firma zapraszająca zostanie pobrana z każdego wiersza pliku. Wpisz w tej kolumnie: ', 'The inviting company is read from each row in the file. Enter this company in that column: ') . '<strong>' . esc_html($generator_company) . '</strong>.</p>';
}

$output .= '
        <div class="pwe-visitor-import__status" data-import-status role="status" aria-live="polite"></div>
        <div class="pwe-visitor-import__errors" data-import-errors role="alert" hidden></div>
        <div class="pwe-visitor-import__preview" data-import-preview hidden>
            <p data-import-summary></p>
            <div class="pwe-visitor-import__table-wrap" tabindex="0" role="region" aria-label="' . $import_text('Podgląd danych do wysłania', 'Preview of data to send') . '">
                <table>
                    <thead><tr>
                        <th scope="col">' . $import_text('Wiersz', 'Row') . '</th>
                        <th scope="col">' . $import_text('Imię i nazwisko', 'Full name') . '</th>
                        <th scope="col">' . $import_text('Firma zapraszająca', 'Inviting company') . '</th>
                        <th scope="col">' . $import_text('Adres e-mail', 'Email') . '</th>
                    </tr></thead>
                    <tbody data-import-rows></tbody>
                </table>
            </div>
        </div>
        <div class="pwe-visitor-import__actions">
            <button type="button" data-import-send disabled>' . $import_text('Prześlij zaproszenia', 'Send invitations') . '</button>
            <button type="button" class="pwe-visitor-import__secondary" data-import-clear hidden>' . $import_text('Wyczyść', 'Clear') . '</button>
        </div>
            </div>
        </div>
    </dialog>
    <noscript><p>' . $import_text('Import plików wymaga włączonej obsługi JavaScript.', 'File import requires JavaScript.') . '</p></noscript>
</section>';

return $output;
