<?php

$output = '';

// Styl
$output .= '<style>
    .pwe-conference__wrapper {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        max-width: 1200px;
        margin: 0 auto !important;
        align-items: stretch;
    }

    .pwe-conference__left {
        flex: 1;
        width: 50%;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 18px;
    }

    .pwe-conference__left img {
        border-radius: 30px;
        height: 90%;
        object-fit: cover;
    }

    .pwe-conference__right {
        flex: 1;
        width: 50%;
        max-width: 560px;
        padding: 24px 24px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 18px;
    }

    .pwe-conference__right-content {
        height: 90%;
        width: 100%;
    }

    .pwe-conference__title {
        font-size: clamp(1rem, 9vw, 8rem);
        text-align: center;
        font-weight: 900;
        line-height: 1;
        white-space: nowrap;
        width: 100%;
        overflow: hidden;
        margin-top: 0px;
        color: var(--accent-color);
        opacity: .5;
        text-align: center;
        text-transform: uppercase;
    }

    .pwe-conference__name {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #000;
    }

    .pwe-conference__desc {
        font-size: 16px;
        line-height: 1.75;
        font-weight: 400;
        color: #000;
        margin-bottom: 30px;
    }

    .pwe-conference__right-content h6 {
        text-align: center;
        display: block;
        margin: 12px auto 8px;
        font-size: 13px;
    }

    .pwe-conference__logo {
        margin-bottom: 20px;
    }

    .pwe-conference__logo img {
        max-width: 50%;
        margin: 0 auto;
        display: block;
    }

    .pwe-conference__buttons {
        display: flex;
        gap: 20px;
        margin-top: 20px;
    }

    .pwe-conference__buttons .btn {
        padding: 12px 24px;
        background-color: #4B1E17;
        color: #fff;
        border: none;
        border-radius: 10px;
        text-decoration: none;
        font-weight: bold;
    }

    .pwe-conference__btn {
        background: var(--accent-color);
        color: white !important;
        min-width: 200px;
        padding: 10px 20px;
        display: block;
        margin: 0 auto;
        border-radius: 10px;
        margin-top: 18px;
        text-align: center;
        transition: all 0.3s ease-in-out;
        font-weight: 500;
    }

    .pwe-conference__buttons .btn.secondary {
        background-color: #2E2E2E;
    }

    .pwe-conference__logotypes {
        display: flex;
        flex-wrap: nowrap;
        gap: 20px;
        overflow-x: auto;
        align-items: center;
        justify-content: center;
        margin-top: 20px;
    }

    .pwe-conference__logotypes img {
        height: 40px;
        object-fit: contain;
        flex-shrink: 0;
    }

    @media (max-width: 768px) {
        .pwe-conference__wrapper {
            flex-direction: column;
        }

        .pwe-conference__left, .pwe-conference__right {
            flex: 1 1 100% !important;
            width: 100% !important;
            max-width: unset !important;
        }

        .pwe-conference__right {
            padding: 24px 0 0;
        }

        .pwe-conference__buttons {
            flex-direction: column;
        }
    }
</style>';

// Tytuł
$output .= '<div class="pwe-conference__title">' . PWECommonFunctions::languageChecker('KONFERENCJA', 'Conference') . '</div>';

// Layout
$output .= '<div class="pwe-conference__wrapper">
    <div class="pwe-conference__left">
        <img src="/doc/new_template/conference_img.webp" alt="Publiczność konferencji">
        <a href="' . PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') . '" class="pwe-conference__btn">' . PWECommonFunctions::languageChecker('WEŹ UDZIAŁ', 'TAKE PART') . '</a>
    </div>
    <div class="pwe-conference__right">
        <div class="pwe-conference__right-content">
            <div class="pwe-conference__name">' . $name . '</div>
            <div class="pwe-conference__desc">' . $desc . '</div>
            <div class="pwe-conference__logo">
                <img src="/doc/kongres-color.webp" alt="Congress logo">
            </div>';

            // Logotypy z CAP
            $logotypy = [];

            $cap_logotypes_data = PWECommonFunctions::get_database_logotypes_data();

            if (!empty($cap_logotypes_data)) {
                $dozwolone_typy = [
                    'partner-targow',
                    'patron-medialny',
                    'partner-strategiczny',
                    'partner-honorowy',
                    'principal-partner',
                    'industry-media-partner',
                    'partner-branzowy',
                    'partner-merytoryczny'
                ];

                foreach ($cap_logotypes_data as $logo_data) {
                    if (in_array($logo_data->logos_type, $dozwolone_typy)) {
                        $logotypy[] = 'https://cap.warsawexpo.eu/public' . $logo_data->logos_url;
                    }
                }
            }

            if (!empty($logotypy)) {
                $output .= '<h6>' . PWECommonFunctions::languageChecker('PATRONI I PARTNERZY', 'PATRONS AND PARTNERS') . '</h6>';
                $output .= '<div class="conf-short-info-default">';
                $output .= '<div class="swiper">';
                $output .= '<div class="swiper-wrapper">';

                foreach ($logotypy as $logo) {
                    $output .= '<div class="swiper-slide">';
                    $output .= '<img id="' . pathinfo($logo)['filename'] . '" data-no-lazy="1" src="' . htmlspecialchars($logo, ENT_QUOTES, 'UTF-8') . '" alt="' . pathinfo($logo)['filename'] . '"/>';
                    $output .= '</div>';
                }

                $output .= '</div>'; // .swiper-wrapper
                $output .= '<div class="swiper-pagination"></div>';
                $output .= '</div>'; // .swiper
                $output .= '</div>'; // .conf-short-info-default

                $swiper_file = trailingslashit(WP_PLUGIN_DIR) . 'PWElements/scripts/swiper.php';

                if ( file_exists($swiper_file) ) {
                    require_once $swiper_file;
                } else {
                    die('Nie znaleziono pliku klasy: ' . $swiper_file);
                }
                $output .= PWESwiperScripts::swiperScripts('conf-short-info-default', '.conf-short-info-default', 'true', '', '', null,
                    rawurlencode(json_encode([
                        ['breakpoint_width' => 320, 'breakpoint_slides' => 2],
                        ['breakpoint_width' => 560, 'breakpoint_slides' => 3],
                        ['breakpoint_width' => 960, 'breakpoint_slides' => 4],
                    ]))
                );

            }

        $output .= '
        </div>
        <a href="' . PWECommonFunctions::languageChecker('/wydarzenia/', '/en/conferences/') . '" class="pwe-conference__btn secondary">' . PWECommonFunctions::languageChecker('DOWIEDZ SIĘ WIĘCEJ', 'FIND OUT MORE') . '</a>
    </div>
</div>';

return $output;
