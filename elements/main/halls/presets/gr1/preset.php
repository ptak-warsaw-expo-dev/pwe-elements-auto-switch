<?php

if (!empty($all_halls)) {
    $output = '
    <div id="pweHalls" class="pwe-halls"
        data-all-items='. json_encode($json_data_all) .'
        data-active-items='. json_encode($json_data_active) .'>
        <div class="pwe-halls__wrapper">

            <div class="pwe-halls__title">
                <h4 class="pwe-main-title">'. PWE_Functions::languageChecker('Największa powierzchnia wystawiennicza w Polsce', 'The largest exhibition space in Poland') .'</h4>
            </div>

            <div class="pwe-halls__details">
                <div class="pwe-halls__details-wrapper">
                    <div class="pwe-halls__details-column logo">
                        <img src="'. PWE_Functions::languageChecker('/doc/logo-color.webp', '/doc/logo-color-en.webp') .'"/>
                        <p class="pwe-halls__date"><strong>'. PWE_Functions::languageChecker(do_shortcode('[trade_fair_date]'), do_shortcode('[trade_fair_date_eng]')) .'</strong></p>
                    </div>
                    <div class="pwe-halls__details-column info">
                        <div class="pwe-halls__information">
                            <p class="pwe-halls__letters">'. $halls_word .' '. $all_halls .'</p>
                            <p class="pwe-halls__time">10:00-17:00</p>
                            <p class="pwe-halls__parking">'. PWE_Functions::languageChecker('DARMOWY PARKING', 'FREE PARKING') .'</p>
                        </div>
                        <div class="pwe-halls__location">
                            <i class="fa fa-location2 fa-1x fa-fw"></i>
                            Al. Katowicka 62, 05-830 Nadarzyn
                        </div>
                    </div>
                </div>
            </div>

            <div class="pwe-halls__model">';
                require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/svg.php';
            $output .= '
            </div>

        </div>
    </div>';
    
} else { $output = '<style>.row-container:has(#pweHalls) {display: none !important;}</style>'; }

return $output;
