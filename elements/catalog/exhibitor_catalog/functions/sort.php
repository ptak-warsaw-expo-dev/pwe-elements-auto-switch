<?php
if (!defined('ABSPATH')) exit;

/**
 * Exhibitor scoring system
 * Generates weighted score used for default sorting.
 */
function ec_calculate_total_score($exhibitor) {

    $score = 0.0;

    // products
    $score += min(($exhibitor['products_count'] ?? 0) * 3.0, 12.0);

    // documents
    $score += min(($exhibitor['documents_count'] ?? 0) * 2.0, 8.0);

    // profile completeness
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


/**
 * Available sort modes
 */
function ec_sort_modes() {
    return [
        'default'      => '' . PWECommonFunctions::languageChecker('Domyślnie', 'Default') . '',
        'alphabetical' => '' . PWECommonFunctions::languageChecker('Alfabetycznie', 'Alphabetically') . '',
        'area'         => '' . PWECommonFunctions::languageChecker('Powierzchnia', 'Surface area') . '',
    ];
}


/**
 * Main sorting entry point
 */
function ec_sort_exhibitors($exhibitors, $mode = 'default') {

    switch ($mode) {
        case 'alphabetical':
            return ec_sort_alphabetical($exhibitors);

        case 'area':
            return ec_sort_area($exhibitors);

        case 'default':
        default:
            return ec_sort_default($exhibitors);
    }
}


/**
 * Default sorting
 */
function ec_sort_default($exhibitors) {

    usort($exhibitors, function($a, $b) {

        $scoreA = ec_calculate_total_score($a);
        $scoreB = ec_calculate_total_score($b);

        if ($scoreA !== $scoreB) {
            return $scoreB <=> $scoreA;
        }

        return mb_strtolower($a['name']) <=> mb_strtolower($b['name']);
    });

    return $exhibitors;
}


/**
 * Alphabetical sorting
 */
function ec_sort_alphabetical($exhibitors) {

    usort($exhibitors, function($a, $b) {

        $aClean = ltrim($a['name']);
        $bClean = ltrim($b['name']);

        // remove leading quotes
        if (in_array(mb_substr($aClean, 0, 1), ['"', "'"])) {
            $aClean = mb_substr($aClean, 1);
        }
        if (in_array(mb_substr($bClean, 0, 1), ['"', "'"])) {
            $bClean = mb_substr($bClean, 1);
        }

        return mb_strtolower($aClean) <=> mb_strtolower($bClean);
    });

    return $exhibitors;
}


/**
 * Sort by stand area
 */
function ec_sort_area($exhibitors) {

    usort($exhibitors, function($a, $b) {
        return floatval($b['area'] ?? 0) <=> floatval($a['area'] ?? 0);
    });

    return $exhibitors;
}


/**
 * Manual order modifications (A <=> B, A =>> B)
 */
function ec_apply_manual_order_changes($changes, $exhibitors, $nameKeys = [
    'Nazwa_wystawcy','company_name','name','exhibitor_name'
]) {

    $changes    = html_entity_decode($changes, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $exhibitors = array_values($exhibitors);

    $indexById = [];
    foreach ($exhibitors as $i => $exh) {
        if (isset($exh['id_numeric'])) {
            $indexById[(int)$exh['id_numeric']] = $i;
        }
    }

    $findIndexById = function($id) use (&$indexById) {
        $id = (int)$id;
        return array_key_exists($id, $indexById) ? $indexById[$id] : null;
    };

    $findIndexByName = function($needle) use (&$exhibitors, $nameKeys) {

        $needle = trim($needle);
        if ($needle === '') return null;

        foreach ($exhibitors as $i => $exh) {
            foreach ($nameKeys as $k) {
                if (!empty($exh[$k]) && is_string($exh[$k]) &&
                    stripos($exh[$k], $needle) !== false) {
                    return $i;
                }
            }
        }

        return null;
    };

    $resolveToIndex = function($token) use (&$findIndexById, &$findIndexByName, &$exhibitors) {

        $token = trim($token);
        if ($token === '') return null;

        if (ctype_digit($token)) {
            $idMatch = $findIndexById((int)$token);
            if ($idMatch !== null) return $idMatch;
        }

        if (ctype_digit($token)) {
            $pos = (int)$token;
            $pos--;
            if ($pos >= 0 && $pos < count($exhibitors)) {
                return $pos;
            }
        }

        return $findIndexByName($token);
    };

    $rebuildIndexById = function() use (&$indexById, &$exhibitors) {
        $exhibitors = array_values($exhibitors);
        $indexById  = [];

        foreach ($exhibitors as $i => $exh) {
            if (isset($exh['id_numeric'])) {
                $indexById[(int)$exh['id_numeric']] = $i;
            }
        }
    };

    $ops = array_filter(
        array_map('trim', explode(';;', $changes)),
        fn($x) => $x !== ''
    );

    foreach ($ops as $op) {

        if (strpos($op, '<=>') !== false) {

            [$a, $b] = array_map('trim', explode('<=>', $op, 2));
            $ia = $resolveToIndex($a);
            $ib = $resolveToIndex($b);

            if ($ia === null || $ib === null || $ia === $ib) continue;

            $tmp = $exhibitors[$ia];
            $exhibitors[$ia] = $exhibitors[$ib];
            $exhibitors[$ib] = $tmp;

            $rebuildIndexById();
            continue;
        }

        if (strpos($op, '=>>') !== false) {

            [$src, $dst] = array_map('trim', explode('=>>', $op, 2));
            $is = $resolveToIndex($src);
            $id = $resolveToIndex($dst);

            if ($is === null || $id === null || $is === $id) continue;

            $item = $exhibitors[$is];
            array_splice($exhibitors, $is, 1);

            if ($is < $id) $id--;

            array_splice($exhibitors, $id, 0, [$item]);

            $rebuildIndexById();
        }
    }

    return array_values($exhibitors);
}


/**
 * Render custom select for sort modes
 */
function exhibitor_catalog_render_sort_select($current_sort) {

    $modes = ec_sort_modes();

    $active_icon = function_exists('pwe_svg_icon')
        ? pwe_svg_icon($current_sort)
        : '';

    $html  = '<div class="catalog-custom-select" data-select="sort" data-current="'.$current_sort.'">';
    $html .= '<div class="catalog-custom-select__selected">';
    $html .= '<span class="catalog-custom-select__icon">'.$active_icon.'</span>';
    $html .= '</div>';

    $html .= '<div class="catalog-custom-select__dropdown">';

    foreach ($modes as $value => $label) {

        $active = ($current_sort === $value) ? 'active' : '';

        $icon = function_exists('pwe_svg_icon')
            ? pwe_svg_icon($value)
            : '';

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