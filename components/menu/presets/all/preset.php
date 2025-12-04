<?php

// Function to display submenu
if (!function_exists('display_sub_menu')) {
    function display_sub_menu($parent_id, $menu_items, $depth = 1) {
        // Maximum nesting depth
        $max_depth = 10;

        // Stop recursion after reaching the maximum depth
        if ($depth > $max_depth) { 
            return '<script>console.error("Maximum submenu depth reached for parent ID: '. $parent_id .'");</script>';
        }

        // Filter children for a given parent
        $children = array_filter($menu_items, function($item) use ($parent_id) { 
            return $item->menu_item_parent == $parent_id;
        });

        if (!empty($children)) {
            $output = '';
            
            $output .= '<ul class="pwe-menu-auto-switch__submenu">';
            foreach ($children as $child) {
                $has_submenu_children = !empty(array_filter($menu_items, function($grandchild) use ($child) {
                    return $grandchild->menu_item_parent == $child->ID;
                }));

                $target_blank = !empty($child->target) ? 'target="_blank"' : '';

                $aria_label_for_visitors = (strpos(esc_url($child->url), PWECommonFunctions::languageChecker('/dla-odwiedzajacych/', '/for-visitors/')) !== false && $has_submenu_children != true) ? 'aria-label="Dlaczego warto: dla odwiedzajacych"' : '';
                $aria_label_for_exhibitors = (strpos(esc_url($child->url), PWECommonFunctions::languageChecker('/dla-wystawcow/', '/for-exhibitors/')) !== false && $has_submenu_children != true) ? 'aria-label="Dlaczego warto: dla wystawcow"' : '';

                if (!empty($aria_label_for_visitors)) {
                    $aria_label = $aria_label_for_visitors;
                } else if ($aria_label_for_exhibitors) {
                    $aria_label = $aria_label_for_exhibitors;
                } else $aria_label = '';

                $output .= '<li class="pwe-menu-auto-switch__submenu-item' . ($has_submenu_children ? ' has-children' : '') . '">';
                $output .= '<a '. $target_blank .' '. $aria_label .' href="' . esc_url($child->url) . '">' . wp_kses_post($child->title) . ($has_submenu_children ? '<span class="pwe-menu-auto-switch__arrow">›</span>' : '') . '</a>';
                $output .= display_sub_menu($child->ID, $menu_items, $depth + 1);
                $output .= '</li>';
            }
            $output .= '</ul>';

            return $output;
        }

        return '';
    }
}

// Get menu locations
$locations = get_nav_menu_locations();

// Check if 'primary' location exists
if (!isset($locations['primary'])) {
    return '<script>console.error("Menu location `primary` not found.");</script>';
}

// Get menu ID for 'primary' location
$menu_id = $locations['primary']; 

// WPML - Get the translated menu ID for the current language
if (function_exists('icl_object_id')) {
    $menu_id = icl_object_id($menu_id, 'nav_menu', true, ICL_LANGUAGE_CODE);
}

// Get menu items
$menu = wp_get_nav_menu_items($menu_id);
if (empty($menu) || !is_array($menu)) {
    return '<script>console.error("No menu items found or invalid menu structure for menu ID: '. $menu_id .'");</script>';
}

// Organize menu items by ID
$menu_items = array();
foreach ($menu as $item) {
    if (isset($item->ID)) {
        $menu_items[$item->ID] = $item;
    } else {
        return '<script>console.error("Menu item without valid ID found.");</script>';
    }
}


$output = '';

if (empty(get_option('pwe_menu_options', [])['pwe_menu_transparent'])) {
    $output .= '
    <style>
    body.home .pwe-menu-auto-switch {
        background-color: var(--accent-color);
    }
    </style>';
}

