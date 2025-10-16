<?php
ob_start();

function get_all_exhibitors() {

    $catalog_id  = do_shortcode('[trade_fair_catalog]');
    $catalog_id = 2;

    $catalog_url = "https://backend-production-df8c.up.railway.app/public/exhibitions/{$catalog_id}/exhibitors.json";
    $res = wp_remote_get($catalog_url, [
        'timeout' => 10,
        'headers' => ['Accept' => 'application/json'],
    ]);
    if (is_wp_error($res)) return [];

    $data = json_decode((string) wp_remote_retrieve_body($res), true);
    return is_array($data) ? $data : [];
}

/* --- POBRANIE I PRZYGOTOWANIE DANYCH --- */
$catalog_json = get_all_exhibitors();
$exhibitors_data = isset($catalog_json['exhibitors']) && is_array($catalog_json['exhibitors']) ? $catalog_json['exhibitors'] : [];

$exhibitors_count = count($exhibitors_data);

$exhibitors_per_page = 20;

/* --- PRZYGOTUJ DANE (jedna pętla, jedna struktura) --- */
$exhibitors_prepared = [];

foreach ($exhibitors_data as $exhibitor) {

    $exhibitor_company_info  = $exhibitor['companyInfo'] ?? [];
    $exhibitor_stand_info    = $exhibitor['stand'] ?? [];
    $exhibitor_address_info  = $exhibitor['exhibitor'] ?? [];

    $products_list  = is_array($exhibitor['products'] ?? null)  ? $exhibitor['products']  : [];
    $documents_list = is_array($exhibitor['documents'] ?? null) ? $exhibitor['documents'] : [];

    // dodaj przygotowany rekord
    $exhibitors_prepared[] = [
        'exhibitor_id'        => (string)($exhibitor['exhibitorId'] ?? ''),
        'id_numeric'          => (int)($exhibitor['exhibitorId'] ?? 0),
        'idNumeric'           => $idNum,

        'name'                => $exhibitor_company_info['name'] ?? '',
        'logo_url'            => $exhibitor_company_info['logoUrl'] ?? '',
        'description'         => $exhibitor_company_info['description'] ?? '',
        'why_visit'           => $exhibitor_company_info['whyVisit'] ?? '',
        'website'             => $exhibitor_company_info['website'] ?? '',
        'contact_phone'       => $exhibitor_company_info['contactInfo'] ?? '',
        'contact_email'       => $exhibitor_company_info['contactEmail'] ?? '',
        'brands'              => $exhibitor_company_info['brands'] ?? '',
        'catalog_tags'        => $exhibitor_company_info['catalogTags'] ?? '',
        'industries'          => $exhibitor_company_info['industries'] ?? '',

        'hall_name'           => $exhibitor_stand_info['hallName'] ?? '',
        'stand_number'        => $exhibitor_stand_info['standNumber'] ?? '',

        'booth_area'          => $boothArea,
        'total_booth_area'    => $boothArea,
        'areaSum'             => $boothArea,

        'nip'                 => $exhibitor_address_info['nip'] ?? '',
        'address'             => $exhibitor_address_info['address'] ?? '',
        'postal_code'         => $exhibitor_address_info['postalCode'] ?? '',
        'city'                => $exhibitor_address_info['city'] ?? '',

        'products_count'      => count($products_list),
        'documents_count'     => count($documents_list),

        'products'            => $products_list,
        'documents'           => $documents_list,

        'products_preview'    => array_values(array_slice($products_list, 0, 2)),
        'documents_preview'   => array_values(array_slice($documents_list, 0, 2)),
    ];
}

/* --- SORTOWANIE --- */
$calculate_total_score = function(array $exhibitor): float {
    $score = 0.0;

    // produkty i dokumenty
    $score += $exhibitor['products_count']  * 3.0;
    $score += $exhibitor['documents_count'] * 2.0;

    // media i treści
    if (!empty($exhibitor['logo_url'])) $score += 2.0;
    if (!empty($exhibitor['description'])) $score += 1.5;

    // dane kontaktowe / kategorie
    if (!empty($exhibitor['website'])) $score += 1.0;
    if (!empty($exhibitor['contact_email'])) $score += 0.8;
    if (!empty($exhibitor['contact_phone'])) $score += 0.8;
    if (!empty($exhibitor['brands'])) $score += 0.8;
    if (!empty($exhibitor['catalog_tags'])) $score += 0.6;

    // stoisko / hala
    if (!empty($exhibitor['stand_number'])) $score += 0.6;
    if (!empty($exhibitor['hall_name'])) $score += 0.4;

    return $score;
};

