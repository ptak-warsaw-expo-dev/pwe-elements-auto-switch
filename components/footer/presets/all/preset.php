<?php

/** Loads and caches menu translations from the JSON file. */
if (!function_exists('get_menu_translations')) {
    function get_menu_translations() {
        static $translations = null;

        if ($translations === null) {
            $file = dirname(__FILE__) . '/assets/translations.json';

            if (file_exists($file)) {
                $json = file_get_contents($file);
                $translations = json_decode($json, true);

                if (!is_array($translations)) {
                    $translations = [];
                }
            } else {
                $translations = [];
            }
        }

        return $translations;
    }
}

/** Applies a menu item translation by URL or label. */
if (!function_exists('apply_anchor_translation')) {
    function apply_anchor_translation(&$item) {

        $translations = get_menu_translations();
        if (empty($translations)) return;

        $lang = PWE_Functions::lang();

        $current_title = trim(wp_strip_all_tags($item->title));
        $current_url   = trim($item->url);

        // Normalize URL for consistent comparison
        $normalize = function($url) {

            $url = trim($url);
            if ($url === '') return '';

            $hash = '';
            if (strpos($url, '#') !== false) {
                [$url, $hash] = explode('#', $url, 2);
                $hash = '#'.$hash;
            }

            $path = parse_url($url, PHP_URL_PATH);
            if ($path !== null) {
                $url = $path;
            }

            $url = '/' . ltrim($url, '/');

            if ($url !== '/') {
                $url = rtrim($url, '/');
            }

            return $url . $hash;
        };

        $current_norm = $normalize($current_url);

        // STEP 1: BUILD URL INDEX
        $index_by_url = [];

        foreach ($translations as $key => $entry) {

            if (!is_array($entry)) continue;

            $source = $entry['en'] ?? null;
            if (empty($source['url'])) continue;

            $source_norm = $normalize($source['url']);

            // Map by normalized URL (this is the real identifier)
            $index_by_url[$source_norm] = $entry;
        }

        // STEP 2: MATCH CURRENT ITEM BY URL ONLY
        $matched_entry = null;

        if (isset($index_by_url[$current_norm])) {
            $matched_entry = $index_by_url[$current_norm];
        }

        // STEP 3: APPLY TRANSLATION FROM MATCHED ENTRY
        if ($matched_entry && isset($matched_entry[$lang])) {

            $target = $matched_entry[$lang];

            if (!empty($target['label'])) {
                $item->title = $target['label'];
            }

            if (!empty($target['url'])) {
                $item->url = $target['url'];
            }

            return;
        }

        // STEP 4: LABEL FALLBACK (ONLY IF NO URL MATCH)
        foreach ($translations as $key => $entry) {

            if (!is_array($entry)) continue;

            $source = $entry['en'] ?? null;
            $target  = $entry[$lang] ?? null;

            if (empty($source) || empty($target)) continue;

            $source_label = trim($source['label'] ?? '');

            if ($source_label && mb_stripos($current_title, $source_label) !== false) {

                if (!empty($target['label'])) {
                    $item->title = $target['label'];
                }

                if (!empty($target['url'])) {
                    $item->url = $target['url'];
                }

                return;
            }
        }
    }
}

/** Translates the label prefix while preserving the year and remaining text. */
if (!function_exists('translate_global_label')) {
    function translate_global_label($text, $lang) {

        $map = get_global_label_translations();

        $clean = html_entity_decode(strip_tags($text));
        $clean = str_replace("\xc2\xa0", ' ', $clean);
        $clean = preg_replace('/\s+/u', ' ', trim($clean));

        foreach ($map as $key => $translations) {

            foreach ($translations as $locale => $variants) {

                if (!is_array($variants)) continue;

                foreach ($variants as $variant) {

                    if (mb_stripos($clean, $variant) === 0) {

                        $rest = trim(mb_substr($clean, mb_strlen($variant)));

                        // Select the target translation.
                        $target_list = $translations[$lang] ?? $translations['en'];

                        $target = is_array($target_list) ? $target_list[0] : $target_list;

                        return $target . ($rest ? ' ' . $rest : '');
                    }
                }
            }
        }

        return $text;
    }
}

