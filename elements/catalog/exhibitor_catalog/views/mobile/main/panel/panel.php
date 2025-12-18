<?php
if (!defined('ABSPATH')) exit;

$domain      = $context['domain'] ?? '';
$total_items = $context['total_items'] ?? 0;
$sort_mode   = $context['sort'] ?? '';

$output = '';

$output .= '
<div class="catalog-mobile-panel '. ($domain !== "warsawexpo.eu" ? "sticky-element" : "") .'">
    <div class="catalog-mobile-panel__wrapper">

        <div class="catalog-mobile-panel__search">
            <form method="get" action="" class="catalog-mobile-panel__search-form">
                <input type="text"
                    class="catalog-mobile-panel__search-input"
                    name="search"
                    value="'. (isset($_GET['search']) ? esc_attr($_GET['search']) : '') .'"
                    placeholder="' . PWECommonFunctions::languageChecker('wyszukaj wystawcę, produkt i markę', 'search for exhibitor, product, and brand') . '" />
            </form>
        </div>

        <div class="catalog-mobile-panel__results-wrapper">
            <div class="catalog-mobile-panel__results">
                <h2 class="catalog-mobile-panel__results-title">
                    ' . PWECommonFunctions::languageChecker('Wyniki', 'Results') . ' <span class="catalog-mobile-panel__results-count">(' . $total_items . ')</span>
                </h2>

                <h2 class="catalog-mobile-panel__results-title-filter">
                    ' . PWECommonFunctions::languageChecker('Filtruj', 'Filters') . '
                </h2>

                <div class="catalog-mobile-panel__buttons">
                    <div class="catalog-mobile-sort">
                        '. exhibitor_catalog_render_sort_select($sort_mode) .'
                    </div>

                    <a class="exhibitor-catalog__panel-filter-clear" href="?' . ec_build_clear_filters_query() . '">' . PWECommonFunctions::languageChecker('Wyczyść', 'Clear') . '</a>

                    <button class="catalog-mobile-panel__filters-btn" id="filterMenu">
                        ' . pwe_svg_icon('filter') . '
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>';

echo $output;
