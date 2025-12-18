<?php

$trade_fair_datetotimer = do_shortcode('[trade_fair_datetotimer]');
$trade_fair_year = date('Y', strtotime($trade_fair_datetotimer));
$lang = PWECommonFunctions::lang_pl() ? 'pl' : 'en';
$year = in_array($trade_fair_year, ['2025', '2026']) ? $trade_fair_year : 'other';

$output = '
<div id="pweMedals" class="pwe-medals">
    <div class="pwe-medals__wrapper">
        <div class="pwe-medals__content">
            <p class="pwe-medals__sub-heading">Ptak Warsaw Expo</p>
            <h4 class="pwe-medals__heading pwe-main-title">Zdobądź prestizową nagrodę w Ptak Warsaw Expo!</h4>
            <div class="pwe-medals__content-desktop">
                <p class="pwe-medals__text">Dołącz do grona najlepszych na Targach Ptach Warsaw Expo i pokaż swoją firmę w świetle zwyciężców! nagrody są przyznawane przez Krajową Izbę Targową oraz Ptak Warsaw Expo - wyróżnij się i zdobądź uznanie!</p>
                <div class="pwe-btn-container">
                    <a class="pwe-link pwe-btn btn-visitors" 
                        href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                        alt="'. PWECommonFunctions::languageChecker('link do rejestracji', 'link to registration') .'">
                        '. PWECommonFunctions::languageChecker('Weź udział', 'Take a part') .'
                        <span class="btn-angle-right">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        <div class="pwe-medals__items">
            <div class="pwe-medals__item">
                <img src="/wp-content/plugins/pwe-media/media/medals/'. $year .'/'. $lang .'/innowacyjnosc.webp"/>
            </div>
            <div class="pwe-medals__item">
                <img src="/wp-content/plugins/pwe-media/media/medals/'. $year .'/'. $lang .'/produkt-targowy.webp"/>
            </div>
            <div class="pwe-medals__item">
                <img src="/wp-content/plugins/pwe-media/media/medals/'. $year .'/'. $lang .'/premiera-targowa.webp"/>
            </div>
            <div class="pwe-medals__item">
                <img src="/wp-content/plugins/pwe-media/media/medals/'. $year .'/'. $lang .'/ekspozycja-targowa.webp"/>
            </div>
        </div>
        <div class="pwe-medals__content-mobile">
            <p class="pwe-medals__text">Dołącz do grona najlepszych na Targach Ptach Warsaw Expo i pokaż swoją firmę w świetle zwyciężców! nagrody są przyznawane przez Krajową Izbę Targową oraz Ptak Warsaw Expo - wyróżnij się i zdobądź uznanie!</p>
            <div class="pwe-btn-container">
                <a class="pwe-link pwe-btn btn-visitors" 
                    href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                    alt="'. PWECommonFunctions::languageChecker('link do rejestracji', 'link to registration') .'">
                    '. PWECommonFunctions::languageChecker('Weź udział', 'Take a part') .'
                    <span class="btn-angle-right">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                        </svg>
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>';
            

return $output;
