<?php

$output = '';

// Layout
$output .= '
<div id="pweAbout" class="pwe-about">
    <div class="pwe-about__content">
        <h2 class="pwe-about__title pwe-main-title">' . PWE_Functions::multi_translation("about_fair") . '</h2>
        <h4 class="pwe-about__subtitle pwe-main-subtitle">' . $title . '</h4>
        <div class="pwe-about__desc pwe-main-desc">' . $desc . '</div>
        <a class="pwe-about__btn pwe-main-btn--primary" href="' . PWE_Functions::multi_translation("reg_url") . '">' . ($b2c ? PWE_Functions::multi_translation("buy_ticket") : PWE_Functions::multi_translation("registration")) . '</a>
    </div>
    <div class="pwe-about__media">';
        if (count($logotypes) == 9 && !empty($logotypes)) {
            $output .= '
            <h4 class="pwe-about__media-title">' . PWE_Functions::multi_translation("exhibitors") . '</h4>
            <div id="pweAboutLogos" class="pwe-about__logos pwe-container-logotypes">';

                foreach($logotypes as $logo) {
                    $output .= '<img class="pwe-about__logo logo-placeholder" src="'. $logo .'" alt="exhibitor">';
                } 

            $output .= '
            </div>';
        } else {
            $output .= $img;
        }
        $output .= '
    </div>
</div>';

return $output;