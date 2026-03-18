<?php

$is_mobile = preg_match('/iPhone|iPad|Android|webOS|BlackBerry|iPod|Opera Mini|IEMobile/i', $_SERVER['HTTP_USER_AGENT'] ?? '');

$output = '
<div id="pweSpeakers" class="pwe-speakers">
    <div class="pwe-speakers__wrapper">
        <div class="pwe-speakers__title">
            <h4 class="pwe-speakers__heading pwe-main-title">' . PWE_Functions::languageChecker('Prelegenci', 'Speakers') . '</h4>
            <p>' . PWE_Functions::languageChecker('Poznaj ekspertów i praktyków, którzy wyznaczają kierunki rozwoju nowoczesnego przemysłu i technologii produkcyjnych', 'Meet the experts and practitioners who set the directions for the development of modern industry and production technologies.') . '</p>
        </div>
        
        <div class="pwe-speakers__items">';
                $count = 0;
                foreach ($speakers as $speaker) {
                    if ($is_mobile) {
                        if ($count >= 3) break;
                    } else {
                        if ($count >= 6) break;
                    }
                    
                    $output .= '
                    <div class="pwe-speakers__item">
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
                        <span class="pwe-speakers__circle"></span>
                    </div>';
                    $count++;
                }

            $output .= '
        </div>';

        if (count($speakers) > 6) {
            $output .= '
            <div class="pwe-speakers__bottom">
                <div class="pwe-speakers__btn">
                    <a class="pwe-main-btn--secondary" href="' . PWE_Functions::languageChecker('/prelegenci/', '/en/speakers/') . '">' . PWE_Functions::languageChecker('Zobacz wszystkich prelegentów', 'See all speakers') . '</a>
                </div>
            </div>';
        }

        $output .= '
    </div>
</div>';

return $output;
