<?php
if (!defined('ABSPATH')) exit;

$all_items       = $context['all_items']       ?? [];
$halls           = $context['halls']           ?? [];
$hall_counts     = $context['hall_counts']     ?? [];
$sectors         = $context['sectors']         ?? [];
$sectors_counts  = $context['sectors_counts']  ?? [];
$brands          = $context['brands']          ?? [];
$brands_counts   = $context['brands_counts']   ?? [];
$tags            = $context['tags']            ?? [];
$tags_counts     = $context['tags_counts']     ?? [];
$visible_filters = $context['visible_filters'] ?? [];

$types = [
    PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitors') => count(array_filter($all_items, fn($i) => $i['type'] === 'exhibitor')),
    PWECommonFunctions::languageChecker('Marki', 'Brands')        => count(array_filter($all_items, fn($i) => $i['type'] === 'brand')),
    PWECommonFunctions::languageChecker('Produkty', 'Products')   => count(array_filter($all_items, fn($i) => $i['type'] === 'product')),
];

if (!empty($halls)) {
    usort($halls, function ($a, $b) {
        return strcasecmp($a, $b);
    });
}

if (!empty($sectors)) {
    usort($sectors, function ($a, $b) use ($sectors_counts) {
        $countA = $sectors_counts[$a] ?? 0;
        $countB = $sectors_counts[$b] ?? 0;

        if ($countA !== $countB) {
            return $countB <=> $countA; // malejąco
        }

        return strcasecmp($a, $b); // fallback
    });
}

if (!empty($brands)) {
    usort($brands, function ($a, $b) use ($brands_counts) {
        $countA = $brands_counts[$a] ?? 0;
        $countB = $brands_counts[$b] ?? 0;

        if ($countA !== $countB) {
            return $countB <=> $countA;
        }

        return strcasecmp($a, $b);
    });
}

/*
| Helper function generating a single group of filters
*/
$render_filter_group = function($title, $items, $counts = [], $group_id = '', $default_open = false) {

    if (empty($items)) return '';

    $is_open = $default_open ? ' is-open' : '';

    $output = '
    <div class="exhibitor-catalog__filter-group is-collapsible' . $is_open . ' filter-group__' . $group_id . '">

        <div class="exhibitor-catalog__heading-container">
            <h3 class="exhibitor-catalog__filter-heading">
                ' . esc_html($title) . '
                <span class="exhibitor-catalog__filter-arrow" aria-hidden="true"></span>
            </h3>
        </div>
        <div class="exhibitor-catalog__labels-container">';

            foreach ($items as $item) {

                $original = $context['category_labels'][$item] ?? $item;
                $label = function_exists('ec_limit_labels') ? ec_limit_labels($original, 5, 34) : $original;

                $count = $counts[$item] ?? 0;

                $selected_values = ec_get_param_array($group_id);
                $checked = in_array($item, $selected_values, true) ? 'checked' : '';

                $output .= '
                <label class="exhibitor-catalog__filter-switch">

                    <input type="checkbox" class="exhibitor-catalog__filter-checkbox" name="' . esc_attr($group_id) . '" value="' . esc_attr($item) . '"' . $checked . '>

                    <div class="exhibitor-catalog__filter-switch-background">
                        <div class="exhibitor-catalog__filter-switch-handle"></div>
                    </div>

                    <span class="exhibitor-catalog__filter-label">
                        ' . esc_html($label) . '
                        <span class="exhibitor-catalog__filter-label-number">(' . $count . ')</span>
                    </span>

                </label>';
            }

        $output .= '
        </div>
    </div>';

    return $output;
};

$output .= '
<form method="get" action="" class="exhibitor-catalog__filters-form">

    <div class="exhibitor-catalog__loader" id="filtersLoader"></div>

    <div class="exhibitor-catalog__filter-container">

        <div class="exhibitor-catalog__filter-group is-collapsible is-open filter-group__type">
            <div class="exhibitor-catalog__heading-container">
                <h3 class="exhibitor-catalog__filter-heading">
                    ' . PWECommonFunctions::languageChecker('Typ', 'Type') . '
                    <span class="exhibitor-catalog__filter-arrow" aria-hidden="true"></span>
                </h3>
            </div>

            <div class="exhibitor-catalog__labels-container">';

                foreach ($types as $label => $count) {

                    switch ($label) {
                        case PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitors'):
                            $value = 'exhibitor';
                        break;

                        case PWECommonFunctions::languageChecker('Marki', 'Brands'):
                            $value = 'brand';
                        break;

                        case PWECommonFunctions::languageChecker('Produkty', 'Products'):
                            $value = 'product';
                        break;

                        default:
                            $value = strtolower($label);
                    }

                    $type_selected = ec_get_param_array('type');
                    $checked = in_array($value, $type_selected, true) ? 'checked' : ''; 

                    $output .= '
                    <label class="exhibitor-catalog__filter-switch">
                        <input type="checkbox" class="exhibitor-catalog__filter-checkbox" name="type" value="' . esc_attr($value) . '" ' . $checked . '>

                        <div class="exhibitor-catalog__filter-switch-background">
                            <div class="exhibitor-catalog__filter-switch-handle"></div>
                        </div>

                        <span class="exhibitor-catalog__filter-label">
                            ' . esc_html($label) . '
                            <span class="exhibitor-catalog__filter-label-number">(' . $count . ')</span>
                        </span>
                    </label>';

                }

            $output .= '
            </div>
        </div>';


        $output .= $render_filter_group(
            PWECommonFunctions::languageChecker('Hale', 'Halls'), 
            $halls, 
            $hall_counts, 
            'hall', 
            true
        );

        if (in_array('sector', $visible_filters, true)) {
            $output .= $render_filter_group(
                PWECommonFunctions::languageChecker('Sektory Branżowe', 'Industry Sectors'),
                $sectors,
                $sectors_counts,
                'sector'
            );
        }

        if (in_array('brand', $visible_filters, true)) {
            $output .= $render_filter_group(
                PWECommonFunctions::languageChecker('Marki', 'Brands'),
                $brands,
                $brands_counts,
                'brand'
            );
        }

        if (in_array('category', $visible_filters, true)) {

            $multi_items = $tags;

            if (!empty($multi_items)) {
                usort($multi_items, function ($a, $b) use ($tags_counts, $context) {
                    $countA = $tags_counts[$a] ?? 0;
                    $countB = $tags_counts[$b] ?? 0;

                    if ($countA !== $countB) {
                        return $countB <=> $countA;
                    }

                    return strcasecmp(
                        $context['category_labels'][$a] ?? $a,
                        $context['category_labels'][$b] ?? $b
                    );
                });
            }

            $output .= $render_filter_group(
                PWECommonFunctions::languageChecker('Kategorie Produktów', 'Product Categories'),
                $multi_items,
                $tags_counts,
                'category'
            );
        }

    $output .= '
    </div>
    <div class="exhibitor-catalog__filter-wrapper  exhibitor-catalog__panel-filter-search--floating">
        <button type="button" class="exhibitor-catalog__panel-filter-search">
            ' . PWECommonFunctions::languageChecker('Pokaż wyniki', 'Show results') . '
        </button>
    </div>

</form>';

echo $output;