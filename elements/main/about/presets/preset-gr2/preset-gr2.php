<?php

$dir    = $_SERVER['DOCUMENT_ROOT'] . '/doc/galeria/';
$webDir = '/doc/galeria/';

$files = is_dir($dir) ? scandir($dir) : [];
$allImages = array_values(array_filter($files, function ($file) use ($dir) {
    return is_file($dir . $file) && preg_match('/\.(jpe?g|png|webp)$/i', $file);
}));
$allImages = array_map(fn($f) => $webDir . $f, $allImages);

$maxImages = 10;
shuffle($allImages);
$mediaImages = array_slice($allImages, 0, $maxImages);

$video = 'https://mr.glasstec.pl/wp-content/uploads/2025/12/roof-expo-intro-test-video-4.mp4';

$output = '
<div id="pweAbout" class="pwe-about" data-images=\''. json_encode($mediaImages) .'\'>
    <div class="pwe-about__wrapper">

        <div class="pwe-about__content">
            <div class="pwe-about__logo">
                <img src="/doc/logo-color.webp">
            </div>
            <h4 class="pwe-about__subtitle pwe-main-subtitle">'. $title .'</h4>
            <div class="pwe-about__desc pwe-main-desc">'. $desc .'</div>
        </div>

        <div class="pwe-about__media">
            <div class="pwe-about__media-wrapper">

                <div class="pwe-about__media-column">
                    <div class="pwe-about__media-item">
                        <div class="flip-card">
                            <div class="flip-face front">
                                <img src="'. ($mediaImages[0] ?? '') .'">
                            </div>
                            <div class="flip-face back">
                                <img src="">
                            </div>
                        </div>
                    </div>

                    <span class="pwe-about__media-item video">
                        <video autoplay muted loop preload="auto">
                            <source src="'. htmlspecialchars($video) .'">
                        </video>
                        <img src="/wp-content/plugins/pwe-media/media/logo_pwe.webp">
                    </span>
                </div>

                <div class="pwe-about__media-column">
                    <div class="pwe-about__media-item">
                        <div class="flip-card">
                            <div class="flip-face front">
                                <img src="'. ($mediaImages[1] ?? '') .'">
                            </div>
                            <div class="flip-face back">
                                <img src="">
                            </div>
                        </div>
                    </div>

                    <div class="pwe-about__media-item">
                        <div class="flip-card">
                            <div class="flip-face front">
                                <img src="'. ($mediaImages[2] ?? '') .'">
                            </div>
                            <div class="flip-face back">
                                <img src="">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>';

return $output;
