<?php
$desc_length = mb_strlen($trade_fair_desc, 'UTF-8');

$output = '';

$output .= '<style>';
if ($desc_length < 30) {
    $output .= '
        @media(min-width:961px) {
            .pwe-hero__title { font-size: clamp(56px, 6vw, 94px) !important; }
        }';
} else if ($desc_length >= 30 && $desc_length < 60) {
    $output .= '
        @media(min-width:961px) {
            .pwe-hero__title { font-size: clamp(42px, 4.5vw, 64px) !important; }
        }';
} else {
    $output .= '
        @media(min-width:961px) {
            .pwe-hero__title { font-size: clamp(32px, 3.5vw, 76px) !important; }
        }';
}
$output .= '</style>';

$selected_lang = PWE_Functions::lang();
$desc = do_shortcode('[pwe_about_desc_'. $selected_lang .']');

$output .= '
<div class="pwe-hero" id="start">
    <div class="pwe-hero__container">
        <div class="pwe-hero__content">

            <h1 class="pwe-hero__title">' . $trade_fair_name . ' <br/><span class="pwe-hero__title-gradient">' . $trade_fair_edition . '</span></h1>

            <p class="pwe-hero__lead">' . $trade_fair_desc . ' </p>

            <div class="pwe-hero__desc">' . $desc .'</div>

            <div class="pwe-hero__details">
                <span class="pwe-hero__detail">
                    <span class="pwe-hero__icon">▦</span>' . $trade_fair_date . '
                </span>
                <span class="pwe-hero__detail">
                    <span class="pwe-hero__icon">⌖</span>' . PWE_Functions::multi_translation('warsaw_poland') . '
                </span>
            </div>

            <div class="pwe-hero__buttons">
                <a class="pwe-hero__btn" href="' . PWE_Functions::multi_translation('link_to_registration') . '" title="' . PWE_Functions::multi_translation('link_to_registration_text') . '">
                    ' .PWE_Functions::multi_translation('register') . '
                </a>

                <a class="pwe-hero__btn pwe-hero__btn--secondary" href="' . PWE_Functions::multi_translation('link_to_registration_exh') . '" title="' . PWE_Functions::multi_translation('link_to_registration_text_exh') . '">
                    ' . PWE_Functions::multi_translation('become_exhibitor') . '
                </a>
            </div>

        </div>
    </div>
</div>';

return $output;