<?php

$output = '';

$logo_long = '/doc/new_template/logo-long.webp';

// Styl
$output .= '<style>
    .pwe-about__container {
        display: flex;
        align-items: stretch;
        gap: 36px;
        border-radius: 18px;
    }
    .pwe-about__media, 
    .pwe-about__content {
        flex: 1 1 calc(50% - 18px);
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
    }
    .pwe-about__media h3 {
        display: block;
        margin: 10px auto;
        font-size: 20px;
        text-transform: uppercase;
    }
    .pwe-about__title {
        font-size: 29px;
    }
    .pwe-about__subtitle {
        font-size: 20px;
        font-weight: 800;
        margin: 0;
    }
    .pwe-about__btn {
        color: white !important;
        min-width: 200px;
        padding: 10px 20px;
        display: block;
        margin: 0 auto;
        border-radius: 10px;
        text-align: center;
        transition: all 0.3s ease-in-out;
        font-weight: 500;
        text-transform: uppercase;
    }
    .pwe-about__btn.pwe-about__btn--primary {
        background: var(--pwe-about__btn--primary-color);
    }
    .pwe-about__btn.pwe-about__btn--secondary {
        background: var(--pwe-about__btn--secondary-color);
    }
    .pwe-about__img {
        border-radius: 18px;
        width: 100%;
        height: 100%;
        object-fit: cover;
        margin: auto;
    }
    .pwe-about__logos-container {
        border-radius: 30px;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        -webkit-box-shadow: 4px 17px 30px -7px rgba(66, 68, 90, 1);
        -moz-box-shadow: 4px 17px 30px -7px rgba(66, 68, 90, 1);
        box-shadow: 4px 17px 30px -7px rgba(66, 68, 90, 1);
    }
    .pwe-about__logos {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }
    .pwe-about__logo {
        aspect-ratio: 3/2;
        object-fit: contain;
        width: calc(30% - 6px);
        height: auto;
    }
    @media(max-width:760px) {
        .pwe-about__container {
            flex-direction: column;
        }
    }
    @media(max-width:570px) {
        .pwe-about__media {
            align-items: center;
        }
        .pwe-about__logos {
            justify-content: center;
        }
        .pwe-about__title {
            text-align: center;
            font-size: 24px;
            width: 100%;
        }
        .pwe-about__subtitle {
            font-size: 18px;
            width: 100%;
            text-align: left;
        }
        .pwe-about__subtitle {
            font-size: 18px;
        }
        .pwe-about__logo {
            width: calc(48% - 6px);
        }
    }
</style>';

// Layout
$output .= '<div id="pwe-about" class="pwe-about">';
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $logo_long)) {
        $output .= '
            <div class="background-image">
                <img src="'. $logo_long .'" alt="Logo [trade_fair_name]"/>
            </div>';
    }
    $output .= '<div class="pwe-about__container">
        <div class="pwe-about__media">';
            if ($hasMany && !empty($logos)) {
                $output .= '<div class="pwe-about__exhibitors">
                    <h3 class="pwe-about__exhibitors-title">' . PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitors') . '</h3>
                    <div class="pwe-about__logos">';
                        foreach ($logos as $logo) {
                            $alt = $logo['name'] ?: 'Exhibitor logo';
                            $output .= '<img class="pwe-about__logo" data-no-lazy="1" src="' . esc_url($logo['url']) . '" alt="' . esc_attr($alt) . '">';
                        }
                    $output .= '</div>
                </div>';
            } else {
                $output .= $img;
            }
            $output .= '<a class="pwe-about__btn pwe-about__btn--primary" href="' . PWECommonFunctions::languageChecker('/galeria/', '/en/galerry/') . '">' . PWECommonFunctions::languageChecker('Galeria targów', 'Trade fair gallery') . '</a>';
        $output .= '</div>
        <div class="pwe-about__content">
            <div class="pwe-about__content-inner">
                <h2 class="pwe-about__title">' . $title . '</h2>
                <p class="pwe-about__desc">' . $desc . '</p>
            </div>
            <a class="pwe-about__btn pwe-about__btn--secondary" href="' . PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') . '">' . PWECommonFunctions::languageChecker('Dołacz do nas', 'Join us') . '</a>
        </div>
    </div>
</div>';

return $output;
