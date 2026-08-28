<?php
$mode = $mode ?? 'forms';
$languages = $languages ?? [];
$forms = $forms ?? [];
$url = $url ?? '';
$access = $access ?? '';

$form_data = [];
foreach ($forms as $form) {
    $form_id = (int) $form['id'];
    $total_count = GFAPI::count_entries($form_id, ['status' => 'active']);
    $total_count = is_wp_error($total_count) ? 0 : (int) $total_count;
    $language_counts = $languages[$form_id] ?? [];
    $ordered_language_counts = [];

    foreach (['pl', 'en'] as $preferred_code) {
        foreach ($language_counts as $code => $meta) {
            if (strtolower((string) $code) === $preferred_code) {
                $ordered_language_counts[$code] = $meta;
            }
        }
    }

    foreach ($language_counts as $code => $meta) {
        if (!in_array(strtolower((string) $code), ['pl', 'en'], true)) {
            $ordered_language_counts[$code] = $meta;
        }
    }

    $form_data[] = [
        'id' => $form_id,
        'title' => (string) $form['title'],
        'total_count' => $total_count,
        'languages' => $ordered_language_counts,
    ];
}

$output = '
<div id="pweGFTool" class="pwe-gf-tool">
    <div class="pwe-gf-tool__header">
        <div><span class="pwe-gf-tool__eyebrow">Gravity Forms</span><h2>Panel eksportu danych</h2><p>Wybierz formularz i pobierz dane z podziałem na języki</p></div>
    </div>
    ' . pwe_forms_render_nav($group ?? $mode) . '
    <div class="pwe-gf-select">
        <label class="pwe-gf-select__label" for="pwe-gf-form-select">Id - Formularz → (Ilość pozycji)</label>
        <select id="pwe-gf-form-select" class="pwe-gf-select__control">
            <option value="">Wybierz formularz</option>';

foreach ($form_data as $item) {
    $option_label = sprintf('%d - %s → (%d)', $item['id'], $item['title'], $item['total_count']);
    $output .= '<option value="' . esc_attr((string) $item['id']) . '">' . esc_html($option_label) . '</option>';
}

$output .= '
        </select>
        <div class="pwe-gf-select__results" aria-live="polite">';

foreach ($form_data as $item) {
    $form_id = $item['id'];
    $output .= '
            <div class="pwe-gf-select__panel" data-form-id="' . esc_attr((string) $form_id) . '" hidden>
                <form action="' . esc_url($url) . '" method="post" class="pwe-gf-select__download-form pwe-gf-select__download-form--all">
                    <input type="hidden" name="action" value="' . esc_attr(PWE_FORMS_DOWNLOAD_ACTION) . '">
                    <input type="hidden" name="export_type" value="form_csv">
                    <input type="hidden" name="form_id" value="' . esc_attr((string) $form_id) . '">
                    <input type="hidden" name="export_lang" value="">
                    ' . $access . '
                    <button type="submit" class="pwe-gf-select__button pwe-gf-select__button--all">Pobierz cały formularz <span aria-hidden="true">↓</span></button>
                </form>
                <div class="pwe-gf-select__languages">';

    foreach ($item['languages'] as $code => $meta) {
        $label = strtoupper((string) $code);
        $lang_count = (int) ($meta['count'] ?? 0);
        $output .= '
                    <form action="' . esc_url($url) . '" method="post" class="pwe-gf-select__download-form">
                        <input type="hidden" name="action" value="' . esc_attr(PWE_FORMS_DOWNLOAD_ACTION) . '">
                        <input type="hidden" name="export_type" value="form_csv">
                        <input type="hidden" name="form_id" value="' . esc_attr((string) $form_id) . '">
                        <input type="hidden" name="export_lang" value="' . esc_attr((string) $code) . '">
                        ' . $access . '
                        <button type="submit" class="pwe-gf-select__button" title="Pobierz ' . esc_attr($label) . '">
                            <strong>' . esc_html($label) . '</strong>
                            <span class="pwe-gf-select__count">(' . esc_html((string) $lang_count) . ')</span>
                            <span aria-hidden="true">↓</span>
                        </button>
                    </form>';
    }

    if (!$item['languages']) {
        $output .= '<p class="pwe-gf-select__empty">Ten formularz nie ma danych językowych.</p>';
    }

    $output .= '
                </div>
            </div>';
}

$output .= '
        </div>
    </div>
</div>';

return $output;
