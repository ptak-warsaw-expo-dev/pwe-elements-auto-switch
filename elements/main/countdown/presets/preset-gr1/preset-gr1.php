<?php

$output = '<div style="visibility: hidden; width: 0; height: 0;" id="main-content">...</div>';

if ((($trade_fair_start_date_timestamp != false && $trade_fair_end_date_timestamp != false) && !empty($trade_fair_start_date)) &&
    $diff_timestamp < (7 * 60 * 60) && $time_to_end_timestamp > 0) {

    $output .= '
    <div id="openingHours" class="opening-hours">
        <div class="opening-hours__block">
            <p class="opening-hours__title">'. PWECommonFunctions::languageChecker('GODZINY OTWARCIA:', 'OPENING HOURS:') .'</p>
            <p class="opening-hours__date pwe-uppercase">'. $trade_fair_start_date_week .' - '. $trade_fair_end_date_week .'<span class="hours">'. $trade_fair_start_date_hour .' - '. $trade_fair_end_date_hour .'</span></p>
            <div class="opening-hours__hall">
                <p>'. $halls_word .' <strong>'. $all_halls .'</strong></p>
                <p>'. $entries_word .' <strong>'. $all_entries .'</strong></p>
            </div>
        </div>
        <p class="opening-hours__adress">Al. Katowicka 62, 05-830 Nadarzyn</p>
    </div>';
} else if ($trade_fair_start_date_timestamp != false && !empty($trade_fair_start_date)) {
    $lang = PWECommonFunctions::lang_pl() ? 'pl' : 'en';

    $show_seconds = false;

    $output .= '
    <div id="pweCountdown" class="pwe-countdown">
        <div class="pwe-countdown__wrapper"
            data-start="'. $trade_fair_start_date .'" 
            data-end="'. $trade_fair_end_date .'"
            data-lang="'. $lang .'"
            data-seconds="'. ($show_seconds ? '1' : '0') .'">
            <div class="pwe-countdown__label"></div>
            <div class="pwe-countdown__timer"></div>
            <div class="pwe-countdown__cta">
                <a href="'. ($lang === 'pl' ? '/zostan-wystawca/' : '/en/become-an-exhibitor/') .'" class="pwe-btn">
                    '. ($lang === 'pl' ? 'Zostań wystawcą' : 'Become an exhibitor') .'
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13 7L1 7M13 7L7 13M13 7L7 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>';
    
}

return $output;
