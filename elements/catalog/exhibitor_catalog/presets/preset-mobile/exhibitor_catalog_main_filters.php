<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('exhibitor_catalog_render_filters')) {
  /**
   * Render sekcji filtrów.
   */

    if (!function_exists('exhibitor_catalog_render_filters')) {

        function exhibitor_catalog_render_filters($all_items, $halls, $hall_counts, $sectors, $sectors_counts, $brands, $brands_counts, $products_tags, $tags_counts, $visible_filters) {

            // --- generator grup filtrów ---
            $render_filter_group = function($title, $items, $counts = [], $initial_visible = 5, $group_id = '') {
                if (empty($items)) return '';

                $gid = $group_id ?: 'filters-' . md5($title);

                $output = '
                <div class="catalog-mobile-filters__group">
                    <div class="catalog-mobile-filters__header">
                        <h3 class="catalog-mobile-filters__title">' . esc_html($title) . '</h3>
                        ' . pwe_svg_icon('arrow') . '
                    </div>
                    <div class="catalog-mobile-filters__body">';

                        $total = count($items);
                        $i = 0;

                        foreach ($items as $item) {
                            $count = $counts[$item] ?? 0;
                            $label = shorten_text($item, 30);

                            $output .= '
                            <label class="catalog-mobile-filters__item">
                                <input type="checkbox" class="catalog-mobile-filters__checkbox-input" 
                                    name="' . esc_attr($group_id) . '[]" 
                                    value="' . esc_attr($item) . '" 
                                    ' . (in_array($item, $_GET[$group_id] ?? []) ? 'checked' : '') . '>
                                <div class="catalog-mobile-filters__checkbox-box">
                                    <div class="catalog-mobile-filters__checkbox-handle"></div>
                                </div>
                                <span class="catalog-mobile-filters__checkbox-label">' . esc_html($label) . '
                                    <span class="catalog-mobile-filters__count">(' . $count . ')</span>
                                </span>
                            </label>';
                        }

                    $output .= '
                    </div>
                </div>';

                return $output;
            };

            // --- struktura filtrów ---
            $output = '
            <div class="catalog-mobile-filters-wrapper">
                <form method="get" action="" class="catalog-mobile-filters__form">
                    <div class="catalog-mobile-filters__loader" id="filtersLoader"></div>
                    <div class="catalog-mobile-filters">';

                        // TYPY
                        $types = [
                            PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitors') => count(array_filter($all_items, fn($i) => $i['type'] === 'exhibitor')),
                            PWECommonFunctions::languageChecker('Marki', 'Brands')        => count(array_filter($all_items, fn($i) => $i['type'] === 'brand')),
                            PWECommonFunctions::languageChecker('Produkty', 'Products')   => count(array_filter($all_items, fn($i) => $i['type'] === 'product')),
                        ];

                        $output .= '
                        <div class="catalog-mobile-filters__group">
                            <div class="catalog-mobile-filters__header">
                                <h3 class="catalog-mobile-filters__title">' . PWECommonFunctions::languageChecker('Typ', 'Type') . '</h3>
                                ' . pwe_svg_icon('arrow') . '
                            </div>
                            <div class="catalog-mobile-filters__body">';

                                foreach ($types as $label => $count) {

                                    switch ($label) {
                                        case '' . PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitors') . '': $value = 'exhibitor'; break;
                                        case '' . PWECommonFunctions::languageChecker('Marki', 'Brands') . '':    $value = 'brand'; break;
                                        case '' . PWECommonFunctions::languageChecker('Produkty', 'Products') . '': $value = 'product'; break;
                                        default: $value = strtolower($label);
                                    }

                                    $output .= '
                                        <label class="catalog-mobile-filters__item">
                                        <input type="checkbox"
                                            class="catalog-mobile-filters__checkbox-input"
                                            name="type[]"
                                            value="' . esc_attr($value) . '"
                                            ' . (in_array($value, $_GET['type'] ?? []) ? 'checked' : '') . '>
                                        <div class="catalog-mobile-filters__checkbox-box">
                                            <div class="catalog-mobile-filters__checkbox-handle"></div>
                                        </div>
                                        <span class="catalog-mobile-filters__checkbox-label">' . esc_html($label) . '
                                            <span class="catalog-mobile-filters__count">(' . $count . ')</span>
                                        </span>
                                        </label>';
                                }
                            $output .= '
                            </div>
                        </div>';

                        // HALE / SEKTORY / MARKI / KATEGORIE
                        $output .= $render_filter_group('Hale', $halls, $hall_counts, 5, 'hall');

                        if (in_array('sector', $visible_filters, true)) {
                            $output .= $render_filter_group(
                                '' . PWECommonFunctions::languageChecker('Sektory Branżowe', 'Industry Sectors') . '',
                                $sectors,
                                $sectors_counts,
                                5,
                                'sector'
                            );
                        }

                        if (in_array('brand', $visible_filters, true)) {
                            $output .= $render_filter_group(
                                '' . PWECommonFunctions::languageChecker('Marki', 'Brands') . '',
                                $brands,
                                $brands_counts,
                                5,
                                'brand'
                            );
                        }

                        if (in_array('category', $visible_filters, true)) {
                            $output .= $render_filter_group(
                                '' . PWECommonFunctions::languageChecker('Kategorie Produktów', 'Product Categories') . '',
                                $products_tags,
                                $tags_counts,
                                5,
                                'category'
                            );
                        }

                    $output .= '
                    </div>';

                $output .= '
                </form>
                <div class="catalog-mobile-filters__bottom-bar">
                    <button type="button" class="catalog-mobile-filters__bottom-apply">
                        Pokaż wyniki
                    </button>
                    <button type="button" class="catalog-mobile-filters__clear catalog-mobile-filters__bottom-clear">
                        Wyczyść filtry
                    </button>
                </div>
            </div>';

            return $output;
        }

    }
}