$output .= '
<style>
    @media (min-width: 961px) {
        body.home .pwe-header-wrapper {
            padding-top: 100px !important;
        }
    }

    .pwe-menu-auto-switch {
        position: fixed;
        left: 0px;
        right: 0px;
        width: 100%;
        height: 100px;
        z-index: 99;
        display: flex;
        transition: 0.3s ease;
        background-color: var(--accent-color);
    }
    body.home .pwe-menu-auto-switch {
        background-color: transparent;
    }
    .pwe-menu-auto-switch__wrapper {
        width: 100%;
        display: flex;
        justify-content: space-between;
        margin: 0 auto;
        padding: 0 18px;
        gap: 18px;
    }
    /* Mobile button take part */
    .pwe-menu-auto-switch .pwe-menu-auto-switch__register-btn {
        display: flex;
        align-self: center;
        visibility: hidden;
        opacity: 0;
        transition: .3s ease;
    }
    .pwe-menu-auto-switch .pwe-menu-auto-switch__register-btn.visible {
        opacity: 1;
        visibility: visible;
    }
    .pwe-menu-auto-switch .pwe-menu-auto-switch__register-btn a {
        background-color:var(--main2-color);
        color: #fff;
        padding: 4px 6px;
        border-radius: 5px;
        font-weight: 700;
        font-size: 12px;
        text-align: center;
    }
    .pwe-menu-auto-switch__main-logo {
        display: flex;
        align-items: center;
        padding: 5px 0;
    }
    .pwe-menu-auto-switch__main-logo a {
        max-height: 60px;
        height: 100%;
    }
    .pwe-menu-auto-switch__main-logo img {
        height: 100%;
        width: auto;
        object-fit: contain;
    }
    .pwe-menu-auto-switch__main-logo-ptak {
        margin-right: 8px;
    }
    .pwe-menu-auto-switch__container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: auto;
    }
    .pwe-menu-auto-switch__container-mobile {
        width: 0;
        display: flex;
        gap: 18px;
    }
    .pwe-menu-auto-switch__nav {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .pwe-menu-auto-switch__item {
        position: relative;
    }
    .pwe-menu-auto-switch__item > a {
        padding: 10px 14px;
        display: flex;
        justify-content: center;
        align-items: center;
        text-decoration: none;
        color: white;
        white-space: nowrap;
        transition: 0.3s ease;
        padding: 4px 8px;
        font-size: 16px;
        font-weight: 500;
        text-transform: uppercase;
    }
    .pwe-menu-auto-switch__item.button > a {
        border: 2px solid white;
        border-radius: 6px;
        padding: 16px;
    }
    .pwe-menu-auto-switch__item > a:hover {
        color: #bababa;
        border-color: #bababa;
    }
    .pwe-menu-auto-switch__item:has(.wpml-ls-flag) > a {
        padding: 0;
        margin-left: 18px;
        min-width: 18px;

    }
    .pwe-menu-auto-switch__item .pwe-menu-auto-switch__submenu {
        margin: 0;
        padding: 18px 0;
        gap: 5px;
    }
    /* First level submenu */
    .pwe-menu-auto-switch__item > .pwe-menu-auto-switch__submenu {
        width: max-content;
        position: absolute;
        top: 100%;
        left: 0;
        visibility: hidden;
        opacity: 0;
        transform: translateY(-10px);
        background-color: var(--accent-color);
        z-index: 10;
        transition: .3s ease;
    }
    /* Submenu of next levels */
    .pwe-menu-auto-switch__submenu .pwe-menu-auto-switch__submenu {
        width: max-content;
        position: absolute;
        top: 0;
        left: 100%;
        visibility: hidden;
        opacity: 0;
        background-color: var(--accent-color);
        z-index: 10;
        transition: .3s ease;
    }
    .pwe-menu-auto-switch__item:hover > .pwe-menu-auto-switch__submenu,
    .pwe-menu-auto-switch__submenu-item:hover > .pwe-menu-auto-switch__submenu {
        visibility: visible;
        opacity: 1;
    }
    .pwe-menu-auto-switch__submenu:hover,
    .pwe-menu-auto-switch__submenu-item:hover > .pwe-menu-auto-switch__submenu {
        visibility: visible;
        opacity: 1;
    }
    .pwe-menu-auto-switch__item.has-children:hover > .pwe-menu-auto-switch__submenu {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
    }
    .pwe-menu-auto-switch__submenu-item {
        position: relative;
    }
    .pwe-menu-auto-switch__item.has-children .pwe-menu-auto-switch__arrow,
    .pwe-menu-auto-switch__submenu-item.has-children .pwe-menu-auto-switch__arrow {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 10px;
        height: 10px;
        margin-left: 6px;
        transform: rotate(0deg);
        transition: 0.3s ease;
    }
    .pwe-menu-auto-switch__item.has-children:hover > a > .pwe-menu-auto-switch__arrow,
    .pwe-menu-auto-switch__submenu-item.has-children:hover > a > .pwe-menu-auto-switch__arrow {
        transform: rotate(90deg);
    }
    .pwe-menu-auto-switch__submenu-item {
        padding: 0 18px;
        font-size: 12px;
        transition: 0.3s ease;
    }
    .pwe-menu-auto-switch__submenu-item a {
        color: white;
    }
    .pwe-menu-auto-switch__submenu-item:hover {
        padding: 0 18px;
        font-size: 12px;
        background-color: #ffffff14;
    }
    .pwe-menu-auto-switch__submenu-item.has-children {
        font-weight: 600;
    }

    /* Social icons */
    .pwe-menu-auto-switch__social,
    .pwe-menu-auto-switch__social a {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
        padding: 0;
    }
    .pwe-menu-auto-switch__social a i {
        padding: 0 6px;
        margin: 0;
        color: white;
    }

    .pwe-menu-auto-switch .pwe-menu-auto-switch__burger-checkbox,
    .pwe-menu-auto-switch .pwe-menu-auto-switch__burger {
        display: none;
    }

    @media (max-width: 960px) {
        body.home .pwe-header-wrapper {
            padding-top: 60px !important;
        }
        body.home .pwe-menu-auto-switch {
            background-color: var(--accent-color);
        }
        .pwe-menu-auto-switch__container {
            flex-direction: column;
            position: fixed;
            z-index: 98;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            padding: 18px 0;
            width: 300px;
            height: 100%;
            background-color: var(--accent-color);
            opacity: .95;
            box-shadow: 0 0 20px rgba(0,0,0,.7);
            transform: translateX(-105%);
        }
        .pwe-menu-auto-switch.burger-menu .pwe-menu-auto-switch__container {
            transform: translateX(0);
        }
        .pwe-menu-auto-switch__container-mobile {
            width: auto;
        }
        /* Menu nav */
        .pwe-menu-auto-switch__nav {
            flex-direction: column;
            padding: 18px;
            overflow: scroll;
        }
        .pwe-menu-auto-switch__item {
            width: 100%;
        }
        .pwe-menu-auto-switch__item > a {
            font-size: 16px;
        }
        .pwe-menu-auto-switch__item.has-children:has(.pwe-menu-auto-switch__submenu.visible) {
            overflow: scroll;
        }
        .pwe-menu-auto-switch__item:has(.wpml-ls-flag) > a {
            margin-left: 0;
            margin-top: 18px;
        }
        .pwe-menu-auto-switch__item .wpml-ls-flag {
            width: 28px;
        }
        .pwe-menu-auto-switch__item.button {
            width: auto;
        }
        .pwe-menu-auto-switch__social {
            margin-top: 18px;
        }
        .pwe-menu-auto-switch__social a i {
            font-size: 24px;
        }
        .pwe-menu-auto-switch__item > .pwe-menu-auto-switch__submenu,
        .pwe-menu-auto-switch__submenu .pwe-menu-auto-switch__submenu {
            width: auto;
            top: unset;
            left: unset;
            position: relative;
            visibility: hidden;
            height: 0;
            opacity: 1;
            transform: unset;
            padding: 0;
            background-color: transparent;
            overflow: hidden;
        }
        .pwe-menu-auto-switch__submenu {
            visibility: hidden;
            height: 0;
            overflow: hidden;
            transition: height 0.3s ease, visibility 0s linear 0.3s;
        }
        .pwe-menu-auto-switch__submenu.visible {
            visibility: visible;
            transition: height 0.3s ease, visibility 0s linear 0s;
        }
        .pwe-menu-auto-switch__submenu-item {
            font-size: 16px;
        }
        .pwe-menu-auto-switch__submenu-item:hover {
            background-color: inherit;
        }
        .pwe-menu-auto-switch__submenu-item a {
            font-size: 16px;
        }


        /* Burger menu */
        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger-checkbox,
        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger {
            display: flex;
            width: 25px;
        }
        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger-checkbox {
            position: absolute;
            top: 0;
            bottom: 0; 
            left: 0;
            right: 0;
            height: 100%;
            width: 100%;
            z-index: 101;
            opacity: 0;
            cursor: pointer;
        }
        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger {
            position: relative;
            z-index: 100;
            flex-direction: column;
            justify-content: center;
        }
        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger span {
            position: relative;
            height: 3px;
            width: 25px;
            background-color: white;
            display: block;
            transition: all .2s ease-in-out;
            cursor: pointer;
        }
        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger span::after, 
        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger span::before {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            height: 3px;
            width: 25px;
            background-color: white;
            transition: all .2s ease-in-out;
            cursor: pointer;
        }

        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger span::before {
            top: -8px;
        }

        .pwe-menu-auto-switch .pwe-menu-auto-switch__burger span::after {
            bottom: -8px;
        }

        .pwe-menu-auto-switch.burger-menu .pwe-menu-auto-switch__burger span {
            background-color: transparent;
        }

        .pwe-menu-auto-switch.burger-menu .pwe-menu-auto-switch__burger span::before {
            background-color: #fff;
            transform: rotate(45deg);
            top: 0;
        }

        .pwe-menu-auto-switch.burger-menu .pwe-menu-auto-switch__burger span::after {
            background-color: #fff;
            transform: rotate(-45deg);
            bottom: 0;
        }

        /* Overlay */
        .pwe-menu-auto-switch .pwe-menu-auto-switch__overlay {
            position: fixed;
            z-index: 97;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: rgba(27, 32, 44, 0.7);
            visibility: hidden;
            opacity: 0;
            backdrop-filter: blur(6px);
            transition: 0.3s;
        }
        .pwe-menu-auto-switch.burger-menu .pwe-menu-auto-switch__overlay {
            visibility: visible;
            opacity: 1;
        }
    }
    @media (max-width: 450px) {
        .pwe-menu-auto-switch__wrapper {
            padding: 0 12px;
        }
        .pwe-menu-auto-switch__main-logo {
            max-width: 240px;
        }
        .pwe-menu-auto-switch__main-logo-ptak.hidden-mobile {
            display: none;
        }
    }
</style>';

$output .= '
<header id="pweMenuAutoSwitch" class="pwe-menu-auto-switch"> 
    <a style="opacity: 0; width: 0; height: 0;"  href="#main-content" class="skip-link">Skip to main content</a>
    <div class="pwe-menu-auto-switch__wrapper">
        <div class="pwe-menu-auto-switch__main-logo">
            <a class="pwe-menu-auto-switch__main-logo-ptak ' . (file_exists($_SERVER['DOCUMENT_ROOT'] . PWECommonFunctions::languageChecker('/doc/logo-x-pl.webp', '/doc/logo-x-en.webp')) ? "hidden-mobile" : "") . '" target="_blank" href="https://warsawexpo.eu'. PWECommonFunctions::languageChecker('/', '/en/') .'">
                <img data-no-lazy="1" src="/wp-content/plugins/pwe-media/media/logo_pwe.webp" alt="logo ptak">
            </a>
            <a class="pwe-menu-auto-switch__main-logo-fair" href="'. PWECommonFunctions::languageChecker('/', '/en/') .'">';
                if (PWECommonFunctions::lang_pl()) {
                    $output .= '<img data-no-lazy="1" src="' . (file_exists($_SERVER['DOCUMENT_ROOT'] . "/doc/logo-x-pl.webp") ? "/doc/logo-x-pl.webp" : "/doc/favicon.webp") . '" alt="logo fair">';
                } else {
                    $output .= '<img data-no-lazy="1" src="' . (file_exists($_SERVER['DOCUMENT_ROOT'] . "/doc/logo-x-en.webp") ? "/doc/logo-x-en.webp" : "/doc/favicon.webp") . '" alt="logo fair">';
                }
            $output .= '
            </a> 
        </div>

        <div class="pwe-menu-auto-switch__container-mobile">
            <div class="pwe-menu-auto-switch__register-btn">
                <a href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'">'. PWECommonFunctions::languageChecker('WEŹ UDZIAŁ', 'TAKE A PART') .'</a>
            </div>
            
            <div class="pwe-menu-auto-switch__burger">
                <span></span>
            </div>
        </div>

        <div class="pwe-menu-auto-switch__container">
            <ul class="pwe-menu-auto-switch__nav">';
                
                foreach ($menu_items as $item) {
                    if (!isset($item->menu_item_parent) || !isset($item->ID)) {
                        $output .= '<script>console.error("Invalid menu item structure detected.");</script>';
                        continue;
                    }

                    if ($item->menu_item_parent == 0) {
                        $has_children = !empty(array_filter($menu_items, function($child) use ($item) {
                            return $child->menu_item_parent == $item->ID;
                        }));

                        $target_blank = !empty($item->target) ? 'target="_blank"' : '';

                        if ((strpos($item->ID, 'wpml') === false)) {
                            $output .= '
                            <li class="pwe-menu-auto-switch__item' . ($has_children ? ' has-children' : '') . ' ' . ($item->button ?? '') . '">
                                <a '. $target_blank .' href="' . esc_url($item->url) . '"> ' . wp_kses_post($item->title) .'
                                    '. ($has_children ? '<span class="pwe-menu-auto-switch__arrow">›</span>' : '') .'
                                </a>
                                '. display_sub_menu($item->ID, $menu_items) .'
                            </li>';
                        }
                        
                    }
                }

$output .= '</ul>'; 

$output .= '
<style>
    .pwe-menu-auto-switch__lang-switch {
        position: relative;
        cursor: pointer;
    }
    .pwe-menu-auto-switch__lang-current {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 6px 10px;
        color: #fff;
    }

    /* Dropdown hidden state */
    .pwe-menu-auto-switch__lang-dropdown {
        width: max-content;
        position: absolute;
        top: 42px;
        background: #1a1a1a;
        border-radius: 6px;
        padding: 6px 0;
        box-shadow: 0 0 6px rgba(0,0,0,0.5);

        opacity: 0;
        visibility: hidden;
        transform: translateY(-5px);

        transition: opacity .25s ease, visibility .25s ease, transform .25s ease;
    }

    /* Dropdown visible */
    .pwe-menu-auto-switch__lang-dropdown--open {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .pwe-menu-auto-switch__lang-dropdown a {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        color: #fff;
        text-decoration: none;
    }
    .pwe-menu-auto-switch__lang-dropdown a:hover {
        background: #333;
    }
    .pwe-menu-auto-switch__lang-dropdown img {
        width: 22px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const trigger = document.querySelector(".pwe-menu-auto-switch__lang-current");
        const dropdown = document.querySelector(".pwe-menu-auto-switch__lang-dropdown");

        trigger.addEventListener("click", function(e) {
            e.stopPropagation();
            dropdown.classList.toggle("pwe-menu-auto-switch__lang-dropdown--open");
        });

        document.addEventListener("click", function(e) {
            if (!trigger.contains(e.target)) {
                dropdown.classList.remove("pwe-menu-auto-switch__lang-dropdown--open");
            }
        });
    });
</script>';

$output .= '
<div class="pwe-menu-auto-switch__nav-right>';

$languages = apply_filters("wpml_active_languages", null);
if (!empty($languages)) {

    // WYŚWIETLENIE AKTYWNEGO JĘZYKA
    foreach ($languages as $lang) {
        if ($lang["active"]) {
            $output .= '
            <div class="pwe-menu-auto-switch__lang-switch">
                <div class="pwe-menu-auto-switch__lang-current">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C10.6333 22 9.34167 21.7373 8.125 21.212C6.90833 20.6867 5.846 19.97 4.938 19.062C4.03 18.154 3.31333 17.0917 2.788 15.875C2.26267 14.6583 2 13.3667 2 12C2 10.6167 2.26267 9.321 2.788 8.113C3.31333 6.905 4.03 5.84667 4.938 4.938C5.846 4.02933 6.90833 3.31267 8.125 2.788C9.34167 2.26333 10.6333 2.00067 12 2C13.3833 2 14.6793 2.26267 15.888 2.788C17.0967 3.31333 18.1547 4.03 19.062 4.938C19.9693 5.846 20.686 6.90433 21.212 8.113C21.738 9.32167 22.0007 10.6173 22 12C22 13.3667 21.7373 14.6583 21.212 15.875C20.6867 17.0917 19.97 18.1543 19.062 19.063C18.154 19.9717 17.0957 20.6883 15.887 21.213C14.6783 21.7377 13.3827 22 12 22ZM12 19.95C12.4333 19.35 12.8083 18.725 13.125 18.075C13.4417 17.425 13.7 16.7333 13.9 16H10.1C10.3 16.7333 10.5583 17.425 10.875 18.075C11.1917 18.725 11.5667 19.35 12 19.95ZM9.4 19.55C9.1 19 8.83767 18.429 8.613 17.837C8.38833 17.245 8.20067 16.6327 8.05 16H5.1C5.58333 16.8333 6.18767 17.5583 6.913 18.175C7.63833 18.7917 8.46733 19.25 9.4 19.55ZM14.6 19.55C15.5333 19.25 16.3627 18.7917 17.088 18.175C17.8133 17.5583 18.4173 16.8333 18.9 16H15.95C15.8 16.6333 15.6127 17.246 15.388 17.838C15.1633 18.43 14.9007 19.0007 14.6 19.55ZM4.25 14H7.65C7.6 13.6667 7.56267 13.3377 7.538 13.013C7.51333 12.6883 7.50067 12.3507 7.5 12C7.49933 11.6493 7.512 11.312 7.538 10.988C7.564 10.664 7.60133 10.3347 7.65 10H4.25C4.16667 10.3333 4.10433 10.6627 4.063 10.988C4.02167 11.3133 4.00067 11.6507 4 12C3.99933 12.3493 4.02033 12.687 4.063 13.013C4.10567 13.339 4.168 13.668 4.25 14ZM9.65 14H14.35C14.4 13.6667 14.4377 13.3377 14.463 13.013C14.4883 12.6883 14.5007 12.3507 14.5 12C14.4993 11.6493 14.4867 11.312 14.462 10.988C14.4373 10.664 14.4 10.3347 14.35 10H9.65C9.6 10.3333 9.56267 10.6627 9.538 10.988C9.51333 11.3133 9.50067 11.6507 9.5 12C9.49933 12.3493 9.512 12.687 9.538 13.013C9.564 13.339 9.60133 13.668 9.65 14ZM16.35 14H19.75C19.8333 13.6667 19.896 13.3377 19.938 13.013C19.98 12.6883 20.0007 12.3507 20 12C19.9993 11.6493 19.9787 11.312 19.938 10.988C19.8973 10.664 19.8347 10.3347 19.75 10H16.35C16.4 10.3333 16.4377 10.6627 16.463 10.988C16.4883 11.3133 16.5007 11.6507 16.5 12C16.4993 12.3493 16.4867 12.687 16.462 13.013C16.4373 13.339 16.4 13.668 16.35 14ZM15.95 8H18.9C18.4167 7.16667 17.8127 6.44167 17.088 5.825C16.3633 5.20833 15.534 4.75 14.6 4.45C14.9 5 15.1627 5.571 15.388 6.163C15.6133 6.755 15.8007 7.36733 15.95 8ZM10.1 8H13.9C13.7 7.26667 13.4417 6.575 13.125 5.925C12.8083 5.275 12.4333 4.65 12 4.05C11.5667 4.65 11.1917 5.275 10.875 5.925C10.5583 6.575 10.3 7.26667 10.1 8ZM5.1 8H8.05C8.2 7.36667 8.38767 6.754 8.613 6.162C8.83833 5.57 9.10067 4.99933 9.4 4.45C8.46667 4.75 7.63733 5.20833 6.912 5.825C6.18667 6.44167 5.58267 7.16667 5.1 8Z" fill="white"/>
                    </svg>
                    <span>' . strtoupper($lang["language_code"]) . '</span>
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.66675 6.6665L8.00008 9.99984L11.3334 6.6665" stroke="white" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="pwe-menu-auto-switch__lang-dropdown">';
        }
    }

    // LISTA POZOSTAŁYCH JĘZYKÓW
    foreach ($languages as $lang) {
        if (!$lang["active"]) {
            $output .= '
            <a href="' . esc_url($lang["url"]) . '">
                <img src="' . esc_url($lang["country_flag_url"]) . '" alt="">
                ' . esc_html($lang["native_name"]) . '
            </a>';
        }
    }

        $output .= '
        </div>
    </div>';
}

            
$socials = ot_get_option('_uncode_social_list');

$socials_cap = array(
    'facebook' => do_shortcode('[pwe_facebook]'),
    'instagram' => do_shortcode('[pwe_instagram]'),
    'linkedin' => do_shortcode('[pwe_linkedin]'),
    'youtube' => do_shortcode('[pwe_youtube]')
);

if ((!empty($socials)) || !empty($socials_cap)) {
    $output .= '<ul class="pwe-menu-auto-switch__social">';
    if (!empty($socials_cap)) {
        if (!empty($socials_cap['facebook'])) {
            $output .= '
            <li class="pwe-menu-auto-switch__social-item-link social-icon facebook">
                <a href="'. esc_url($socials_cap['facebook']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Facebook profile">
                    <i class="fa fa-facebook-square"></i>
                </a>
            </li>';
        }
        if (!empty($socials_cap['instagram'])) {
            $output .= '
            <li class="pwe-menu-auto-switch__social-item-link social-icon instagram">
                <a href="'. esc_url($socials_cap['instagram']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Instagram profile">
                    <i class="fa fa-instagram"></i>
                </a>
            </li>';
        }
        if (!empty($socials_cap['linkedin'])) {
            $output .= '
            <li class="pwe-menu-auto-switch__social-item-link social-icon linkedin">
                <a href="'. esc_url($socials_cap['linkedin']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Linkedin profile">
                    <i class="fa fa-linkedin-square"></i>
                </a>
            </li>';
        }
        if (!empty($socials_cap['youtube'])) {
            $output .= '
            <li class="pwe-menu-auto-switch__social-item-link social-icon youtube">
                <a href="'. esc_url($socials_cap['youtube']) .'" class="social-menu-link" target="_blank" aria-label="Visit our Youtube profile">
                    <i class="fa fa-youtube-play"></i>
                </a>
            </li>';
        }
    } else if (!empty($socials)) {
        foreach ($socials as $social) { 
            $output .= '
            <li class="pwe-menu-auto-switch__social-item-link social-icon '.esc_attr($social['_uncode_social_unique_id']).'">
                <a href="'.esc_url($social['_uncode_link']).'" class="social-menu-link" target="_blank">
                    <i class="'.esc_attr($social['_uncode_social']).'"></i>
                </a>
            </li>';
        }
    }
    $output .= '</ul>';
}

$output .= '
</div>';

$output .= '
        </div>
    </div>
    <div class="pwe-menu-auto-switch__overlay"></div>
</header>';

$output .= '
<script>

    const menu_transparent = menu_js.menu_transparent;
    const trade_fair_datetotimer = menu_js.trade_fair_datetotimer;
    const trade_fair_enddata = menu_js.trade_fair_enddata;

    document.addEventListener("DOMContentLoaded", function () {
        const adminBar = document.querySelector("#wpadminbar");
        const pweNavMenu = document.querySelector("#pweMenuAutoSwitch");
        const bodyHome = document.querySelector("body.home");
        const pweNavMenuHome = document.querySelector("body.home #pweMenuAutoSwitch");
        const burgerButton = pweNavMenu.querySelector(".pwe-menu-auto-switch__burger");
        const menuContainer = pweNavMenu.querySelector(".pwe-menu-auto-switch__container");

        const mainContainer = document.querySelector(".main-container");

        const uncodePageHeader = document.querySelector("#page-header");
        const pweCustomHeader = document.querySelector("#pweHeader");

        if (menuContainer) {
            menuContainer.style.transition = ".3s";
        }

        if (pweNavMenu && mainContainer) {
            if (!(uncodePageHeader && pweCustomHeader)) {
                mainContainer.style.marginTop = pweNavMenu.offsetHeight + "px";
            } else if (uncodePageHeader && pweCustomHeader && !bodyHome) {
                mainContainer.style.marginTop = pweNavMenu.offsetHeight + "px";
            }
        }

        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // Uncode sticky element
        const uncodeStickyElement = document.querySelector(".row-container.sticky-element");
        if (uncodeStickyElement && !isMobile) {
            let stickyHeight = uncodeStickyElement.offsetHeight;
            let stickyPos;

            if (adminBar) {
                stickyPos = uncodeStickyElement.getBoundingClientRect().top + window.scrollY - (adminBar.offsetHeight * 2);
            } else {
                stickyPos = uncodeStickyElement.getBoundingClientRect().top + (window.scrollY - pweNavMenu.offsetHeight);
            }

            // Create a negative margin to prevent content "jumps":
            const jumpPreventDiv = document.createElement("div");
            jumpPreventDiv.className = "jumps-prevent";
            uncodeStickyElement.parentNode.insertBefore(jumpPreventDiv, uncodeStickyElement.nextSibling);
            uncodeStickyElement.style.zIndex = "99";

            function jumpsPrevent() {
                stickyHeight = uncodeStickyElement.offsetHeight;
                uncodeStickyElement.style.marginBottom = "-" + stickyHeight + "px";
                uncodeStickyElement.nextElementSibling.style.paddingTop = stickyHeight + "px";
            }

            jumpsPrevent();

            window.addEventListener("resize", function () {
                jumpsPrevent();
            });

            function stickerFn() {
                const winTop = window.scrollY;

                if (winTop >= stickyPos) {
                    if (pweNavMenu) {
                        uncodeStickyElement.style.position = "fixed";

                        if (adminBar) {
                            uncodeStickyElement.style.top = (pweNavMenu.offsetHeight + adminBar.offsetHeight) + "px";
                        } else {
                            uncodeStickyElement.style.top = pweNavMenu.offsetHeight + "px";
                        }
                    }
                } else {
                    uncodeStickyElement.style.position = "relative";
                    uncodeStickyElement.style.top = "0";
                } 
            }

            window.addEventListener("scroll", function () {
                stickerFn();
            });
        }

        // Background color for nav menu
        if (menu_transparent === "true") {
            if (pweNavMenuHome && window.innerWidth >= 960) {
                if (window.scrollY > pweNavMenu.offsetHeight) {
                    pweNavMenuHome.style.background = accent_color;
                }
                window.addEventListener("scroll", function () {
                    if (window.scrollY > pweNavMenu.offsetHeight) {
                        pweNavMenuHome.style.background = accent_color;
                        pweNavMenuHome.classList.add("color");

                    } else {
                        pweNavMenuHome.style.background = "transparent";
                        pweNavMenuHome.classList.remove("color");
                    }
                });
            } else {
                pweNavMenu.classList.add("color");
            }
        } 
        
        if (burgerButton && pweNavMenu) {
            // Listening for click on burger menu
            burgerButton.addEventListener("click", function() {
                pweNavMenu.classList.toggle("burger-menu");
                
                // If the menu is open, close all submenus
                const openSubmenus = document.querySelectorAll(".pwe-menu-auto-switch__submenu.visible");
                openSubmenus.forEach(submenu => {
                    closeSubmenu(submenu);
                });
            });

            // Click outside the menu - close burger menu
            document.addEventListener("click", function (e) {
                if (pweNavMenu.classList.contains("burger-menu") && !menuContainer.contains(e.target) && !burgerButton.contains(e.target)) {
                    pweNavMenu.classList.remove("burger-menu");

                    // Close all open submenus
                    const openSubmenus = document.querySelectorAll(".pwe-menu-auto-switch__submenu.visible");
                    openSubmenus.forEach(submenu => {
                        closeSubmenu(submenu);
                    });
                }
            });
        }

        // Function to close submenu
        const closeSubmenu = (submenu) => {
            if (submenu) {
                submenu.style.height = `${submenu.scrollHeight}px`;
                requestAnimationFrame(() => {
                    submenu.style.height = "0";
                });
                submenu.classList.remove("visible");
            }
        };

        // Function to open submenu
        const openSubmenu = (submenu) => {
            if (submenu) {
                submenu.style.height = "0";
                submenu.classList.add("visible");
                requestAnimationFrame(() => {
                    submenu.style.height = `${submenu.scrollHeight}px`;
                });
            }
        };

        // Function to switch submenus
        const toggleSubmenu = (link) => {
            const submenu = link.parentElement.querySelector(".pwe-menu-auto-switch__submenu");

            if (submenu) {
                const isVisible = submenu.classList.contains("visible");

                // Close all other submenus on the same level
                const siblings = Array.from(link.parentElement.parentElement.children)
                    .filter(item => item !== link.parentElement);

                siblings.forEach(sibling => {
                    const siblingSubmenu = sibling.querySelector(".pwe-menu-auto-switch__submenu");
                    if (siblingSubmenu && siblingSubmenu.classList.contains("visible")) {
                        closeSubmenu(siblingSubmenu);
                    }
                });

                // Open or close the current submenu
                if (isVisible) {
                    closeSubmenu(submenu);
                } else {
                    openSubmenu(submenu);
                }

                // Remove height after animation is finished
                submenu.addEventListener(
                    "transitionend",
                    function () {
                        if (submenu.classList.contains("visible")) {
                            submenu.style.height = "auto";
                        }
                    },
                    { once: true }
                );
            }
        };

        // Handling clicks on submenu links
        const menuLinks = document.querySelectorAll(".pwe-menu-auto-switch__item.has-children > a, .pwe-menu-auto-switch__submenu-item.has-children > a");
        if (menuLinks.length && window.innerWidth < 960) {
            menuLinks.forEach(link => {
                let clickedOnce = false;

                link.addEventListener("click", function (e) {
                    const href = this.getAttribute("href");

                    // Links without `href` or with `#` always open/close submenu
                    if (!href || href === "#") {
                        e.preventDefault();
                        toggleSubmenu(this);
                        return;
                    }

                    const submenu = this.parentElement.querySelector(".pwe-menu-auto-switch__submenu");
                    if (submenu && !submenu.classList.contains("visible")) {
                        // Block link
                        e.preventDefault();
                        // Open submenu
                        toggleSubmenu(this); 
                        clickedOnce = true;
                    } else if (clickedOnce) {
                        // Second click: allow the transition if the link is valid
                        clickedOnce = false;
                    } else {
                        // Block link
                        e.preventDefault();
                        // Close submenu if open
                        toggleSubmenu(this); 
                    }
                });
            });
        }

        const registerButtons = document.querySelectorAll(".pwe-menu-auto-switch__item.button a");
        const mobileRegisterButton = document.querySelector(".pwe-menu-auto-switch__register-btn a");
        const mobileRegisterButtonContainer = document.querySelector(".pwe-menu-auto-switch__register-btn");

        if (registerButtons.length > 0 && mobileRegisterButton) {
            // Get the page language
            const htmlLang = document.documentElement.lang;

            registerButtons.forEach(registerButton => {
                if (
                    registerButton.innerText.toLowerCase() === (htmlLang === "pl-PL" ? "weź udział" : "join us") ||
                    registerButton.innerText.toLowerCase() === (htmlLang === "pl-PL" ? "zostań wystawcą" : "book a stand")
                ) {
                    // Create a Date object based on the trade fair end date
                    let endDate = new Date(trade_fair_enddata);

                    // Get today`s date
                    let todayDate = new Date();

                    // Add 90 days to today`s date
                    let threeMonths = new Date(todayDate);
                    threeMonths.setDate(todayDate.getDate() + 90);

                    const currentDomain = window.location.hostname;
                    const b2cDomains = [
                        "animalsdays.eu",
                        "fiwe.pl",
                        "warsawmotorshow.com",
                        "oldtimerwarsaw.com",
                        "motorcycleshow.pl"
                    ];

                    // Check if the current domain is NOT in the B2C domains list
                    if (!b2cDomains.includes(currentDomain)) {
                        let newText, newHref;
                        
                        // Check if the trade fair end date is less than 90 days away
                        if (endDate < threeMonths) {
                            newText = (htmlLang === "pl-PL") ? "WEŹ UDZIAŁ" : "JOIN US";
                            newHref = (htmlLang === "pl-PL") ? "/rejestracja/" : "/en/registration/";
                        } else {
                            newText = (htmlLang === "pl-PL") ? "ZOSTAŃ WYSTAWCĄ" : "BOOK A STAND";
                            newHref = (htmlLang === "pl-PL") ? "/zostan-wystawca/" : "/en/become-an-exhibitor/";
                        }

                        // Update text and link for both desktop and mobile buttons
                        registerButton.innerText = newText;
                        registerButton.href = newHref;
                        mobileRegisterButton.innerText = newText;
                        mobileRegisterButton.href = newHref;
                    }
                }
            });

            window.addEventListener("resize", function () {
                if (window.innerWidth < 960) {
                    mobileRegisterButtonContainer.classList.add("visible");
                } else {
                    mobileRegisterButtonContainer.classList.remove("visible");
                }
            });
            
            // Run once on page load to set initial state
            if (window.innerWidth < 960) {
                mobileRegisterButtonContainer.classList.add("visible");
            } else {
                mobileRegisterButtonContainer.classList.remove("visible");
            }
            
        }
    });

</script>';

return $output;