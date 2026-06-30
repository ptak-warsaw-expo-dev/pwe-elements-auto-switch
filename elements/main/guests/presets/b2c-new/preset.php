<?php

$output = '
<section id="pweGuests" class="pwe-element-auto-switch pwe-guests-section">
    <div class="pwe-guests-container">

        <div class="pwe-guests__header">
            <div class="pwe-guests__header-title">
                <span class="pwe-subtitle">' . (!empty($subtitle) ? $subtitle : ''. PWE_Functions::multi_translation('guests_title') .'') . ' </span>
                <h2 class="pwe-title">' . (!empty($title) ? $title : 'Gwiazdy <span class="text-red">Warsaw Motorcycle Show</span>') . '  <span class="text-red">' . do_shortcode('[trade_fair_name]') . '</span></h2>
            </div>

            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </div>
                <div class="swiper-button-next">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </div>
        </div>';

        if (!empty($tabs)) {
            $output .= '
            <div class="pwe-guests__tabs-wrapper">
                <div class="pwe-guests__tabs">';
                foreach ($tabs as $i => $tab) {
                    $name = $tab['name_'.$lang] ?? $tab['name_en'] ?? $tab['name_pl'] ?? '';
                    $active = $i === 0 ? 'is-active' : '';

                    $output .= '
                    <button class="pwe-guests__tab-btn '.$active.'" data-tab="'.$tab['slug'].'">
                        '.$name.'
                    </button>';
                }
                $output .= '
                </div>
            </div>';
        }

        $output .= '
        <div class="pwe-guests__tab-contents">';

            foreach ($guests_by_tab as $slug => $list) {
                $active = (!empty($tabs) && ($tabs[0]['slug'] ?? '') === $slug) ? 'is-active' : (empty($tabs) ? 'is-active' : '');

                $output .= '
                <div class="pwe-guests__tab-content '.$active.'" data-tab="'.$slug.'">

                    <div class="pwe-guests__slider swiper" role="group" aria-roledescription="carousel" aria-live="polite">
                        <div class="swiper-wrapper">';

                            foreach ($list as $guest) {
                                if (empty($guest['img'])) continue;

                                $guest_role = !empty($guest['label']) ? $guest['label'] : '';
                                $guest_bio  = !empty($guest['bio']) ? $guest['bio'] : '';

                                $output .= '
                                <div class="swiper-slide pwe-guests__card group '.$guest['id'].'">

                                    <div class="pwe-guests__image-wrapper">
                                        <img src="'.$guest['img'].'" alt="" class="pwe-guests__image">
                                    </div>

                                    <div class="pwe-guests__card-body">
                                        ' . (!empty($guest_role) ? '<p class="pwe-guests__guest-role">' . $guest_role . '</p>' : '') . '
                                        <h3 class="pwe-guests__guest-name">' . $guest['name'] . '</h3>
                                        ' . (!empty($guest_bio) ? '<div class="pwe-guests__guest-desc">' . $guest_bio . '</div>' : '') . '
                                    </div>

                                </div>';
                            }

                            $output .= '
                        </div>
                    </div>

                    <div class="swiper-nav">
                        <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
                    </div>

                </div>';
            }

            $output .= '
        </div>

    </div>
</section>';

// Konfiguracja skryptu Swiper dla 4 slajdów na dużym ekranie i ukrywania nawigacji (watchOverflow)
$output .= PWE_Swiper::swiperScripts(
    '#pweGuests',
    [
        0 => ['slidesPerView' => 1],
        640 => ['slidesPerView' => 2],
        1024 => ['slidesPerView' => 3],
        1280 => ['slidesPerView' => 4]
    ],
    true, // navigation (strzałki)
    true, // pagination (kropki)
    1,
    false
);

return $output;