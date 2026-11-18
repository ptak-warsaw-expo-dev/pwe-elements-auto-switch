<?php

$output = '
<div id="pweExhibitors" class="pwe-exhibitors">
    <div class="pwe-exhibitors__wrapper">
        <div class="pwe-exhibitors__text">
            <div class="pwe-exhibitors__text-wrapper">
                <h4 class="pwe-main-title">'. PWE_Functions::multi_translation("catalog_title") .'</h4>';
                
                if (do_shortcode('[pwe_catalog_numbers]') === 'on') {
                    $output .= '
                    <div class="pwe-exhibitors__stats">
                        <div class="pwe-exhibitors__stat">
                            <strong>'. number_format_i18n($exhibitors_count) .'</strong>
                            <span>'. esc_html(PWE_Functions::multi_translation("exhibitors_count_label")) .'</span>
                        </div>
                        <div class="pwe-exhibitors__stat">
                            <strong>'. number_format_i18n($brands_count) .'</strong>
                            <span>'. esc_html(PWE_Functions::multi_translation("brands_count_label")) .'</span>
                        </div>
                    </div>';
                } else {
                    $output .= '<p>'. PWE_Functions::multi_translation("catalog_desc") .' '. (ceil($exhibitors_count / 100) * 100) .'+</p>';
                }
            
                $output .= '
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