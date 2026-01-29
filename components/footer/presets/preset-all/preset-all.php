<?php

$output = '
<div id="pweFooter" class="pwe-footer pwe-component">
    <div class="pwe-footer__wrapper">';

    function generateFooterNavEl($locale, $menus) {
        $base_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $base_url .= "://".$_SERVER['HTTP_HOST'];
        $page_url = $locale == 'pl_PL' ? $base_url : $base_url . '/en';
        $logo_file_path = $locale == 'pl_PL' ? '/doc/logo' : '/doc/logo-en';
        $logo_url = file_exists($_SERVER['DOCUMENT_ROOT'] . $logo_file_path . '.webp') ? $logo_file_path . '.webp' : (file_exists($_SERVER['DOCUMENT_ROOT'] . $logo_file_path . '.png') ? $logo_file_path . '.png' : '');
    
        $menu_titles = $locale == 'pl_PL' ? [do_shortcode('[trade_fair_name]'), 'DLA ODWIEDZAJĄCYCH', 'DLA WYSTAWCÓW'] : [do_shortcode('[trade_fair_name_eng]'), 'FOR VISITORS', 'FOR EXHIBITORS'];
    
        $output = '
        <div class="pwe-footer__nav">
            <div class="pwe-footer__nav-wrapper">
                <div class="pwe-footer__nav-left-column">
                    <div class="pwe-footer__nav-logo-column">
                        <div class="pwe-footer__nav-logo-top"><a href="' . $page_url . '"><img src="/wp-content/plugins/pwe-media/media/logo_pwe_ufi.webp" alt="logo pwe & ufi"></a></div>
                        <div class="pwe-footer__nav-logo-bottom text-centered">
                            <a href="' . $page_url . '">
                                <span><img src="' . $logo_url . '" alt="logo-'. do_shortcode('[trade_fair_name]') .'"></span>
                            </a>
                        </div>
                    </div>
                </div>   
                <div class="pwe-footer__nav-right-column">';
    
                    foreach ($menus as $index => $menu) {
                        if (isset($menu)) { 
                            $output .= '
                            <!-- nav-column-item -->
                            <div class="pwe-footer__nav-column">
                                <h4><span class="pwe-uppercase">' . $menu_titles[$index] . '</span></h4>
                                <div class="pwe-footer__nav-links">' . wp_nav_menu(["menu" => $menu, "echo" => false]) . '</div>
                            </div>';
                        }
                    }
    
                $output .= '
                </div>
            </div>
        </div>';

        $socials = array(
            'facebook' => do_shortcode('[pwe_facebook]'),
            'instagram' => do_shortcode('[pwe_instagram]'),
            'linkedin' => do_shortcode('[pwe_linkedin]'),
            'youtube' => do_shortcode('[pwe_youtube]')
        );

        $output .= ' 
        <div class="pwe-footer__bottom pwe-footer__row">
            <div class="pwe-footer__bottom-wrapper">';
                    
                    if (!empty($socials)) {
                        $output .= '
                        <div class="pwe-footer__bottom-icons">
                            <ul class="pwe-footer__social">';
                                if (!empty($socials['facebook'])) {
                                    $output .= '
                                    <li class="pwe-footer__social-item-link social-icon facebook">
                                        <a href="'. esc_url($socials['facebook']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Facebook profile">
                                            <i class="fa fa-facebook-square"></i>
                                        </a>
                                    </li>';
                                }
                                if (!empty($socials['instagram'])) {
                                    $output .= '
                                    <li class="pwe-footer__social-item-link social-icon instagram">
                                        <a href="'. esc_url($socials['instagram']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Instagram profile">
                                            <i class="fa fa-instagram"></i>
                                        </a>
                                    </li>';
                                }
                                if (!empty($socials['linkedin'])) {
                                    $output .= '
                                    <li class="pwe-footer__social-item-link social-icon linkedin">
                                        <a href="'. esc_url($socials['linkedin']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Linkedin profile">
                                            <i class="fa fa-linkedin-square"></i>
                                        </a>
                                    </li>';
                                }
                                if (!empty($socials['youtube'])) {
                                    $output .= '
                                    <li class="pwe-footer__social-item-link social-icon youtube">
                                        <a href="'. esc_url($socials['youtube']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Youtube profile">
                                            <i class="fa fa-youtube-play"></i>
                                        </a>
                                    </li>';
                                }
                        
                                $output .= '
                            </ul>
                        </div>';
                    }
                
                    $output .= '
                <div class="pwe-footer__bottom-text">
                    <p>© '. do_shortcode('[trade_fair_actualyear]') .' Ptak Warsaw Expo Sp. z o.o.</p> 
                </div>
            </div>
        </div>';
    
        return $output;
    }
    
    if (get_locale() == 'pl_PL' && isset($menu_1_pl, $menu_2_pl, $menu_3_pl)) {
        $output .= generateFooterNavEl('pl_PL', [$menu_1_pl, $menu_2_pl, $menu_3_pl]);
    } elseif (isset($menu_1_en, $menu_2_en, $menu_3_en)) {
        $output .= generateFooterNavEl('en_US', [$menu_1_en, $menu_2_en, $menu_3_en]);
    }

    $output .= '  
    </div>
</div>';    

require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/script.php';

return $output;