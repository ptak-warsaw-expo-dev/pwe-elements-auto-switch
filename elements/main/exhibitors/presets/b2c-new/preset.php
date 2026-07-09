<?php

$js_exhibitors = array_map(function($ex) {
    return [
        'name' => esc_html($ex['name'] ?? ''),
        'logo' => esc_url($ex['logo'] ?? '')
    ];
}, $exhibitors);

$output = '
<div id="pweExhibitors" class="pwe-exhibitors pwe-marquee-mode">
    <div class="pwe-exhibitors__wrapper">

        <div class="pwe-exhibitors__header text-center">
            <span class="pwe-exhibitors__badge">'. PWE_Functions::multi_translation("catalog_title") .'</span>
            <h2 class="pwe-exhibitors__title">
                '. PWE_Functions::multi_translation("catalog_subtitle") .'
            </h2>
        </div>
        <div class="pwe-marquee-container">
            <div class="pwe-marquee-row dir-right" id="pwe-row-1"></div>
            <div class="pwe-marquee-row dir-left" id="pwe-row-2"></div>
            <div class="pwe-marquee-row dir-right" id="pwe-row-3"></div>
        </div>
            <div class="pwe-exhibitors__buttons">
                <div class="pwe-exhibitors__button desktop">
                    <a href="'. PWE_Functions::multi_translation("catalog_link") .'">'. PWE_Functions::multi_translation("catalog_link_text") .'</a>
                </div>
            </div>
    </div>
</div>';

// Przekazanie bezpiecznych danych do JS
$output .= '<script>
    window.PWE_EXHIBITORS_LIST = ' . wp_json_encode($js_exhibitors, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';
</script>';


$output .= '
<script>
jQuery(function($){
    const items = window.PWE_EXHIBITORS_LIST || [];
    if (!items.length) return;

    const row1Data = [...items].reverse();
    const row2Data = [...items];
    const row3Data = [...items].sort(() => 0.5 - Math.random());

    function buildRowHtml(dataArray) {
        const tripled = [...dataArray, ...dataArray, ...dataArray];
        let html = \'<div class="pwe-marquee-track">\';

        tripled.forEach(function(item){
            html += `
            <div class="pwe-marquee-card">
                <img src="${item.logo}" alt="${item.name}" onerror="this.style.display=\'none\'">
            </div>`;
        });

        html += \'</div>\';
        return html;
    }

    $("#pwe-row-1").html(buildRowHtml(row1Data));
    $("#pwe-row-2").html(buildRowHtml(row2Data));
    $("#pwe-row-3").html(buildRowHtml(row3Data));
});
</script>';

return $output;