/** Returns shared labels used by dynamic menu items. */
if (!function_exists('get_global_label_translations')) {
    function get_global_label_translations() {
        return [
            'post_trade_report' => [
                'pl' => ['Raport potargowy', 'Post Show Report'],
                'en' => ['Post-trade fair report', 'Post Show Report'],
                'de' => ['Post-Messe-Bericht'],
                'it' => ['Rapporto post-fiera'],
                'cs' => ['Zpráva po veletrhu'],
                'sk' => ['Raport po veletrhu'],
                'uk' => ['Звіт після виставки'],
                'lt' => ['Po parodos ataskaita'],
                'lv' => ['Pēc tirdzniecības izstādes ziņojums'],
                'ro' => ['Raport după târg'],
                'et' => ['Raport pärast messi'],
                'hu' => ['Raport a mese után']
            ],
            'edition' => [
                'pl' => ['Edycja'],
                'en' => ['Edition'],
                'de' => ['Ausgabe'],
                'it' => ['Edizione'],
                'cs' => ['Vydání'],
                'sk' => ['Vydanie'],
                'uk' => ['Видання'],
                'lt' => ['Leidimas'],
                'lv' => ['Izdevums'],
                'ro' => ['Ediție'],
                'et' => ['Versioon'],
                'hu' => ['Kiadás']
            ],
        ];
    }
}

/** Custom footer menu renderer */
if (!function_exists('render_footer_menu')) {
    function render_footer_menu($menu_name) {

        $items = wp_get_nav_menu_items($menu_name);
        if (empty($items)) return '';

        $menu_items = [];
        foreach ($items as $item) {
            $menu_items[$item->ID] = $item;
        }

        // anchor translations
        $lang = PWE_Functions::lang();

        // anchor + label translations
        foreach ($menu_items as $id => $item) {
            apply_anchor_translation($item);

            if (!empty($item->title)) {
                $item->title = translate_global_label($item->title, $lang);
            }

            $menu_items[$id] = $item;
        }

        $output = '<ul class="pwe-footer__menu">';

        foreach ($menu_items as $item) {

            if ($item->menu_item_parent != 0) continue;

            $children = array_filter($menu_items, function($child) use ($item) {
                return $child->menu_item_parent == $item->ID;
            });

            $output .= '<li class="pwe-footer__menu-item'. (!empty($children) ? ' has-children' : '') .'">';

            $output .= '<a href="' . esc_url($item->url) . '">';
            $output .= esc_html($item->title);
            $output .= '</a>';

            // children
            if (!empty($children)) {
                $output .= '<ul class="pwe-footer__submenu">';

                foreach ($children as $child) {
                    $output .= '<li class="pwe-footer__submenu-item">';
                    $output .= '<a href="' . esc_url($child->url) . '">';
                    $output .= esc_html($child->title);
                    $output .= '</a>';
                    $output .= '</li>';
                }

                $output .= '</ul>';
            }

            $output .= '</li>';
        }

        $output .= '</ul>';

        return $output;
    }
}

