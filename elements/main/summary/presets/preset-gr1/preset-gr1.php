<?php

$output = '';

$output = '
<div id="pweSummary" class="pwe-summary">
    <h2 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Ptak Warsaw Expo - łączymy świat biznesu', 'Ptak Warsaw Expo - we connect the world of business') .'</h2>

    <div class="pwe-summary__top">

        <div class="pwe-summary__logos">
            <img class="icon-info" src="/wp-content/plugins/pwe-media/media/numbers-el/info-icon.webp"/>
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/certifed.webp" alt="Certifed" class="pwe-summary__logo" />
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/ufi.webp" alt="Ufi" class="pwe-summary__logo" />
        </div>

        <div class="pwe-summary__info">
        <img src="/wp-content/plugins/pwe-media/media/stolica.webp" alt="Stolica" class="pwe-summary__info-bg" />

        <div class="pwe-summary__info-overlay">
            <div class="pwe-summary__info-item">
            <h2 class="pwe-summary__info-title">'. PWECommonFunctions::languageChecker('Stolica targów', 'The capital of trade fairs') .'</h2>
            <p class="pwe-summary__info-description">'. PWECommonFunctions::languageChecker('Targi / Konferencje / Eventy', 'Trade fairs / Conferences / Events') .'</p>
            </div>
            <a href="'. PWECommonFunctions::languageChecker('https://warsawexpo.eu/kalendarz-targowy/', 'https://warsawexpo.eu/en/fair-calendar/') .'" target="_blank">
                <div class="pwe-summary__calendar-link">'. PWECommonFunctions::languageChecker('Kalendarz targowy', 'Trade show calendar') .'</div>
            </a>
        </div>
        </div>

    </div>

    <div class="pwe-summary__stats">
        <div class="pwe-summary__stat">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/exhibitors.webp" alt="Ikona wystawców" class="pwe-summary__stat-icon" />
            <h2 class="pwe-summary__stat-value">20000</h2>
            <p class="pwe-summary__stat-description">'. PWECommonFunctions::languageChecker('Wystawców rocznie', 'Exhibitors per year') .'</p>
        </div>
        <div class="pwe-summary__stat">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/visitors.webp" alt="Ikona odwiedzających" class="pwe-summary__stat-icon" />
            <h2 class="pwe-summary__stat-value">2mln+</h2>
            <p class="pwe-summary__stat-description">'. PWECommonFunctions::languageChecker('Odwiedzających rocznie', 'Visitors per year') .'</p>
        </div>
        <div class="pwe-summary__stat">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/fairs.webp" alt="Ikona targów" class="pwe-summary__stat-icon" />
            <h2 class="pwe-summary__stat-value">150+</h2>
            <p class="pwe-summary__stat-description">'. PWECommonFunctions::languageChecker('Targów B2B rocznie', 'B2B trade fairs <br>per year') .'</p>
        </div>
        <div class="pwe-summary__stat">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/area.webp" alt="Ikona powierzchni" class="pwe-summary__stat-icon" />
            <h2 class="pwe-summary__stat-value">153k</h2>
            <p class="pwe-summary__stat-description">'. PWECommonFunctions::languageChecker('Powierzchni wystawienniczej m²', 'Exhibition space m²') .'</p>
        </div>
    </div>
</div>';  

return $output;