<?php

$output = '
<style>
    #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
        margin-top: 36px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__items.swiper {
        padding: 12px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item {
        height: 100%;
        background-color: white;    
        border-radius: 10px;
        overflow: hidden;
        padding: 10px;   
        box-shadow: 2px 2px 12px #cccccc;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__item p {
        text-transform: uppercase;
        font-size: 12px;
        font-weight: 700;
        color: black;
        white-space: break-spaces;
        text-align: center;
        line-height: 1.1;
        margin: 5px;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__button {
        margin: 36px auto 0;
        width: fit-content;
    }
    #pweLogotypes'. $slug_id .' .pwe-logotypes__button a {
        color: white;
    }
</style>';

if ($logotypes_slug === 'patrons-partners-conference') {

} else if ($logotypes_slug === 'europe-event') {
    $output .= '
    <style>
        #pweLogotypes'. $slug_id .' .pwe-logotypes__item-title {
            margin-top: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
        }
    </style>';
} else {
    $output .= '
    <style>
        #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 16px;
        }
        #pweLogotypes'. $slug_id .' .pwe-logotypes__item {
            background-color: white;    
            border-radius: 10px;
            overflow: hidden;
            padding: 10px;   
            box-shadow: 2px 2px 12px #cccccc;
        }
        @media(max-width: 960px) {
            #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
                grid-template-columns: repeat(5, 1fr);
                gap: 16px;
            }
        }
        @media(max-width: 550px) {
            #pweLogotypes'. $slug_id .' .pwe-logotypes__items {
                grid-template-columns: repeat(3, 1fr);
                gap: 10px;
            }
            #pweLogotypes'. $slug_id .' .pwe-logotypes__item {
                padding: 5px;
            }
        }
        @media(max-width: 450px) {
            #pweLogotypes'. $slug_id .' .pwe-logotypes__item p {
                font-size: 10px;
            }
        }
        @media(max-width: 380px) {
            #pweLogotypes'. $slug_id .' .pwe-logotypes__item p {
                font-size: 8px;
            }
        }
    </style>';
}

$output .= '
<div id="pweLogotypes'. $slug_id .'" class="pwe-logotypes '. $logotypes_slug . ($logotypes_slug === "patrons-partners-conference" ? " slider" : "") .'">
    <div class="pwe-logotypes__wrapper">
        <div class="pwe-logotypes__text">
            <div class="pwe-logotypes__title">
                <h4 class="pwe-main-title">'. $title .'</h4>
            </div>
        </div>';

        if ($logotypes_slug === 'patrons-partners-conference') {
            $output .= '
            <div class="pwe-logotypes__items swiper">
                <div class="swiper-wrapper">';

                    foreach ($logotypes as $logo) {
                        $logo_caption = '<p>'. (PWECommonFunctions::lang_pl() ? $logo['desc_pl'] : $logo['desc_en']) .'</p>';
                        $logo_caption = str_replace((PWECommonFunctions::lang_pl() ? "międzynarodowy" : "international"), "", mb_strtolower($logo_caption, "UTF-8"));

                        $output .= '
                        <div class="pwe-logotypes__item swiper-slide">
                            <img src="'. $logo["url"] .'">
                            '. $logo_caption .'
                        </div>';
                    }
    
                $output .= '  
                </div>  
            </div>';

            $output .= PWE_Swiper::swiperScripts('#pweLogotypes'. $slug_id .'', [0   => ['slidesPerView' => 3], 570 => ['slidesPerView' => 4],960 => ['slidesPerView' => 6],], true);

        } else if ($logotypes_slug === 'europe-event') {
            function format_title($title) {
                return preg_replace('/\((.*?)\)/', '<br><span style="color: #888;">($1)</span>', $title);
            }

            $output .= '
            <div class="pwe-logotypes__items swiper">
                <div class="swiper-wrapper">';

                    $non_warsaw = [];
                    $warsaw_logos = [];
                    
                    // Division of logos into two groups
                    foreach ($logotypes as $logo) {
                        // Get file alt
                        $filename_events = $logo["alt"];
                        
                        // Check if name contains "warsaw" or "warsawa" (case-insensitive)
                        if (stripos($filename_events, "warsaw") !== false || stripos($filename_events, "warszawa") !== false) {
                            $warsaw_logos[] = $logo;
                        } else {
                            $non_warsaw[] = $logo;
                        }
                    }
                    
                    // Merge the boards – the ones with warsaw at the end
                    $sorted_logos = array_merge($non_warsaw, $warsaw_logos);
                    
                    foreach ($sorted_logos as $logo) {
                        // Get file name without extension
                        $filename_events = $logo["alt"];
                    
                        // Matching name in format "Europe/IPM (Essen, Germany) - IPM (Essen, Germany)"
                        if (preg_match('/^(.*) - (.*)$/', $filename_events, $matches)) {
                            // Polish name before " - "
                            $title_pl = trim($matches[1]); 
                            // English name after " - "
                            $title_en = trim($matches[2]); 
                        } else {
                            // If no match, use full name
                            $title_pl = $filename_events; 
                            $title_en = $filename_events;
                        }
                    
                        $formatted_title_pl = format_title($title_pl ?? '');
                        $formatted_title_en = format_title($title_en ?? '');
                                                                                
                        $output .= '
                        <div class="pwe-logotypes__item swiper-slide">
                            <img src="'. $logo["url"] .'" alt="'. (PWECommonFunctions::lang_pl() ? $title_pl : $title_en) .'"/>
                            <div class="pwe-logotypes__item-title"><span>'. (PWECommonFunctions::lang_pl() ? $formatted_title_pl : $formatted_title_en) .'</span></div>
                        </div>'; 
                    }
    
                $output .= '  
                </div>  
            </div>';

            $output .= PWE_Swiper::swiperScripts('#pweLogotypes'. $slug_id .'', [0   => ['slidesPerView' => 2], 570 => ['slidesPerView' => 3],960 => ['slidesPerView' => 5],], true);

        } else {
            $output .= '
            <div class="pwe-logotypes__items">';

                foreach ($logotypes as $logo) {
                    $logo_caption = '<p>'. (PWECommonFunctions::lang_pl() ? $logo['desc_pl'] : $logo['desc_en']) .'</p>';
                    $logo_caption = str_replace((PWECommonFunctions::lang_pl() ? "międzynarodowy" : "international"), "", mb_strtolower($logo_caption, "UTF-8"));

                    $target_blank = (strpos($logo["link"], 'http') !== false) ? 'target="_blank"' : '';

                    if (empty($logo["link"])) {
                        $output .= '
                        <div class="pwe-logotypes__item">
                            <img src="'. $logo["url"] .'">
                            '. $logo_caption .'
                        </div>';
                    } else {
                        $output .= '
                        <a ' . $target_blank . ' href="' . $logo["link"] . '">
                            <div class="pwe-logotypes__item">
                                <img src="'. $logo["url"] .'">
                                '. $logo_caption .'
                            </div>
                        </a>';
                    }
                }
    
            $output .= '    
            </div>';
        }

        if ($logotypes_slug === 'patrons-partners' || $logotypes_slug === 'patrons-partners-international') {
            $output .= '
            <div class="pwe-logotypes__button pwe-main-btn--primary">
                <a class="" href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'">'. PWECommonFunctions::languageChecker('Weź udział', 'Take a part') .'</a> 
            </div>';
        }

    $output .= '
    </div>
</div>';    

return $output;
