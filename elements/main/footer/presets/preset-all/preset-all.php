<?php

$output = '
<div id="pweFooter" class="pwe-footer">
    <div class="pwe-footer__wrapper">';

    $menus = wp_get_nav_menus();

    foreach ($menus as $menu) {
        $menu_name_lower = strtolower($menu->name);
        $patterns = ['1 pl', '1 en', '2 pl', '2 en', '3 pl', '3 en'];
        foreach ($patterns as $pattern) {
            if (strpos($menu_name_lower, $pattern) !== false) {
                $varName = 'menu_' . str_replace(' ', '_', $pattern);
                // $menu_1_pl, $menu_2_pl ...
                $$varName = $menu->name;
                break;
            }
        }
    }      

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
        $output .= generateFooterNavEl('pl_PL', [$menu_1_pl, $menu_2_pl, $menu_3_pl], $footer_logo_color_invert);
    } elseif (isset($menu_1_en, $menu_2_en, $menu_3_en)) {
        $output .= generateFooterNavEl('en_US', [$menu_1_en, $menu_2_en, $menu_3_en], $footer_logo_color_invert);
    }

    $output .= '  
    </div>
</div>';    

$name        = PWECommonFunctions::lang_pl() ? do_shortcode('[trade_fair_name]')      : do_shortcode('[trade_fair_name_eng]');
$description = PWECommonFunctions::lang_pl() ? do_shortcode('[trade_fair_desc]')      : do_shortcode('[trade_fair_desc_eng]');
$url         = 'https://' . do_shortcode('[trade_fair_domainadress]') . (PWECommonFunctions::lang_pl() ? '' : '/en/');
$offerName   = PWECommonFunctions::lang_pl() ? 'Rejestracja' : 'Registration';
$offerUrl    = $url . (PWECommonFunctions::lang_pl() ? '/rejestracja/' : 'registration/');

$output .= '
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const uncodeNavMenu = document.querySelector("#masthead");
        const pweNavMenu = document.querySelector("#pweMenu");

        // Top main menu "For exhibitors"
        const mainMenu = pweNavMenu ? document.querySelector(".pwe-menu__nav") : document.querySelector("ul.menu-primary-inner");
        const secondChild = mainMenu.children[1];
        const dropMenu = pweNavMenu ? secondChild.querySelector(".pwe-menu__submenu") : secondChild.querySelector("ul.drop-menu");

        // Create new element li
        const newMenuItem = document.createElement("li");
        newMenuItem.id = pweNavMenu ? "" : "menu-item-99999";
        newMenuItem.className = pweNavMenu ? "pwe-menu__submenu-item" : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999";
        newMenuItem.innerHTML = `<a title="'. (get_locale() == "pl_PL" ? 'Zostań agentem' : 'Become an agent') .'" target="_blank" href="https://warsawexpo.eu'. (get_locale() == "pl_PL" ? '/formularz-dla-agentow/' : '/en/forms-for-agents/') .'">'. (get_locale() == "pl_PL" ? 'Zostań agentem' : 'Become an agent') .'</a>`;

        // Add new element as second in the list
        if (dropMenu && dropMenu.children.length > 1) {
            dropMenu.insertBefore(newMenuItem, dropMenu.children[1]);
        } else {
            dropMenu.appendChild(newMenuItem);
        }

        // --------------------------------------------

        // Bottom main menu "For exhibitors"
        const footerMenu = document.querySelector(".pwe-footer__nav-right-column");
        const footerThirdChild = footerMenu.children[2];
        const footerMenuChild = footerThirdChild.querySelector(".pwe-footer__nav-column .menu");

        // Create new element li
        const newFooterMenuItem = document.createElement("li");
        newFooterMenuItem.id = "menu-item-99999";
        newFooterMenuItem.className = "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999";
        newFooterMenuItem.innerHTML = `<a title="'. (get_locale() == "pl_PL" ? 'Zostań agentem' : 'Become an agent') .'" target="_blank" href="https://warsawexpo.eu'. (get_locale() == "pl_PL" ? '/formularz-dla-agentow/' : '/en/forms-for-agents/') .'">'. (get_locale() == "pl_PL" ? 'Zostań agentem' : 'Become an agent') .'</a>`;

        // Add new element as second in the footer list
        if (footerMenuChild && footerMenuChild.children.length > 1) {
            footerMenuChild.insertBefore(newFooterMenuItem, footerMenuChild.children[1]);
        } else {
            footerMenuChild.appendChild(newFooterMenuItem);
        }
    });
</script>';

$output .= '
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "ExhibitionEvent",
    "name": "'. $name .'",
    "url": "'. $url .'",
    "description": "'. $description .'",
    "image": "https://'. do_shortcode('[trade_fair_domainadress]') .'/doc/kafelek.jpg",
    "startDate": "'. do_shortcode('[trade_fair_datetotimer]') .'",
    "endDate": "'. do_shortcode('[trade_fair_enddata]') .'",
    "eventStatus": "https://schema.org/EventScheduled",
    "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
    "isAccessibleForFree": true,
    "organizer": {
        "@type": "Organization",
        "name": "Ptak Warsaw Expo",
        "url": "https://warsawexpo.eu",
        "sameAs": [
            "https://www.facebook.com/warsawexpo/",
            "https://www.instagram.com/ptak_warsaw_expo/",
            "https://www.linkedin.com/company/warsaw-expo/",
            "https://www.youtube.com/@ptakwarsawexpo2557"
        ]
    },
    "location": {
        "@type": "Place",
        "name": "Ptak Warsaw Expo",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Aleja Katowicka 62",
            "addressLocality": "Nadarzyn",
            "postalCode": "05-830",
            "addressCountry": "PL"
        }
    },
    "offers": {
        "@type": "Offer",
        "name": "'. $offerName .'",
        "price": "0",
        "priceCurrency": "PLN",
        "url": "'. $offerUrl .'",
        "availability": "https://schema.org/InStock"
    }
}
</script>';

return $output;
