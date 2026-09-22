<?php

$name        = PWE_Functions::lang_pl() ? do_shortcode('[trade_fair_name]')      : do_shortcode('[trade_fair_name_eng]');
$description = PWE_Functions::lang_pl() ? do_shortcode('[trade_fair_desc]')      : do_shortcode('[trade_fair_desc_eng]');
$url         = 'https://' . do_shortcode('[trade_fair_domainadress]') . (PWE_Functions::lang_pl() ? '' : '/en/');
$offerName   = PWE_Functions::lang_pl() ? 'Rejestracja' : 'Registration';
$offerUrl    = $url . (PWE_Functions::lang_pl() ? '/rejestracja/' : 'registration/');
        
$output .= '
<script>
document.addEventListener("DOMContentLoaded", function() {

    // -----------------------------
    // LANG CONFIG
    // -----------------------------
    const currentLang = "' . PWE_Functions::lang() . '";
    const catalogID = "' . do_shortcode('[trade_fair_catalog_id]') . '";

    const isAutoSwitchMenu = !!document.querySelector("#pweMenuAutoSwitch");

    // -----------------------------
    // MAIN LABELS
    // -----------------------------

    // Become an agent
    const agentTranslations = {
        pl: "Zostań agentem",
        en: "Become an agent",
        uk: "Стань агентом",
        cs: "Staňte se agentem",
        de: "Werden Sie Agent",
        it: "Diventa un agente",
        lt: "Tapk agentu",
        lv: "Kļūsti par aģentu",
        sk: "Staňte sa agentom",
        ro: "Deveniți agent",
        et: "Saage agendiks"
    };

    const agentLabel = agentTranslations[currentLang] || agentTranslations.en;

    const agentUrl = "https://warsawexpo.eu" + (
        currentLang === "pl"
            ? "/formularz-dla-agentow/"
            : "/en/forms-for-agents/"
    );

    // Medal ceremony
    const medalCeremonyTranslations = {
        pl: "Ceremonia medalowa",
        en: "Medal ceremony",
        uk: "Церемонія нагородження",
        cs: "Medailový ceremoniál",
        de: "Medaillenzeremonie",
        it: "Cerimonia di premiazione",
        lt: "Medalių įteikimo ceremonija",
        lv: "Medalju pasniegšanas ceremonija",
        sk: "Medailový ceremoniál",
        ro: "Ceremonia de premiere",
        et: "Medalite üleandmise tseremoonia"
    };

    const medalCeremonyLabel = medalCeremonyTranslations[currentLang] || medalCeremonyTranslations.en;

    const medalCeremonyUrl = 
        currentLang === "pl" ? "/ceremonia-medalowa/" : "/en/medal-ceremony/";

    // -----------------------------
    // HELPERS
    // -----------------------------
    function createMenuItem(className, title, text, href) {
        const li = document.createElement("li");
        if (className) li.className = className;

        li.innerHTML =
            "<a target=\"_blank\" title=\"" + title + "\" href=\"" + href + "\">" +
            text +
            "</a>";

        return li;
    }

    function insertAt(parent, element, index) {
        if (!parent) return;

        const ref = parent.children[index];
        if (ref) {
            parent.insertBefore(element, ref);
        } else {
            parent.appendChild(element);
        }
    }

    function insertPenultimate(parent, element) {
        if (!parent) return;

        const index = parent.children.length - 1;

        if (index > 0) {
            parent.insertBefore(element, parent.children[index]);
        } else {
            parent.appendChild(element);
        }
    }

    // -----------------------------
    // TOP MENU
    // -----------------------------
    const mainMenu = isAutoSwitchMenu
        ? document.querySelector(".pwe-menu-auto-switch__nav")
        : document.querySelector("ul.menu-primary-inner");

    if (mainMenu && mainMenu.children.length >= 2) {

        const secondItem = mainMenu.children[1];

        const dropMenu = secondItem
            ? (
                secondItem.querySelector(".pwe-menu-auto-switch__submenu") ||
                secondItem.querySelector("ul.drop-menu")
            )
            : null;

        if (dropMenu) {

            // -----------------------------
            // ITEM 1 - AGENT (always)
            // -----------------------------
            const agentItem = createMenuItem(
                isAutoSwitchMenu
                    ? "pwe-menu-auto-switch__submenu-item"
                    : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999",
                agentLabel,
                agentLabel,
                agentUrl
            );

            insertAt(dropMenu, agentItem, 1);

            // -----------------------------
            // ITEM 2 - MEDAL CEREMONY (only non PL/EN)
            // -----------------------------
            const medalCeremonyItem = createMenuItem(
                isAutoSwitchMenu
                    ? "pwe-menu-auto-switch__submenu-item"
                    : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999",
                medalCeremonyLabel,
                medalCeremonyLabel,
                medalCeremonyUrl
            );

            insertAt(dropMenu, medalCeremonyItem, 2);

            // -----------------------------
            // ITEM 3 - STORE (only non PL/EN)
            // -----------------------------
            if (currentLang !== "pl" && currentLang !== "en") {

                const storeItem = createMenuItem(
                    isAutoSwitchMenu
                        ? "pwe-menu-auto-switch__submenu-item"
                        : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999",
                    "PWE Sponsoring Store",
                    "PWE Sponsoring Store",
                    "https://warsawexpo.eu/" + (currentLang === "pl" ? "sklep/" : "en/store/")
                );

                insertAt(dropMenu, storeItem, 3);
            }

            if (catalogID) {
                // -----------------------------
                // EXTRA ITEMS (PL + EN only)
                // -----------------------------
                if (currentLang === "pl" || currentLang === "en") {

                    const instructionItem = createMenuItem(
                        isAutoSwitchMenu
                            ? "pwe-menu-auto-switch__submenu-item"
                            : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999",
                        currentLang === "pl" ? "Instrukcja aplikacji" : "Application instructions",
                        currentLang === "pl" ? "Instrukcja aplikacji" : "Application instructions",
                        "https://warsawexpo.eu/docs/' . PWE_Functions::languageChecker('Instrukcja-do-aplikacji.pdf', 'Instrukcja-do-aplikacji-EN.pdf') . '"
                    );

                    const loginItem = createMenuItem(
                        isAutoSwitchMenu
                            ? "pwe-menu-auto-switch__submenu-item"
                            : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999",
                        currentLang === "pl" ? "Login" : "Log in to the application",
                        currentLang === "pl" ? "Zaloguj się do aplikacji" : "Log in to the application",
                        "https://wystawca.exhibitorlist.warsawexpo.eu/login"
                    );

                    insertPenultimate(dropMenu, instructionItem);
                    insertPenultimate(dropMenu, loginItem);
                }
            }
        }
    }

    // -----------------------------
    // FOOTER MENU
    // -----------------------------
    const footerMenu = document.querySelector(".pwe-footer__nav-right-column");

    if (footerMenu && footerMenu.children.length >= 3) {

        const footerGroup = footerMenu.children[2];

        const footerList = footerGroup
            ? footerGroup.querySelector(".pwe-footer__nav-column .pwe-footer__menu")
            : null;

        if (footerList) {

            const footerAgent = createMenuItem(
                "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999",
                agentLabel,
                agentLabel,
                agentUrl
            );

            insertAt(footerList, footerAgent, 1);
        }
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