<?php

$output = '';
            
$output .= '
<div id="pweStatistics" class="pwe-statistics">
    <div class="pwe-statistics__wrapper">
        <div class="pwe-statistics__main-tile pwe-statistics__border">
            <h4 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Estymacje', 'Estimates') .'</h4>
            <div class="pwe-statistics__main-text">
                <strong><p>' . do_shortcode('[pwe_edition]') . PWECommonFunctions::languageChecker('. edycja', ' edition') .'</p></strong>
                <p>'. do_shortcode(PWECommonFunctions::languageChecker('[trade_fair_date]', '[trade_fair_date_eng]')) .'</p>
            </div>
            <a class="pwe-statistics__btn pwe-main-btn--secondary" href="' . PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') . '">' . PWECommonFunctions::languageChecker('Zarejestruj się', 'Registration') . '</a>
        </div>
        <div class="pwe-statistics__tiles">
            <div class="pwe-statistics__tiles-visitors pwe-statistics__border">
                <div class="pwe-statistics__tile-text">
                    <div class="pwe-statistics__tile-visitors">
                        <span class="pwe-statistics__tile-number" data-target="' . do_shortcode('[pwe_visitors]') . '">0</span>
                        <span class="pwe-statistics__tile-number-desc">'. PWECommonFunctions::languageChecker('Odwiedzający', 'Visitors') .'</span>
                    </div>
                    <hr class="pwe-statistics__tile-divider">
                    <div class="pwe-statistics__tile-visitors-abroad">
                        <span class="pwe-statistics__tile-number" data-target="' . do_shortcode('[pwe_visitors_foreign]') . '">0</span>
                        <span class="pwe-statistics__tile-number-desc">'. PWECommonFunctions::languageChecker('W tym z zagranicy', 'Including from abroad') .'</span>
                        <span class="pwe-statistics__tile-percent" data-target="' . $visitors_percent . '" data-suffix=" %">0</span>
                    </div>
                </div>
                <div class="pwe-statistics__icon-box">
                    ' . $svg_icon_visitors . '
                </div>
            </div>
            <div class="pwe-statistics__tiles-other">
                <div class="pwe-statistics__tile-exhibitors pwe-statistics__border">
                    <div class="pwe-statistics__tile-text">
                        <span class="pwe-statistics__tile-number" data-target="' . do_shortcode('[pwe_exhibitors]') . '">0</span>
                        <span class="pwe-statistics__tile-number-desc">'. PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitors') .'</span>
                    </div>
                    <div class="pwe-statistics__icon-box">
                        ' . $svg_icon_exhibitors . '
                    </div>
                </div>
                <div class="pwe-statistics__tile-area pwe-statistics__border">
                    <div class="pwe-statistics__tile-text">
                        <span class="pwe-statistics__tile-number" data-target="' . do_shortcode('[pwe_area]') . '" data-suffix="&nbsp;m<sup>2</sup>">0&nbsp;m<sup>2</sup>">0</span>
                        <span class="pwe-statistics__tile-number-desc">'. PWECommonFunctions::languageChecker('Powierzchni wystawienniczej', 'Exhibition space') .'</span>
                    </div>
                    <div class="pwe-statistics__icon-box">
                        ' . $svg_icon_area . '
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

return $output;
