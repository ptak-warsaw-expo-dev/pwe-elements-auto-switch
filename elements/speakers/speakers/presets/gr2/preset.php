<?php

$output = '
<div id="pweSpeakers" class="pwe-speakers">
    <div class="pwe-speakers__wrapper">
        <div class="pwe-speakers__title">
            <div>
                <h4 class="pwe-speakers__heading pwe-main-title">' . PWE_Functions::languageChecker('Prelegenci', 'Speakers') . '</h4>
                <p>' . PWE_Functions::languageChecker('Poznaj ekspertów i praktyków, którzy wyznaczają kierunki rozwoju nowoczesnego przemysłu i technologii produkcyjnych', 'Meet the experts and practitioners who set the directions for the development of modern industry and production technologies.') . '</p>
            </div>
            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev">⏴</div>
                <div class="swiper-button-next">⏵</div>
            </div>
        </div>
        
        <div class="swiper pwe-speakers__items">
            <div class="swiper-wrapper">';
                foreach ($speakers as $speaker) {
                    $output .= '
                    <div class="pwe-speakers__item swiper-slide">
                        <div class="pwe-speakers__speaker-img">
                            <img data-no-lazy="1" src="'. $speaker['speaker_img'] .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Speaker photo"/>
                        </div>
                        <div class="pwe-speakers__item-text">
                            <h3 class="pwe-speakers__item-name">'. $speaker['speaker_name'] .'</h3>
                            <p class="pwe-speakers__item-position">'. $speaker['speaker_position'] .'</p>
                            <div class="pwe-speakers__item-company-wrapper">
                                <p class="pwe-speakers__item-company">'. $speaker['speaker_company_name'] .'</p>
                                <div class="pwe-speakers__company-img">
                                    <img data-no-lazy="1" src="'. $speaker['speaker_company_img'] .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Company logo"/>
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
        </div>
    </div>
</div>';

$output .= PWE_Swiper::swiperScripts('#pweSpeakers', null, true, true);

return $output;
