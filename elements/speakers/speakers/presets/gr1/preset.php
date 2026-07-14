<?php

$output = '
<div id="pweSpeakers" class="pwe-speakers">
    <div class="pwe-speakers__wrapper">

        <div class="pwe-speakers__title">
            <h4 class="pwe-speakers__heading pwe-main-title">' . PWE_Functions::languageChecker('Prelegenci', 'Speakers') . '</h4>
        </div>

        <div class="pwe-speakers__items">'; 

                foreach ($speakers as $speaker) {
                    
                    $output .= '
                    <div id="pwe-speakers__item-'. PWE_Functions::id_rnd() .'" class="pwe-speakers__item">
                        <div class="pwe-speakers__speaker-img">
                            <img data-no-lazy="1" src="'. $speaker['img'] .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Speaker photo"/>';
                            if (!empty($speaker['bio'])) {
                                $output .= '<button class="pwe-speakers__item-bio-button">' . PWE_Functions::languageChecker('Zobacz BIO', 'See BIO') . '</button>';
                            }
                            $output .= '
                        </div>
                        <div class="pwe-speakers__item-text">
                            <div class="pwe-speakers__item-top-wrapper">
                                <h3 class="pwe-speakers__item-name">'. $speaker['name'] .'</h3>
                                <p class="pwe-speakers__item-position">'. $speaker['position'] .'</p>
                            </div>
                            <div class="pwe-speakers__item-bottom-wrapper">
                                <p class="pwe-speakers__item-company">'. $speaker['company'] .'</p>
                                <div class="pwe-speakers__company-img">
                                    <img data-no-lazy="1" src="'. $speaker['logo'] .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Company logo"/>
                                </div>
                            </div>
                            <div class="pwe-speakers__item-desc">'. $speaker['bio'] .'</div>
                        </div>
                        <span class="pwe-speakers__circle"></span>
                    </div>';

                }

            $output .= '
        </div>';

        $output .= '
    </div>
</div>';

return $output;
