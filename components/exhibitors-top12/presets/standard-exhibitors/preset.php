<?php

$output = '
<div id="pweExhibitorsTop12" class="pwe-exhibitors-top12 standard">
    <div class="pwe-exhibitors-top12-wrapper">
        <h2 class="pwe-exhibitors-top12-title">'. PWE_Functions::multi_translation("title") . ' ' . do_shortcode('[trade_fair_catalog_year]') . '</h2>
        <div class="pwe-exhibitors-top12-logos">';
            foreach ($exhibitors as $exhibitor) {
                $output .= '
                <div class="pwe-exhibitors-top12-logo">
                    <a href="'. $exhibitor['www'] .'" target="_blank">
                        <img src="'. $exhibitor['logo'] .'" alt="'. $exhibitor['name'] .' logo">
                    </a>
                </div>';
            }
        $output .= '
        </div>
    </div>
</div>';

return $output;
