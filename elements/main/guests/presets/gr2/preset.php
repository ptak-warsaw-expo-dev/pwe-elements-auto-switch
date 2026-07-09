<?php

$output = '
<div id="pweGuests" class="pwe-guests embla-coverflow">

    <div class="pwe-guests__wrapper">

        <div class="pwe-guests__top">

            <div class="pwe-guests__title">
                <h4 class="pwe-guests__heading pwe-main-title">'.$title.'</h4>
                <p class="pwe-guests__desc">'.$subtitle.'</p>
            </div>';

            if (!empty($tabs)) {

                $output .= '
                <div class="pwe-guests__tabs">';

                foreach ($tabs as $i => $tab) {

                    $name = $tab['name_'.$lang]
                        ?? $tab['name_en']
                        ?? $tab['name_pl']
                        ?? '';

                    $active = $i === 0 ? 'is-active' : '';

                    $output .= '
                    <button class="pwe-guests__tab-btn '.$active.'" data-tab="'.$tab['slug'].'">
                        '.$name.'
                    </button>';
                }

                $output .= '
                </div>';
            }

            $output .= '
        </div>

        <div class="pwe-guests__tab-contents">';

            foreach ($guests_by_tab as $slug => $list) {

                $active = (!empty($tabs) && ($tabs[0]['slug'] ?? '') === $slug)
                    ? 'is-active'
                    : (empty($tabs) ? 'is-active' : '');

                $output .= '
                <div class="pwe-guests__tab-content '.$active.'" data-tab="'.$slug.'">

                    <div class="embla">
                        <div class="pwe-guests__items embla__container">';

                            foreach ($list as $guest) {

                                if (empty($guest['img'])) continue;

                                $output .= '
                                <div class="pwe-guests__item embla__slide '.$guest['id'].'">
                                    <div class="pwe-guests__item-wrapper embla__slide__inner">

                                        <div class="pwe-guests__guest-img">';

                                            if (!empty($guest['label'])) {
                                                $output .= '<span class="pwe-guests__guest-role">'.$guest['label'].'</span>';
                                            }

                                            $output .= '
                                            <img data-no-lazy="1" src="'.$guest['img'].'" alt="">
                                        </div>

                                        <div class="pwe-guests__item-text">
                                            <h4 class="pwe-guests__item-name">'.$guest['name'].'</h4>
                                            <div class="pwe-guests__item-desc">'.$guest['bio'].'</div>
                                        </div>

                                    </div>
                                </div>';
                            }

                            $output .= '
                        </div>
                    </div>

                </div>';
            }

            $output .= '
        </div>

    </div>
</div>';

$output .= '
<script src="https://unpkg.com/embla-carousel/embla-carousel.umd.js"></script>
<script src="https://unpkg.com/embla-carousel-autoplay/embla-carousel-autoplay.umd.js"></script>';

return $output;