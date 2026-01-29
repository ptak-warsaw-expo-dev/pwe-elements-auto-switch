<?php

$output = '
<div id="pweConference" class="pwe-conference"> 
    <div class="pwe-conference__wrapper">

        <div class="pwe-conference__title">
            <h4 class="pwe-main-title">'. PWE_Functions::languageChecker('Konferencja', 'Conference') .'</h4>
        </div>

        <div class="pwe-conference__left" style="background: url(/doc/new_template/conference_img.webp) center / cover no-repeat;">
            <div class="pwe-conference__buttons">
                <div class="pwe-btn-container">
                    <a class="pwe-link pwe-btn btn-visitors" 
                        href="'. PWE_Functions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                        alt="'. PWE_Functions::languageChecker('link do rejestracji', 'link to registration') .'">
                            '. PWE_Functions::languageChecker('Weź udział', 'Take a part') .'
                            <span class="btn-angle-right">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                                </svg>
                            </span>
                    </a>
                </div>
                <div class="pwe-btn-container"> 
                    <a class="pwe-link pwe-btn btn-more" 
                        href="'. PWE_Functions::languageChecker('/wydarzenia/', '/en/conferences/') .'" 
                        alt="'. PWE_Functions::languageChecker('Konferencja', 'Conference') .'">
                            '. PWE_Functions::languageChecker('Dowiedz się więcej', 'Find out more') .' 
                            <span class="btn-angle-right">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="var(--accent-color)"/>
                                </svg>
                            </span>
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
                        <h5>Partnerzy Konferencji:</h5> 
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

                    $output .= PWE_Swiper::swiperScripts('#pweConference', [0   => ['slidesPerView' => 2],450   => ['slidesPerView' => 3],650   => ['slidesPerView' => 5],1120   => ['slidesPerView' => 3]], false, true);
                }

            $output .= '
            </div>
        </div>

    </div>
</div>';

return $output;
