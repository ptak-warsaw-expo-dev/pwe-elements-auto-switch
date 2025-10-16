<?php

$output = '';

// Layout
$output .= '
<div id="pweAbout" class="pwe-about">
    <div class="pwe-about__content">
        <h2 class="pwe-about__title pwe-main-title">' . PWECommonFunctions::languageChecker('O targach', 'About the fair') . '</h2>
        <h4 class="pwe-about__subtitle pwe-main-subtitle">' . $title . '</h4>
        <div class="pwe-about__desc pwe-main-desc">' . $desc . '</div>
        <a class="pwe-about__btn pwe-main-btn--primary" href="' . PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') . '">' . PWECommonFunctions::languageChecker('Zarejestruj się', 'Registration') . '</a>
    </div>
    <div class="pwe-about__media">';
        if ($hasMany && !empty($logos)) {
            $output .= '
            <h4 class="pwe-about__media-title">' . PWECommonFunctions::languageChecker('Top Wystawcy', 'Top Exhibitors') . '</h4>
            <div id="pweAboutLogos" class="pwe-about__logos pwe-container-logotypes" data-logos=\'' . $logos_json . '\'>';

            for ($i = 0; $i < 9; $i++) {
                $output .= '<img class="pwe-about__logo logo-placeholder"
                    src="' . $logoUrl . '" alt="" style="visibility:hidden;opacity:0">';
            }

            $output .= '</div>';
        } else {
            $output .= $img;
        }
        $output .= '</div>
</div>';

return $output;