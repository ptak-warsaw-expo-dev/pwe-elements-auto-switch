<?php

$output = '
<div id="pweSummary" class="pwe-summary">
    <h2 class="pwe-main-title">'. PWE_Functions::multi_translation("summary_title") .'</h2>

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
            <h2 class="pwe-summary__info-title">'. PWE_Functions::multi_translation("info_title") .'</h2>
            <p class="pwe-summary__info-description">'. PWE_Functions::multi_translation("info_desc") .'</p>
            </div>
            <a href="'. PWE_Functions::multi_translation("calendar_link") .'" target="_blank">
                <div class="pwe-summary__calendar-link">'. PWE_Functions::multi_translation("calendar_link_name") .'</div>
            </a>
        </div>
        </div>

    </div>

    <div class="pwe-summary__stats">
        <div class="pwe-summary__stat">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/exhibitors.webp" alt="Ikona wystawców" class="pwe-summary__stat-icon" />
            <h2 class="pwe-summary__stat-value">20000</h2>
            <p class="pwe-summary__stat-description">'. PWE_Functions::multi_translation("exhibitor_year") .'</p>
        </div>
        <div class="pwe-summary__stat">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/visitors.webp" alt="Ikona odwiedzających" class="pwe-summary__stat-icon" />
            <h2 class="pwe-summary__stat-value">2mln+</h2>
            <p class="pwe-summary__stat-description">'. PWE_Functions::multi_translation("visitors_year") .'</p>
        </div>
        <div class="pwe-summary__stat">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/fairs.webp" alt="Ikona targów" class="pwe-summary__stat-icon" />
            <h2 class="pwe-summary__stat-value">150+</h2>
            <p class="pwe-summary__stat-description">'. PWE_Functions::multi_translation("fairs_year") .'</p>
        </div>
        <div class="pwe-summary__stat">
            <img src="/wp-content/plugins/pwe-media/media/numbers-el/area.webp" alt="Ikona powierzchni" class="pwe-summary__stat-icon" />
            <h2 class="pwe-summary__stat-value">153k</h2>
            <p class="pwe-summary__stat-description">'. PWE_Functions::multi_translation("exhibition_space") .'</p>
        </div>
    </div>
</div>

<script>
const pweSummaryUfi = {
    ufi_info_text: ' . json_encode(PWE_Functions::multi_translation("ufi_info_text")) . '
};
</script>';  

return $output;