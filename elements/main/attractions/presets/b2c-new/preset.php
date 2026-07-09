<?php

$output = '
<section id="pweAttractions" class="pwe-element-auto-switch pwe-attractions-section">
    <div class="pwe-attractions-container">

        <div class="pwe-attractions__header">
            <div class="pwe-attractions__header-title">
                <span class="pwe-subtitle">' . PWE_Functions::multi_translation("attractions_subtitle") . ' </span>
                <h2 class="pwe-title">' . PWE_Functions::multi_translation("attractions_title") . '</h2>
            </div>

            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </div>
                <div class="swiper-button-next">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </div>
        </div>

        <div class="pwe-attractions__slider swiper" role="group" aria-roledescription="carousel" aria-live="polite">
            <div class="swiper-wrapper">';

                foreach ($slides as $slide) {
                    $output .= '
                    <div class="swiper-slide pwe-attractions__card group">

                        <div class="pwe-attractions__image-wrapper">
                            <img src="'.esc_url($slide['img']).'" alt="'.esc_attr($slide['name']).'" class="pwe-attractions__image">
                            <div class="pwe-attractions__gradient"></div>
                        </div>



                        <div class="pwe-attractions__card-body">
                            <h3 class="pwe-attractions__card-title">'.$slide['name'].'</h3>
                            <p class="pwe-attractions__card-desc">'.$slide['desc'].'</p>
                        </div>

                    </div>';
                }

            $output .= '
            </div>
        </div>

        <div class="swiper-nav">
            <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
        </div>

    </div>
</section>';

$output .= PWE_Swiper::swiperScripts('#pweAttractions', [0 => ['slidesPerView' => 1, 'spaceBetween' => 16], 650 => ['slidesPerView' => 2, 'spaceBetween' => 24], 1024 => ['slidesPerView' => 3, 'spaceBetween' => 24]], true, true, 1, false);

return $output;