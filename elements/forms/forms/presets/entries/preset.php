<?php
$mode = $mode ?? 'entries';
$languages = $languages ?? [];
$url = $url ?? '';
$access = $access ?? '';

$output = '
<div id="pweGFTool" class="pwe-gf-tool">
    <div class="pwe-gf-tool__header">
        <div><span class="pwe-gf-tool__eyebrow">Gravity Forms</span><h2>Skaner QR</h2><p>Wklej kody QR ze skanera, aby błyskawicznie pobrać dane wpisów (imię, e-mail, telefon) w pliku CSV.</p></div>
    </div>
    ' . pwe_forms_render_nav($group ?? $mode) . '';
$output .= '<form class="pwe-gf-tool__card" action="' . esc_url($url) . '" method="post">'
	. '<input type="hidden" name="action" value="' . esc_attr(PWE_FORMS_DOWNLOAD_ACTION) . '">'
	. '<input type="hidden" name="export_type" value="scanner_csv">' . $access
	. '<label for="pwe-gf-tool__codes"><strong>Kody QR ze skanera</strong><span>Wklej po jednym kodzie w każdym wierszu. Narzędzie odczyta z kodu ID wpisu Gravity Forms i pobierze imię, e-mail oraz telefon.</span></label>'
	. '<div class="pwe-gf-tool__scanner-help">Akceptowany układ kodu: litery + 3 cyfry + ID wpisu + litery + cyfry. Niepasujące lub nieistniejące kody zostaną pominięte.</div>'
	. '<textarea id="pwe-gf-tool__codes" name="qr_search" rows="10" required placeholder="Wklej kody QR…"></textarea>'
	. '<button class="pwe-gf-tool__button" type="submit">Pobierz dane skanera CSV</button>'
	. '</form>';
$output .= '</div>';

return $output;
