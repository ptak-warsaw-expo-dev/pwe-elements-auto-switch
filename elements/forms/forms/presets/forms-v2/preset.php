<?php
$mode = $mode ?? 'forms';
$languages = $languages ?? [];
$forms = $forms ?? [];
$url = $url ?? '';
$access = $access ?? '';

$output = '
<div id="pweGFTool" class="pwe-gf-tool">
    <div class="pwe-gf-tool__header">
        <div><span class="pwe-gf-tool__eyebrow">Gravity Forms</span><h2>Panel eksportu danych</h2><p>Wybierz formularz i pobierz dane z podziałem na języki</p></div>
    </div>
    ' . pwe_forms_render_nav($group ?? $mode) . '
    <div class="pwe-gf-tool__form-list">
        <div class="pwe-gf-tool__form-search">
            <input id="pwe-gf-tool__form-search" type="search" placeholder="Szukaj po nazwie lub ID formularza" aria-label="Szukaj po nazwie lub ID formularza">
        </div>

        <div class="pwe-gf-tool__form-filters" aria-label="Filtr formularzy">
            <button type="button" class="pwe-gf-tool__form-filter is-selected" data-form-filter="all"><span>Wszystkie</span></button>
            <button type="button" class="pwe-gf-tool__form-filter" data-form-filter="active"><span>Aktywne</span></button>
            <button type="button" class="pwe-gf-tool__form-filter" data-form-filter="inactive"><span>Nieaktywne</span></button>
        </div>';

    foreach ($forms as $form) {
        $form_id = (int) $form['id'];
        $form_status = !empty($form['is_active']) ? 'active' : 'inactive';
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
        $language_counts = $ordered_language_counts;
        $search_value = strtolower((string) $form['title'] . ' ' . $form_id);

        $badges_output = '<form action="' . esc_url($url) . '" method="post" class="pwe-gf-tool__inline-form pwe-gf-tool__language-form">'
            . '<input type="hidden" name="action" value="' . esc_attr(PWE_FORMS_DOWNLOAD_ACTION) . '">'
            . '<input type="hidden" name="export_type" value="form_csv">'
            . '<input type="hidden" name="form_id" value="' . $form_id . '">'
            . '<input type="hidden" name="export_lang" value="">'
            . $access
            . '<button type="submit" class="pwe-language-badge" title="Pobierz całość formularza">'
            . '<strong class="pwe-language-badge__code">ALL</strong>'
            . '<span class="pwe-language-badge__divider"></span>'
            . '<span class="pwe-language-badge__name">' . esc_html((string) $total_count) . '</span>'
            . '</button></form>';

        foreach ($language_counts as $code => $meta) {
            $lang_count = (int) ($meta['count'] ?? 0);
            $label = strtoupper((string) $code);
            $flag_url = isset($meta['flag']) && $meta['flag'] !== '' ? esc_url($meta['flag']) : '';

            $badge_flag = $flag_url !== '' ? '<img src="' . $flag_url . '" alt="" class="pwe-language-badge__flag">' : '';
            $badges_output .= '<form action="' . esc_url($url) . '" method="post" class="pwe-gf-tool__inline-form pwe-gf-tool__language-form">'
                . '<input type="hidden" name="action" value="' . esc_attr(PWE_FORMS_DOWNLOAD_ACTION) . '">'
                . '<input type="hidden" name="export_type" value="form_csv">'
                . '<input type="hidden" name="form_id" value="' . $form_id . '">'
                . '<input type="hidden" name="export_lang" value="' . esc_attr((string) $code) . '">'
                . $access
                . '<button type="submit" class="pwe-language-badge" title="Pobierz ' . esc_attr($label) . '">'
                . $badge_flag
                . '<strong class="pwe-language-badge__code">' . esc_html($label) . '</strong>'
                . '<span class="pwe-language-badge__divider"></span>'
                . '<span class="pwe-language-badge__name">' . esc_html((string) $lang_count) . '</span>'
                . '</button></form>';
        }

        $output .= '
        <div class="pwe-gf-tool__form-row" data-search="' . esc_attr($search_value) . '" data-form-status="' . esc_attr($form_status) . '" tabindex="0" role="button" aria-expanded="false" aria-controls="pwe-gf-tool__form-panel-' . $form_id . '">
            <div class="pwe-gf-tool__form-main">
                <div class="pwe-gf-tool__form-header">
                    <span class="pwe-gf-tool__form-id">' . $form_id . '</span>
                    <div class="pwe-gf-tool__form-content"><h3>' . esc_html($form['title']) . '</h3></div>
                    <div class="pwe-gf-tool__form-meta" aria-hidden="true">
                        <div class="pwe-gf-tool__form-count--badge"><svg version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <style type="text/css"> .st0{fill:#000000;} </style> <g> <path class="st0" d="M157.604,321.598c7.26-2.232,10.041-6.696,10.6-10.046c-0.559-4.469-3.143-6.279-3.986-14.404 c-0.986-9.457,6.91-32.082,9.258-36.119c-0.32-0.772-0.65-1.454-0.965-2.247c-11.002-6.98-22.209-19.602-27.359-42.416 c-2.754-12.197-0.476-24.661,6.121-35.287c0,0-7.463-52.071,3.047-86.079c-9.818-4.726-20.51-3.93-35.164-2.466 c-11.246,1.126-12.842,3.516-21.48,2.263c-9.899-1.439-17.932-4.444-20.348-5.654c-1.392-0.694-14.449,10.89-18.084,20.35 c-11.531,29.967-8.435,50.512-5.5,66.057c-0.098,1.592-0.224,3.178-0.224,4.787l2.68,11.386c0.01,0.12,0,0.232,0.004,0.346 c-5.842,5.24-9.363,12.815-7.504,21.049c3.828,16.934,12.07,23.802,20.186,26.777c5.383,15.186,10.606,24.775,16.701,31.222 c1.541,7.027,2.902,16.57,1.916,26.032C83.389,336.78,0,315.904,0,385.481c0,9.112,25.951,23.978,88.818,28.259 c-0.184-1.342-0.31-2.695-0.31-4.078C88.508,347.268,129.068,330.379,157.604,321.598z"></path> <path class="st0" d="M424.5,297.148c-0.986-9.457,0.371-18.995,1.912-26.011c6.106-6.458,11.328-16.052,16.713-31.246 c8.113-2.977,16.35-9.848,20.174-26.774c1.77-7.796-1.293-15.006-6.59-20.2c3.838-12.864,18.93-72.468-26.398-84.556 c-15.074-18.839-28.258-18.087-50.871-15.827c-11.246,1.126-12.844,3.516-21.477,2.263c-1.89-0.275-3.682-0.618-5.41-0.984 c1.658,2.26,3.238,4.596,4.637,7.092c15.131,27.033,11.135,61.27,6.381,82.182c5.67,10.21,7.525,21.944,4.963,33.285 c-5.15,22.8-16.352,35.419-27.348,42.4c-0.551,1.383-2.172,4.214,0.06,7.006c2.039,3.305,2.404,2.99,4.627,5.338 c1.539,7.027,2.898,16.57,1.91,26.032c-0.812,7.85-14.352,14.404-10.533,17.576c3.756,1.581,8.113,3.234,13,5.028 c28.025,10.29,74.928,27.516,74.928,89.91c0,1.342-0.117,2.659-0.291,3.96C486.524,409.195,512,394.511,512,385.481 C512,315.904,428.613,336.78,424.5,297.148z"></path> <path class="st0" d="M301.004,307.957c-1.135-10.885,0.432-21.867,2.201-29.956c7.027-7.423,13.047-18.476,19.244-35.968 c9.34-3.427,18.826-11.335,23.23-30.826c2.028-8.976-1.494-17.276-7.586-23.256c4.412-14.81,21.785-83.437-30.398-97.353 c-17.354-21.692-32.539-20.825-58.57-18.222c-12.951,1.294-14.791,4.048-24.731,2.603c-11.4-1.657-20.646-5.117-23.428-6.508 c-1.602-0.803-16.637,12.538-20.826,23.428c-13.27,34.5-9.705,58.159-6.33,76.056c-0.111,1.833-0.264,3.658-0.264,5.511 l3.092,13.11c0.01,0.135,0,0.264,0.004,0.399c-6.726,6.03-10.777,14.752-8.636,24.232c4.402,19.498,13.894,27.404,23.238,30.828 c6.199,17.485,12.207,28.533,19.231,35.956c1.773,8.084,3.34,19.076,2.205,29.966c-4.738,45.626-100.744,21.593-100.744,101.706 c0,12.355,41.4,33.902,144.906,33.902c103.506,0,144.906-21.547,144.906-33.902C401.748,329.549,305.742,353.583,301.004,307.957z M240.039,430.304l-26.276-106.728l32.324,13.453l-1.738,15.619l5.135-0.112L240.039,430.304z M276.209,430.304l-9.447-77.768 l5.135,0.112l-1.738-15.619l32.324-13.453L276.209,430.304z"></path> </g> </g></svg></div>
                        <span class="pwe-gf-tool__form-count pwe-gf-tool__form-count--total">' . esc_html((string) $total_count) . '</span>
                        <div class="pwe-gf-tool__form-count--badge"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><circle fill="#88c9f9" cx="18" cy="18" r="18"/><path fill="#5c913b" d="M25.716 1.756c-1.022.568-1.872 1.528-3.028 1.181-1.875-.562-4.375-1.812-6-.25s-2 3 0 2.938 3.375-2.438 4.375-1.438.749 1.813-1.625 2.125S14.5 7 13.125 7s-1.688.812-.75 1.688-.563.937-2.125 1.812.375 1.25 1.688 2 2.312-.188 2.875-1.438 2.981-2.75 3.99-2.562 1.01.688.822 1.562.75.625.812-.375 1.188-1.75 2.062-1.812 1.625 1.188.625 1.812-2 1.125-.75 1.438 2.125 1.938.688 2.625-3.937 1.125-5.062.562-3.688-1.375-4.375-.938-1.062.89-1.875 1.195-4.125 1.805-4.188 3.743-.124 4.126 1.188 4.188 4.5-.812 5.5-1.625 2.375-.625 2.812.312.125 1.5-.312 3 .286 2.25.987 3.562 1.263 2.062 1.263 3 1 1.875 2.5.312 2.875-4.625 3.5-5.75 1.125-3.625 1.875-4.125 1.938-1.688 1.062-1.5-2.625-.062-3.062-1.312-2.312-3.625-1.438-3.875 1.875 1.39 2.25 2.164.875 1.711 1.625 1.961 2.375-1.673 2.875-1.961.125-1.476-.875-1.351-2.312 0-2.312-.624 1.25-1.438 2.25-1.25 1.75.5 2.375 1.25 1.875 2.125 2.375 3 .875 1 1.125-.562c.166-1.038.387-1.609.59-2.222-1.013-5.829-4.82-10.683-9.999-13.148"/></svg></div>
                        <span class="pwe-gf-tool__form-count pwe-gf-tool__form-count--langs">' . esc_html((string) count($language_counts)) . '</span>
                    </div>
                </div>

                <div class="pwe-gf-tool__form-panel" id="pwe-gf-tool__form-panel-' . $form_id . '" aria-hidden="true">
                    <div class="pwe-language-badges">' . $badges_output . '</div>
                </div>
            </div>
        </div>';
    }

$output .= '</div>
</div>';

return $output;