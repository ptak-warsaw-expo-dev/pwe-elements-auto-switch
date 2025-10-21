<?php

$output = '
<style>
    .pwe-header__container {
        padding: 36px;
        position: relative;
        overflow: hidden; 
    }
    .pwe-header__wrapper { 
        z-index:1; 
        display: flex;
        justify-content: center;
        gap: 36px;
    }
    .pwe-header__column.pwe-header__content-column {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .pwe-header__content-wrapper {
        display: flex;
        flex-direction: column;
    }
    .pwe-header__logo {
        max-width: 400px !important;
    }
    .pwe-header__tile {
        background: transparent;
        max-width: 700px;
        // backdrop-filter: blur(10px);
        padding: 36px;
        // border-radius: 12px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .pwe-header__main-content-block {
        text-align: center;
    }
    .pwe-header__title h1 {
        font-size: 32px;
        text-align: center;
        text-transform: uppercase;
    }
    .pwe-header__title h1, 
    .pwe-header__title p {
        margin-top: 18px;
        color: white;
        text-shadow: 0 0 6px black;
    }
    .pwe-header__edition {
        text-align: center;
    }  
    .pwe-header__edition span {
        background: white;
        color: black;
        border-radius: 8px;
        text-transform: uppercase;
        font-weight: 600;
        font-size: 30px;
        padding: 6px 8px;
        width: fit-content;
    }
    .pwe-header__date-block {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .pwe-header__date-block h2 {
        margin-top: 12px;
        font-size: 56px;
        font-weight: 600;
        letter-spacing: 5px;
        text-align: center;
        color: white;
        text-shadow: 0 0 6px black;
    }
    .pwe-header__date-block p {
        font-size: 30px;
        font-weight: 500;
        margin: 0;
        color: white;
        text-shadow: 0 0 6px black;
    }



    .pwe-header__bottom {
        margin: 0 auto;
    }
    .pwe-header .pwe-btn-container {
        position: relative;
        width: 300px;
        height: 60px;
        padding: 0;
    }
    .pwe-header .pwe-btn {
        background: var(--main2-color);
        color: white !important;
        width: 100%;
        height: 100%;
        transform: scale(1) !important;
        transition: .3s ease;
        font-size: 16px;
        font-weight: 600;
        padding: 6px 18px !important;
        letter-spacing: 0.1em;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-transform: uppercase;
        border-radius: 10px !important;
    }
    .pwe-header .pwe-btn-container .btn-small-text {
        font-size: 10px;
    }
    .pwe-header .pwe-btn-container .btn-angle-right {
        color: white;
        position: absolute;
        right: 25px;
        top: -30%;
        height: 35px;
        font-size: 72px;
        transition: .3s ease;
    }
    .pwe-header .pwe-btn-container:hover .btn-angle-right {
        right: 20px;
    }
    .pwe-header .pwe-btn:hover {
        background: var(--main2_darker_color);
    }





    
    .pwe-header__partners {
        position: absolute;
        top: 50%;
        transform: translate(0, -50%);
        right: 18px;
        display: flex;
        justify-content: center;
        flex-direction: column;
        background-color: white;
        border-radius: 18px;
        padding: 10px;
        gap: 18px;
        z-index: 1;
    }





    .video-background {
        position: relative;
    }
    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0) 100%);
    }
    .pwe-header .video-background {
        position: absolute;
        top: 0;
        left: 0; 
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 0;
        pointer-events: none;
    }
    .pwe-header .video-background iframe {
        position: absolute;
        top: -36vh;
        left: 0;
        width: 100vw;
        height: 160vh;
        object-fit: cover;
        z-index: -1;
        pointer-events: none;
    }
    .video-background video {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: translate(-50%, -50%);
    }




    @media(max-width:1350px){
        .pwe-header .video-background iframe {
            width: 100vw;
            height: 100vh;
            top: -9vh;
        }
    }
    @media(max-width: 1200px) {
        .pwe-header__wrapper { 
            flex-direction: column;
            align-items: center;
        }
        .pwe-header__partners {
            position: static;
            top: unset;
            right: unset;
            transform: unset;
            flex-direction: row;
            flex-wrap: wrap;
        }
        .pwe-header__partners-items {
            flex-direction: row;
            flex-wrap: wrap;
        }
    }
    @media(max-width:960px){
        .pwe-header__tile {
            background: #00000099;
            backdrop-filter: blur(10px);
            border-radius: 12px;
        }
        .pwe-header__content-wrapper { 
            gap: 36px;
        }
        .pwe-header__title h1 {
            font-size: 22px;
        }
        .pwe-header__date-block h2 {
            font-size: 34px;
        }
        .pwe-header__date-block p {
            display: none;
        }
        .video-background {
            display:none !important;
        }
        .pwe-header__partners-items {
            flex-wrap: wrap !important;
        }
        .pwe-header__partners-container,
        .pwe-header__partners-title h3 {
            max-width: 100% !important;
        }
    }
    @media(max-width:570px) {
        .pwe-header__column.pwe-header__content-column {
            width: 100%;
        }
        .pwe-header__content-wrapper {
            width: 100%;
        }
        .pwe-header__date-block {
            justify-content: flex-start;
        }
        .pwe-header__tile h1 {
            font-size: 22px;
        }
        .pwe-header__edition span {
            padding: 7px 21px;
            font-size: 14px;
        }
        .pwe-header__tile {
            padding: 18px;
            width: 100%;
        }
        .pwe-header__date-block h2 {
            font-size: 30px;
        }
        .pwe-header__column.pwe-header__content-column {
            align-items: center;
        }
    }
    @media(max-width:450px) {
        .pwe-header__date-block h2 {
            font-size: 24px;
        }
    }
