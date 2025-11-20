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
            <p class="pwe-medals__text">Dołącz do grona najlepszych na Targach Ptach Warsaw Expo i pokaż swoją firmę w świetle zwyciężców! nagrody są przyznawane przez Krajową Izbę Targową oraz Ptak Warsaw Expo - wyróżnij się i zdobądź uznanie!</p>
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

    </div>
</div>';
            

return $output;
