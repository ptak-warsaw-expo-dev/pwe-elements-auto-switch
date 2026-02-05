<?php

$trade_fair_edition = do_shortcode('[trade_fair_edition]');

if (trim($trade_fair_edition) === "Premierowa" || trim($trade_fair_edition) === "Premier") {
    $output = '
    <div id="pweMedals" class="pwe-medals premiere-edition">
        <div class="pwe-medals__wrapper">

            <h4 class="pwe-medals__heading pwe-main-title">'.PWE_Functions::languageChecker('Zdobądź prestizową nagrodę <span style="display:inline-block"> w PTAK WARSAW EXPO!</span>', 'Win a prestigious award <span style="display:inline-block">at PTAK WARSAW EXPO!</span>') .'</h4>
            <div class="pwe-medals__text">'.
                PWE_Functions::languageChecker(
                    <<<PL
                        <p>Dołącz do najlepszych na Targach Ptak Warsaw Expo i pokaż swoją firmę jako lidera! Zdobądź prestiżowy medal przyznawany przez Krajową Izbę Targową i Ptak Warsaw Expo.</p>
                    PL,
                    <<<EN
                        <p>Join the best at Ptak Warsaw Expo and showcase your company as a leader! Earn the prestigious medal awarded by the National Chamber of Exhibitions and Ptak Warsaw Expo.</p>
                    EN
                )
            .'</div>
            <div class="pwe-medals__text">'.
                PWE_Functions::languageChecker(
                    <<<PL
                        <p><strong>Honorujemy wyjątkowych partnerów w dwóch kategoriach:</strong></p>
                    PL,
                    <<<EN
                        <p><strong>We honor exceptional partners in two categories:</strong></p>
                    EN
                )
            .'</div>
            <div class="pwe-medals__items-container">
                <div class="pwe-medals__item">
                    <img src="'. PWE_Functions::languageChecker('/wp-content/plugins/pwe-media/media/medals/premier-fair.webp', '/wp-content/plugins/pwe-media/media/medals/premier-fair-en.webp') .'"/>
                </div>
                <div class="pwe-medals__items-text">
                    <p>'. PWE_Functions::languageChecker('Dla Izb i Stowarzyszeń:<br><strong>„Kluczowy Partner Targów”</strong>', 'For Chambers and Associations:<br><strong>“Key Partner of the Fair”</strong>') .'</p>
                    <p>'. PWE_Functions::languageChecker('Dla Firm:<br><strong>„Współtwórca Sukcesu Targów”</strong>', 'For Companies:<br><strong>“Co-Creator of Fair Success”</strong>') .'</p>
                </div>
            </div>';

            $output .= '
            <div class="pwe-medals__button">
                <a class="pwe-main-btn--black" href="' . PWE_Functions::languageChecker('/zostan-wystawca/', '/en/become-an-exhibitor/') . '">'. PWE_Functions::languageChecker('Zostań wystawcą', 'Book a stand') .'</a>
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
                <a class="pwe-button-link btn" href="'. PWE_Functions::multi_translation("book_stand") .'">'. PWE_Functions::multi_translation("book_stand_button") .'</a>
            </div>
        </div>
    </div>';
}       

return $output;
