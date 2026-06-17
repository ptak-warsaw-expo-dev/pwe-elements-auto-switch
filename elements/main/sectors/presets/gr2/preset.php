<?php

$output .= '
<div id="pweSectors" class="pwe-sectors">
    <div class="pwe-sectors__wrapper">
        <div class="pwe-sectors__title">
            <p>'. (PWE_Functions::lang_pl() ? do_shortcode('[trade_fair_name]') : do_shortcode('[trade_fair_name_eng]')) .'</p>
            <hr>
            <h4 class="pwe-main-title">'. PWE_Functions::multi_translation('title') .'</h4>
        </div>
        <div class="pwe-sectors__items">';
            
            foreach ($sectors as $sector) {
                $sector_name = $sector['sector_name'];
                $sector_image = $sector['sector_image'];

                $output .= '
                <div class="pwe-sectors__item">
                    <div class="pwe-sectors__item-wrapper">
                        <div class="pwe-sectors__item-icon">
                            <img src="' . $sector_image . '">
                        </div>
                        <div class="pwe-sectors__item-name">
                            <p>' . $sector_name . '</p>
                        </div>
                    </div>
                </div>';
            }

        $output .= '
        </div>

        <div class="pwe-logotypes__button pwe-main-btn--primary">
            <a href="'. PWE_Functions::multi_translation("catalog_btn_url") .'">'. PWE_Functions::multi_translation("catalog_btn") .'</a> 
        </div>
    </div>
</div>';

return $output;
