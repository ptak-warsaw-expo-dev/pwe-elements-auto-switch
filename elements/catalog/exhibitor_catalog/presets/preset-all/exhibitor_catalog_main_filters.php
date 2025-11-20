<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('exhibitor_catalog_render_filters')) {
  /**
   * Dopisuje do $output całą sekcję filtrów (.exhibitor-catalog__filter-container).
   * Korzysta z istniejącej w main.php funkcji limit_labels().
   * @param mixed  $products
   * @param array  $halls
   * @param array  $sectors
   * @param array  $products_tags
   * @param string &$output
   */

    if (!function_exists('exhibitor_catalog_render_filters')) {

    function exhibitor_catalog_render_filters($all_items, $halls, $hall_counts, $sectors, $sectors_counts, $brands, $brands_counts, $products_tags, $tags_counts, $visible_filters) {

        // --- Pomocnicza funkcja generująca pojedynczą grupę filtrów ---
        $render_filter_group = function($title, $items, $counts = [], $initial_visible = 5, $group_id = '') {
            if (empty($items)) return '';

            $output = '<div class="exhibitor-catalog__filter-group">';
            $output .= '
            <div class="exhibitor-catalog__heading-container">
                <h3 class="exhibitor-catalog__filter-heading">' . esc_html($title) . '</h3>
            </div>
            <div class="exhibitor-catalog__labels-container">';

            $total = count($items);
            $i = 0;

            // 1️⃣ pierwsze $initial_visible — zawsze widoczne
            foreach ($items as $item) {
                if ($i >= $initial_visible) break;
                $count = $counts[$item] ?? 0;
                $label = limit_labels($item);
                $output .= '
                <label class="exhibitor-catalog__filter-switch">
                    <input type="checkbox" class="exhibitor-catalog__filter-checkbox" name="' . esc_attr($group_id) . '[]" value="' . esc_attr($item) . '" ' . (in_array($item, $_GET[$group_id] ?? []) ? 'checked' : '') . '>
                    <div class="exhibitor-catalog__filter-switch-background">
                    <div class="exhibitor-catalog__filter-switch-handle"></div>
                    </div>
                    <span class="exhibitor-catalog__filter-label">' . esc_html($label) . '
                    <span class="exhibitor-catalog__filter-label-number">(' . $count . ')</span>
                    </span>
                </label>';
                $i++;
            }

            // 2️⃣ reszta w "collapsible"
            if ($total > $initial_visible) {
                $gid = $group_id ?: 'filters-' . md5($title);
                $output .= '<div id="' . esc_attr($gid) . '" class="exhibitor-catalog__collapsible">';
                for ($j = $initial_visible; $j < $total; $j++) {
                    $item = $items[$j];
                    $label = limit_labels($item);
                    $count = $counts[$item] ?? 0;
                    $output .= '
                    <label class="exhibitor-catalog__filter-switch">
                        <input type="checkbox"
                            class="exhibitor-catalog__filter-checkbox"
                            name="' . esc_attr($group_id) . '[]"
                            value="' . esc_attr($item) . '"
                            ' . (in_array($item, $_GET[$group_id] ?? [], true) ? 'checked' : '') . '>
                        <div class="exhibitor-catalog__filter-switch-background">
                            <div class="exhibitor-catalog__filter-switch-handle"></div>
                        </div>
                        <span class="exhibitor-catalog__filter-label">'
                            . esc_html($label) . '
                            <span class="exhibitor-catalog__filter-label-number">(' . $count . ')</span>
                        </span>
                    </label>';
                }
                $output .= '</div>';

            }

            $output .= '</div>
            </div>';
            $output .= '
                <button type="button"
                        class="exhibitor-catalog__more-btn"
                        data-target="#' . esc_attr($gid) . '"
                        aria-expanded="false"
                        aria-controls="' . esc_attr($gid) . '">
                    Pokaż więcej
                </button>';
            return $output;
        };

        // --- GŁÓWNA STRUKTURA FILTRÓW ---
        $output = '
        <form method="get" action="" class="exhibitor-catalog__filters-form">
            <div class="exhibitor-catalog__loader" id="filtersLoader"></div>';

            $output .= '<div class="exhibitor-catalog__filter-container">';

            // Typy
            $types = [
                PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitors') => count(array_filter($all_items, fn($i) => $i['type'] === 'exhibitor')),
                PWECommonFunctions::languageChecker('Marki', 'Brands')        => count(array_filter($all_items, fn($i) => $i['type'] === 'brand')),
                PWECommonFunctions::languageChecker('Produkty', 'Products')   => count(array_filter($all_items, fn($i) => $i['type'] === 'product')),
            ];

            $output .= '<div class="exhibitor-catalog__filter-group">
            <div class="exhibitor-catalog__heading-container">
                <h3 class="exhibitor-catalog__filter-heading">' . PWECommonFunctions::languageChecker('Typ', 'Type') . '</h3>
            </div>
            <div class="exhibitor-catalog__labels-container">';

                foreach ($types as $label => $count) {
                    // Mapowanie etykiety -> wartości systemowej
                    switch ($label) {
                        case '' . PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitors') . '': $value = 'exhibitor'; break;
                        case '' . PWECommonFunctions::languageChecker('Marki', 'Brands') . '':    $value = 'brand'; break;
                        case '' . PWECommonFunctions::languageChecker('Produkty', 'Products') . '': $value = 'product'; break;
                        default:         $value = strtolower($label);
                    }

                    $output .= '
                        <label class="exhibitor-catalog__filter-switch">
                        <input type="checkbox"
                            class="exhibitor-catalog__filter-checkbox"
                            name="type[]"
                            value="' . esc_attr($value) . '"
                            ' . (in_array($value, $_GET['type'] ?? []) ? 'checked' : '') . '>
                        <div class="exhibitor-catalog__filter-switch-background">
                            <div class="exhibitor-catalog__filter-switch-handle"></div>
                        </div>
                        <span class="exhibitor-catalog__filter-label">' . esc_html($label) . '
                            <span class="exhibitor-catalog__filter-label-number">(' . $count . ')</span>
                        </span>
                        </label>';
                }
            $output .= '</div>
            </div>';

            // Pozostałe grupy (hale, sektory, marki, kategorie)
            $output .= $render_filter_group('Hale', $halls, $hall_counts, 5, 'hall');
            if (in_array('sector', $visible_filters, true)) {
                $output .= $render_filter_group('' . PWECommonFunctions::languageChecker('Sektory Branżowe', 'Industry Sectors') . '', $sectors, $sectors_counts, 5, 'sector');
            }

            if (in_array('brand', $visible_filters, true)) {
                $output .= $render_filter_group('' . PWECommonFunctions::languageChecker('Marki', 'Brands') . '', $brands, $brands_counts, 5, 'brand');
            }

            if (in_array('category', $visible_filters, true)) {
                $output .= $render_filter_group('' . PWECommonFunctions::languageChecker('Kategorie Produktów', 'Product Categories') . '', $products_tags, $tags_counts, 5, 'category');
            }

            $output .= '</div>'; // /filter-container

        $output .= '</form>';
        return $output;
    }

    }

}