/** Footer layout */
if (!function_exists('generateFooterNavEl')) {
    function generateFooterNavEl($menus) {

        $lang = strtolower(PWE_Functions::lang());

        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'];

        $page_url = $base_url;

        if ($lang !== 'pl') {
            $page_url .= '/' . $lang;
        }

        $logo_file_path = $lang === 'pl' ? '/doc/logo' : '/doc/logo-en';

        $logo_webp = $_SERVER['DOCUMENT_ROOT'] . $logo_file_path . '.webp';
        $logo_png  = $_SERVER['DOCUMENT_ROOT'] . $logo_file_path . '.png';

        if (file_exists($logo_webp)) {
            $logo_url = $logo_file_path . '.webp';
        } elseif (file_exists($logo_png)) {
            $logo_url = $logo_file_path . '.png';
        } else {
            $logo_url = '/wp-content/plugins/pwe-media/media/logo_pwe.webp';
        }


        $menu_titles_map = [
            'pl' => [ do_shortcode('[pwe_name_pl]'), 'DLA ODWIEDZAJĄCYCH', 'DLA WYSTAWCÓW'],
            'en' => [ do_shortcode('[pwe_name_en]'), 'FOR VISITORS', 'FOR EXHIBITORS' ],
            'de' => [ do_shortcode('[pwe_name_de]'), 'FÜR BESUCHER', 'FÜR AUSSTELLER' ],
            'it' => [ do_shortcode('[pwe_name_it]'), 'PER VISITATORI', 'PER ESPOSITORI' ],
            'lt' => [ do_shortcode('[pwe_name_lt]'), 'LANKYTOJAMS', 'PARODOS DALYVIAMS' ],
            'lv' => [ do_shortcode('[pwe_name_lv]'), 'Apmeklētājiem', 'Izstādes dalībniekiem' ],
            'cs' => [ do_shortcode('[pwe_name_cs]'), 'PRO NÁVŠTĚVNÍKY', 'PRO VYSTAVOVATELE' ],
            'sk' => [ do_shortcode('[pwe_name_sk]'), 'PRE NÁVŠTEVNÍKOV', 'PRE VYSTAVOVATEĽOV' ],
            'uk' => [ do_shortcode('[pwe_name_uk]'), 'ДЛЯ ВІДВІДУВАЧІВ', 'ДЛЯ ВИСТАВЦІВ' ],
            'ro' => [ do_shortcode('[pwe_name_ro]'), 'PENTRU VIZITATORI', 'PENTRU EXPOZANȚI' ],
            'et' => [ do_shortcode('[pwe_name_et]'), 'KÜLASTAJATELE', 'NÄITUSE OSAVÕTJATELE' ],
        ];

        $menu_titles = $menu_titles_map[$lang] ?? $menu_titles_map['en'];

        $output = '
        <div class="pwe-footer__nav notranslate" translate="no">
            <div class="pwe-footer__nav-wrapper">
                <div class="pwe-footer__nav-left-column">
                    <div class="pwe-footer__nav-logo-column">
                        <div class="pwe-footer__nav-logo-top">
                            <a href="' . $page_url . '">
                                <img src="/wp-content/plugins/pwe-media/media/logo_pwe_ufi.webp" alt="logo pwe & ufi">
                            </a>
                        </div>
                        <div class="pwe-footer__nav-logo-bottom text-centered">
                            <a href="' . $page_url . '">
                                <span><img src="' . $logo_url . '" alt="logo"></span>
                            </a>
                        </div>
                    </div>
                </div>   
                <div class="pwe-footer__nav-right-column">';

                    foreach ($menus as $index => $menu) {

                        if (empty($menu)) continue;

                        $output .= '
                        <div class="pwe-footer__nav-column">
                            <h4><span class="pwe-uppercase">' . $menu_titles[$index] . '</span></h4>
                            <div class="pwe-footer__nav-links">'
                                . render_footer_menu($menu) .
                            '</div>
                        </div>';
                    }

                $output .= '
                </div>
            </div>
        </div>';

        // socials
        $socials = [
            'facebook' => do_shortcode('[pwe_facebook]'),
            'instagram' => do_shortcode('[pwe_instagram]'),
            'linkedin' => do_shortcode('[pwe_linkedin]'),
            'youtube' => do_shortcode('[pwe_youtube]')
        ];

        $output .= '
        <div class="pwe-footer__bottom pwe-footer__row">
            <div class="pwe-footer__bottom-wrapper">';

                if (!empty($socials)) {
                    $output .= '
                    <div class="pwe-footer__bottom-icons">
                        <ul class="pwe-footer__social">';

                            foreach ($socials as $key => $url) {
                                if (empty($url)) continue;

                                $output .= '
                                <li class="pwe-footer__social-item-link social-icon '. esc_attr($key) .'">
                                    <a href="'. esc_url($url) .'" target="_blank">
                                        <i class="fa fa-'. esc_attr($key) .'"></i>
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
}

$lang = strtolower(PWE_Functions::lang());

$menus = wp_get_nav_menus();

$grouped = [];

foreach ($menus as $menu) {

    $name = strtolower($menu->name);

    // footer menu 1 en -> group=1 lang=en
    if (preg_match('/(\d+)\s*([a-z]{2})$/', $name, $matches)) {
        $group = $matches[1];
        $menu_lang = $matches[2];

        $grouped[$group][$menu_lang] = $menu->name;
    }
}

$base_url = ( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http' ) . '' . $_SERVER['HTTP_HOST'];
$page_url = 'https://' . $_SERVER['HTTP_HOST'] . PWE_Functions::lang_pl() ? '' : '/'. $lang .'/';

$output = '
<footer id="pweFooter" class="pwe-footer pwe-component">
    <div class="pwe-footer__wrapper">';




// Render footer if all 3 menus for the current language are available
$default_lang = ($lang === 'pl') ? 'pl' : 'en';

// Render footer if menus exist
if (
    !empty($grouped['1'][$default_lang]) &&
    !empty($grouped['2'][$default_lang]) &&
    !empty($grouped['3'][$default_lang])
) {
    $output .= generateFooterNavEl(
        [
            $grouped['1'][$default_lang],
            $grouped['2'][$default_lang],
            $grouped['3'][$default_lang]
        ]
    );
}

$output .= '  
    </div>
</footer>';

require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/script.php';

return $output;