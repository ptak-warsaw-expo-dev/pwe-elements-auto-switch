<?php

$output = '<div id="pweFairPlan" class="pwe-fair-plan">';

if (!empty($plan_imgs)) {

    $output .= '<div class="pwe-fair-plan__gallery">';

    foreach ($plan_imgs as $img) {
        $output .= '
            <div class="pwe-fair-plan__item">
                <img
                    src="' . esc_url($img) . '"
                    alt="' . esc_attr__('Trade Fair Plan', 'pwe') . '"
                    loading="lazy"
                >
            </div>';
    }

    $output .= '</div>';
}

if (!empty($plan_pdf)) {
    $output .= '
        <div class="pwe-fair-plan__download">
            <a href="' . esc_url($plan_pdf) . '" class="pwe-fair-plan__btn" target="_blank">
                ' . PWE_Functions::multi_translation('download_btn_text') . '
            </a>
        </div>';
}

$output .= '</div>';

return $output;