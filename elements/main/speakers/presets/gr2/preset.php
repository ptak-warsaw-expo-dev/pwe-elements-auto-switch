<?php

$speakers_limited = array_slice($speakers, 0, 12);

$output = '';

if ($_SERVER['HTTP_HOST'] === 'warsawtechweek.com') {

    $output .= '
    <style>
        .pwe-element-auto-switch .pwe-speakers__items {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        @media (max-width:960px) {
            .pwe-element-auto-switch .pwe-speakers__items {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width:600px) {
            .pwe-element-auto-switch .pwe-speakers__items {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width:500px) {
            .pwe-element-auto-switch .pwe-speakers__items {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div id="pweSpeakers" class="pwe-speakers">
        <div class="pwe-speakers__wrapper">
            <div class="pwe-speakers__title">
                <h4 class="pwe-speakers__heading pwe-main-title">' . PWE_Functions::multi_translation('title') . '</h4>
            </div>
            
            <div class="pwe-speakers__items">';
                foreach ($speakers_limited as $speaker) {
                    $output .= '
                    <div class="pwe-speakers__item">
                        <div class="pwe-speakers__speaker-img">
                            <img data-no-lazy="1" src="'. $speaker['img'] .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Speaker photo"/>
                        </div>
                        <div class="pwe-speakers__item-text">
                            <h3 class="pwe-speakers__item-name">'. $speaker['name'] .'</h3>
                            <p class="pwe-speakers__item-position">'. $speaker['position'] .'</p>
                            <div class="pwe-speakers__item-company-wrapper">
                                <p class="pwe-speakers__item-company">'. $speaker['company'] .'</p>
                                <div class="pwe-speakers__company-img">
                                    <img data-no-lazy="1" src="'. $speaker['logo'] .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Company logo"/>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
                $output .= '
            </div>';

            if (count($speakers) > 12) {
                $output .= '
                <div class="pwe-speakers__bottom">
                    <div class="pwe-speakers__btn">
                        <a class="pwe-main-btn--secondary" href="' . PWE_Functions::languageChecker('/prelegenci/', '/en/speakers/') . '">' . PWE_Functions::multi_translation('all_speakers_btn') . '</a>
                    </div>
                </div>';
            }
            
        $output .= '    
        </div>
    </div>';

} else {
    $output .= '
    <div id="pweSpeakers" class="pwe-speakers">
        <div class="pwe-speakers__wrapper">
            <div class="pwe-speakers__title">
                <h4 class="pwe-speakers__heading pwe-main-title">' . PWE_Functions::multi_translation('title') . '</h4>
                <div class="swiper-buttons-arrows">
                    <div class="swiper-button-prev">⏴</div>
                    <div class="swiper-button-next">⏵</div>
                </div>
            </div>
            
            <div class="swiper pwe-speakers__items">
                <div class="swiper-wrapper">';
                    foreach ($speakers_limited as $speaker) {
                        $output .= '
                        <div class="pwe-speakers__item swiper-slide">
                            <div class="pwe-speakers__speaker-img">
                                <img data-no-lazy="1" src="'. $speaker['img'] .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Speaker photo"/>
                            </div>
                            <div class="pwe-speakers__item-text">
                                <h3 class="pwe-speakers__item-name">'. $speaker['name'] .'</h3>
                                <p class="pwe-speakers__item-position">'. $speaker['position'] .'</p>
                                <div class="pwe-speakers__item-company-wrapper">
                                    <p class="pwe-speakers__item-company">'. $speaker['company'] .'</p>
                                    <div class="pwe-speakers__company-img">
                                        <img data-no-lazy="1" src="'. $speaker['logo'] .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Company logo"/>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                $output .= '
                </div>
                <div class="swiper-nav">
                    <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
                </div>
            </div>';

            if (count($speakers) > 6) {
                $output .= '
                <div class="pwe-speakers__bottom">
                    <div class="pwe-speakers__btn">
                        <a class="pwe-main-btn--secondary" href="' . PWE_Functions::languageChecker('/prelegenci/', '/en/speakers/') . '">' . PWE_Functions::multi_translation('all_speakers_btn') . '</a>
                    </div>
                </div>';
            }
            
        $output .= '    
        </div>
    </div>';

    $output .= PWE_Swiper::swiperScripts('#pweSpeakers', null, true, true);
}

return $output;
