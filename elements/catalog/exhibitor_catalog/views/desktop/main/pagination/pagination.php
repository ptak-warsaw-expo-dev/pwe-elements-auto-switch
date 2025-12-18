<?php
if (!defined('ABSPATH')) exit;

$total_pages  = $context['total_pages'] ?? 1;
$current_page = $context['current_page'] ?? 1;

$output = '';

if ($total_pages <= 1) {
    echo $output;
    return;
}

$query_params = $_GET;
unset($query_params['exh-page']);

$output .= '
<div class="exhibitor-catalog__pagination">
';

if ($current_page < $total_pages) {

    $next_link = ec_paginate_build_link($current_page + 1, $query_params);

    $output .= '
        <a class="exhibitor-catalog__pagination-btn" href="' . $next_link . '">
            ' . PWECommonFunctions::languageChecker('Załaduj więcej', 'Load more') . '
        </a>
    ';
}

$output .= '</div>';

echo $output;
