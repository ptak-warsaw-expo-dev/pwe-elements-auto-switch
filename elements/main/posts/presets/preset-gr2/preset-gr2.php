<?php

$word_count = 25;

// Get post content
$post_content = get_post_field('post_content', $post_id);

// Extract content inside [vc_column_text] shortcode
preg_match('/\[vc_column_text.*?\](.*?)\[\/vc_column_text\]/s', $post_content, $matches);
$vc_content = isset($matches[1]) ? $matches[1] : '';

// Remove HTML
$vc_content = wp_strip_all_tags($vc_content);

// Check if the content is not empty
if (!empty($vc_content)) {
    // Split content into words
    $words = explode(' ', $vc_content);

    // Extract the first $word_count words
    $excerpt = array_slice($words, 0, $word_count);

    // Combine words into one string
    $excerpt = implode(' ', $excerpt);

    // Add an ellipsis at the end
    $excerpt .= '...';
} else {
    $excerpt = '';
}
            
$output = '
<div id="pwePosts" class="pwe-posts">
    <div class="pwe-posts__wrapper">
        <h4 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Expo Aktualności', 'Expo News') .'</h4>
        
        <div class="pwe-posts__slider swiper" role="group" aria-roledescription="carousel" aria-live="polite">
            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev">⏴</div>
                <div class="swiper-button-next">⏵</div>
            </div>
            <div class="swiper-wrapper">';

                foreach ($pwe_posts['items'] as $item) {
                    $output .= '
                    <a class="pwe-posts__post swiper-slide" href="' . esc_url($item['link']) . '">
                        <div class="pwe-posts__post-thumbnail">
                            <div class="image-container" style="background-image:url(' . esc_url($item['img']) . ');"></div>
                        </div>
                        <div class="pwe-posts__post-content">
                            <h4 class="pwe-posts__post-title">' . esc_html($item['title']) . '</h4>
                            <div class="pwe-posts__post-excerpt">'. $excerpt .'</div>
                            <span class="pwe-posts__post-btn">'. PWECommonFunctions::languageChecker('Czytaj więcej', 'Read more') .' <span class="pwe-posts__post-arrow">🡲</span></span>
                        </div>
                    </a>';
                }

            $output .= '
            </div>
            <div class="swiper-nav">
                <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
            </div>
        </div>
    </div>
</div>';

$output .= PWE_Swiper::swiperScripts('#pwePosts', [0   => ['slidesPerView' => 1],650 => ['slidesPerView' => 2]], true, true, 1, false);

return $output;