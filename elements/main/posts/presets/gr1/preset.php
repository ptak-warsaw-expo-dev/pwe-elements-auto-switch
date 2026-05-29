<?php

$output = '';
            
$output .= '
<div id="pwePosts" class="pwe-posts">
    <div class="pwe-posts__wrapper">
        <h4 class="pwe-main-title">'. PWE_Functions::multi_translation("title") .'</h4>
        <div class="pwe-posts__slider swiper" role="group" aria-roledescription="carousel" aria-live="polite">
            <div class="swiper-wrapper">';

                foreach ($pwe_posts['items'] as $item) {
                    $output .= '
                    <a class="pwe-posts__post swiper-slide" href="' . esc_url($item['link']) . '">
                        <div class="pwe-posts__post-thumbnail">
                            <div class="image-container" style="background-image:url(' . esc_url($item['img']) . ');"></div>
                        </div>
                        <div class="pwe-posts__post-content">
                            <h4 class="pwe-posts__post-title">' . esc_html($item['title']) . '</h4>
                            <span class="pwe-posts__post-btn">'. PWE_Functions::multi_translation("more") .' <span class="pwe-posts__post-arrow">➜</span></span>
                        </div>
                    </a>';
                }

            $output .= '
            </div>
            <div class="swiper-scrollbar"></div>
        </div>
        <div class="pwe-posts__btn-container">
            <a class="pwe-posts__see-all pwe-main-btn--secondary" href="/'. PWE_Functions::languageChecker('aktualnosci', 'news') .'">'. PWE_Functions::multi_translation("read_all") .'</a>
        </div>
    </div>
</div>';

$output .= PWE_Swiper::swiperScripts('#pwePosts', [0   => ['slidesPerView' => 1], 570 => ['slidesPerView' => 2],960 => ['slidesPerView' => 3],], true);

return $output;