<?php

$output = '';

// Layout
$output .= '<div class="pwe-conference__wrapper">
    <div class="pwe-conference__left">
        <img src="/doc/new_template/conference_img.webp" alt="Publiczność konferencji">
    </div>
    <div class="pwe-conference__right">
        <div class="pwe-conference__right-content">
            <h2 class="pwe-conference__title">' . PWECommonFunctions::languageChecker('Konferencja', 'Conference') . '</h2>
            <h4 class="pwe-conference__name">' . $name . '</h4>
            <div class="pwe-conference__desc">' . $desc . '</div>
            <div class="pwe-conference__btn-container">
                <a href="' . PWECommonFunctions::languageChecker('/wydarzenia/', '/en/conferences/') . '" class="pwe-conference__btn">' . PWECommonFunctions::languageChecker('Szczegóły', 'Details') . '</a>
                <a href="' . PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') . '" class="pwe-conference__btn pwe-conference__btn_accent">' . PWECommonFunctions::languageChecker('Zarejestruj się', 'Registration') . '</a>
            </div>
        </div>
        <div class="pwe-conference__logo">
            <img src="/doc/kongres-color.webp" alt="Congress logo">
        </div>
    </div>
</div>';

return $output;
