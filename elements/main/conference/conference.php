<?php

if (!defined('ABSPATH')) exit;

class Conference {

    public static function get_data() {
        $domain = parse_url(site_url(), PHP_URL_HOST);
        $rows = self::get_conferences_brief($domain);
        $useSchedule = false;
        foreach ($rows as $r) {
            if (!empty($r->conf_date_range) && self::conference_overlaps_fair((string)$r->conf_date_range)) {
                $useSchedule = true;
                break;
            }
        }

        $home_fairs = [
            'warsawhome.eu',
            'warsawhomefurniture.com',
            'warsawhometextile.com',
            'warsawhomelight.com',
            'warsawhomekitchen.com',
            'warsawhomebathroom.com',
            'warsawbuild.eu',
            'mr.glasstec.pl'

        ];

        $is_home_fair = false;

        foreach ($home_fairs as $fair) {
            if (strpos($domain, $fair) !== false) {
                $is_home_fair = true;
                break;
            }
        }

        if (strpos($domain, 'warsawsecuritydefenceexpo.com') !== false) {
            $presets = [
                'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
            ];

            $useSchedule = true;
        } else {
            // Standard display logic for every domain, including HOME fairs.
            $presets = $useSchedule
                ? [
                    'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1-shedule/preset.php',
                    'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2-shedule/preset.php',
                    'week' => plugin_dir_path(__FILE__) . 'presets/week-shedule/preset.php',
                ]
                : [
                    'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                    'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
                ];
        }

        // HOME preset is additional and never replaces the standard preset.
        $home_presets = $is_home_fair ? ['gr2' => plugin_dir_path(__FILE__) . 'presets/gr2-home/preset.php',] : [];

        return [
            'types'       => ['main'],
            'presets'     => $presets,
            'homePresets' => $home_presets,
            'useSchedule' => $useSchedule,
            'isHomeFair'  => $is_home_fair,
        ];

    }

    public static function get_conferences_brief($domain) {
        $results = PWE_Functions::get_database_conferences_data($domain);

        if (empty($results) || !is_array($results)) {
            return [];
        }

        return $results;
    }

    public static function conference_overlaps_fair(string $conf_date_range): bool {
        $start_raw = do_shortcode('[trade_fair_datetotimer]'); // "Y/m/d H:i"
        $end_raw   = do_shortcode('[trade_fair_enddata]');     // "Y/m/d H:i"
        $fairStart = DateTime::createFromFormat('Y/m/d H:i', $start_raw);
        $fairEnd   = DateTime::createFromFormat('Y/m/d H:i', $end_raw);

        if (!$fairStart || !$fairEnd) {
            return false;
        }

        if ($fairEnd < $fairStart) {
            [$fairStart, $fairEnd] = [$fairEnd, $fairStart];
        }

        // porównujemy po dniach
        $fairStart = DateTime::createFromFormat('!Y-m-d', $fairStart->format('Y-m-d'));
        $fairEnd   = DateTime::createFromFormat('!Y-m-d', $fairEnd->format('Y-m-d'));
        $conf_date_range = trim($conf_date_range);

        if (strpos($conf_date_range, ' to ') !== false) {
            $parts = explode(' to ', $conf_date_range, 2);
            $cStart = DateTime::createFromFormat('!Y/m/d', trim($parts[0]));
            $cEnd   = DateTime::createFromFormat('!Y/m/d', trim($parts[1]));

            if (!$cStart || !$cEnd) {
                return false;
            }

            if ($cEnd < $cStart) {
                [$cStart, $cEnd] = [$cEnd, $cStart];
            }
        } else {
            $cStart = DateTime::createFromFormat('!Y/m/d', $conf_date_range);

            if (!$cStart) {
                return false;
            }

            $cEnd = clone $cStart;
        }

        return ($cStart <= $fairEnd) && ($cEnd >= $fairStart);
    }

    public static function getConferenceOrganizer($conf_id, $conf_slug, $lang) {

        $logo_url = 'https://cap.warsawexpo.eu/public/uploads/conf/' . $conf_slug . '/organizer/conf_organizer.webp';
        
        $organizer_name = '';

        $preferred_slugs = ($lang === 'pl')
            ? ['org-name_pl']
            : ['org-name_en', 'org-name_pl'];

        // Use the shared PWE cache layer:
        // STATIC -> TRANSIENT -> JSON FILE -> DATABASE
        $rows = PWE_Functions::get_database_conference_adds_data($conf_id);

        $by_slug = [];

        if (!empty($rows) && is_array($rows)) {

            foreach ($rows as $row) {

                // get_database_conference_adds_data() currently returns ARRAY_A.
                $slug = $row['slug'] ?? '';
                $data = $row['data'] ?? '';

                if ($slug !== '' && $data !== '' && $data !== 'null') {
                    $by_slug[$slug] = trim($data, "\"");
                }
            }
        }

        foreach ($preferred_slugs as $slug_key) {
            if (!empty($by_slug[$slug_key])) {
                $organizer_name = $by_slug[$slug_key];
                break;
            }
        }

        // This is an HTTP check, not a CAP database query.
        $has_logo = false;

        $response = wp_remote_head($logo_url);

        $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);

        if ($code >= 200 && $code < 400) {
            $has_logo = true;
        }

        if (empty($organizer_name) && !$has_logo) {
            return null;
        }

