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
                            <span class="pwe-posts__post-btn">
                                '. PWECommonFunctions::languageChecker('Czytaj więcej', 'Read more') .' 
                                <span class="pwe-posts__post-arrow">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                                    </svg>
                                </span>
                            </span>
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