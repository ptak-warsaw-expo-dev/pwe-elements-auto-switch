<?php
$desc_length = mb_strlen($trade_fair_desc, 'UTF-8');

$output = '';

if ($desc_length < 30) {
    $output .= '
    <style>
        @media(min-width:961px) {
            .pwe-element-auto-switch .pwe-header__title h1 {
                font-size: 72px !important;
            }
        }
    </style>';
} else if ($desc_length > 30 && $desc_length < 60) {
    $output .= '
    <style>
        @media(min-width:961px) {
            .pwe-element-auto-switch .pwe-header__title h1 {
                font-size: 52px !important;
            }
        }
    </style>';
} else {
    $output .= '
    <style>
        @media(min-width:961px) {
            .pwe-element-auto-switch .pwe-header__title h1 {
                font-size: 40px !important;
            }
        }
    </style>';
}

$output .= '
<header id="pweHeader" class="pwe-header">

    <div class="video-background">
        <div class="video-overlay-left"></div>
        <div class="video-overlay-bottom"></div>
        <video autoplay muted loop playsinline class="bg-video">
            <source src="https://motorcycleshow.pl/doc/header.mp4" type="video/mp4">
        </video>
    </div>

    <div class="pwe-header__container">
        <div class="pwe-header__wrapper">
            <div class="pwe-header__column">

                <div class="pwe-header__edition">
                    <span>'. $trade_fair_edition .' • '. PWE_Functions::multi_translation('warsaw_poland') . ' | ' . $trade_fair_date .'</span>
                </div>

                <div class="pwe-header__title">
                    <h1>'. $trade_fair_desc .'</h1>
                </div>

                <div class="pwe-header__date">
                    <h2>'. PWE_Functions::multi_translation('europe_text') .'</h2>
                </div>

                <div class="pwe-header__buttons">

                    <div class="pwe-btn-container">
                        <a class="pwe-btn btn-visitors"
                           href="'. PWE_Functions::multi_translation('link_to_registration') .'"
                           alt="'. PWE_Functions::multi_translation('link_to_registration_text') .'">
                            '. ($b2c ? PWE_Functions::languageChecker('Kup bilet teraz', 'Buy ticket now') : PWE_Functions::multi_translation('register') . ' ' . PWE_Functions::multi_translation('free_ticket')).'
                        </a>
                    </div>

                    <div class="pwe-btn-container hidden-mobile">
                        <a class="pwe-btn btn-exhibitors"
                           href="'. PWE_Functions::multi_translation('link_to_registration_exh') .'"
                           alt="'. PWE_Functions::multi_translation('link_to_registration_text_exh') .'">
                            '. PWE_Functions::multi_translation('become_exhibitor') .'
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</header>';

return $output;