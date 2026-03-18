<?php

$output = '
<div id="pweSimpleHeader" class="pwe-simple-header">
    <div class="pwe-simple-header__container pwe-simple-header__background" style="background-image: url(/doc/background.webp);">
        
        <div class="pwe-simple-header__wrapper">
           
            <div class="pwe-simple-header__logo">
                <img src="'. (PWE_Functions::lang_pl() ? '/doc/logo.webp' : '/doc/logo-en.webp') .'" alt="logo-'. $trade_fair_name .'">
                <div class="pwe-simple-header__btn">
                    <a
                        class="pwe-link pwe-btn"
                        href="'. PWE_Functions::languageChecker('/rejestracja/', '/en/registration/') .'"
                        alt="'. PWE_Functions::languageChecker('rejestracja', 'registration') .'">
                        '. PWE_Functions::languageChecker('Weź udział', 'Take a part') .'
                    </a>
                </div>
            </div>

            <div class="pwe-simple-header__info">
                <h2>'. $trade_fair_date .'</h2>
                <h1>'. get_the_title() .'</h1>
            </div>

        </div>

    </div>
</div>';

return $output;
