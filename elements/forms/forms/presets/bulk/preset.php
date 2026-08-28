<?php
$mode = $mode ?? 'bulk';
$languages = $languages ?? [];
$forms = $forms ?? [];
$url = $url ?? '';
$access = $access ?? '';
$preselected_form_ids = $preselected_form_ids ?? [];
$preselected = array_map('absint', $preselected_form_ids ?? []);

$output = '
<div id="pweGFTool" class="pwe-gf-tool">
    <div class="pwe-gf-tool__header">
        <div><span class="pwe-gf-tool__eyebrow">Gravity Forms</span><h2>Eksport JSON</h2><p>Zaznacz formularze i pobierz ich wpisy w formacie JSON, podzielone na segmenty VIP Gold, Klaviyo i gości.</p></div>
    </div>
    ' . pwe_forms_render_nav($group ?? $mode) . '';
$output .= '<form class="pwe-gf-tool__card" action="' . esc_url($url) . '" method="post">'
    . '<input type="hidden" name="action" value="' . esc_attr(PWE_FORMS_DOWNLOAD_ACTION) . '"><input type="hidden" name="export_type" value="forms_json">' . $access
    . '<label><strong>Formularze do eksportu JSON</strong><span>Wpisy zostaną podzielone na VIP Gold, Klaviyo i gości.</span></label>'
    . '<div class="pwe-gf-tool__checks">';
foreach ($forms as $form) {
    $is_checked = checked(in_array((int) $form['id'], $preselected, true), true, false);
    $output .= '<label><input type="checkbox" name="form_ids[]" value="' . (int) $form['id'] . '" ' . $is_checked . '><span>' . esc_html($form['title']) . '</span></label>';
}
$output .= '</div><button class="pwe-gf-tool__button" type="submit">Pobierz paczkę JSON</button></form>';
$output .= '<details class="pwe-gf-tool__danger" style="border-color:var(--line)">'
    . '<summary style="color:var(--ink)">Eksport według dokładnej nazwy formularza</summary>'
    . '<form action="' . esc_url($url) . '" method="post">'
    . '<input type="hidden" name="action" value="' . esc_attr(PWE_FORMS_DOWNLOAD_ACTION) . '"><input type="hidden" name="export_type" value="form_name_json">' . $access
    . '<input type="text" name="form_name" required placeholder="Pełna nazwa formularza" style="width:100%;padding:12px 13px;border:1px solid #ccd3df;border-radius:10px">'
    . '<button class="pwe-gf-tool__button" type="submit" style="margin-top:0">Pobierz JSON</button>'
    . '</form>'
    . '</details>';
$output .= '</div>';

return $output;
