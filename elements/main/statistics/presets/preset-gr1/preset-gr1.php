<?php
            
$output = '
<div id="pweStatistics" class="pwe-statistics">
    <div class="pwe-statistics__wrapper">
        <div class="pwe-statistics__main-tile pwe-statistics__border">
            <h4 class="pwe-main-title">'. PWE_Functions::languageChecker('Estymacje', 'Estimates') .'</h4>
            <div class="pwe-statistics__main-text">
                <strong><p>' . do_shortcode('[pwe_edition]') . PWE_Functions::languageChecker('. edycja', ' edition') .'</p></strong>
                <p>'. do_shortcode(PWE_Functions::languageChecker('[trade_fair_date]', '[trade_fair_date_eng]')) .'</p>
            </div>
            <a class="pwe-statistics__btn pwe-main-btn--secondary" href="' . PWE_Functions::languageChecker('/rejestracja/', '/en/registration/') . '">' . PWE_Functions::languageChecker('Zarejestruj się', 'Registration') . '</a>
        </div>
        <div class="pwe-statistics__tiles">
            <div class="pwe-statistics__tiles-visitors pwe-statistics__border">
                <div class="pwe-statistics__tile-text">
                    <div class="pwe-statistics__tile-visitors">
                        <span class="pwe-statistics__tile-number" data-target="' . do_shortcode('[pwe_visitors]') . '">0</span>
                        <span class="pwe-statistics__tile-number-desc">'. PWE_Functions::languageChecker('Odwiedzający', 'Visitors') .'</span>
                    </div>
                    <hr class="pwe-statistics__tile-divider">
                    <div class="pwe-statistics__tile-visitors-abroad">
                        <span class="pwe-statistics__tile-number" data-target="' . do_shortcode('[pwe_visitors_foreign]') . '">0</span>
                        <span class="pwe-statistics__tile-number-desc">'. PWE_Functions::languageChecker('W tym z zagranicy', 'Including from abroad') .'</span>
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
                        <span class="pwe-statistics__tile-number-desc">'. PWE_Functions::languageChecker('Wystawcy', 'Exhibitors') .'</span>
                    </div>
                    <div class="pwe-statistics__icon-box">
                        ' . $svg_icon_exhibitors . '
                    </div>
                </div>
                <div class="pwe-statistics__tile-area pwe-statistics__border">
                    <div class="pwe-statistics__tile-text">
                        <span class="pwe-statistics__tile-number" data-target="' . do_shortcode('[pwe_area]') . '" data-suffix="&nbsp;m<sup>2</sup>">0&nbsp;m<sup>2</sup>">0</span>
                        <span class="pwe-statistics__tile-number-desc">'. PWE_Functions::languageChecker('Powierzchni wystawienniczej', 'Exhibition space') .'</span>
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
