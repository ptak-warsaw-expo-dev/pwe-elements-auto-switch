<?php

$trade_fair_edition = do_shortcode('[trade_fair_edition]');

if (trim($trade_fair_edition) === "Premierowa" || trim($trade_fair_edition) === "Premier") {
    $output = '
    <div id="pweMedals" class="pwe-medals premiere-edition">
        <div class="pwe-medals__wrapper">

            <h4 class="pwe-medals__heading pwe-main-title">'. PWE_Functions::multi_translation("award") .'</h4>
            <div class="pwe-medals__text">'. PWE_Functions::multi_translation("text_premier") .'</div>
            <div class="pwe-medals__text">'. PWE_Functions::multi_translation("category_premier") .'</div>
            <div class="pwe-medals__items-container">
                <div class="pwe-medals__item">
                    <img src="'. PWE_Functions::languageChecker('/wp-content/plugins/pwe-media/media/medals/premier-fair.webp', '/wp-content/plugins/pwe-media/media/medals/premier-fair-en.webp') .'"/>
                </div>
                <div class="pwe-medals__items-text">
                    <p>'. PWE_Functions::multi_translation("partner_premier") .'</p>
                    <p>'. PWE_Functions::multi_translation("cocreator_premier") .'</p>
                </div>
            </div>';

            $output .= '
            <div class="pwe-medals__button">
                <a class="pwe-main-btn--black" href="'. PWE_Functions::multi_translation("book_stand") .'">'. PWE_Functions::multi_translation("book_stand_button") .'</a>
            </div>

        </div>
    </div>';
} else {
    $output = '
    <div id="pweMedals" class="pwe-medals other-edition">
        <div class="pwe-medals__wrapper">
            <div class="pwe-medals__heading">
                <h4>'. PWE_Functions::multi_translation("award") .'</h4>
            </div>
            <div class="pwe-medals__text">'. PWE_Functions::multi_translation("text") .'</div>
            <div class="pwe-medals__items">
                <div class="pwe-medals__item"><img src="/wp-content/plugins/pwe-media/media/medals/medale-2026.webp"/></div>
                <div class="pwe-medals__item"><img src="/wp-content/plugins/pwe-media/media/medals/medale-2026r.webp"/></div>
            </div>';

            $output .= '
            <div class="pwe-medals__heading">
                <h4>'. PWE_Functions::multi_translation("lider_text") .'</h4>
            </div>
            <div class="pwe-medals__button">
                <a class="pwe-button-link btn" href="'. PWE_Functions::multi_translation("medal_link") .'">'. PWE_Functions::multi_translation("medal_button") .'</a>
            </div>
        </div>
    </div>';
}       

return $output;
