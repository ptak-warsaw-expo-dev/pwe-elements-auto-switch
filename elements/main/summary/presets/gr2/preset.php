<?php

$output = '
<div class="pwe-summary">

    <div class="pwe-summary__heading">
        <div class="pwe-summary__heading-text">
            <p class="pwe-main-subtitle">PTAK WARSAW EXPO</p>
            <h2 class="pwe-summary__title pwe-main-title">'. PWE_Functions::languageChecker('Największe Centrum Targowe w Europie Środkowo-Wschodniej', 'The largest trade fair center in Central and Eastern Europe') .'</h2>
        </div>

        <div class="pwe-summary__logos">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/certifed.webp" alt="Certified UFI Member">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/ufi.webp" alt="Ufi">
        </div>
    </div>

    <div class="pwe-summary__background mobile"></div>

    <div class="pwe-summary__background">
        <div class="pwe-summary__background-wrapper">
            <div class="pwe-summary__stats">
                <div class="pwe-summary__stat mobile">
                    <img src="/wp-content/plugins/pwe-media/media/numbers-el/certifed.webp" alt="Certified UFI Member">
                </div>
                
                <div class="pwe-summary__stat mobile">
                    <img src="/wp-content/plugins/pwe-media/media/numbers-el/ufi.webp" alt="Ufi">
                </div>

                <div class="pwe-summary__stat">
                    <img src="/wp-content/plugins/pwe-media/media/numbers-el/exhibitors.webp" alt="">
                    <h3>2000+</h3>
                    <p>'. PWE_Functions::languageChecker('Wystawców rocznie', 'Exhibitors per year') .'</p>
                </div>

                <div class="pwe-summary__stat">
                    <img src="/wp-content/plugins/pwe-media/media/numbers-el/visitors.webp" alt="">
                    <h3>1 mln+</h3>
                    <p>'. PWE_Functions::languageChecker('Odwiedzających rocznie', 'Visitors per year') .'</p>
                </div>

                <div class="pwe-summary__stat">
                    <img src="/wp-content/plugins/pwe-media/media/numbers-el/fairs.webp" alt="">
                    <h3>150+</h3>
                    <p>'. PWE_Functions::languageChecker('Targów B2B rocznie', 'B2B fairs per year') .'</p>
                </div>

                <div class="pwe-summary__stat">
                    <img src="/wp-content/plugins/pwe-media/media/numbers-el/area.webp" alt="">
                    <h3>153k</h3>
                    <p>'. PWE_Functions::languageChecker('Powierzchni m²', 'Area m²') .'</p>
                </div>
            </div>
        </div>
    </div>

</div>';  

return $output;