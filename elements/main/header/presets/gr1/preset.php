<?php

$trade_fair_dates_custom_format = str_replace("|", " | ", $trade_fair_dates_custom_format);

$output = '
<div id="pweHeader" class="pwe-header">
    <div class="pwe-header__container pwe-header__background" style="background-image: url(doc/background.webp);">

        <div class="pwe-header__wrapper">

            <div class="pwe-header__column pwe-header__content-column">
                <div class="pwe-header__content-wrapper">
                    <div class="pwe-header__tile">
                        <div class="pwe-header__main-content-block">
                            <img class="pwe-header__logo" src="'. (PWE_Functions::lang_pl() ? '/doc/logo-color.webp' : '/doc/logo-color-en.webp') .'" alt="logo-'. $trade_fair_name .'">
                            <div class="pwe-header__edition"><p><span>'. $trade_fair_edition .'</span></p></div>
                            <div class="pwe-header__title">
                                <h1>'. $trade_fair_desc .'</h1>
                            </div>
                        </div>
                        <div class="pwe-header__date-block">
                            <h2>
                                <span>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19 19H5V8H19M16 1V3H8V1H6V3H5C3.89 3 3 3.89 3 5V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19V5C21 4.46957 20.7893 3.96086 20.4142 3.58579C20.0391 3.21071 19.5304 3 19 3H18V1M17 12H12V17H17V12Z" fill="var(--accent-color)"/>
                                    </svg>
                                    '. $trade_fair_dates_custom_format .'
                                </span>
                                <span>
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.67206 4.09497C7.35381 2.43784 9.61843 1.50643 11.9794 1.50082C14.3404 1.49521 16.6095 2.41585 18.2991 4.06497H18.3011L18.3331 4.09497C21.8781 7.58197 21.8851 13.183 18.3751 16.635L12.7041 22.213C12.517 22.3971 12.265 22.5003 12.0026 22.5003C11.7401 22.5003 11.4881 22.3971 11.3011 22.213L5.63006 16.635C4.79749 15.821 4.13595 14.8489 3.6843 13.7757C3.23266 12.7025 3 11.5498 3 10.3855C3 9.22111 3.23266 8.06848 3.6843 6.99529C4.13595 5.92209 4.79749 4.94995 5.63006 4.13597L5.67206 4.09497ZM12.0001 6.49997C11.6061 6.49997 11.216 6.57757 10.852 6.72833C10.488 6.8791 10.1573 7.10008 9.87874 7.37865C9.60017 7.65723 9.37919 7.98794 9.22842 8.35192C9.07766 8.7159 9.00006 9.10601 9.00006 9.49997C9.00006 9.89394 9.07766 10.284 9.22842 10.648C9.37919 11.012 9.60017 11.3427 9.87874 11.6213C10.1573 11.8999 10.488 12.1208 10.852 12.2716C11.216 12.4224 11.6061 12.5 12.0001 12.5C12.7957 12.5 13.5588 12.1839 14.1214 11.6213C14.684 11.0587 15.0001 10.2956 15.0001 9.49997C15.0001 8.70432 14.684 7.94126 14.1214 7.37865C13.5588 6.81604 12.7957 6.49997 12.0001 6.49997Z" fill="var(--accent-color)"/>
                                    </svg>
                                    '. PWE_Functions::multi_translation('warsaw') .', '. PWE_Functions::multi_translation('poland') .'
                                </span>
                            </h2>
                        </div>
                        <div id="pweBtnRegistration" class="pwe-btn-container header-button">
                            <a
                                class="pwe-link pwe-btn"
                                href="'. PWE_Functions::languageChecker('/rejestracja/', '/en/registration/') .'"
                                alt="'. PWE_Functions::multi_translation('link_to_registration') .'">
                                '. PWE_Functions::multi_translation('register') .'
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M13 7L1 7M13 7L7 13M13 7L7 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>';

            // Partners widget --------------------------------------------------------------------------------------<
            $cap_logotypes_data = PWE_Functions::get_database_logotypes_data();
            if (!empty($cap_logotypes_data)) {
                require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'widgets/gr1.php';
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