usort($exhibitors_prepared, function(array $exhibitor_a, array $exhibitor_b) use ($calculate_total_score) {
    $score_a = $calculate_total_score($exhibitor_a);
    $score_b = $calculate_total_score($exhibitor_b);

    if ($score_a !== $score_b) {
        return $score_b <=> $score_a;
    }

    $name_a = mb_strtolower($exhibitor_a['name']);
    $name_b = mb_strtolower($exhibitor_b['name']);

    return $name_a <=> $name_b;
});

// Po przygotowaniu $exhibitors_prepared, $exhibitors_per_page — ZANIM zrobisz include szablonów:

$js_base_url  = plugin_dir_url(__FILE__)  . 'assets/';
$js_base_path = plugin_dir_path(__FILE__) . 'assets/';

$ver = static function($p){ return file_exists($p) ? filemtime($p) : null; };

// 1) CORE (utils + stan + renderCard)
wp_enqueue_script(
  'exhibitors-core',
  $js_base_url . 'exhibitors-core.js',
  [],
  $ver($js_base_path . 'exhibitors-core.js'),
  true
);
// dane dla JS (oddzielny blok, wstrzyknięty PRZED plikiem core)
wp_add_inline_script(
  'exhibitors-core',
  'window.__EXHIBITORS__=' . wp_json_encode($exhibitors_prepared, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) .
  ';window.__PER_PAGE__=' . (int)$exhibitors_per_page . ';',
  'before'
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

// (opcjonalnie) MODAL, jeśli masz oddzielny plik
if (file_exists($js_base_path . 'exhibitor.modal.js')) {
  wp_enqueue_script(
    'exhibitor-modal',
    $js_base_url . 'exhibitor.modal.js',
    [],
    $ver($js_base_path . 'exhibitor.modal.js'),
    true
  );
}

// 6) INIT — Twój obecny script.js
wp_enqueue_script(
  'exhibitors-init',
  $js_base_url . 'script.js',
  ['exhibitors-core','exhibitors-filters','exhibitors-sort','exhibitors-pagination','exhibitors-search'],
  $ver($js_base_path . 'script.js'),
  true
);

$halls = [];
$sectors = [];
$products_tags =[];

foreach ($exhibitors_prepared as $exhibitor_row) {
    if (!empty($exhibitor_row['hall_name'])) {
        $halls[$exhibitor_row['hall_name']] = true;
    }
    if (!empty($exhibitor_row['catalog_tags'])) {
        foreach (preg_split('/\s*,\s*/u', $exhibitor_row['catalog_tags']) as $tag) {
            if ($tag !== '') $sectors[$tag] = true;
        }
    }
    if (!empty($exhibitor_row['products']) && is_array($exhibitor_row['products'])) {
        foreach ($exhibitor_row['products'] as $p) {
            // może być: array|string|null
            $raw = $p['tags'] ?? [];

            // ujednolicenie do tablicy
            if (is_string($raw))       $candidates = [$raw];
            elseif (is_array($raw))    $candidates = $raw;
            else                       $candidates = [];

            foreach ($candidates as $cand) {
                if (!is_string($cand) || $cand === '') continue;

                // dziel po przecinku LUB po >=2 spacjach
                $parts = preg_split('/\s*,\s*|\s{2,}/u', $cand, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($parts as $t) {
                    $t = trim($t);
                    if ($t !== '') $products_tags[$t] = true;
                }
            }
        }
    }
}
ksort($halls, SORT_NATURAL | SORT_FLAG_CASE);
ksort($sectors, SORT_NATURAL | SORT_FLAG_CASE);
ksort($products_tags, SORT_NATURAL | SORT_FLAG_CASE);

$halls   = array_keys($halls);
$sectors = array_keys($sectors);
$products_tags = array_keys($products_tags);


include  plugin_dir_path(__FILE__) . 'assets/exhibitor_catalog_main.php';
include plugin_dir_path(__FILE__) . 'assets/exhibitor_catalog_single.php';

$output = ob_get_clean();

return $output;