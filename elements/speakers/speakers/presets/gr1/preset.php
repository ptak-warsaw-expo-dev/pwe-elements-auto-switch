<?php

$output = '
<div id="pweSpeakers" class="pwe-speakers">
    <div class="pwe-speakers__wrapper">

        <div class="pwe-speakers__items">';

                foreach ($speakers as $speaker) {
                    
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

                }

            $output .= '
        </div>';

        $output .= '
    </div>
</div>';

return $output;
