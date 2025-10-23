<?php

$output = '';

// Styl
$output .= '<style>
    .pwe-conference__wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        max-width: 1200px;
        margin: 0 auto !important;
        align-items: stretch;
        gap: 18px;
    }
    .pwe-conference__left {
        position: relative;
        flex: 1;
        width: 50%;
        padding: 0;
        display: flex;
        align-items: end;
        justify-content: end;
        gap: 18px;
        border-radius: 30px;
        padding: 36px;
    }
    .pwe-conference__left:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        background: linear-gradient(to top, var(--accent-color) 0%, rgba(0, 0, 0, 0) 50%);
        border-radius: 30px;
    }
    .pwe-conference__right {
        flex: 1;
        width: 50%;
        max-width: 560px;
        padding: 24px 24px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 18px;
        border: 1px solid black;
        border-radius: 30px;
    }
    .pwe-conference__right-content {
        height: 90%;
        width: 100%;
    }
    .pwe-conference__right-content-main {
        background: var(--background-color);
        padding: 36px;
        border-radius: 18px 18px 0 0;
    }
    .pwe-conference__title {
        font-size: clamp(1rem, 9vw, 8rem);
        text-align: center;
        font-weight: 900;
        line-height: 1;
        white-space: nowrap;
        width: 100%;
        overflow: hidden;
        margin-top: 0px;
        color: var(--accent-color);
        opacity: .5;
        text-align: center;
        text-transform: uppercase;
    }
    .pwe-conference__name {
        margin-bottom: 16px !important;
    }
    .pwe-conference__logo {
        margin-bottom: 20px;
    }
    .pwe-conference__logo img {
        max-width: 50%;
        display: block;
    }
    .pwe-conference__partners {
        margin-top: 18px;
    }
    .pwe-conference .swiper-buttons-arrows {
        height: 45px;
        margin: 18px auto;
        justify-content: center;
    }
    .pwe-conference__buttons {
        display: flex;
        justify-content: center;
        width: 100%;
        justify-content: start;
        gap: 10px;
    }
    .pwe-conference__buttons .pwe-btn-container {
        position: relative;
    }
    .pwe-conference__buttons .pwe-btn {
        display: flex;
        color: white !important;
        transform: scale(1);
        transition: .3s ease;
        font-size: 16px;
        align-items: flex-end;
        border-radius: 10px;
        padding: 30px 60px 18px 18px;
        font-weight: 600;
        gap: 10px;
        min-width: auto;
    }
    .pwe-conference__buttons .pwe-btn.btn-visitors {
        background: var(--main2-color);
        max-width: 140px !important;
        min-width: 140px !important;
    }
    .pwe-conference__buttons .pwe-btn.btn-more {
        background: var(--background-color);
        color: var(--accent-color) !important;
        max-width: 170px !important;
        min-width: 170px !important;
    }
    .pwe-conference__buttons .pwe-btn-container .btn-angle-right {
        position: absolute;
        right: 36px;
        transition: .3s ease;
        transform: rotate(-45deg);
    }
    .pwe-conference__buttons .pwe-btn-container:hover .btn-angle-right {
        right: 20px;
    }

    .pwe-conference__logo-pwe {
        max-width: 100px;
        z-index: 1;  
    }

    @media (max-width: 768px) {
        .pwe-conference__wrapper {
            flex-direction: column;
        }
        .pwe-conference__left, 
        .pwe-conference__right {
            flex: 1 1 100% !important;
            width: 100% !important;
        }
    }
</style>';

$output .= '
<div id="pweConference" class="pwe-conference">
    <div class="pwe-conference__wrapper">

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
