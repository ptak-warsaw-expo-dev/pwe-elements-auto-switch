<?php
if (!defined('ABSPATH')) exit;

class Logotypes {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
                'week' => plugin_dir_path(__FILE__) . 'presets/week/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));
        $logotypes_slug = $atts['slug'] ?? $params['slug'] ?? 'patrons-partners';


        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $b2c = isset($atts['b2c']) ? $atts['b2c'] : false;

            $cap_logotypes_data = PWE_Functions::get_database_logotypes_data();
            $cap_logotypes_order = PWE_Functions::get_database_meta_data('logos_meta_order', $_SERVER['HTTP_HOST']);
            if (!empty($cap_logotypes_order)) { 
                $cap_logotypes_order = $cap_logotypes_order[0]->meta_data; 
            }

            $logotypes = [];

            if (!empty($cap_logotypes_data)) {

                // Logo saving function
                $saving_paths = function (&$logotypes, $logo_data) {

                    $meta = json_decode($logo_data->meta_data, true);
                    $data = json_decode($logo_data->data ?? '{}', true);

                    $currentLocale = get_locale();
                    $visibilityFlags = array_filter($data, function ($key) {
                        return preg_match('/^logos_[a-z]{2}_[A-Z]{2}$/', $key);
                    }, ARRAY_FILTER_USE_KEY);

                    if (empty($visibilityFlags)) {
                        $showLogo = true;
                    } else {
                        $allNull = true;
                        foreach ($visibilityFlags as $val) {
                            if (!is_null($val)) {
                                $allNull = false;
                                break;
                            }
                        }

                        if ($allNull) {
                            $showLogo = true;
                        } else {
                            $keyForCurrentLocale = 'logos_' . $currentLocale;

                            if (isset($visibilityFlags[$keyForCurrentLocale])) {
                                $showLogo = ($visibilityFlags[$keyForCurrentLocale] === 'true');
                            } else {
                                $showLogo = false;
                            }
                        }
                    }

                    if (!$showLogo) return;

                    $desc_pl = $meta["desc_pl"] ?? '';
                    $desc_en = $meta["desc_en"] ?? '';

                    $linkKey = PWE_Functions::languageChecker('logos_link', 'logos_link_en');
                    $altKey  = PWE_Functions::languageChecker('logos_alt', 'logos_alt_en');
                    $orderKey  = 'logos_order';

                    $logo_url = preg_replace('#^/uploads/domains/[^/]+/#', '/', $logo_data->logos_url);
                    $domain_server = str_replace('.', '-', strtolower($_SERVER['HTTP_HOST']));

                    $element = [
                        'url'      => 'https://cap.warsawexpo.eu/public/uploads/domains/' . $domain_server . $logo_url,
                        'name'     => $data['logos_exh_name'] ?? '',
                        'desc_pl'  => $desc_pl,
                        'desc_en'  => $desc_en,
                        'link'     => $data[$linkKey] ?? '',
                        'alt'      => $data[$altKey] ?? '',
                        'order'    => $logo_data->logos_order
                    ];

                    if (!in_array($element, $logotypes)) {
                        $logotypes[] = $element;
                    }
                };

                // Getting order from JSON ($cap_logotypes_order)
                $sorted_types = [];
                if (!empty($cap_logotypes_order)) {

                    // If JSON text – decode
                    if (!is_array($cap_logotypes_order)) {
                        $cap_logotypes_order = json_decode($cap_logotypes_order, true);
                    }

                    if (is_array($cap_logotypes_order)) {
                        asort($cap_logotypes_order, SORT_NUMERIC); // klucz -> wartość

                        // Available types in the database
                        $existing_types = array_unique(
                            array_map(fn($l) => $l->logos_type, $cap_logotypes_data)
                        );

                        // Getting types in order from JSON
                        foreach ($cap_logotypes_order as $key => $val) {
                            if (in_array($key, $existing_types)) {
                                $sorted_types[] = $key;
                            }
                        }
                    }
                }

                // Fallback – if JSON empty
                if (empty($sorted_types)) {
                    $sorted_types = array_unique(
                        array_map(fn($l) => $l->logos_type, $cap_logotypes_data)
                    );
                }

                //  BLOCK 1: patrons-partners
                if ($logotypes_slug === 'patrons-partners') {

                    $logotypes = [];          
                    $exclude = ["international-partner", "miedzynarodowy-patron-medialny", "europe-event"];
                    $grouped = [];

                    foreach ($cap_logotypes_data as $logo_data) {

                        // 1) 1) if it starts with header-
                        if (strpos($logo_data->logos_type, 'header-') === 0) {
                            continue;
                        }

                        // 2) if it exists in other blocks
                        if (in_array($logo_data->logos_type, $exclude)) {
                            continue;
                        }

                        // 3) only types from the arranged order
                        if (in_array($logo_data->logos_type, $sorted_types)) {
                            $grouped[$logo_data->logos_type][] = $logo_data;
                        }
                    }

                    // Listing by type order
                    foreach ($sorted_types as $type) {
                        if (!empty($grouped[$type])) {
                            // Sorting by order only in this group
                            usort($grouped[$type], function($a, $b) {
                                return ($a->logos_order ?? 999) <=> ($b->logos_order ?? 999);
                            });

                            foreach ($grouped[$type] as $logo_data) {
                                $saving_paths($logotypes, $logo_data);
                            }
                        }
                    }

                    if (count($logotypes) < 1) {
                        $logotypes = [];
                    }
                }

                // BLOCK 2: patrons-partners-conference
                if ($logotypes_slug === 'patrons-partners-conference') {

                    $logotypes = [];
                    $allowed = ["patron-medialny", "partner-merytoryczny"];
                    $grouped = [];

                    foreach ($cap_logotypes_data as $logo_data) {
                        // Types from the arranged order
                        if (in_array($logo_data->logos_type, $allowed)) {
                            $grouped[$logo_data->logos_type][] = $logo_data;
                        }
                    }

                    // Listing by type order
                    foreach ($allowed as $type) {
                        if (!empty($grouped[$type])) {
                            // Sorting by order only in this group
                            usort($grouped[$type], function($a, $b) {
                                return ($a->logos_order ?? 999) <=> ($b->logos_order ?? 999);
                            });

                            foreach ($grouped[$type] as $logo_data) {
                                $saving_paths($logotypes, $logo_data);
                            }
                        }
                    }

                    if (count($logotypes) < 1) $logotypes = [];
                }

                // BLOCK 3: patrons-partners-international
                if ($logotypes_slug === 'patrons-partners-international') {

                    $logotypes = [];
                    $allowed = ["international-partner", "miedzynarodowy-patron-medialny"];
                    $grouped = [];

                    foreach ($cap_logotypes_data as $logo_data) {
                        // Types from the arranged order
                        if (in_array($logo_data->logos_type, $allowed)) {
                            $grouped[$logo_data->logos_type][] = $logo_data;
                        }
                    }

                    // Listing by type order
                    foreach ($allowed as $type) {
                        if (!empty($grouped[$type])) {
                            // Sorting by order only in this group
                            usort($grouped[$type], function($a, $b) {
                                return ($a->logos_order ?? 999) <=> ($b->logos_order ?? 999);
                            });

                            foreach ($grouped[$type] as $logo_data) {
                                $saving_paths($logotypes, $logo_data);
                            }
                        }
                    }

                    if (count($logotypes) < 1) $logotypes = [];
                }

                // BLOCK 4: europe-event
                if ($logotypes_slug === 'europe-event') {

                    $logotypes = [];
                    $allowed = ["europe-event"];
                    $grouped = [];

                    foreach ($cap_logotypes_data as $logo_data) {
                        // Types from the arranged order
                        if ($logo_data->logos_type === "europe-event") {
                            $grouped[$logo_data->logos_type][] = $logo_data;
                        }
                    }

                    // Listing by type order
                    foreach ($allowed as $type) {
                        if (!empty($grouped[$type])) {
                            // Sorting by order only in this group
                            usort($grouped[$type], function($a, $b) {
                                return ($a->logos_order ?? 999) <=> ($b->logos_order ?? 999);
                            });

                            foreach ($grouped[$type] as $logo_data) {
                                $saving_paths($logotypes, $logo_data);
                            }
                        }
                    }

                    if (count($logotypes) < 1) $logotypes = [];
                }
            }

