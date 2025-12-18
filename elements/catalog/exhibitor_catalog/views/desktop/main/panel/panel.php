<?php
if (!defined('ABSPATH')) exit;

$domain      = $context['domain']      ?? '';
$total_items = $context['total_items'] ?? 0;
$sort_mode   = $context['sort']   ?? '';

$year = null;

if (!empty($_SERVER['REQUEST_URI'])) {
    if (preg_match('/20[0-9]{2}/', $_SERVER['REQUEST_URI'], $m)) {
        $year = $m[0];
    }
}

$output = '';

$output .= '
<div class="exhibitor-catalog__panel">
    <div class="exhibitor-catalog__panel-wrapper">

        <div class="exhibitor-catalog__panel-filter">
            <h2 class="exhibitor-catalog__panel-filter-title">' . PWECommonFunctions::languageChecker('Filtruj', 'Filters') . '</h2>
            <a class="exhibitor-catalog__panel-filter-clear" href="?' . ec_build_clear_filters_query() . '">' . PWECommonFunctions::languageChecker('Wyczyść', 'Clear') . '</a>
        </div>

        <div class="exhibitor-catalog__panel-items">';
            if ($year) {
                $output .= '
                <h2 class="exhibitor-catalog__panel-items-name">' . PWECommonFunctions::languageChecker('Katalog ', 'Catalog ') . $year . '</h2>';
            }
            $output .= '
            <h2 class="exhibitor-catalog__panel-items-title">
                ' . PWECommonFunctions::languageChecker('Wyniki', 'Results') . '
                <span class="exhibitor-catalog__panel-items-count">(' . $total_items . ')</span>
            </h2>

            <div class="exhibitor-catalog__search">
                <form method="get" action="" class="exhibitor-catalog__search-form">
                    <input type="text"
                        class="exhibitor-catalog__search-input"
                        name="search"
                        value="' . (isset($_GET["search"]) ? esc_attr($_GET["search"]) : '') . '"
                        placeholder="' . PWECommonFunctions::languageChecker('wyszukaj wystawcę, produkt i markę', 'search for exhibitor, product, and brand') . '" />
                </form>
            </div>

            ' . exhibitor_catalog_render_sort_select($sort_mode) . '

        </div>

    </div>
</div>';

echo $output;
