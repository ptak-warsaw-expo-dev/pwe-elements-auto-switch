<?php
$mode = $mode ?? 'qrcode';
$languages = $languages ?? [];
$forms = $forms ?? [];

$output = '
<div id="pweGFTool" class="pwe-gf-tool">
    <div class="pwe-gf-tool__header">
        <div><span class="pwe-gf-tool__eyebrow">Gravity Forms</span><h2>Kody QR</h2><p>Przeglądaj, wyszukuj i pobieraj kody QR wygenerowane dla poszczególnych wpisów formularzy.</p></div>
    </div>
    ' . pwe_forms_render_nav($group ?? $mode) . '';
$output .= pwe_forms_qr_view($forms);
$output .= '</div>';

return $output;
