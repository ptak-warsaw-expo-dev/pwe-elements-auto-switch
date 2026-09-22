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
                    <span class="pwe-hero__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 19H5V8H19M16 1V3H8V1H6V3H5C3.89 3 3 3.89 3 5V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19V5C21 4.46957 20.7893 3.96086 20.4142 3.58579C20.0391 3.21071 19.5304 3 19 3H18V1M17 12H12V17H17V12Z" fill="var(--accent-color)"/>
                        </svg>
                    </span>' . $trade_fair_date . '
                </span>
                <span class="pwe-hero__detail">
                    <span class="pwe-hero__icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.67206 4.09515C7.35381 2.43802 9.61843 1.50661 11.9794 1.501C14.3404 1.49539 16.6095 2.41603 18.2991 4.06516H18.3011L18.3331 4.09515C21.8781 7.58215 21.8851 13.1832 18.3751 16.6352L12.7041 22.2132C12.517 22.3973 12.265 22.5005 12.0026 22.5005C11.7401 22.5005 11.4881 22.3973 11.3011 22.2132L5.63006 16.6352C4.79749 15.8212 4.13595 14.849 3.6843 13.7758C3.23266 12.7026 3 11.55 3 10.3857C3 9.22129 3.23266 8.06867 3.6843 6.99547C4.13595 5.92227 4.79749 4.95014 5.63006 4.13616L5.67206 4.09515ZM12.0001 6.50015C11.6061 6.50015 11.216 6.57775 10.852 6.72852C10.488 6.87928 10.1573 7.10026 9.87874 7.37883C9.60016 7.65741 9.37919 7.98813 9.22842 8.3521C9.07766 8.71608 9.00006 9.10619 9.00006 9.50015C9.00006 9.89412 9.07766 10.2842 9.22842 10.6482C9.37919 11.0122 9.60016 11.3429 9.87874 11.6215C10.1573 11.9001 10.488 12.121 10.852 12.2718C11.216 12.4226 11.6061 12.5002 12.0001 12.5002C12.7957 12.5002 13.5588 12.1841 14.1214 11.6215C14.684 11.0589 15.0001 10.2958 15.0001 9.50015C15.0001 8.70451 14.684 7.94144 14.1214 7.37883C13.5588 6.81623 12.7957 6.50015 12.0001 6.50015Z" fill="var(--accent-color)"/>
                        </svg>
                    </span>' . PWE_Functions::multi_translation('warsaw_poland') . '
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