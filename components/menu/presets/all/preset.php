<?php

// Function to display submenu
if (!function_exists('render_submenu')) {
    function render_submenu($parent_id, $menu_items, $depth = 1, $root_index = null) {
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
            
            $output .= '
            <ul class="pwe-menu-auto-switch__submenu">';

                foreach ($children as $child) {
                    $has_submenu_children = !empty(array_filter($menu_items, function($grandchild) use ($child) {
                        return $grandchild->menu_item_parent == $child->ID;
                    }));

                    $target_blank = !empty($child->target) ? 'target="_blank"' : '';

                    $aria_label_for_visitors = (strpos(esc_url($child->url), PWE_Functions::languageChecker('/dla-odwiedzajacych/', '/for-visitors/')) !== false && $has_submenu_children != true) ? 'aria-label="Dlaczego warto: dla odwiedzajacych"' : '';
                    $aria_label_for_exhibitors = (strpos(esc_url($child->url), PWE_Functions::languageChecker('/dla-wystawcow/', '/for-exhibitors/')) !== false && $has_submenu_children != true) ? 'aria-label="Dlaczego warto: dla wystawcow"' : '';

                    if (!empty($aria_label_for_visitors)) {
                        $aria_label = $aria_label_for_visitors;
                    } else if ($aria_label_for_exhibitors) {
                        $aria_label = $aria_label_for_exhibitors;
                    } else $aria_label = '';

                    $output .= '
                    <li class="pwe-menu-auto-switch__submenu-item' . ($has_submenu_children ? ' has-children' : '') . '">
                        <a '. $target_blank .' '. $aria_label .' href="' . esc_url($child->url) . '">
                            <span class="pwe-menu-auto-switch__item-title">' . wp_kses_post($child->title) .'</span>
                            '. ($has_submenu_children ? '
                            <svg class="pwe-menu-auto-switch__arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9 20L16 12L9 4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>' : '') . '
                            
                        </a>
                        '. render_submenu($child->ID, $menu_items, $depth + 1, $root_index) .'
                    </li>';
                }
            
            $output .= '
            </ul>';

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

$socials = ot_get_option('_uncode_social_list');

$socials_cap = array(
    'facebook' => do_shortcode('[pwe_facebook]'),
    'instagram' => do_shortcode('[pwe_instagram]'),
    'linkedin' => do_shortcode('[pwe_linkedin]'),
    'youtube' => do_shortcode('[pwe_youtube]')
);

$output .= '
<header id="pweMenuAutoSwitch" class="pwe-menu-auto-switch"> 
    <a style="opacity: 0; width: 0; height: 0;"  href="#main-content" class="skip-link">Skip to main content</a>
    <div class="pwe-menu-auto-switch__wrapper">
        <div class="pwe-menu-auto-switch__logotypes">
            <a class="pwe-logo ' . (file_exists($_SERVER['DOCUMENT_ROOT'] . PWE_Functions::languageChecker('/doc/logo-x-pl.webp', '/doc/logo-x-en.webp')) ? "hidden-mobile" : "") . '" target="_blank" href="https://warsawexpo.eu'. PWE_Functions::languageChecker('/', '/en/') .'">
                <div class="pwe-menu-auto-switch__logo-container">
                    <img data-no-lazy="1" src="/wp-content/plugins/pwe-media/media/logo_pwe.webp" alt="logo ptak">
                </div>
            </a>
            <a class="fair-logo" href="'. PWE_Functions::languageChecker('/', '/en/') .'">
                <div class="pwe-menu-auto-switch__logo-container pwe-menu-auto-switch__logo-fair">';
                    if (PWE_Functions::lang_pl()) {
                        $output .= '<img data-no-lazy="1" src="' . (file_exists($_SERVER['DOCUMENT_ROOT'] . "/doc/logo-x-pl.webp") ? "/doc/logo-x-pl.webp" : "/doc/favicon.webp") . '" alt="logo fair">';
                    } else {
                        $output .= '<img data-no-lazy="1" src="' . (file_exists($_SERVER['DOCUMENT_ROOT'] . "/doc/logo-x-en.webp") ? "/doc/logo-x-en.webp" : "/doc/favicon.webp") . '" alt="logo fair">';
                    }
                $output .= '
                </div>
            </a> 
        </div>

        <div class="pwe-menu-auto-switch__container">
            <ul class="pwe-menu-auto-switch__nav">';
                
                $menu_index = 0;
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
                                $locale=get_locale();
                                $title = trim(mb_strtolower($item->title));

                                $map = [
                                    'pl_PL' => [                 
                                        'lista wystawców' => 'Katalog wystawców',
                                    ],
                                    'en_US' => [
                                        'exhibitors list' => 'Exhibitor catalogue',
                                    ],
                                    'de_DE' => [
                                        'ausstellerliste' => 'Ausstellerkatalog',
                                    ],
                                ];        
                                
                                if (isset($map[$locale][$title])) {
                                    $item->title = $map[$locale][$title];
                                }

                            $output .= '
                            <li class="pwe-menu-auto-switch__item' . ($has_children ? ' has-children' : '') . ' ' . ($item->button ?? '') . '">
                                <a '. $target_blank .' href="' . esc_url($item->url) . '">
                                    <span class="pwe-menu-auto-switch__item-title">' . wp_kses_post($item->title) .'</span>
                                    '. ($has_children ? '
                                    <svg class="pwe-menu-auto-switch__arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M9 20L16 12L9 4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>' : '') .'
                                </a>
                                '. render_submenu($item->ID, $menu_items, 1, $menu_index) .'
                            </li>';
                        }
                    }
                    
                }

                $output .= '
            </ul>'; 

            $output .= '
            <div class="pwe-menu-auto-switch__nav-icons">';

                $languages = apply_filters("wpml_active_languages", null);
                if (!empty($languages)) {

                    $output .= '
                    <div class="pwe-menu-auto-switch__lang-switch">';

                        // Display active language
                        foreach ($languages as $lang) {
                            if ($lang["active"]) {
                                $output .= '
                                <div class="pwe-menu-auto-switch__lang-current">
                                    <div class="pwe-menu-auto-switch__lang-current-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 22C10.6333 22 9.34167 21.7373 8.125 21.212C6.90833 20.6867 5.846 19.97 4.938 19.062C4.03 18.154 3.31333 17.0917 2.788 15.875C2.26267 14.6583 2 13.3667 2 12C2 10.6167 2.26267 9.321 2.788 8.113C3.31333 6.905 4.03 5.84667 4.938 4.938C5.846 4.02933 6.90833 3.31267 8.125 2.788C9.34167 2.26333 10.6333 2.00067 12 2C13.3833 2 14.6793 2.26267 15.888 2.788C17.0967 3.31333 18.1547 4.03 19.062 4.938C19.9693 5.846 20.686 6.90433 21.212 8.113C21.738 9.32167 22.0007 10.6173 22 12C22 13.3667 21.7373 14.6583 21.212 15.875C20.6867 17.0917 19.97 18.1543 19.062 19.063C18.154 19.9717 17.0957 20.6883 15.887 21.213C14.6783 21.7377 13.3827 22 12 22ZM12 19.95C12.4333 19.35 12.8083 18.725 13.125 18.075C13.4417 17.425 13.7 16.7333 13.9 16H10.1C10.3 16.7333 10.5583 17.425 10.875 18.075C11.1917 18.725 11.5667 19.35 12 19.95ZM9.4 19.55C9.1 19 8.83767 18.429 8.613 17.837C8.38833 17.245 8.20067 16.6327 8.05 16H5.1C5.58333 16.8333 6.18767 17.5583 6.913 18.175C7.63833 18.7917 8.46733 19.25 9.4 19.55ZM14.6 19.55C15.5333 19.25 16.3627 18.7917 17.088 18.175C17.8133 17.5583 18.4173 16.8333 18.9 16H15.95C15.8 16.6333 15.6127 17.246 15.388 17.838C15.1633 18.43 14.9007 19.0007 14.6 19.55ZM4.25 14H7.65C7.6 13.6667 7.56267 13.3377 7.538 13.013C7.51333 12.6883 7.50067 12.3507 7.5 12C7.49933 11.6493 7.512 11.312 7.538 10.988C7.564 10.664 7.60133 10.3347 7.65 10H4.25C4.16667 10.3333 4.10433 10.6627 4.063 10.988C4.02167 11.3133 4.00067 11.6507 4 12C3.99933 12.3493 4.02033 12.687 4.063 13.013C4.10567 13.339 4.168 13.668 4.25 14ZM9.65 14H14.35C14.4 13.6667 14.4377 13.3377 14.463 13.013C14.4883 12.6883 14.5007 12.3507 14.5 12C14.4993 11.6493 14.4867 11.312 14.462 10.988C14.4373 10.664 14.4 10.3347 14.35 10H9.65C9.6 10.3333 9.56267 10.6627 9.538 10.988C9.51333 11.3133 9.50067 11.6507 9.5 12C9.49933 12.3493 9.512 12.687 9.538 13.013C9.564 13.339 9.60133 13.668 9.65 14ZM16.35 14H19.75C19.8333 13.6667 19.896 13.3377 19.938 13.013C19.98 12.6883 20.0007 12.3507 20 12C19.9993 11.6493 19.9787 11.312 19.938 10.988C19.8973 10.664 19.8347 10.3347 19.75 10H16.35C16.4 10.3333 16.4377 10.6627 16.463 10.988C16.4883 11.3133 16.5007 11.6507 16.5 12C16.4993 12.3493 16.4867 12.687 16.462 13.013C16.4373 13.339 16.4 13.668 16.35 14ZM15.95 8H18.9C18.4167 7.16667 17.8127 6.44167 17.088 5.825C16.3633 5.20833 15.534 4.75 14.6 4.45C14.9 5 15.1627 5.571 15.388 6.163C15.6133 6.755 15.8007 7.36733 15.95 8ZM10.1 8H13.9C13.7 7.26667 13.4417 6.575 13.125 5.925C12.8083 5.275 12.4333 4.65 12 4.05C11.5667 4.65 11.1917 5.275 10.875 5.925C10.5583 6.575 10.3 7.26667 10.1 8ZM5.1 8H8.05C8.2 7.36667 8.38767 6.754 8.613 6.162C8.83833 5.57 9.10067 4.99933 9.4 4.45C8.46667 4.75 7.63733 5.20833 6.912 5.825C6.18667 6.44167 5.58267 7.16667 5.1 8Z" fill="white"/>
                                        </svg>
                                        <p>
                                            <span class="pwe-menu-auto-switch__lang-code notranslate">' . strtoupper($lang["language_code"]) . '</span>
                                            <span class="pwe-menu-auto-switch__lang-text notranslate">'. (get_locale() === 'pl_PL' ? 'Język' : (get_locale() === 'de_DE' ? 'Sprache' : 'Language')) .'</span>
                                        </p>
                                    </div>
                                    <svg class="pwe-menu-auto-switch__arrow" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M9 20L16 12L9 4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>';
                            }
                        }

                        $output .= '
                        <div class="pwe-menu-auto-switch__lang-dropdown">';

                            // List of other languages
                            foreach ($languages as $lang) {
                                if (!$lang["active"]) {
                                    $output .= '
                                    <a class="notranslate" href="' . esc_url($lang["url"]) . '">
                                        <img src="' . esc_url($lang["country_flag_url"]) . '" alt="language flag ('. esc_html($lang["native_name"]) .')">
                                        ' . esc_html($lang["native_name"]) . '
                                    </a>';
                                }
                            }

                            $output .= '
                        </div>';
                        $output .= '
                    </div>';
                }

                if ((!empty($socials)) || !empty($socials_cap)) {
                    $output .= '
                    <ul class="pwe-menu-auto-switch__social">';
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
                        $output .= '
                    </ul>';
                }

                $output .= '
            </div>';

            $output .= '
        </div>

        <div class="pwe-menu-auto-switch__container-mobile">
            <li class="pwe-menu-auto-switch__register-btn pwe-menu-auto-switch__item button">
                <a href="'. (get_locale() === 'pl_PL' ? '/rejestracja/' : (get_locale() === 'de_DE' ? '/de/registrieren/' : '/en/registration/')) .'">
                    <span class="pwe-menu-auto-switch__item-title">'. (get_locale() === 'pl_PL' ? 'WEŹ UDZIAŁ' : (get_locale() === 'de_DE' ? 'JETZT TEILNEHMEN' : 'JOIN US')) .'</span>
                </a>
            </li>
            
            <div class="pwe-menu-auto-switch__burger">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8 10H18C18.5304 10 19.0391 10.2107 19.4142 10.5858C19.7893 10.9609 20 11.4696 20 12C20 12.5304 19.7893 13.0391 19.4142 13.4142C19.0391 13.7893 18.5304 14 18 14H8C7.46957 14 6.96086 13.7893 6.58579 13.4142C6.21071 13.0391 6 12.5304 6 12C6 11.4696 6.21071 10.9609 6.58579 10.5858C6.96086 10.2107 7.46957 10 8 10ZM22 26H32C32.5304 26 33.0391 26.2107 33.4142 26.5858C33.7893 26.9609 34 27.4696 34 28C34 28.5304 33.7893 29.0391 33.4142 29.4142C33.0391 29.7893 32.5304 30 32 30H22C21.4696 30 20.9609 29.7893 20.5858 29.4142C20.2107 29.0391 20 28.5304 20 28C20 27.4696 20.2107 26.9609 20.5858 26.5858C20.9609 26.2107 21.4696 26 22 26ZM8 18H32C32.5304 18 33.0391 18.2107 33.4142 18.5858C33.7893 18.9609 34 19.4696 34 20C34 20.5304 33.7893 21.0391 33.4142 21.4142C33.0391 21.7893 32.5304 22 32 22H8C7.46957 22 6.96086 21.7893 6.58579 21.4142C6.21071 21.0391 6 20.5304 6 20C6 19.4696 6.21071 18.9609 6.58579 18.5858C6.96086 18.2107 7.46957 18 8 18Z" fill="white"/>
                </svg>
            </div>
        </div>

    </div>
    <div class="pwe-menu-auto-switch__overlay"></div>
