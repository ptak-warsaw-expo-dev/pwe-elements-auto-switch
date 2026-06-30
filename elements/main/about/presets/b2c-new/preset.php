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

$video = '/wp-content/plugins/pwe-media/media/video_pwe.mp4';

$output = '
<section id="pweAbout" class="pwe-about" data-images=\''. json_encode($mediaImages) .'\'>

    <div class="pwe-about__watermark">
        Warsaw 2026
    </div>

    <div class="pwe-about__container">
        <div class="pwe-about__grid">

            <div class="pwe-about__media-grid">

                <div class="pwe-about__media-col">

                    <div class="pwe-about__media-item h-60 flip-card-container">
                        <div class="flip-card">
                            <div class="flip-face front">
                                <img src="'. ($mediaImages[0] ?? 'https://images.unsplash.com/photo-1558981403-c5f9899a28bc?auto=format&fit=crop&w=600&q=80') .'" alt="Motocykle Klasyczne">
                            </div>
                            <div class="flip-face back">
                                <img src="'. ($mediaImages[4] ?? '') .'" alt="Galeria">
                            </div>
                        </div>
                    </div>

                    <div class="pwe-about__media-item h-35 flip-card-container">
                        <div class="flip-card">
                            <div class="flip-face front">
                                <img src="'. ($mediaImages[1] ?? 'https://images.unsplash.com/photo-1591637333184-19aa84b3e01f?auto=format&fit=crop&w=600&q=80') .'" alt="Detale Motocykla">
                            </div>
                            <div class="flip-face back">
                                <img src="'. ($mediaImages[5] ?? '') .'" alt="Galeria">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="pwe-about__media-col pt-offset">

                    <div class="pwe-about__media-item h-35 flip-card-container">
                        <div class="flip-card">
                            <div class="flip-face front">
                                <img src="'. ($mediaImages[2] ?? 'https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?auto=format&fit=crop&w=600&q=80') .'" alt="Premiery">
                            </div>
                            <div class="flip-face back">
                                <img src="'. ($mediaImages[6] ?? '') .'" alt="Galeria">
                            </div>
                        </div>
                    </div>

                    <div class="pwe-about__media-item h-60 video-item">
                        <video autoplay muted loop preload="auto" playsinline webkit-playsinline class="pwe-about__video">
                            <source src="'. htmlspecialchars($video) .'" type="video/mp4">
                        </video>
                        <div class="video-overlay-brand">
                            <img src="/wp-content/plugins/pwe-media/media/logo_pwe.webp" onerror="this.style.display=\'none\'">
                        </div>
                    </div>

                </div>

            </div>

            <div class="pwe-about__content">

                <span class="pwe-about__badge">'. PWE_Functions::multi_translation('lider_title') .'</span>

                <h2 class="pwe-about__title">
                    '. (!empty($title) ? $title : 'Największe Targi Motocyklowe w <span class="text-red">Europie Środkowo-Wschodniej</span>') .'
                </h2>

                <div class="pwe-about__desc">
                    '. (!empty($desc) ? $desc : 'Warsaw Motorcycle Show to wydarzenie branżowe, którego celem jest zgromadzenie czołowych firm, ekspertów technicznych i praktyków związanych z sektorem motocykli w Polsce i całym regionie. To tutaj innowacje spotykają się z praktycznym zapotrzebowaniem.') .'
                </div>

                <div class="pwe-about__features">

                    <div class="pwe-feature-item">
                        <div class="pwe-feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        </div>
                        <div class="pwe-feature-text">
                            <h4>'. PWE_Functions::multi_translation('business_title') .'</h4>
                            <p>'. PWE_Functions::multi_translation('business_desc') .'</p>
                        </div>
                    </div>

                    <div class="pwe-feature-item">
                        <div class="pwe-feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5 5 0 0 0 8 8c0 1.3.5 2.6 1.5 3.5.7.8 1.3 1.5 1.5 2.5"></path><line x1="9" y1="18" x2="15" y2="18"></line><line x1="10" y1="22" x2="14" y2="22"></line></svg>
                        </div>
                        <div class="pwe-feature-text">
                            <h4>'. PWE_Functions::multi_translation('innovation_title') .'</h4>
                            <p>'. PWE_Functions::multi_translation('innovation_desc') .'</p>
                        </div>
                    </div>

                </div>



            </div>

        </div>
    </div>
</section>
';

return $output;