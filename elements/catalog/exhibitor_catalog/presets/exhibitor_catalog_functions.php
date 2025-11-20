<?php
if (!defined('ABSPATH')) exit;

function is_mobile() {
    return preg_match(
        '/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    );
}

function applyManualOrderChanges($changes, $exhibitors, $nameKeys = ['Nazwa_wystawcy','company_name','name','exhibitor_name']) {
    $changes = html_entity_decode($changes, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // 🔧 KLUCZOWA LINIA — zapewnij indeksy 0..N-1
    $exhibitors = array_values($exhibitors);

    // Szybkie mapy pomocnicze
    $indexById = [];
    foreach ($exhibitors as $i => $exh) {
        if (isset($exh['id_numeric'])) {
            $indexById[(int)$exh['id_numeric']] = $i; // teraz $i to prawdziwa pozycja
        }
    }

    $findIndexById = function($id) use (&$indexById): ?int {
        $id = (int)$id;
        return array_key_exists($id, $indexById) ? $indexById[$id] : null;
    };

    $findIndexByName = function(string $needle) use (&$exhibitors, $nameKeys): ?int {
        $needle = trim($needle);
        if ($needle === '') return null;
        foreach ($exhibitors as $i => $exh) {
            foreach ($nameKeys as $k) {
                if (!empty($exh[$k]) && is_string($exh[$k]) && stripos($exh[$k], $needle) !== false) {
                    return $i;
                }
            }
        }
        return null;
    };

    $resolveToIndex = function(string $token) use ($findIndexById, $findIndexByName, &$exhibitors): ?int {
        $token = trim($token);
        if ($token === '') return null;

        // Najpierw ID (jeśli liczba)
        if (ctype_digit($token)) {
            $asInt = (int)$token;
            $idxById = $findIndexById($asInt);
            if ($idxById !== null) return $idxById;
        }

        // Potem POZYCJA (1-based -> 0-based), o ile liczba w zakresie
        if (ctype_digit($token)) {
            $pos = (int)$token;
            if ($pos > 0) $pos--;
            if ($pos >= 0 && $pos < count($exhibitors)) return $pos;
        }

        // Na końcu dopiero szukanie po fragmencie nazwy
        $idxByName = $findIndexByName($token);
        if ($idxByName !== null) return $idxByName;

        return null;
    };

    // 2) applyManualOrderChanges: popraw klauzulę use na referencję do $exhibitors
    $rebuildIndexById = function() use (&$indexById, &$exhibitors): void {
        // dla pewności – utrzymuj indeksy 0..N-1
        $exhibitors = array_values($exhibitors);

        $indexById = [];
        foreach ($exhibitors as $i => $exh) {
            if (isset($exh['id_numeric'])) {
                $indexById[(int)$exh['id_numeric']] = $i;
            }
        }
    };

    $ops = array_filter(array_map('trim', explode(';;', $changes)), fn($x) => $x !== '');
    foreach ($ops as $op) {
        if (strpos($op, '<=>') !== false) {
            [$a, $b] = array_map('trim', explode('<=>', $op, 2));
            $ia = $resolveToIndex($a);
            $ib = $resolveToIndex($b);

            if ($ia === null) { error_log("[orderChanger] Nie znaleziono A: {$a} w operacji '{$op}'"); continue; }
            if ($ib === null) { error_log("[orderChanger] Nie znaleziono B: {$b} w operacji '{$op}'"); continue; }
            if ($ia === $ib)  { continue; }

            // Zamiana miejscami
            $tmp = $exhibitors[$ia];
            $exhibitors[$ia] = $exhibitors[$ib];
            $exhibitors[$ib] = $tmp;

            $rebuildIndexById();

        } elseif (strpos($op, '=>>') !== false) {
            [$src, $dst] = array_map('trim', explode('=>>', $op, 2));
            $is = $resolveToIndex($src);
            $id = $resolveToIndex($dst);

            if ($is === null) { error_log("[orderChanger] Nie znaleziono źródła: {$src} w operacji '{$op}'"); continue; }
            if ($id === null) { error_log("[orderChanger] Nie znaleziono celu: {$dst} w operacji '{$op}'"); continue; }
            if ($is === $id)  { continue; }

            // Wyjmij element źródłowy
            $item = $exhibitors[$is];
            array_splice($exhibitors, $is, 1);

            // Po usunięciu indeksy przesuwają się:
            if ($is < $id) $id--;

            // Wstaw na miejsce celu (A trafia w miejsce B)
            array_splice($exhibitors, $id, 0, [$item]);

            $rebuildIndexById();

        } else {
            error_log("[orderChanger] Pominięto nieznaną operację: {$op}");
        }
    }

    return array_values($exhibitors);
}

function exhibitor_catalog_calculate_total_score($exhibitor) {

    $score = 0.0;

    // LIMITY
    $products_points  = min(($exhibitor['products_count'] ?? 0) * 3.0, 12.0);
    $documents_points = min(($exhibitor['documents_count'] ?? 0) * 2.0, 8.0);

    $score += $products_points;
    $score += $documents_points;

    if (!empty($exhibitor['logo_url']))      $score += 2.0;
    if (!empty($exhibitor['description']))   $score += 1.5;
    if (!empty($exhibitor['website']))       $score += 1.0;
    if (!empty($exhibitor['contact_email'])) $score += 0.8;
    if (!empty($exhibitor['contact_phone'])) $score += 0.8;
    if (!empty($exhibitor['brands']))        $score += 0.8;
    if (!empty($exhibitor['catalog_tags']))  $score += 0.6;
    if (!empty($exhibitor['stand_number']))  $score += 0.6;
    if (!empty($exhibitor['hall_name']))     $score += 0.4;

    return $score;
}

/* ============================================================
 * SORTOWANIA KATALOGU
 * ============================================================
 */

function exhibitor_catalog_sort_modes() {
    return [
        'default'      => 'Domyślnie',
        'alphabetical' => 'Alfabetycznie',
        'area'         => 'Powierzchnia',
        // 'new'          => 'Nowi',
        // 'featured'     => 'Wyróżnieni',
    ];
}

function exhibitor_catalog_render_sort_select($current) {

    $modes = exhibitor_catalog_sort_modes();

    // aktywna opcja
    $selected_icon = pwe_svg_icon('default');

    $html  = '<div class="catalog-custom-select" data-select="sort" data-current="'.$current.'">';

    // aktywna pozycja widoczna
    $html .= '<div class="catalog-custom-select__selected">';
    $html .= '<span class="catalog-custom-select__icon">'.$selected_icon.'</span>';
    $html .= '</div>';

    // dropdown
    $html .= '<div class="catalog-custom-select__dropdown">';

    foreach ($modes as $value => $label) {

        $active = ($current === $value) ? 'active' : '';
        $icon   = pwe_svg_icon($value);

        $html .= "
            <div class='catalog-custom-select__option {$active}' data-value='{$value}'>
                <span class=\"catalog-custom-select__icon\">{$icon}</span>
                <span class=\"catalog-custom-select__label\">{$label}</span>
            </div>
        ";
    }

    $html .= '</div></div>';

    return $html;
}

function exhibitor_catalog_sort($exhibitors, $mode, $calculate_total_score) {

    $mode = $mode ?: 'default';

    switch ($mode) {

        case 'alphabetical':
            return exhibitor_catalog_sort_alphabetical($exhibitors);

        case 'area':
            return exhibitor_catalog_sort_area($exhibitors);

        case 'default':
        default:
            return exhibitor_catalog_sort_default($exhibitors, $calculate_total_score);
    }
}

/* ----------------- 1) default ----------------- */
function exhibitor_catalog_sort_default($exhibitors, $calculate_total_score) {

    usort($exhibitors, function($a, $b) use ($calculate_total_score) {

        // liczenie punktów tylko tutaj
        $scoreA = $calculate_total_score($a);
        $scoreB = $calculate_total_score($b);

        if ($scoreA !== $scoreB) {
            return $scoreB <=> $scoreA;
        }

        // jeśli punkty równe, sortujemy A-Z
        return mb_strtolower($a['name']) <=> mb_strtolower($b['name']);
    });

    return $exhibitors;
}

/* ----------------- 2) ALFABETYCZNE ----------------- */
function exhibitor_catalog_sort_alphabetical($exhibitors) {

    usort($exhibitors, function($a, $b) {

        // Removing whitespace from the beginning
        $aClean = ltrim($a['name']);
        $bClean = ltrim($b['name']);

        // Removing " or ' from the beginning
        if (in_array(mb_substr($aClean, 0, 1), ['"', "'"])) {
            $aClean = mb_substr($aClean, 1);
        }
        if (in_array(mb_substr($bClean, 0, 1), ['"', "'"])) {
            $bClean = mb_substr($bClean, 1);
        }

        // Sorting by cleaned text
        return mb_strtolower($aClean) <=> mb_strtolower($bClean);
    });

    return $exhibitors;
}


/* ----------------- 3) POWIERZCHNIA STOISKA ----------------- */
function exhibitor_catalog_sort_area($exhibitors) {

    usort($exhibitors, function($a, $b) {
        return floatval($b['area'] ?? 0) <=> floatval($a['area'] ?? 0);
    });

    return $exhibitors;
}

function get_all_exhibitors($atts) {
    
    $normalize_string_array = function ($input) {
        if (!is_array($input)) return [];
        $cleanedValues = [];
        foreach ($input as $value) {
            if (!is_string($value)) continue;
            $trimmed = trim($value);
            if ($trimmed !== '') $cleanedValues[] = $trimmed;
        }
        return array_values($cleanedValues);
    };

    $catalog_ids_array  = do_shortcode('[trade_fair_catalog_id]');
    $exh_catalog_cron_pass = PWECommonFunctions::get_database_meta_data('cron_secret_pass');
    $domain = $_SERVER['HTTP_HOST'];
    $raw_exhibitors = [];

    $use_fallback = true;

    if (!empty($atts['archive_catalog_id'])) {
        $catalog_ids_array = $atts['archive_catalog_id'];
        $use_fallback = false;
        if (current_user_can('administrator')) {
            echo '<script>console.log("Użyto archive_catalog_id: ' . esc_js($catalog_ids_array) . ' (bez fallbacku)")</script>';
        }
    }
    
    try {
        // --- 1️⃣ Pobieranie danych z API ---

        $catalog_ids = array_filter(array_map('trim', explode(',', (string)$catalog_ids_array)));
        $catalog_ids = array_filter(array_map('intval', $catalog_ids), fn($id) => $id > 0);

        if (empty($catalog_ids)) {
            throw new Exception("Brak poprawnych katalog ID w shortcodzie [trade_fair_catalog_id]");
        }

        foreach ($catalog_ids as $id) {
            $exh_catalog_address = PWECommonFunctions::get_database_meta_data('exh_catalog_address_2');
            $catalog_url = "{$exh_catalog_address}{$id}/exhibitors.json";

            $res = wp_remote_get($catalog_url, [
                'timeout' => 10,
                'headers' => ['Accept' => 'application/json'],
            ]);

            if (is_wp_error($res)) {
                throw new Exception("Błąd połączenia z API: " . $res->get_error_message());
            }

            $status = wp_remote_retrieve_response_code($res);
            if ($status !== 200) {
                throw new Exception("Nieprawidłowy kod HTTP: {$status} dla URL: {$catalog_url}");
            }

            $body = (string) wp_remote_retrieve_body($res);
            if (trim($body) === '') {
                throw new Exception("Pobrano pustą odpowiedź z API: {$catalog_url}");
            }

            $data = json_decode($body, true);
            if (!is_array($data)) {
                throw new Exception("Błąd dekodowania JSON dla URL: {$catalog_url}");
            }

            if (empty($data['success']) || $data['success'] !== true) {
                throw new Exception("Nieprawidłowa struktura danych w {$catalog_url}");
            }

            if (!isset($data['exhibitors']) || !is_array($data['exhibitors'])) {
                throw new Exception("Brak poprawnej listy wystawców w {$catalog_url}");
            }

            $raw_exhibitors = array_merge($raw_exhibitors, $data['exhibitors']);
        }
    } catch (Exception $e) {
        if (!$use_fallback) {
            // archiwum -> nie próbujemy pliku, tylko kończymy
            error_log("[get_all_exhibitors] Błąd API (archive mode, bez fallbacku): " . $e->getMessage());
            return [];
        }
        // --- 2️⃣ Fallback z pliku ---
        $exh_catalog_local_file = PWECommonFunctions::get_database_meta_data('exh_catalog_address_doc');
        $local_file = $_SERVER['DOCUMENT_ROOT'] . $exh_catalog_local_file;

        if (file_exists($local_file) && is_readable($local_file)) {
            $json = file_get_contents($local_file);

            $catalog_data = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($catalog_data)) {
                $raw_exhibitors = $catalog_data;

                if (current_user_can('administrator')) {
                    echo '
                    <script>
                        console.log("Dane pobrane z lokalnego pliku (https://'.  $_SERVER['HTTP_HOST'] .'/doc/pwe-exhibitors.json) dla katalogu ' . $catalog_ids_array . '")
                        console.log("Link do odświeżenia katalogu: https://' . $domain . '/wp-content/plugins/custom-element/other/mass_vip_cron.php?pass=' . $exh_catalog_cron_pass . '")
                    </script>';
                };

            } else {
                error_log("[get_all_exhibitors] Błąd JSON w pliku: " . json_last_error_msg());
            }
        } else {
            error_log("[get_all_exhibitors] Brak dostępu do pliku lokalnego: {$local_file}");
        }
    }

    if (empty($raw_exhibitors)) return [];

    // --- 3️⃣ Przetwarzanie danych ---
    $exhibitors_prepared = [];
    foreach ($raw_exhibitors as $exhibitor) {
        $exhibitor_company_info  = $exhibitor['companyInfo'] ?? [];
        $exhibitor_stand_info    = $exhibitor['stand'] ?? [];
        $products_list  = is_array($exhibitor['products'] ?? null)  ? $exhibitor['products']  : [];
        $documents_list = is_array($exhibitor['documents'] ?? null) ? $exhibitor['documents'] : [];

        $idNum     = (int)($exhibitor['exhibitorId'] ?? 0);
        $boothArea = (float)($exhibitor_stand_info['boothArea'] ?? 0);

        $website_url = trim($exhibitor_company_info['website'] ?? '');
        if ($website_url !== '' && !preg_match('~^https?://~i', $website_url)) {
            $website_url = 'https://' . $website_url;
        }

        $exhibitors_prepared[] = [
            'exhibitor_id'        => (string)($exhibitor['exhibitorId'] ?? ''),
            'id_numeric'          => (int)($exhibitor['exhibitorId'] ?? 0),
            'idNumeric'           => $idNum,
            'name'                => $exhibitor_company_info['displayName'] ?? '',
            'logo_url'            => $exhibitor_company_info['logoUrl'] ?? '',
            'description'         => $exhibitor_company_info['description'] ?? '',
            'why_visit'           => $exhibitor_company_info['whyVisit'] ?? '',
            'website'             => $website_url,
            'contact_phone'       => $exhibitor_company_info['contactPhone'] ?? '',
            'contact_email'       => $exhibitor_company_info['contactEmail'] ?? '',
            'facebook'            => $exhibitor_company_info['facebook']  ?? '',
            'instagram'           => $exhibitor_company_info['instagram'] ?? '',
            'linkedin'            => $exhibitor_company_info['linkedin']  ?? '',
            'youtube'             => $exhibitor_company_info['youtube']   ?? '',
            'tiktok'              => $exhibitor_company_info['tiktok']    ?? '',
            'x'                   => $exhibitor_company_info['x']         ?? '',
            'brands'              => $normalize_string_array($exhibitor_company_info['brands'] ?? []),
            'catalog_tags'        => $normalize_string_array($exhibitor_company_info['catalogTags'] ?? []),
            'industries'          => $exhibitor_company_info['industries'] ?? '',
            'hall_name'           => $exhibitor_stand_info['hallName'] ?? '',
            'stand_number'        => $exhibitor_stand_info['standNumber'] ?? '',
            'area'          => $boothArea,
            'products_count'      => count($products_list),
            'documents_count'     => count($documents_list),
            'products'            => $products_list,
            'documents'           => $documents_list,
            'products_preview'    => array_values(array_slice($products_list, 0, 2)),
            'documents_preview'   => array_values(array_slice($documents_list, 0, 2)),
        ];
    }

    // pobierz tryb sortowania z URL lub shortcode
    $sort_mode = $_GET['sort_mode'] ?? ($atts['sort_mode'] ?? 'default');

    // wykonaj sortowanie zgodnie z wybranym trybem
    $exhibitors_prepared = exhibitor_catalog_sort(
        $exhibitors_prepared,
        $sort_mode,
        'exhibitor_catalog_calculate_total_score'
    );

    // --- 5️⃣ Modyfikacje kolejności wg parametru exhibitor_changer ---
    if (!empty($atts['exhibitor_changer']) && is_string($atts['exhibitor_changer'])) {
        $exhibitors_prepared = applyManualOrderChanges($atts['exhibitor_changer'], $exhibitors_prepared);

        if (current_user_can('administrator')) {
            echo '<script>console.log("Zastosowano ręczne zmiany kolejności: ' . esc_js($atts['exhibitor_changer']) . '")</script>';
        }
    }

    return $exhibitors_prepared;
}

function limit_labels($text, $limit = 4) {
    $words = explode(' ', trim($text));
    if (count($words) > $limit) {
        // Skróć do 4 słów
        $short = array_slice($words, 0, $limit);

        // Jeśli ostatnie słowo to "samotna" litera/spójnik – usuń
        $last = mb_strtolower(end($short), 'UTF-8');
        $short_connectors = ['i', 'a', 'o', 'w', 'z', 'u', 'na', 'do', 'po', 'od'];

        if (in_array($last, $short_connectors, true)) {
            array_pop($short);
        }

        $text = implode(' ', $short) . '...';
    }

    return $text;
}

function shorten_text($text, $limit = 60) {
    $text = trim($text);
    if (mb_strlen($text) > $limit) {
        $text = mb_substr($text, 0, $limit - 3) . '...';
    }
    return $text;
}

if (!function_exists('pl_select')) {
  function pl_select($n) {
    $n = abs((int)$n);
    if ($n === 1) return 'one';
    $mod10  = $n % 10;
    $mod100 = $n % 100;
    if ($mod10 >= 2 && $mod10 <= 4 && !($mod100 >= 12 && $mod100 <= 14)) return 'few';
    if ($n === 0 || $mod10 === 0 || $mod10 >= 5 || ($mod100 >= 12 && $mod100 <= 14)) return 'many';
    return 'other';
  }

  function pl($key, $n) {
    static $NOUNS = [
      'Wyszukanie' => ['one'=>'Wyszukiwanie','few'=>'Wyszukiwania','many'=>'Wyszukiwań','other'=>'Wyszukiwań'],
    ];
    $cat = pl_select($n);
    return $NOUNS[$key][$cat] ?? '';
  }

  function fmt_count($n, $key) {
    return $n . ' ' . pl($key, $n);
  }
}

function exhibitor_catalog_count_filters($all_items, $field_type) {
    $counts = [];

    foreach ($all_items as $row) {
        $type = $row['type'] ?? '';
        $data = $row['data'] ?? [];

        switch ($field_type) {

            // 🔹 HALE — suma wszystkich typów (exhibitor + brand + product)
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

            // 🔹 SEKTORY — tylko wystawcy
            case 'sector':
                if ($type === 'exhibitor' && !empty($data['catalog_tags']) && is_array($data['catalog_tags'])) {
                    foreach ($data['catalog_tags'] as $tag) {
                        $tag = trim($tag);
                        if ($tag !== '') {
                            $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                        }
                    }
                }
                break;

            // 🔹 MARKI — tylko wystawcy (dotyczy filtrów marek)
            case 'brand':
                if ($type === 'exhibitor' && !empty($data['brands']) && is_array($data['brands'])) {
                    // wystawcy mają listę marek
                    foreach ($data['brands'] as $brand) {
                        $brand = trim($brand);
                        if ($brand !== '') {
                            $counts[$brand] = ($counts[$brand] ?? 0) + 1;
                        }
                    }
                } elseif ($type === 'brand' && !empty($data['brand'])) {
                    // pojedynczy wpis typu "brand"
                    $brand = trim($data['brand']);
                    if ($brand !== '') {
                        $counts[$brand] = ($counts[$brand] ?? 0) + 1;
                    }
                }
                break;

            // 🔹 KATEGORIE PRODUKTÓW — tylko produkty
            case 'tag':
                if ($type === 'product' && !empty($data['product']['tags']) && is_array($data['product']['tags'])) {
                    foreach ($data['product']['tags'] as $tag) {
                        $tag = trim($tag);
                        if ($tag !== '') {
                            $counts[$tag] = ($counts[$tag] ?? 0) + 1;
                        }
                    }
                }
                break;
        }
    }

    return $counts;
}

function exhibitor_catalog_apply_filters($exhibitors_prepared) { 
    $selected_types      = isset($_GET['type']) ? (array)$_GET['type'] : [];
    $selected_halls      = isset($_GET['hall']) ? (array)$_GET['hall'] : [];
    $selected_sectors    = isset($_GET['sector']) ? (array)$_GET['sector'] : [];
    $selected_brands     = isset($_GET['brand']) ? (array)$_GET['brand'] : [];
    $selected_categories = isset($_GET['category']) ? (array)$_GET['category'] : [];
    $search_query        = isset($_GET['search']) ? trim(mb_strtolower($_GET['search'])) : '';

    $filtered = [];

    foreach ($exhibitors_prepared as $exhibitor) {
        $matches_hall     = true;
        $matches_sector   = true;
        $matches_brand    = true;
        $matches_category = true;
        $matches_search   = true;

        // --- HALA ---
        if (!empty($selected_halls)) {
            $matches_hall = in_array($exhibitor['hall_name'] ?? '', $selected_halls, true);
        }

        // --- SEKTORY (tylko wystawcy) ---
        if (!empty($selected_sectors)) {
            $matches_sector = false;
            if (!empty($exhibitor['catalog_tags']) && is_array($exhibitor['catalog_tags'])) {
                foreach ($exhibitor['catalog_tags'] as $tag) {
                    if (in_array($tag, $selected_sectors, true)) {
                        $matches_sector = true;
                        break;
                    }
                }
            }
        }

        // --- MARKI (wystawcy + marki) ---
        if (!empty($selected_brands)) {
            $matches_brand = false;

            if (!empty($exhibitor['brands']) && is_array($exhibitor['brands'])) {
                // przefiltruj tylko pasujące marki
                $filtered_brands = array_values(array_filter(
                    $exhibitor['brands'],
                    fn($b) => in_array($b, $selected_brands, true)
                ));

                // nadpisz listę marek w danych wystawcy
                $exhibitor['brands'] = $filtered_brands;

                if (!empty($filtered_brands)) {
                    $matches_brand = true;
                }
            }
        }

        // --- KATEGORIE PRODUKTÓW (tylko produkty) ---
        if (!empty($selected_categories)) {
            $matches_category = false;

            if (!empty($exhibitor['products']) && is_array($exhibitor['products'])) {
                // przefiltruj produkty — zostaw tylko te z wybranych kategorii
                $filtered_products = [];
                foreach ($exhibitor['products'] as $product) {
                    if (empty($product['tags']) || !is_array($product['tags'])) continue;

                    $has_match = false;
                    foreach ($product['tags'] as $tag) {
                        if (in_array($tag, $selected_categories, true)) {
                            $has_match = true;
                            break;
                        }
                    }

                    if ($has_match) {
                        $filtered_products[] = $product;
                        $matches_category = true;
                    }
                }

                // Nadpisz produkty tylko tymi pasującymi
                $exhibitor['products'] = $filtered_products;
            }
        }

        // --- WYSZUKIWANIE ---
        if ($search_query !== '') {
            // minimum 3 characters
            if (mb_strlen($search_query) < 3) {
                $matches_search = true; // ignore
            } else {
                $matches_search = false;
                $haystacks = [];

                $haystacks[] = mb_strtolower($exhibitor['name'] ?? '');
                $haystacks[] = mb_strtolower($exhibitor['description'] ?? '');
                $haystacks[] = mb_strtolower($exhibitor['website'] ?? '');
                $haystacks[] = mb_strtolower($exhibitor['stand_number'] ?? '');

                if (!empty($exhibitor['brands'])) {
                    foreach ($exhibitor['brands'] as $brand) {
                        $haystacks[] = mb_strtolower($brand);
                    }
                }

                if (!empty($exhibitor['products'])) {
                    foreach ($exhibitor['products'] as $product) {
                        $haystacks[] = mb_strtolower($product['name'] ?? '');
                        $haystacks[] = mb_strtolower($product['description'] ?? '');
                    }
                }

                foreach ($haystacks as $text) {
                    if (strpos($text, $search_query) !== false) {
                        $matches_search = true;
                        break;
                    }
                }
            }
        }

        if ($matches_hall && $matches_sector && $matches_brand && $matches_category && $matches_search) {
            $filtered[] = $exhibitor;
        }
    }

    // 🔹 Nowa logika wyboru typów – łączy aktywne filtry i wybory użytkownika
    $types_for_display = [];

    // 1️⃣ najpierw to, co użytkownik wybrał ręcznie
    if (!empty($selected_types)) {
        $types_for_display = array_merge($types_for_display, $selected_types);
    }

    // 2️⃣ jeśli aktywny jest filtr sektorów → dołóż wystawców
    if (!empty($selected_sectors) && !in_array('exhibitor', $types_for_display, true)) {
        $types_for_display[] = 'exhibitor';
    }

    // 3️⃣ jeśli aktywny jest filtr marek → dołóż wystawców i marki
    if (!empty($selected_brands)) {
        if (!in_array('exhibitor', $types_for_display, true)) {
            $types_for_display[] = 'exhibitor';
        }
        if (!in_array('brand', $types_for_display, true)) {
            $types_for_display[] = 'brand';
        }
    }

    // 4️⃣ jeśli aktywny jest filtr kategorii → dołóż produkty
    if (!empty($selected_categories) && !in_array('product', $types_for_display, true)) {
        $types_for_display[] = 'product';
    }

    // 🧩 jeśli wybrano tylko kategorię, usuń z wyświetlania wystawców
    if (!empty($selected_categories) && empty($selected_types)) {
        $types_for_display = ['product'];
    }

    // 5️⃣ jeżeli dalej nic nie wybrano – pokaż wszystko
    if (empty($types_for_display)) {
        $types_for_display = ['exhibitor', 'brand', 'product'];
    }

    // 🔹 Określ widoczne filtry w zależności od typów
    $visible_filters = [];

    if (in_array('exhibitor', $types_for_display, true)) {
        // dla wystawców: sektory i marki
        $visible_filters[] = 'sector';
        $visible_filters[] = 'brand';
    }
    if (in_array('brand', $types_for_display, true)) {
        // dla marek: tylko marki
        $visible_filters[] = 'brand';
    }
    if (in_array('product', $types_for_display, true)) {
        // dla produktów: kategorie
        $visible_filters[] = 'category';
    }

    // usuń duplikaty
    $visible_filters = array_unique($visible_filters);

    return [
        'filtered' => $filtered,
        'types_for_display' => $types_for_display,
        'visible_filters' => $visible_filters,
    ];

}

function exhibitor_catalog_min_info($exhibitor) {
    return [
        'exhibitor_id'            => $exhibitor['exhibitor_id'] ?? '',
        'exhibitor_name'          => $exhibitor['name'] ?? '',
        'exhibitor_stand_number'  => $exhibitor['stand_number'] ?? '',
        'hall_name'               => $exhibitor['hall_name'] ?? '',
    ];
}