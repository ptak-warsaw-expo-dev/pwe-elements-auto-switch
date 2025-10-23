<?php

$output = '';

$output .= '
<style>
    .pwe-about {
    }
    .pwe-about__wrapper {
        display: flex;
        justify-content: space-between;
        gap: 18px;
    }
    .pwe-about__content {
        width: 43%;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    .pwe-about__media {
        width: 55%;
    }
    .pwe-about__logo {
        max-width: 260px;
    }
    .pwe-about__subtitle {
        font-size: 22px !important;
        margin: 0;
    }
    .pwe-about__media-wrapper {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
    }
    .pwe-about__media-column {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .pwe-about__media-column.first,
    .pwe-about__media-column.last {
        margin-top: 40px;
    }
    .pwe-about__media-image {
        object-fit: cover;
        border-radius: 20px;
    }
    .pwe-about__media-image.small {
        aspect-ratio: 1;
    }
    .pwe-about__media-image.middle {
        aspect-ratio: 3/4;
    }
    .pwe-about__media-image.big {
        position: relative;
        aspect-ratio: 4/7;
        background-position: center !important;
        background-size: cover !important;
        overflow: hidden;
    }
    .pwe-about__media-image.big img {
        position: absolute;
        width: 60px;
        left: 50%;
        bottom: 80px;
        transform: translate(-50%);
    }
    .pwe-about__media-image.big:before {
        position: absolute;
        content: "";
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--accent-color);
        opacity: 80%;
        z-index: 0;
    }
</style>';

$mediaImages = [
    '/doc/galeria/DSC08310.jpg', // top-left
    '/doc/galeria/DSC08311.jpg', // top-center
    '/doc/galeria/DSC08313.jpg', // top-right
    '/doc/galeria/DSC08314.jpg', // bottom-left
    '/doc/galeria/DSC08315.jpg', // bottom-right
];
$centerGif = '/doc/cat.gif';

// Layout
$output .= '
<div id="pweAbout" class="pwe-about">
    <div class="pwe-about__wrapper">
        <div class="pwe-about__content">
            <div class="pwe-about__logo"><img src="/doc/logo-color.webp"></div>
            <h4 class="pwe-about__subtitle pwe-main-subtitle">' . $title . '</h4>
            <div class="pwe-about__desc pwe-main-desc">' . $desc . '</div>
        </div>
        <div class="pwe-about__media">
            <div class="pwe-about__media-wrapper">
                <div class="pwe-about__media-column first">
                    <img class="pwe-about__media-image small" src="'. $mediaImages[0] .'">
                    <img class="pwe-about__media-image middle" src="'. $mediaImages[1] .'">
                </div>
                <div class="pwe-about__media-column">
                    <img class="pwe-about__media-image small" src="'. $mediaImages[2] .'">
                    <span class="pwe-about__media-image big" style="background: url('. htmlspecialchars($centerGif) .')">
                        <img src="/wp-content/plugins/pwe-media/media/logo_pwe.webp">
                    </span>
                </div>
                <div class="pwe-about__media-column last">
                    <img class="pwe-about__media-image small" src="'. $mediaImages[3] .'">
                    <img class="pwe-about__media-image middle" src="'. $mediaImages[4] .'">
                </div>
            </div>
        </div>
    </div>
</div>';

return $output;