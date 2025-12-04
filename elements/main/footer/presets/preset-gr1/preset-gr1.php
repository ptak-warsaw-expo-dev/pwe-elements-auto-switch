<?php

$output = '
<div id="pweFooter" class="pwe-footer">
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