</style>';

$output .= '
<div id="pweHeader" class="pwe-header">
    <div style="background-image: url('. $background_header .');"  class="pwe-header__container pwe-header__background">
        
        <div class="pwe-header__wrapper">

            <div class="pwe-header__column pwe-header__content-column">
                <div class="pwe-header__content-wrapper">
                    <div class="pwe-header__tile">
                        <div class="pwe-header__main-content-block">
                            <img class="pwe-header__logo" src="'. PWECommonFunctions::languageChecker('/doc/logo.webp', '/doc/logo-en.webp') .'" alt="logo-'. $trade_fair_name .'">
                            <div class="pwe-header__edition"><p><span>'. $trade_fair_edition .'</span></p></div>
                            <div class="pwe-header__title">
                                <h1>'. $trade_fair_desc .'</h1>
                            </div>
                        </div>
                        <div class="pwe-header__date-block">
                            <h2>'. $trade_fair_dates_custom_format .'</h2>
                            <p>'. PWECommonFunctions::languageChecker(' Warszawa', ' Warsaw') .'</p>
                        </div>
                    </div>
                    <div class="pwe-header__bottom">
                        <div id="pweBtnRegistration" class="pwe-btn-container header-button">
                            <a 
                                class="pwe-link pwe-btn" 
                                href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                                alt="'. PWECommonFunctions::languageChecker('link do rejestracji', 'link to registration') .'">
                                '. PWECommonFunctions::languageChecker('Zarejestruj się', 'Register') .'
                                <span class="btn-small-text" style="display: block; font-weight: 300;">
                                    '. PWECommonFunctions::languageChecker('Odbierz darmowy bilet', 'Get a free ticket') .'
                                </span>
                            </a>
                            <span class="btn-angle-right">&#8250;</span>
                        </div>
                    </div>
                </div>
            </div>';

            // Partners widget --------------------------------------------------------------------------------------<
            $cap_logotypes_data = PWECommonFunctions::get_database_logotypes_data();
            if (!empty($cap_logotypes_data)) { 
                require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'widgets/partners.php';
            }
            
            $output .= '

            <div class="video-background">
                <div class="video-overlay"></div>
                <video autoplay="" muted="" loop="" preload="auto" class="bg-video" src="/doc/header.mp4"></video>
            </div>
        </div>
    </div>
</div>';

return $output;
