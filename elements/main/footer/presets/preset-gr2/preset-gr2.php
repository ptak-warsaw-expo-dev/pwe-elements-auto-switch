<?php

$socials = ot_get_option('_uncode_social_list');

$socials_cap = array(
    'facebook' => do_shortcode('[pwe_facebook]'),
    'instagram' => do_shortcode('[pwe_instagram]'),
    'linkedin' => do_shortcode('[pwe_linkedin]'),
    'youtube' => do_shortcode('[pwe_youtube]')
);

$output = '
<div id="pweFooter" class="pwe-footer">
    <div class="pwe-footer__wrapper">

        <div class="pwe-footer__top pwe-footer__row">
            <div class="pwe-footer__top-wrapper">
                <div class="pwe-footer__top-logotypes">
                    <a href="' . $page_url . '"><img src="/wp-content/plugins/pwe-media/media/logo_pwe_white.webp" alt="logo pwe"></a>
                    <a href="' . $page_url . '"><img src="/doc/'. (PWECommonFunctions::lang_pl() ? 'logo.webp' :'logo-en.webp') .'" alt="logo-'. do_shortcode('[trade_fair_name]') .'"></a>
                    <a href="' . $page_url . '"><img src="/wp-content/plugins/pwe-media/media/logo_ufi_white.webp" alt="logo ufi"></a>
                </div>
            </div>
        </div>';

        function generate_footer_nav_el($locale, $menus) {
            $menu_title = $locale == 'pl' ? [do_shortcode('[trade_fair_name]'), 'Odwiedzający', 'Wystawca'] : [do_shortcode('[trade_fair_name_eng]'), 'Visitor', 'Exhibitor'];
        
            $output = '
            <div class="pwe-footer__nav pwe-footer__row">

                <div class="pwe-footer__button">
                    <div class="pwe-btn-container footer-button">
                        <a class="pwe-link pwe-btn btn-visitors" 
                           href="'. (PWECommonFunctions::lang_pl() ? '/rejestracja/' : '/en/registration/') .'" 
                           alt="'. (PWECommonFunctions::lang_pl() ? 'link do rejestracji' : 'link to registration') .'">
                            '. (PWECommonFunctions::lang_pl() ? 'Zostań' : 'Become') .'<br>
                            '. (PWECommonFunctions::lang_pl() ? 'odwiedzajacym' : 'a Visitor') .'
                            <span class="btn-angle-right">🡲</span>
                        </a>
                    </div>
                </div>

                <div class="pwe-footer__nav-wrapper">
                    <div class="pwe-footer__nav-columns">';
        
                        foreach ($menus as $index => $menu) {
                            if (isset($menu)) { 
                                $output .= '
                                <!-- nav-column-item -->
                                <div class="pwe-footer__nav-column">
                                    <h4>' . $menu_title[$index] . '</h4>
                                    <div class="pwe-footer__nav-links">' . wp_nav_menu(["menu" => $menu, "echo" => false]) . '</div>
                                </div>';
                            }
                        }
        
                    $output .= '
                    </div>
                </div>

                <div class="pwe-footer__button">
                    <div class="pwe-btn-container footer-button">
                        <a class="pwe-link pwe-btn btn-exhibitors" 
                           href="'. (PWECommonFunctions::lang_pl() ? '/zostan-wystawca/' : '/en/become-an-exhibitor/') .'" 
                           alt="'. (PWECommonFunctions::lang_pl() ? 'link do rejestracji wystawcy' : 'link to exhibitor registration') .'">
                            '. (PWECommonFunctions::lang_pl() ? 'Zostań' : 'Become') .'<br>
                            '. (PWECommonFunctions::lang_pl() ? 'Wystawcą' : 'an Exhibitor') .' 
                            <span class="btn-angle-right">🡲</span>
                        </a>
                    </div>
                </div>

            </div>';
        
            return $output;
        }
    
        if (PWECommonFunctions::lang_pl() && isset($menu_1_pl, $menu_2_pl, $menu_3_pl)) {
            $output .= generate_footer_nav_el('pl', [$menu_1_pl, $menu_2_pl, $menu_3_pl]);
        } elseif (isset($menu_1_en, $menu_2_en, $menu_3_en)) {
            $output .= generate_footer_nav_el('en', [$menu_1_en, $menu_2_en, $menu_3_en]);
        }

        $output .= ' 
        <div class="pwe-footer__bottom pwe-footer__row">
            <div class="pwe-footer__bottom-wrapper">
                <div class="pwe-footer__bottom-icons">';
                    
                    if ((!empty($socials)) || !empty($socials_cap)) {
                        $output .= '<ul class="pwe-footer__social">';
                        if (!empty($socials_cap)) {
                            if (!empty($socials_cap['facebook'])) {
                                $output .= '
                                <li class="pwe-footer__social-item-link social-icon facebook">
                                    <a href="'. esc_url($socials_cap['facebook']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Facebook profile">
                                        <i class="fa fa-facebook-square"></i>
                                    </a>
                                </li>';
                            }
                            if (!empty($socials_cap['instagram'])) {
                                $output .= '
                                <li class="pwe-footer__social-item-link social-icon instagram">
                                    <a href="'. esc_url($socials_cap['instagram']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Instagram profile">
                                        <i class="fa fa-instagram"></i>
                                    </a>
                                </li>';
                            }
                            if (!empty($socials_cap['linkedin'])) {
                                $output .= '
                                <li class="pwe-footer__social-item-link social-icon linkedin">
                                    <a href="'. esc_url($socials_cap['linkedin']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Linkedin profile">
                                        <i class="fa fa-linkedin-square"></i>
                                    </a>
                                </li>';
                            }
                            if (!empty($socials_cap['youtube'])) {
                                $output .= '
                                <li class="pwe-footer__social-item-link social-icon youtube">
                                    <a href="'. esc_url($socials_cap['youtube']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Youtube profile">
                                        <i class="fa fa-youtube-play"></i>
                                    </a>
                                </li>';
                            }
                        } else if (!empty($socials)) {
                            foreach ($socials as $social) { 
                                $output .= '
                                <li class="pwe-footer__social-item-link social-icon '.esc_attr($social['_uncode_social_unique_id']).'">
                                    <a href="'.esc_url($social['_uncode_link']).'" class="social-menu-link" target="_blank">
                                        <i class="'.esc_attr($social['_uncode_social']).'"></i>
                                    </a>
                                </li>';
                            }
                        }
                        $output .= '</ul>';
                    }
                
                    $output .= '
                </div>
                <div class="pwe-footer__bottom-text">
                    <p>© 2025 Ptak Warsaw Expo Sp. z o.o.</p> 
                </div>
            </div>
        </div>';

    $output .= '  
    </div>
</div>';    

require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/script.php';

return $output;