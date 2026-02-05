<?php
if (!defined('ABSPATH')) exit;

class Logotypes {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/preset-gr1/preset-gr1.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/preset-gr2/preset-gr2.php',
                'week' => plugin_dir_path(__FILE__) . 'presets/preset-week/preset-week.php',
            ],
        ];
    }

    // public static function url_returns_404(string $url): bool {
    //     $headers = @get_headers($url, 1);

    //     if ($headers === false) {
    //         return true;
    //     }

    //     if (is_array($headers) && isset($headers[0])) {  !!! PHP Fatal error:  Uncaught Error: Call to undefined function str_contains() in .../pwe-elements-auto-switch/elements/main/logotypes/logotypes.php:24
    //         return str_contains($headers[0], '404');
    //     }

    //     return true;
    // }

    public static function render($group, $params = [], $atts = []) {
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

            $cap_logotypes_data = PWE_Functions::get_database_logotypes_data();
            if (!empty($cap_logotypes_data)) {

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
                        // If no flags: show
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

                    if (!$showLogo) {
                        return;
                    }

                    $desc_pl = $meta["desc_pl"] ?? '';
                    $desc_en = $meta["desc_en"] ?? '';

                    $linkKey = PWE_Functions::languageChecker('logos_link', 'logos_link_en');
                    $altKey = PWE_Functions::languageChecker('logos_alt', 'logos_alt_en');

                    $element = [
                        'url'  => 'https://cap.warsawexpo.eu/public' . $logo_data->logos_url,
                        'name' => $data['logos_exh_name'] ?? '',
                        'desc_pl' => $desc_pl,
                        'desc_en' => $desc_en,
                        'link' => $data[$linkKey] ?? '',
                        'alt' => $data[$altKey] ?? ''
                    ];

                    // $isAdmin = current_user_can('administrator');

                    // if (!$isAdmin && self::url_returns_404($element['url'])) {
                    //     return;
                    // }

                    if (!in_array($element, $logotypes)) {
                        $logotypes[] = $element;
                    }
                };

                if ($logotypes_slug === 'patrons-partners') {
                    $logotypes = [];

                    $order = [
                        "partner-honorowy",
                        "partner-merytoryczny",
                        "partner-targow",
                        "patron-medialny",
                        "partner-branzowy",
                        "industry-media-partner",
                        "partner-strategiczny",
                        "principal-partner"
                    ];

                    // Group by logos_type
                    $grouped = [];
                    foreach ($cap_logotypes_data as $logo_data) {
                        if (in_array($logo_data->logos_type, $order)) {
                            $grouped[$logo_data->logos_type][] = $logo_data;
                        }
                    }

                    // Add in the specified order
                    foreach ($order as $type) {
                        if (!empty($grouped[$type])) {
                            foreach ($grouped[$type] as $logo_data) {
                                $saving_paths($logotypes, $logo_data);
                            }
                        }
                    }

                    if (count($logotypes) < 1) {
                        $logotypes = [];
                    }
                }

                if ($logotypes_slug === 'patrons-partners-conference') {
                    $logotypes = [];

                    foreach ($cap_logotypes_data as $logo_data) {
                        if ($logo_data->logos_type === "patron-medialny" ||
                            $logo_data->logos_type === "partner-merytoryczny") {
                            $saving_paths($logotypes, $logo_data);
                        }
                    }

                    if (count($logotypes) < 1) {
                        $logotypes = [];
                    }
                }

                if ($logotypes_slug === 'patrons-partners-international') {
                    $logotypes = [];

                    foreach ($cap_logotypes_data as $logo_data) {
                        if ($logo_data->logos_type === "international-partner" ||
                            $logo_data->logos_type === "miedzynarodowy-patron-medialny") {
                            $saving_paths($logotypes, $logo_data);
                        }
                    }

                    if (count($logotypes) < 1) {
                        $logotypes = [];
                    }
                }

                if ($logotypes_slug === 'europe-event') {
                    $logotypes = [];

                    foreach ($cap_logotypes_data as $logo_data) {
                        if ($logo_data->logos_type === "europe-event") {
                            $saving_paths($logotypes, $logo_data);
                        }
                    }

                    if (count($logotypes) < 1) {
                        $logotypes = [];
                    }
                }

            }

            if ($logotypes_slug === 'patrons-partners-pwe') {

                $logotypes = [];

                $files = glob(
                    $_SERVER['DOCUMENT_ROOT'] . '/wp-content/plugins/pwe-media/media/wspieraja-nas/*.{jpeg,jpg,png,webp,JPEG,JPG,PNG,WEBP}',
                    GLOB_BRACE
                ) ?: [];

                foreach ($files as $file) {
                    $relativePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $file);

                    $exclude_file =
                        strpos($relativePath, 'Instytut-mysli-ekologicznej-logo.webp') !== false
                        && $group === 'gr1';

                    if ($exclude_file) {
                        continue;
                    }

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
