<?php

$output = '
<div id="pweStep2" class="pwe-step2">
    <div class="pwe-step2__wrapper">
        <div class="pwe-step2__column pwe-step2__column--left">
            <h5 class="pwe-step2__subtitle">'. PWE_Functions::multi_translation("subtitle") .'</h5>
            <h2 class="pwe-step2__title">'. PWE_Functions::multi_translation("title") .'</h2>
            <p class="pwe-step2__description">'. PWE_Functions::multi_translation("description") .'</p>
            <h3 class="pwe-step2__exhibitor-title">'. PWE_Functions::multi_translation("exhibitor_title") .'</h3>
            <div class="pwe-step2__btn-container">
                <a href="[url_potwierdzenie_rejestracji_wystawcy]">
                    <button type="submit" class="btn exhibitor-yes" name="exhibitor-yes">'. PWE_Functions::multi_translation("exhibitor_yes_text") .'</button>
                </a>
                <a href="' . $link . '">
                    <button type="submit" class="btn exhibitor-no" name="exhibitor-no">' . PWE_Functions::multi_translation("exhibitor_no_text") . '</button>
                </a>
            </div>
        </div>
        <div class="pwe-step2__column pwe-step2__column--right">
            <img src="' . $logo_src . '" alt="Fair Logo" class="pwe-step2__logo">
            <h4 class="pwe-step2__date">[trade_fair_date_multilang]</h4>
            <h6>w Ptak Warsaw Expo</h6>
        </div>
    </div>
    <div class="pwe-step2__footer">
        <div class="pwe-step2__logos">
            <div class="pwe-step2__pwe-logo">
                <a href="https://warsawexpo.eu/" target="_blanc"><img src="/wp-content/plugins/pwe-media/media/logo_pwe_black.webp"></a>
            </div>
            <div class="pwe-step2__fair-logo">
                <a href="'. home_url('/') .'"><img src="' . $logo_src . '"></a>
            </div>
        </div>
        <div class="pwe-step2__numbers">
            <div class="pwe-step2__for-exhibitors">
                <i class="fa fa-envelope-o fa-3x fa-fw"></i>
                <p>'. PWE_Functions::multi_translation("become_an_exhibitor") .'<br> <a href="mailto:[trade_fair_contact]">[trade_fair_contact]</a>

            </div>
            <div class="pwe-step2__for-visitors">
                <i class="fa fa-phone fa-3x fa-fw"></i>
                <p>'. PWE_Functions::multi_translation("visitors") .'<br> <a href="tel:48 518 739 124">+48 518 739 124</a>
            </div>
        </div>
    </div>
</div>';

return $output;