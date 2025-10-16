<?php

$output = '';

$output = '
<div id="pweMedals" class="pwe-medals">
    <div class="pwe-medals__wrapper">
        <h4 class="pwe-medals__heading pwe-main-title">'.PWECommonFunctions::languageChecker('Zdobądź prestizową nagrodę <span style="display:inline-block"> w PTAK WARSAW EXPO!</span>', 'Win a prestigious award <span style="display:inline-block">at PTAK WARSAW EXPO!</span>') .'</h4>
        <div class="pwe-medals__text">'.
            PWECommonFunctions::languageChecker(
                <<<PL
                    <p>Dołącz do najlepszych na Targach Ptak Warsaw Expo i pokaż swoją firmę jako lidera! Zdobądź prestiżowy medal przyznawany przez Krajową Izbę Targową i Ptak Warsaw Expo.</p>
                PL,
                <<<EN
                    <p>Join the best at Ptak Warsaw Expo and showcase your company as a leader! Earn the prestigious medal awarded by the National Chamber of Exhibitions and Ptak Warsaw Expo.</p>
                EN
            )
        .'</div>
        <div class="pwe-medals__text">'.
            PWECommonFunctions::languageChecker(
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
                <img src="'. PWECommonFunctions::languageChecker('/wp-content/plugins/pwe-media/media/medals/premier-fair.webp', '/wp-content/plugins/pwe-media/media/medals/premier-fair-en.webp') .'"/>
            </div>
            <div class="pwe-medals__items-text">
                <p>'. PWECommonFunctions::languageChecker('Dla Izb i Stowarzyszeń:<br><strong>„Kluczowy Partner Targów”</strong>', 'For Chambers and Associations:<br><strong>“Key Partner of the Fair”</strong>') .'</p>
                <p>'. PWECommonFunctions::languageChecker('Dla Firm:<br><strong>„Współtwórca Sukcesu Targów”</strong>', 'For Companies:<br><strong>“Co-Creator of Fair Success”</strong>') .'</p>
            </div>
        </div>';

        $output .= '
        <div class="pwe-medals__button">
            <a class="pwe-main-btn--black" href="' . PWECommonFunctions::languageChecker('/zostan-wystawca/', '/en/become-an-exhibitor/') . '">'. PWECommonFunctions::languageChecker('Zostań wystawcą', 'Book a stand') .'</a>
        </div>

    </div>
</div>';
            

return $output;
