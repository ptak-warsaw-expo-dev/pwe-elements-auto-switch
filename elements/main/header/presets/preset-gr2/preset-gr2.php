<?php

$output = '
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
                    <p>'. PWE_Functions::multi_translation('warsaw_poland') .'</p>
                </div>

                <div class="pwe-header__buttons">
                    <div class="pwe-btn-container header-button">
                        <a class="pwe-link pwe-btn btn-visitors" 
                           href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                           alt="'. PWE_Functions::multi_translation('link_to_registration') .'">
                            '. PWE_Functions::multi_translation('register') .'<br>
                            '. PWE_Functions::multi_translation('free_ticket') .' 
                            <span class="btn-angle-right">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                                </svg>
                            </span>
                        </a>
                    </div>
                    <div class="pwe-btn-container header-button">
                        <a class="pwe-link pwe-btn btn-exhibitors" 
                           href="'. PWECommonFunctions::languageChecker('/zostan-wystawca/', '/en/become-an-exhibitor/') .'" 
                           alt="'. PWE_Functions::multi_translation('link_to_registration_exh') .'">
                            '. PWE_Functions::multi_translation('become_exhibitor') .' 
                            <span class="btn-angle-right">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                                </svg>
                            </span>
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
