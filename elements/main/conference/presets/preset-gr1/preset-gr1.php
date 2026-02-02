<?php

$output = '';

// Layout
$output .= '<div class="pwe-conference__wrapper">
    <div class="pwe-conference__left">
        <img src="'. $conference_img .'" alt="Publiczność konferencji">
    </div>
    <div class="pwe-conference__right">
        <div class="pwe-conference__right-content">
            <h2 class="pwe-conference__title">' . PWE_Functions::languageChecker('Konferencja', 'Conference') . '</h2>
            <h4 class="pwe-conference__name">' . $name . '</h4>
            <div class="pwe-conference__desc">' . $desc . '</div>
            <div class="pwe-conference__btn-container">
                <a href="' . PWE_Functions::languageChecker('/wydarzenia/', '/en/conferences/') . '" class="pwe-conference__btn">' . PWE_Functions::languageChecker('Szczegóły', 'Details') . '</a>
                <a href="' . PWE_Functions::languageChecker('/rejestracja/', '/en/registration/') . '" class="pwe-conference__btn pwe-conference__btn_accent">' . PWE_Functions::languageChecker('Zarejestruj się', 'Registration') . '</a>
            </div>
        </div>
        <div class="pwe-conference__logo">
            <img src="/doc/kongres-color.webp" alt="Congress logo">
        </div>
    </div>
</div>';

return $output;
