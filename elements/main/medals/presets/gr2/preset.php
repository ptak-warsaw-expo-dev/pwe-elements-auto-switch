<?php

$output = '
<div id="pweMedals" class="pwe-medals">
    <div class="pwe-medals__hero">
        <div class="pwe-medals__hero-content">
            <h2 class="pwe-medals__hero-title">'. PWE_Functions::multi_translation("title") .'</h2>
            <p class="pwe-medals__hero-desc">'. PWE_Functions::multi_translation("desc") .'</p>
            <a class="pwe-medals__participate pwe-main-btn--secondary" href="' . (PWE_Functions::lang_pl() ? '/ceremonia-medalowa/' : '/en/medal-ceremony/') . '">'. PWE_Functions::multi_translation("participate") .'</a>
        </div>
        <div class="pwe-medals__hero-image-wrapper">
            <img src="/wp-content/plugins/pwe-media/media/medals/gr2/medal_img.webp" alt="Medale Ptak Warsaw Expo" class="pwe-medals__hero-image">
        </div>
    </div>
    <div class="pwe-medals__content">
        <span class="pwe-medals__content-subtitle">'. PWE_Functions::multi_translation("content_subtitle") .'</span>
        <h3 class="pwe-medals__content-title">'. PWE_Functions::multi_translation("content-title") .'</h3>
        <div class="pwe-medals__categories">
            <div class="pwe-medals__category">
                <div class="pwe-medals__category-icon-wrapper">
                    <img src="/wp-content/plugins/pwe-media/media/medals/gr1/rocket-icon.webp" alt="" class="pwe-medals__category-icon">
                </div>
                <h4 class="pwe-medals__category-title">'. PWE_Functions::multi_translation("category-title_1") .'</h4>
                <div class="pwe-medals__category-divider"></div>
                <p class="pwe-medals__category-desc">'. PWE_Functions::multi_translation("category-desc_1") .'</p>
            </div>
            <div class="pwe-medals__category">
                <div class="pwe-medals__category-icon-wrapper">
                    <img src="/wp-content/plugins/pwe-media/media/medals/gr1/light-icon.webp" alt="" class="pwe-medals__category-icon">
                </div>
                <h4 class="pwe-medals__category-title">'. PWE_Functions::multi_translation("category-title_2") .'</h4>
                <div class="pwe-medals__category-divider"></div>
                <p class="pwe-medals__category-desc">'. PWE_Functions::multi_translation("category-desc_2") .'</p>
            </div>
            <div class="pwe-medals__category">
                <div class="pwe-medals__category-icon-wrapper">
                    <img src="/wp-content/plugins/pwe-media/media/medals/gr1/star-icon.webp" alt="" class="pwe-medals__category-icon">
                </div>
                <h4 class="pwe-medals__category-title">'. PWE_Functions::multi_translation("category-title_3") .'</h4>
                <div class="pwe-medals__category-divider"></div>
                <p class="pwe-medals__category-desc">'. PWE_Functions::multi_translation("category-desc_3") .'</p>
            </div>
            <div class="pwe-medals__category">
                <div class="pwe-medals__category-icon-wrapper">
                    <img src="/wp-content/plugins/pwe-media/media/medals/gr1/diamond-icon.webp" alt="" class="pwe-medals__category-icon">
                </div>
                <h4 class="pwe-medals__category-title">'. PWE_Functions::multi_translation("category-title_4") .'</h4>
                <div class="pwe-medals__category-divider"></div>
                <p class="pwe-medals__category-desc">'. PWE_Functions::multi_translation("category-desc_4") .'</p>
            </div>
        </div>
        <div class="pwe-medals__icon-wrapper">
            <img src="/wp-content/plugins/pwe-media/media/medals/gr1/medal_ceremony_icon.webp" alt="" class="pwe-medals__icon">
        </div>
    </div>
</div>';

return $output;
