<?php

$output = '
<div id="pweExhibitors" class="pwe-exhibitors">
    <div class="pwe-exhibitors__wrapper">
        <div class="pwe-exhibitors__title">
            <h4 class="pwe-main-title">Wystawcy 2025</h4>

            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev">⏴</div>
                <div class="swiper-button-next">⏵</div>
            </div>
        </div>
        <div class="pwe-exhibitors__items swiper">
            <div class="swiper-wrapper">';

            foreach ($exhibitors as $exhibitor) {
                $output .= '
                <div class="pwe-exhibitors__item swiper-slide">
                    <img name="'. $exhibitor["name"] .'" src="'. $exhibitor["logo"] .'" alt="'. $exhibitor["name"] .'">
                </div>';
            }

            $output .= '
            </div>
 
        </div>

        <div class="swiper-nav">
            <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
        </div>

    </div>
</div>';    

$output .= PWE_Swiper::swiperScripts('#pweExhibitors', [0   => ['slidesPerView' => 2],650   => ['slidesPerView' => 3],960   => ['slidesPerView' => 5]], true, true, 3);

return $output;
