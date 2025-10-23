<?php
if (!defined('ABSPATH')) exit;

class Conference {

    public static function get_conferences_brief($domain) {
        $cap_db = PWECommonFunctions::connect_database();
        if (!$cap_db) {
            if (current_user_can('administrator') && !is_admin()) {
                echo '<script>console.error("Brak połączenia z bazą danych.")</script>';
            }
            return [];
        }

        $results = $cap_db->get_results(
            $cap_db->prepare(
                "SELECT id, conf_slug, conf_name_pl, conf_name_en, conf_order, conf_date_range FROM conferences WHERE conf_site_link LIKE %s AND deleted_at IS NULL",
                '%' . $domain . '%'
            )
        );

        // SQL error checking
        if ($cap_db->last_error) {
            return [];
            if (current_user_can("administrator") && !is_admin()) {
                echo '<script>console.error("Błąd SQL: '. addslashes($cap_db->last_error) .'")</script>';
            }
        }

        return $results;
    }

    public static function conference_overlaps_fair(string $conf_date_range): bool {
        // zawsze z shortcodów
        $start_raw = do_shortcode('[trade_fair_datetotimer]'); // "Y/m/d H:i"
        $end_raw   = do_shortcode('[trade_fair_enddata]');     // "Y/m/d H:i"

        $fairStart = DateTime::createFromFormat('Y/m/d H:i', $start_raw);
        $fairEnd   = DateTime::createFromFormat('Y/m/d H:i', $end_raw);
        if (!$fairStart || !$fairEnd) return false;
        if ($fairEnd < $fairStart) [$fairStart, $fairEnd] = [$fairEnd, $fairStart];

        // porównujemy po datach (bez czasu)
        $fairStart = new DateTime($fairStart->format('Y-m-d'));
        $fairEnd   = new DateTime($fairEnd->format('Y-m-d'));

        // "YYYY/MM/DD to YYYY/MM/DD"
        $parts = explode(' to ', trim($conf_date_range), 2);
        if (count($parts) !== 2) return false;

        $cStart = DateTime::createFromFormat('Y/m/d', trim($parts[0]));
        $cEnd   = DateTime::createFromFormat('Y/m/d', trim($parts[1]));
        if (!$cStart || !$cEnd) return false;
        if ($cEnd < $cStart) [$cStart, $cEnd] = [$cEnd, $cStart];

        return ($cStart <= $fairEnd) && ($cEnd >= $fairStart);
    }

    public static function getConferenceOrganizer($conf_id, $conf_slug, $lang) {
        $logo_url = 'https://cap.warsawexpo.eu/public/uploads/conf/' . $conf_slug . '/organizer/conf_organizer.webp';
        $organizer_name = '';

        $preferred_slugs = ($lang === 'pl') ? ['org-name_pl'] : ['org-name_en', 'org-name_pl'];

        $cap_db = PWECommonFunctions::connect_database();
        if ($cap_db) {
            $placeholders = implode(',', array_fill(0, count($preferred_slugs), '%s'));
            $sql = $cap_db->prepare(
                "SELECT slug, data FROM conf_adds WHERE conf_id = %d AND slug IN ($placeholders)",
                array_merge([$conf_id], $preferred_slugs)
            );
            $rows = $cap_db->get_results($sql, ARRAY_A);

            $by_slug = [];
            foreach ($rows as $r) {
                if (!empty($r['data']) && $r['data'] !== 'null') {
                    $by_slug[$r['slug']] = trim($r['data'], "\"");
                }
            }
            foreach ($preferred_slugs as $slug_key) {
                if (!empty($by_slug[$slug_key])) { $organizer_name = $by_slug[$slug_key]; break; }
            }
        }

        $has_logo = false;
        $response = wp_remote_head($logo_url);
        $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
        if ($code >= 200 && $code < 400) $has_logo = true;

        if (empty($organizer_name) && !$has_logo) return null;

        return ['logo_url' => $has_logo ? $logo_url : null, 'desc' => $organizer_name];
    }

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

        $presets = $useSchedule
            ? [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/preset-gr1-shedule/preset-gr1-shedule.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/preset-gr2-shedule/preset-gr2-shedule.php',
            ]
            : [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/preset-gr1/preset-gr1.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/preset-gr2/preset-gr2.php',
            ];

        return [
            'types'       => ['main'],
            'presets'     => $presets,
            'useSchedule' => $useSchedule,
        ];
    }

    public static function render($group) {
        $data = self::get_data();
        $useSchedule = !empty($data['useSchedule']);
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);

        // Assets per group (schedule vs normal)
        if ($useSchedule) {
            PWE_Functions::assets_per_group($element_slug, 'gr1-shedule', $element_type);
        } else {
            PWE_Functions::assets_per_group($element_slug, $group, $element_type);
        }

        $preset_file = $data['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

                $lang = PWECommonFunctions::languageChecker('pl', 'en');
                $domain = parse_url(site_url(), PHP_URL_HOST);

                $fairs_data_adds = PWECommonFunctions::get_database_fairs_data_adds($domain);

                $first_fair_adds = $fairs_data_adds[0] ?? null;
                $name  = $first_fair_adds ? ($first_fair_adds->{'konf_name'} ?? '') : '';
                $title = $first_fair_adds ? ($first_fair_adds->{'konf_title_' . $lang} ?? '') : '';
                $desc  = $first_fair_adds ? ($first_fair_adds->{'konf_desc_' . $lang} ?? '') : '';


                // CAP logotypes of partners
                $cap_logotypes_data = PWECommonFunctions::get_database_logotypes_data();
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

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;
            if ($output) {
                echo $output;
            }
        }
    }
}
