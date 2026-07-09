<?php

$output = '
<div id="pwePosts" class="pwe-posts news-section">
    <div class="pwe-posts__wrapper">

        <div class="pwe-posts__title news-header">
            <div class="news-header-left">
                <span class="news-subtitle">' . PWE_Functions::multi_translation("subtitle") . '</span>
                <h4 class="pwe-main-title news-title">
                    ' . PWE_Functions::multi_translation("title") . ' <span class="text-red">& Newsy</span>
                </h4>
            </div>

            <div class="swiper-buttons-arrows news-arrows">
                <div class="swiper-button-prev news-arrow-btn">
                    <svg class="news-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </div>
                <div class="swiper-button-next news-arrow-btn">
                    <svg class="news-arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </div>
        </div>

        <div class="pwe-posts__slider swiper" role="group" aria-roledescription="carousel" aria-live="polite">
            <div class="swiper-wrapper">';

                foreach ($pwe_posts['items'] as $item) {
                    $item_date = isset($item['date']) ? $item['date'] : date('d.m.Y');
                    $item_tag  = isset($item['tag']) ? $item['tag'] : 'NEWS';

                    $output .= '
                    <a class="pwe-posts__post swiper-slide news-card" href="' . esc_url($item['link']) . '">

                        <div class="pwe-posts__post-thumbnail news-thumb-wrapper">
                            <div class="image-container news-image" style="background-image:url(' . esc_url($item['img']) . ');"></div>
                            <div class="news-tag">' . esc_html($item_tag) . '</div>
                        </div>

                        <div class="pwe-posts__post-content news-content">
                            <div>
                                <div class="news-date-box">
                                    <svg class="news-clock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    ' . esc_html($item_date) . '
                                </div>

                                <h4 class="pwe-posts__post-title news-card-title">' . esc_html($item['title']) . '</h4>
                                <div class="pwe-posts__post-excerpt news-card-excerpt">' . esc_html($item['excerpt']) . '</div>
                            </div>

                            <span class="pwe-posts__post-btn news-card-btn">
                                ' . PWE_Functions::multi_translation("read_more") . '
                                <span class="pwe-posts__post-arrow news-btn-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1.5 6H10.5M10.5 6L5.5 1M10.5 6L5.5 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                            </span>
                        </div>
                    </a>';
                }

            $output .= '
            </div>
        </div>

        <div class="swiper-nav news-nav">
            <div class="swiper-dots news-dots" aria-label="Slider navigation" role="tablist"></div>
        </div>

    </div>
</div>';

$output .= PWE_Swiper::swiperScripts('#pwePosts', [
    0 => ['slidesPerView' => 1.2, 'centeredSlides' => true, 'spaceBetween' => 20],
    650 => ['slidesPerView' => 2, 'centeredSlides' => true, 'spaceBetween' => 30],
    1024 => ['slidesPerView' => 3, 'centeredSlides' => false, 'spaceBetween' => 40]
], true, true, 1, false);

return $output;
