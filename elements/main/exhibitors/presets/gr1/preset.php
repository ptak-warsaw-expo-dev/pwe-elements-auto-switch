<?php

$output = '
<div id="pweExhibitors" class="pwe-exhibitors">
    <div class="pwe-exhibitors__wrapper">
        <div class="pwe-exhibitors__text">
            <div class="pwe-exhibitors__text-wrapper">
                <h4 class="pwe-main-title">'. PWE_Functions::multi_translation("catalog_title") .'</h4>
                <p>'. PWE_Functions::multi_translation("catalog_desc") .' '. (ceil(count($exhibitors) / 100) * 100) .'+</p>
                <a class="pwe-main-btn--secondary" href="'. PWE_Functions::multi_translation("catalog_link") .'">'. PWE_Functions::multi_translation("catalog_link_text") .'</a>';
                if ($active_current_fair_plan) {
                    $output .= '
                    <a class="pwe-main-btn--secondary" href="'. (PWE_Functions::lang_pl() ? '/plan-targow/' : '/en/fair-plan/') .'">'. PWE_Functions::multi_translation('fair_plan') .'</a>';
                }
                $output .= '
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

            <div class="swiper-scrollbar"></div>
        </div>
    </div>
</div>';    

$output .= PWE_Swiper::swiperScripts('#pweExhibitors', [0   => ['slidesPerView' => 2], 570 => ['slidesPerView' => 4],960 => ['slidesPerView' => 4],], true);

return $output;
