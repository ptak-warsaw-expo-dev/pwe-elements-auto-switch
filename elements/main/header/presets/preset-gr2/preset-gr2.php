<?php

$output = '
<style>
    .pwe-header {
        position: relative;
    }
    .pwe-header:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #ffffff;
        opacity: 20%;
        z-index: 1;
    }
    .pwe-header__container {
        padding: 36px;
        position: relative;
        overflow: hidden; 
    }
    .pwe-header__wrapper { 
        z-index: 1; 
        display: flex;
        justify-content: space-between;
    }
    .pwe-header__column {
        display: flex;
        flex-direction: column;
        justify-content: center;
        max-width: 700px;
    }

    .pwe-header__edition p,
    .pwe-header__title h1,
    .pwe-header__date h2,
    .pwe-header__date p {
        color: white;
        text-align: left;
        margin: 0;
    }
    .pwe-header__edition p {
        font-size: 24px;
        font-weight: 600;
        text-transform: uppercase; 
    }
    .pwe-header__title h1 {
        font-size: 64px;
        font-weight: 700;
        text-transform: uppercase;
    }
    .pwe-header__date h2 {
        font-size: 30px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .pwe-header__date p {
        font-size: 22px;
        font-weight: 600;
    }
    .pwe-header__buttons {
        display: flex;
        justify-content: left;
        gap: 10px;
    }
    .pwe-header .pwe-btn-container {
        position: relative;
    }
    .pwe-header .pwe-btn {
        display: flex;
        color: white !important;
        transform: scale(1);
        transition: .3s ease;
        font-size: 16px;
        align-items: flex-end;
        border-radius: 10px;
        padding: 30px 60px 18px 18px;
        font-weight: 600;
        gap: 10px;
        min-width: auto;
    }
    .pwe-header .pwe-btn.btn-visitors {
        background: var(--main2-color);
    }
    .pwe-header .pwe-btn.btn-exhibitors {
        background: var(--accent-color);
    }
    .pwe-header .pwe-btn-container .btn-angle-right {
        position: absolute;
        right: 36px;
        transition: .3s ease;
        transform: rotate(-45deg);
    }
    .pwe-header .pwe-btn-container:hover .btn-angle-right {
        right: 20px;
    }




    .pwe-header .video-background {
        position: relative;
    }
    .pwe-header .video-overlay {
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
    .pwe-header .video-background video {
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

    }
    @media(max-width:960px){
        .video-background {
            display:none !important;
        }
    }
    @media(max-width:570px) {
        
    }
    @media(max-width:450px) {
        
    }
</style>';

$output .= '
<div id="pweHeader" class="pwe-header">
    <div class="pwe-header__container pwe-header__background" style="background-image: url(doc/background.webp);">
        
        <div class="pwe-header__wrapper">
            <div class="pwe-header__column">

                <div class="pwe-header__edition">
                    <p><span>'. $trade_fair_edition .'</span></p>
                </div>
                <div class="pwe-header__title">
                    <h1>'. $trade_fair_desc .'</h1>
                </div>
                <div class="pwe-header__date">
                    <h2>'. $trade_fair_date .'</h2>
                    <p>'. PWECommonFunctions::languageChecker(' Warszawa', ' Warsaw') .'</p>
                </div>

                <div class="pwe-header__buttons">
                    <div class="pwe-btn-container header-button">
                        <a class="pwe-link pwe-btn btn-visitors" 
                           href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                           alt="'. PWECommonFunctions::languageChecker('link do rejestracji', 'link to registration') .'">
                                '. PWECommonFunctions::languageChecker('Zarejestruj się', 'Register') .'<br>
                                '. PWECommonFunctions::languageChecker('Odbierz darmowy bilet', 'Get a free ticket') .' 
                                <span class="btn-angle-right">🡲</span>
                        </a>
                    </div>
                    <div class="pwe-btn-container header-button">
                        <a class="pwe-link pwe-btn btn-exhibitors" 
                           href="'. PWECommonFunctions::languageChecker('/zostan-wystawca/', '/en/become-an-exhibitor/') .'" 
                           alt="'. PWECommonFunctions::languageChecker('link do rejestracji wystawcy', 'link to exhibitor registration') .'">
                                '. PWECommonFunctions::languageChecker('Zostań', 'Become') .'<br>
                                '. PWECommonFunctions::languageChecker('Wystawcą', 'an Exhibitor') .' 
                                <span class="btn-angle-right">🡲</span>
                        </a>
                    </div>

                </div>
            </div>';

            // Partners widget --------------------------------------------------------------------------------------<
            $cap_logotypes_data = PWECommonFunctions::get_database_logotypes_data();
            if (!empty($cap_logotypes_data)) { 
                require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'widgets/partners-gr2.php';
            }
            
            $output .= '
        </div>

        <div class="video-background">
            <div class="video-overlay"></div>
            <video autoplay muted loop preload="auto" class="bg-video">
                <source src="/doc/header.mp4" media="(min-width: 961px)">
            </video>
        </div>
        
    </div>
</div>';

return $output;