            //  BLOCK 5: patrons-partners-pwe (static files)
            if ($logotypes_slug === 'patrons-partners-pwe') {

                $logotypes = [];

                $files = glob(
                    $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/pwe-media/media/wspieraja-nas/*.{jpeg,jpg,png,webp,JPEG,JPG,PNG,WEBP}',
                    GLOB_BRACE
                ) ?: [];

                foreach ($files as $file) {
                    $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);

                    $exclude_file = strpos($relativePath, 'Instytut-mysli-ekologicznej-logo.webp') !== false
                        && $group === 'gr1';

                    if ($exclude_file) continue;

                    $logotypes[] = [
                        'url'     => $relativePath,
                        'desc_pl' => '',
                        'desc_en' => '',
                        'link'    => '',
                        'alt'     => pathinfo($file, PATHINFO_FILENAME),
                    ];
                }
            }

            $slug_id = ucfirst(str_replace(' ', '', ucwords(str_replace('-', ' ', $logotypes_slug))));

            if ($logotypes_slug === 'patrons-partners-international') {
                if (do_shortcode("[trade_fair_group]") === "gr2") {
                    $title = PWE_Functions::languageChecker('Patroni i Partnerzy Zagraniczni', 'Foreign Patrons and Partners');
                } else {
                    $title = PWE_Functions::languageChecker('Patroni Międzynarodowi', 'International Patrons');
                }
            } else if ($logotypes_slug === 'patrons-partners') {
                $title = PWE_Functions::languageChecker('Patroni i Partnerzy', 'Patrons and Partners');
            } else if ($logotypes_slug === 'patrons-partners-pwe') {
                $title = PWE_Functions::languageChecker('Partnerzy Ptak Warsaw Expo', 'Partners of Ptak Warsaw Expo');
            } else if ($logotypes_slug === 'patrons-partners-conference') {
                $title = PWE_Functions::languageChecker('Patroni Targów i Konferencji', 'Patrons Of The Trade Fair And Conference');
            } else if ($logotypes_slug === 'europe-event') {
                $title = PWE_Functions::languageChecker('Najważniejsze wydarzenia branżowe w europie', 'Key industry events in europe');
            }

            /* <-------------> General code end <-------------> */
            if (empty($logotypes)) {
                return;
            }
            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
