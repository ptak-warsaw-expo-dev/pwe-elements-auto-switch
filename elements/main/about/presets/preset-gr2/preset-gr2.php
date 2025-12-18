<?php

$mediaImages = [
    '/doc/galeria/DSC08310.jpg',
    '/doc/galeria/DSC08311.jpg',
    '/doc/galeria/DSC08313.jpg',
];

$centerGif = 'https://mr.glasstec.pl/wp-content/uploads/2025/12/roof-expo-intro-test-video-4.mp4';
    
$output = '
<div id="pweAbout" class="pwe-about">
    <div class="pwe-about__wrapper">
        <div class="pwe-about__content">
            <div class="pwe-about__logo"><img src="/doc/logo-color.webp"></div>
            <h4 class="pwe-about__subtitle pwe-main-subtitle">' . $title . '</h4>
            <div class="pwe-about__desc pwe-main-desc">' . $desc . '</div>
        </div>
        <div class="pwe-about__media">
            <div class="pwe-about__media-wrapper">
                <div class="pwe-about__media-column">
                    <img class="pwe-about__media-image" src="'. $mediaImages[0] .'">
                    <span class="pwe-about__media-image video">
                        <video autoplay muted loop preload="auto">
                            <source src="'. htmlspecialchars($centerGif) .'">
                        </video>
                        <img src="/wp-content/plugins/pwe-media/media/logo_pwe.webp">
                    </span>
                </div>
                <div class="pwe-about__media-column">
                    <img class="pwe-about__media-image" src="'. $mediaImages[1] .'">
                    <img class="pwe-about__media-image" src="'. $mediaImages[2] .'">
                </div>
            </div>
        </div>
    </div>
</div>';

return $output;