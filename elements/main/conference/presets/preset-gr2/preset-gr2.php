<?php

$output = '
<div id="pweConference" class="pwe-conference">
    <div class="pwe-conference__wrapper">

        <div class="pwe-conference__title">
            <h4 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Konferencja', 'Conference') .'</h4>
        </div>

        <div class="pwe-conference__left" style="background: url(/doc/new_template/conference_img.webp) center / cover no-repeat;">
            <div class="pwe-conference__buttons">
                <div class="pwe-btn-container header-button">
                    <a class="pwe-link pwe-btn btn-visitors" 
                        href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                        alt="'. PWECommonFunctions::languageChecker('link do rejestracji', 'link to registration') .'">
                            '. PWECommonFunctions::languageChecker('Weź udział', 'Take a part') .'
                            <span class="btn-angle-right">🡲</span>
                    </a>
                </div>
                <div class="pwe-btn-container header-button">
                    <a class="pwe-link pwe-btn btn-more" 
                        href="'. PWECommonFunctions::languageChecker('/wydarzenia/', '/en/conferences/') .'" 
                        alt="'. PWECommonFunctions::languageChecker('Konferencja', 'Conference') .'">
                            '. PWECommonFunctions::languageChecker('Dowiedz się więcej', 'Find out more') .' 
                            <span class="btn-angle-right">🡲</span>
                    </a>
                </div>
            </div>
            <div class="pwe-conference__logo-pwe">
                <img src="/wp-content/plugins/pwe-media/media/logo_pwe.webp" alt="Ptak Warsaw Expo Logo">
            </div>
        </div>

        <div class="pwe-conference__right">
            <div class="pwe-conference__right-content">
                <div class="pwe-conference__right-content-main">
                    <div class="pwe-conference__logo">
                        <img src="/doc/kongres-color.webp" alt="Congress logo">
                    </div>
                    <div class="pwe-conference__name pwe-main-subtitle">' . $name . '</div>
                    <div class="pwe-conference__desc pwe-main-desc">' . $desc . '</div>
                </div>';
                
                if (!empty($partners)) {
                    $output .= '
                    <div class="pwe-conference__partners">    
                        <div class="swiper">
                            <div class="swiper-wrapper">';

                                foreach ($partners as $logo) {
                                    $output .= '<div class="swiper-slide">';
                                    $output .= '<img id="' . pathinfo($logo)['filename'] . '" data-no-lazy="1" src="' . htmlspecialchars($logo, ENT_QUOTES, 'UTF-8') . '" alt="' . pathinfo($logo)['filename'] . '"/>';
                                    $output .= '</div>';
                                }

                            $output .= '
                            </div>
                        </div>
                        <div class="swiper-buttons-arrows">
                            <div class="swiper-button-prev">⏴</div>
                            <div class="swiper-button-next">⏵</div>
                        </div>
                    </div>';

                    $output .= PWE_Swiper::swiperScripts('#pweConference', [0   => ['slidesPerView' => 2],450   => ['slidesPerView' => 3]], false, true);
                }

            $output .= '
            </div>
        </div>

    </div>
</div>';

return $output;
