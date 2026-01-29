<?php

$name        = PWE_Functions::lang_pl() ? do_shortcode('[trade_fair_name]')      : do_shortcode('[trade_fair_name_eng]');
$description = PWE_Functions::lang_pl() ? do_shortcode('[trade_fair_desc]')      : do_shortcode('[trade_fair_desc_eng]');
$url         = 'https://' . do_shortcode('[trade_fair_domainadress]') . (PWE_Functions::lang_pl() ? '' : '/en/');
$offerName   = PWE_Functions::lang_pl() ? 'Rejestracja' : 'Registration';
$offerUrl    = $url . (PWE_Functions::lang_pl() ? '/rejestracja/' : 'registration/');

$output .= '
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const uncodeNavMenu = document.querySelector("#masthead");
        const pweNavMenu = document.querySelector("#pweMenuAutoSwitch");

        // Top main menu "For exhibitors"
        const mainMenu = pweNavMenu ? document.querySelector(".pwe-menu-auto-switch__nav") : document.querySelector("ul.menu-primary-inner");
        const secondChild = mainMenu.children[1];
        const dropMenu = pweNavMenu ? secondChild.querySelector(".pwe-menu-auto-switch__submenu") : secondChild.querySelector("ul.drop-menu");

        // Create new element li
        const newMenuItem = document.createElement("li");
        newMenuItem.id = pweNavMenu ? "" : "menu-item-99999";
        newMenuItem.className = pweNavMenu ? "pwe-menu-auto-switch__submenu-item" : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999";
        newMenuItem.innerHTML = `<a title="'. (get_locale() == "pl_PL" ? 'Zostań agentem' : 'Become an agent') .'" target="_blank" href="https://warsawexpo.eu'. (get_locale() == "pl_PL" ? '/formularz-dla-agentow/' : '/en/forms-for-agents/') .'">'. (get_locale() == "pl_PL" ? 'Zostań agentem' : 'Become an agent') .'</a>`;

        // Add new element as second in the list
        if (dropMenu && dropMenu.children.length > 1) {
            dropMenu.insertBefore(newMenuItem, dropMenu.children[1]);
        } else {
            dropMenu.appendChild(newMenuItem);
        }';
        
        $output .= '
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