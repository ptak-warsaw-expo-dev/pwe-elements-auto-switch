<?php

$output = '
<div id="pweExhibitors" class="pwe-exhibitors">
    <div class="pwe-exhibitors__wrapper">
        <div class="pwe-exhibitors__text">
            <div class="pwe-exhibitors__text-wrapper">
                <h4 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Wystawcy na targach', 'Exhibitors at the fair') .'</h4>
                <p>Zobacz wszystkich wystawców '. (ceil(count($exhibitors) / 100) * 100) .'+</p>
                <a class="pwe-main-btn--secondary" href="'. PWECommonFunctions::languageChecker('/katalog-wystawcow/', '/en/exhibitors-catalog/') .'">'. PWECommonFunctions::languageChecker('Pełen katalog', 'Full catalog') .'</a>
            </div>
        </div>
        <div class="pwe-exhibitors__items swiper">
            <div class="swiper-wrapper">';

            foreach ($exhibitors as $exhibitor) {
                $output .= '
                <div class="pwe-exhibitors__item swiper-slide">
                    <img src="'. $exhibitor["URL_logo_wystawcy"] .'">
                </div>';
            }

            $output .= '
            </div>

            <div class="swiper-scrollbar"></div>
        </div>
    </div>
</div>';    

$output .= PWE_Swiper::swiperScripts('#pweExhibitors', [0   => ['slidesPerView' => 3], 570 => ['slidesPerView' => 4],960 => ['slidesPerView' => 4],], true);

return $output;