</header>';

if (!empty(do_shortcode('[trade_fair_catalog_id]'))) {
    $output .= '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pweMenuAutoSwitch = document.querySelector("#pweMenuAutoSwitch");

            // Top main menu "For exhibitors"
            const mainMenu = pweMenuAutoSwitch ? document.querySelector(".pwe-menu-auto-switch__nav") : document.querySelector("ul.menu-primary-inner");
            const secondChild = mainMenu.children[1];
            const dropMenu = pweMenuAutoSwitch ? secondChild.querySelector(".pwe-menu-auto-switch__submenu") : secondChild.querySelector("ul.drop-menu");

            // Create new element li
            const instructionMenuItem = document.createElement("li");
            instructionMenuItem.className = pweMenuAutoSwitch ? "pwe-menu-auto-switch__submenu-item" : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999";
            instructionMenuItem.innerHTML = `<a title="'. PWE_Functions::languageChecker('Instrukcja aplikacji', 'Application instructions') .'" target="_blank" href="https://warsawexpo.eu/docs/'. PWE_Functions::languageChecker('Instrukcja-do-aplikacji.pdf', 'Instrukcja-do-aplikacji-EN') .'">'. PWE_Functions::languageChecker('Instrukcja aplikacji', 'Application instructions') .'</a>`;

            // Add new element as penultimate in the list
            if (dropMenu && dropMenu.children.length > 0) {
            const penultimateItem = dropMenu.children[dropMenu.children.length - 1];
                dropMenu.insertBefore(instructionMenuItem, penultimateItem);
            } else {
                dropMenu.appendChild(instructionMenuItem);
            }

            // Create new element li
            const loginMenuItem = document.createElement("li");
            loginMenuItem.className = pweMenuAutoSwitch ? "pwe-menu-auto-switch__submenu-item" : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999";
            loginMenuItem.innerHTML = `<a title="'. PWE_Functions::languageChecker('Zaloguj się do aplikacji', 'Log in to the application') .'" target="_blank" href="https://wystawca.exhibitorlist.eu/login">'. PWE_Functions::languageChecker('Zaloguj się do aplikacji', 'Log in to the application') .'</a>`;

            // Add new element as penultimate in the list
            if (dropMenu && dropMenu.children.length > 0) {
            const penultimateItem = dropMenu.children[dropMenu.children.length - 2];
                dropMenu.insertBefore(loginMenuItem, penultimateItem);
            } else {
                dropMenu.appendChild(loginMenuItem);
            }
        });
    </script>'; 
}

return $output;