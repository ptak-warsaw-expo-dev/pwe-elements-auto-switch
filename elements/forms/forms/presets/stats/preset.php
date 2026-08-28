<?php
$mode = $mode ?? 'stats';
$languages = $languages ?? [];
$forms = $forms ?? [];
$url = $url ?? '';
$access = $access ?? '';
$active_stats = pwe_forms_form_stats($forms);

$output = '
<div id="pweGFTool" class="pwe-gf-tool">
    <div class="pwe-gf-tool__header">
        <div><span class="pwe-gf-tool__eyebrow">Gravity Forms</span><h2>Statystyki</h2><p>Sprawdź liczbę wpisów i wyświetleń każdego formularza oraz pobierz zbiorcze zestawienia CSV i JSON.</p></div>
    </div>
    ' . pwe_forms_render_nav($group ?? $mode) . '';
$output .= '<div class="pwe-gf-tool__stats-heading"><h3>Pobierz statystyki wszystkich formularzy</h3></div><div class="pwe-gf-tool__grid pwe-gf-tool__grid-2 pwe-gf-tool__stats-actions">';
foreach (['stats_csv' => ['CSV', 'Tabela gotowa do Excela'], 'stats_json' => ['JSON', 'Dane do dalszego przetwarzania']] as $type => $copy) {
    $output .= '<form class="pwe-gf-tool__card" action="' . esc_url($url) . '" method="post">'
        . '<input type="hidden" name="action" value="' . esc_attr(PWE_FORMS_DOWNLOAD_ACTION) . '"><input type="hidden" name="export_type" value="' . esc_attr($type) . '">' . $access
        . '<h3>' . esc_html($copy[0]) . '</h3><p>' . esc_html($copy[1]) . '</p><button class="pwe-gf-tool__button" type="submit">Pobierz ' . esc_html($copy[0]) . '</button>'
        . '</form>';
}
$output .= '</div><div class="pwe-gf-tool__stats-heading"><h3>Aktywne formularze</h3><p>' . count($active_stats) . ' formularzy</p></div><div class="pwe-gf-tool__stats-grid">';
foreach ($active_stats as $stat) {
    $output .= '<article class="pwe-gf-tool__stat-card">'
        . '<span class="pwe-gf-tool__stat-id">ID ' . (int) $stat['form id'] . '</span>'
        . '<h4>' . esc_html($stat['nazwa']) . '</h4>'
        . '<dl><div><dt>Wpisy</dt><dd>' . (int) $stat['wpisy'] . '</dd></div><div><dt>Wyświetlenia</dt><dd>' . (int) $stat['wyswietlenia'] . '</dd></div></dl>'
        . '</article>';
}
$output .= '</div>';
$output .= '</div>';

return $output;
