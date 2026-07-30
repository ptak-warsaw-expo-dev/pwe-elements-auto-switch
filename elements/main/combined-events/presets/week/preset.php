<?php

$associates_raw = isset($combined_events[0]->fair_associates) ? $combined_events[0]->fair_associates : '';
$domains = array_filter(array_map('trim', explode(',', $associates_raw)));

$theme_mapping = [
    'ai' => [
        'color' => 'purple'
    ],
    'cyber' => [
        'color' => 'green'
    ],
    'data' => [
        'color' => 'blue'
    ],
    'hr' => [
        'color' => 'lime'
    ],
    'default' => [
        'color' => 'blue'
    ]
];

$events_data = [];
foreach ($domains as $domain) {

    $name = do_shortcode('[pwe_name_pl domain="' . esc_attr($domain) . '"]');

    $selected_lang = PWE_Functions::lang();
    $desc  = do_shortcode('[pwe_desc_'. $selected_lang .']');

    if (empty($name)) {
        $name = $domain;
    }

    $matched_theme = $theme_mapping['default'];
    foreach ($theme_mapping as $key => $theme) {
        if (strpos(strtolower($domain), $key) !== false) {
            $matched_theme = $theme;
            break;
        }
    }

    $events_data[] = [
        'name' => $name,
        'domain' => $domain,
        'color' => $matched_theme['color'],
        'icon' => 'https://' . $domain . '/doc/favicon-color.webp',
        'desc' => $desc,
        'link' => 'https://' . $domain
    ];
}

$total_events = count($events_data);
$use_slider = $total_events > 4;

$output .= '
<style>
    .pwe-combined-grid {
        display: grid;
        grid-template-columns: repeat(' . min(4, $total_events) . ', 1fr);
        gap: 20px;
    }
</style>';

$output .= '
<div id="pweEvents" class="pwe-combined-section">
    <div class="wrap">
        <h2 class="section-title">' . PWE_Functions::multi_translation('fairs_title') . ' '. do_shortcode('[trade_fair_name]') .'</h2>';

if ($use_slider) {

    $output .= '
        <div class="pwe-carousel-container" id="pweCarousel">
            <button class="pwe-carousel-btn prev" aria-label="Poprzedni">‹</button>
            <div class="pwe-carousel-viewport">
                <div class="pwe-carousel-track">';

                foreach ($events_data as $event) {
                    $output .= '
                    <article class="card ' . $event['color'] . '">
                        <div>
                            <div class="icon"><img src="' . $event['icon'] . '"/></div>
                            <h3>' . $event['name'] . '</h3>
                            <p>' . $event['desc'] . '</p>
                        </div>
                        <a class="more" href="' . esc_url($event['link']) . '">' . PWE_Functions::multi_translation('see_more') . '</a>
                    </article>';
                }

    $output .= '
                </div>
            </div>
            <button class="pwe-carousel-btn next" aria-label="Następny">›</button>
        </div>';
} else {

    $output .= '
        <div class="cards pwe-combined-grid">';
        foreach ($events_data as $event) {
            $output .= '
            <article class="card ' . $event['color'] . '">
                <div>
                    <div class="icon"><img src="' . $event['icon'] . '"/></div>
                    <h3>' . $event['name'] . '</h3>
                    <p>' . $event['desc'] . '</p>
                </div>
                <a class="more" href="' . esc_url($event['link']) . '">' . PWE_Functions::multi_translation('see_more') . '</a>
            </article>';
        }
    $output .= '
        </div>';
}

$output .= '
    </div>
</div>';


if ($use_slider) {
    $output .= '
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const carousel = document.getElementById("pweCarousel");
        if (!carousel) return;

        const viewport = carousel.querySelector(".pwe-carousel-viewport");
        const track = carousel.querySelector(".pwe-carousel-track");
        const prevBtn = carousel.querySelector(".pwe-carousel-btn.prev");
        const nextBtn = carousel.querySelector(".pwe-carousel-btn.next");

        let cards = Array.from(track.children);
        const originalCount = cards.length;

        cards.forEach(card => {
            const cloneStart = card.cloneNode(true);
            const cloneEnd = card.cloneNode(true);
            track.appendChild(cloneStart);
            track.insertBefore(cloneEnd, track.firstChild);
        });

        const allCards = Array.from(track.children);
        let currentIndex = originalCount; // Startujemy od pierwszego oryginalnego elementu
        let isTransitioning = false;

        function getVisibleCardsCount() {
            if (window.innerWidth <= 680) return 1;
            if (window.innerWidth <= 1050) return 2;
            return 4;
        }

        function getGap() {
            return 20; // Stały odstęp w pikselach (gap: 20px)
        }

        function updateSliderPosition(instant = false) {
            const cardWidth = cards[0].getBoundingClientRect().width;
            const gap = getGap();
            const offset = currentIndex * (cardWidth + gap);

            if (instant) {
                track.style.transition = "none";
            } else {
                track.style.transition = "transform 0.4s cubic-bezier(0.25, 1, 0.5, 1)";
            }
            track.style.transform = `translateX(-${offset}px)`;
        }

        function moveNext() {
            if (isTransitioning) return;
            isTransitioning = true;
            currentIndex++;
            updateSliderPosition();
        }

        function movePrev() {
            if (isTransitioning) return;
            isTransitioning = true;
            currentIndex--;
            updateSliderPosition();
        }

        track.addEventListener("transitionend", () => {
            isTransitioning = false;

            if (currentIndex >= originalCount * 2) {
                currentIndex = originalCount;
                updateSliderPosition(true);
            } else if (currentIndex < originalCount) {
                currentIndex = originalCount * 2 - 1;
                updateSliderPosition(true);
            }
        });

        nextBtn.addEventListener("click", moveNext);
        prevBtn.addEventListener("click", movePrev);

        let startX = 0;
        let currentX = 0;
        let isDragging = false;

        const handleStart = (e) => {
            isDragging = true;
            startX = e.type.includes("touch") ? e.touches[0].clientX : e.clientX;
            track.style.transition = "none";
        };

        const handleMove = (e) => {
            if (!isDragging) return;
            currentX = e.type.includes("touch") ? e.touches[0].clientX : e.clientX;
            const diff = startX - currentX;
            const cardWidth = cards[0].getBoundingClientRect().width;
            const gap = getGap();
            const currentOffset = currentIndex * (cardWidth + gap);
            track.style.transform = `translateX(-${currentOffset + diff}px)`;
        };

        const handleEnd = (e) => {
            if (!isDragging) return;
            isDragging = false;
            const diff = startX - currentX;
            const threshold = 50; // czułość przesunięcia w pikselach

            if (diff > threshold) {
                moveNext();
            } else if (diff < -threshold) {
                movePrev();
            } else {
                updateSliderPosition();
            }
        };

        viewport.addEventListener("mousedown", handleStart);
        viewport.addEventListener("mousemove", handleMove);
        window.addEventListener("mouseup", handleEnd);

        viewport.addEventListener("touchstart", handleStart);
        viewport.addEventListener("touchmove", handleMove);
        viewport.addEventListener("touchend", handleEnd);

        let autoplay = setInterval(moveNext, 5000);
        carousel.addEventListener("mouseenter", () => clearInterval(autoplay));
        carousel.addEventListener("mouseleave", () => autoplay = setInterval(moveNext, 5000));

        window.addEventListener("resize", () => updateSliderPosition(true));
        setTimeout(() => updateSliderPosition(true), 150);
    });
    </script>';
}

return $output;