        return [
            'logo_url' => $has_logo ? $logo_url : null,
            'desc' => $organizer_name
        ];
    }

    public static function getConferenceOrganizersAll($conf_slug) {
        $domain = parse_url(site_url(), PHP_URL_HOST);

        if (empty($domain)) {
            return [];
        }

        // Use the shared PWE cache layer for conferences.
        $conferences = PWE_Functions::get_database_conferences_data($domain);

        if (empty($conferences) || !is_array($conferences)) {
            return [];
        }

        $conference = null;

        foreach ($conferences as $row) {

            if (isset($row->conf_slug) && (string) $row->conf_slug === (string) $conf_slug) {
                $conference = $row;
                break;
            }
        }

        if (!$conference || empty($conference->id) || empty($conference->organizers_img)) {
            return [];
        }

        $conf_id = (int) $conference->id;
        $logos = array_filter(array_map('trim', explode(',', $conference->organizers_img)));

        if (empty($logos)) {
            return [];
        }

        // Load all conference additions once through the shared cache layer.
        $conference_adds = PWE_Functions::get_database_conference_adds_data($conf_id);

        $adds_by_slug = [];

        if (!empty($conference_adds) && is_array($conference_adds)) {

            foreach ($conference_adds as $row) {
                $slug = $row['slug'] ?? '';

                if ($slug === '') {
                    continue;
                }

                $adds_by_slug[$slug] = $row['data'] ?? '';
            }

        }

        $results = [];

        foreach ($logos as $logo) {

            if ($logo === '') {
                continue;
            }

            $slug = 'org-' . $logo;

            $data = [];

            if (!empty($adds_by_slug[$slug])) {

                $decoded = json_decode($adds_by_slug[$slug], true);

                if (is_array($decoded)) {
                    $data = $decoded;
                }

            }

            $results[] = [
                'src' => 'https://cap.warsawexpo.eu/public/uploads/conf/' . $conf_slug . '/organizer/' . $logo,
                'data' => $data
            ];

        }

        return $results;
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $useSchedule = !empty($data['useSchedule']);

        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        $domain = parse_url(site_url(), PHP_URL_HOST);

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);

        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);

        $is_home_fair = !empty($data['isHomeFair']);

        // HOME assets are loaded additionally.
        if ($is_home_fair && !empty($data['homePresets'][$group])) {
            PWE_Functions::assets_per_group($element_slug, $group . '-home', $element_type);
        }

        // Standard assets: same logic as for every other domain.
        if ($useSchedule) {
            if (strpos($domain, 'warsawsecuritydefenceexpo.com') !== false) {
                PWE_Functions::assets_per_group($element_slug, $group, $element_type);
            } else {
                PWE_Functions::assets_per_group($element_slug, $group . '-shedule', $element_type);
            }
        } else {
            PWE_Functions::assets_per_group($element_slug, $group, $element_type);
        }

        $preset_file = $data['presets'][$group] ?? null;
        $home_preset_file = $data['homePresets'][$group] ?? null;

        if (
            ($preset_file && file_exists($preset_file)) ||
            ($home_preset_file && file_exists($home_preset_file))
        ) {

            /* <-------------> General code start <-------------> */

                $b2c = isset($atts['b2c']) ? $atts['b2c'] : false;
                $lang = PWE_Functions::languageChecker('pl', 'en');
                $domain = parse_url(site_url(), PHP_URL_HOST);

                $fairs_data_adds = PWE_Functions::get_database_fairs_data_adds($domain);
                $first_fair_adds = $fairs_data_adds[0] ?? null;

                $conf_name  = $first_fair_adds ? ($first_fair_adds->{'konf_name'} ?? '') : '';

                $multi_lang = PWE_Functions::lang();

                $conf_title = do_shortcode('[pwe_conference_title_' . $multi_lang . ']');
                $conf_desc = do_shortcode('[pwe_conference_desc_' . $multi_lang . ']');

                $show_standard_preset = !empty($conf_desc) || $useSchedule === true;

                // Keep the original visibility rule for the standard preset.
                // On HOME fairs, the additional -home preset may still render.
                if (!$show_standard_preset && !$is_home_fair) {
                    echo '<style>.pwe-element-auto-switch.conference {display:none;}</style>';
                    return;
                }

                // CAP logotypes of partners
                $cap_logotypes_data = PWE_Functions::get_database_logotypes_data();

                $partners = [];

                if (!empty($cap_logotypes_data)) {

                    $allowed_types = [
                        'partner-targow',
                        'patron-medialny',
                        'partner-strategiczny',
                        'partner-honorowy',
                        'principal-partner',
                        'industry-media-partner',
                        'partner-branzowy',
                        'partner-merytoryczny'
                    ];

                    foreach ($cap_logotypes_data as $logo_data) {
                        if (in_array($logo_data->logos_type, $allowed_types)) {
                            $partners[] = 'https://cap.warsawexpo.eu/public' . $logo_data->logos_url;
                        }
                    }

                }

                $conference_img = '/doc/new_template/conference_img.webp';
                $fallback_img   = '/wp-content/plugins/pwe-media/media/main-page/conference_img.webp';
                $conference_img_path = ABSPATH . ltrim($conference_img, '/');

                if (!is_file($conference_img_path)) {
                    $conference_img = $fallback_img;
                }

            /* <-------------> General code end <-------------> */

            // Additional HOME element.
            if ($is_home_fair && $home_preset_file && file_exists($home_preset_file)) {
                $home_output = include $home_preset_file;

                if ($home_output) {
                    echo do_shortcode($home_output);
                }
            }

            // Standard element.
            if ($show_standard_preset && $preset_file && file_exists($preset_file)) {
                $output = include $preset_file;

                if ($output) {
                    echo do_shortcode($output);
                }
            }

        }

    }

}