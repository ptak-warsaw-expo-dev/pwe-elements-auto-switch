<?php

$trade_fair_dates_custom_format = str_replace("|", " | ", $trade_fair_dates_custom_format);

$output = '
<div id="pweHeader" class="pwe-header" style="background-image: url(/doc/background.webp);">
    <div class="pwe-header__container pwe-header__background">
        
        <div class="pwe-header__wrapper">

            <div class="pwe-header__column pwe-header__content-column">
                <div class="pwe-header__content-wrapper">
                    <div class="pwe-header__tile">
                        <div class="pwe-header__main-content-block">
                            <img class="pwe-header__logo" src="'. PWECommonFunctions::languageChecker('/doc/logo-color.webp', '/doc/logo-color-en.webp') .'" alt="logo-'. $trade_fair_name .'">
                            <div class="pwe-header__edition"><p><span>'. $trade_fair_edition .'</span></p></div>
                            <div class="pwe-header__title">
                                <h1>'. $trade_fair_desc .'</h1>
                            </div>
                        </div>
                        <div class="pwe-header__date-block">
                            <i class="fa fa-location-outline fa-2x fa-fw"></i>
                            <h2>'. $trade_fair_dates_custom_format . PWECommonFunctions::languageChecker(' Warszawa', ' Warsaw') .'</h2>
                            <p></p>
                        </div>
                        <div id="pweBtnRegistration" class="pwe-btn-container header-button">
                            <a 
                                class="pwe-link pwe-btn" 
                                href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                                alt="'. PWECommonFunctions::languageChecker('link do rejestracji', 'link to registration') .'">
                                '. PWECommonFunctions::languageChecker('Zarejestruj się', 'Register') .'
                            </a>
                        </div>
                    </div>
                </div>
            </div>';

            // Partners widget --------------------------------------------------------------------------------------<
            $cap_logotypes_data = PWECommonFunctions::get_database_logotypes_data();
            if (!empty($cap_logotypes_data)) { 
                require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'widgets/partners.php';
            }
            
            $output .= '

        </div>

        <div class="video-background">
            <div class="video-overlay"></div>
            <video autoplay="" muted="" loop="" preload="auto" class="bg-video" src="/doc/header.mp4"></video>
        </div>
    </div>
</div>';

return $output;
