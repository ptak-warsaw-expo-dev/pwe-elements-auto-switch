<?php

/**
 * Gets all days of the fair as an array of YYYY-mm-dd.
 */
function getFairDays(): array {
    $startRaw = do_shortcode('[trade_fair_datetotimer]');
    $endRaw   = do_shortcode('[trade_fair_enddata]');

    $start = DateTime::createFromFormat('Y/m/d H:i', $startRaw);
    $end   = DateTime::createFromFormat('Y/m/d H:i', $endRaw);

    if (!$start || !$end) return [];

    if ($end < $start) {
        [$start, $end] = [$end, $start];
    }

    $start = new DateTime($start->format('Y-m-d'));
    $end   = new DateTime($end->format('Y-m-d'));

    $days = [];
    for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
        $days[] = $d->format('Y-m-d');
    }

    return $days;
}


/**
 * Parses a date range in the format "Y/m/d to Y/m/d".
 */
function parseDateRange(?string $range): ?array {
    if (!$range) return null;

    $parts = explode(' to ', trim($range), 2);
    if (count($parts) !== 2) return null;

    $start = DateTime::createFromFormat('Y/m/d', trim($parts[0]));
    $end   = DateTime::createFromFormat('Y/m/d', trim($parts[1]));

    if (!$start || !$end) return null;

    if ($end < $start) {
        [$start, $end] = [$end, $start];
    }

    return [
        new DateTime($start->format('Y-m-d')),
        new DateTime($end->format('Y-m-d')),
    ];
}


/**
 * Debug for administrator only.
 */
function admin_log($msg) {
    if (function_exists('current_user_can') && current_user_can('administrator')) {
        $safe = json_encode($msg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        echo "<script>console.log({$safe});</script>";
    }
}

$fairDays  = getFairDays();
$totalDays = count($fairDays);

if ($totalDays === 0) {
    return '';
}

$allConferences = self::get_conferences_brief($domain);
$processed = [];


foreach ($allConferences as $conf) {

    admin_log("Sprawdzam konferencję ID={$conf->id}, slug={$conf->conf_slug}");

    if (empty($conf->conf_date_range)) {
        admin_log("Brak conf_date_range – pomijam");
        continue;
    }

    if (!self::conference_overlaps_fair((string)$conf->conf_date_range)) {
        admin_log("Brak overlapu z targami dla: {$conf->conf_date_range}");
        continue;
    }

    $range = parseDateRange((string)$conf->conf_date_range);
    if (!$range) {
        admin_log("Nie udało się parsować zakresu: {$conf->conf_date_range}");
        continue;
    }

    [$cStart, $cEnd] = $range;

    admin_log("Zakres OK: {$cStart->format('Y-m-d')} – {$cEnd->format('Y-m-d')}");

    $fairStart = new DateTime(reset($fairDays));
    $fairEnd   = new DateTime(end($fairDays));

    $cStart = max($cStart, $fairStart);
    $cEnd   = min($cEnd,   $fairEnd);

    $startIndex = array_search($cStart->format('Y-m-d'), $fairDays, true);
    $endIndex   = array_search($cEnd->format('Y-m-d'),   $fairDays, true);

    if ($startIndex === false || $endIndex === false) {
        admin_log("Nie znaleziono start/end w fairDays");
        continue;
    }

    // Conference Organizer (OLD)
    $organizer = self::getConferenceOrganizer(
        (int)$conf->id,
        (string)$conf->conf_slug,
        $lang
    );

    // Conference organizers (NEW)
    $organizers_all = self::getConferenceOrganizersAll($conf->conf_slug);

    // Preparing lists
    $org_src_list  = [];
    $org_name_list = [];

    if (!empty($organizers_all)) {

        foreach ($organizers_all as $o) {

            if (empty($o['src'])) {
                continue;
            }

            // Collect all the logos
            $org_src_list[] = esc_url($o['src']);

            // Names of the organizers
            $name_pl = !empty($o['data']['orgNamePl']) ? esc_html($o['data']['orgNamePl']) : '';
            $name_en = !empty($o['data']['orgNameEn']) ? esc_html($o['data']['orgNameEn']) : '';

            $name = ($lang === 'PL')
                ? $name_pl
                : (!empty($name_en) ? $name_en : $name_pl);

            if (!empty($name)) {
                $org_name_list[] = $name;
            }
        }

        // Final values ​​(if empty - will go to fallback)
        $org_src  = $org_src_list;
        $org_name = implode(', ', $org_name_list);

    } else if ($organizer && !empty($organizer['logo_url'])) {

        // Fallback — old way, single organizer
        $org_src  = [ esc_url($organizer['logo_url']) ]; // też w tablicy, dla spójności
        $org_name = esc_html($organizer['desc']);

    } else {

        admin_log("Brak organizatora – pomijam");
        continue;
    }

    // Conference title
    $title = PWECommonFunctions::languageChecker(
        $conf->conf_name_pl ?: ($conf->conf_name_en ?: $conf->conf_slug),
        $conf->conf_name_en ?: ($conf->conf_name_pl ?: $conf->conf_slug)
    );

    $order = is_numeric($conf->conf_order) ? (int)$conf->conf_order : PHP_INT_MAX;

    $processed[] = [
        'title'       => $title,
        'logo'        => $org_src,      // Array
        'organizer'   => $org_name,     // "ABC, XYZ"
        'start_index' => $startIndex,
        'end_index'   => $endIndex,
        'slug'        => (string)$conf->conf_slug,
        'order'       => $order,
    ];

    admin_log("Dodano konferencję: " . json_encode(end($processed)));
}

// If conference is empty

if (empty($processed)) {
    admin_log("Brak konferencji – wczytuję preset-gr1.php");

    PWE_Functions::assets_per_group($element_slug, 'gr1', $element_type);

    $output = include_once plugin_dir_path(__DIR__) . 'preset-gr1/preset-gr1.php';
    echo $output;

    return;
}

// Sorting

usort($processed, function($a, $b) {
    return $a['order'] <=> $b['order']
        ?: strcasecmp($a['title'], $b['title']);
});

// Render view

$groups = array_chunk($processed, 5);
$useSwiper = count($groups) > 1;

$output  = '
<div id="pweConfSchedule" class="pwe-conference-schedule">
    <div class="pwe-conference-schedule__wrapper">

        <div class="pwe-conference-schedule__top">
            <img src="/doc/kongres-color.webp" alt="Congress logo">
            <div class="pwe-conference-schedule__title-container">
                <h2 class="pwe-conference-schedule__conf-name">' . do_shortcode('[trade_fair_conferance]') . '</h2>
                <h3>' . esc_html($title) . '</h3>
            </div>
        </div>

        <div class="pwe-conference-schedule__multi-table-wrapper">';

        if ($useSwiper) {
            $output .= '
            <div class="swiper">
                <div class="swiper-wrapper">';
        }

        /**
         * Array render
         */
        $renderTable = function(array $group) use ($fairDays, $totalDays) {

            $html = '
            <table class="pwe-conference-schedule__table">
                <thead>
                    <tr>
                        <th>' . PWECommonFunctions::languageChecker('Organizator', 'Organizer') . '</th>
                        <th>' . PWECommonFunctions::languageChecker('Temat', 'Subject') . '</th>';

                        foreach ($fairDays as $date) {
                            $html .= '<th>' . date('d.m', strtotime($date)) . '</th>';
                        }

                    $html .= '
                    </tr>
                </thead>

                <tbody>';

                    foreach ($group as $conf) {

                        $href = '/' . PWECommonFunctions::languageChecker('wydarzenia', 'en/conferences')
                            . '/?konferencja=' . esc_attr($conf['slug']);

                        // Multiple logos (array -> HTML)
                        if (!empty($conf['logo']) && is_array($conf['logo'])) {
                            $logo_html = '';
                            foreach ($conf['logo'] as $src) {
                                if (!empty($src)) {
                                    $logo_html .= '
                                        <img src="' . esc_url($src) . '" class="pwe-conference-schedule__org-logo">';
                                }
                            }
                        } else {
                            // Fallback
                            $logo_html = !empty($conf['logo'])
                                ? '<img src="' . esc_url($conf['logo']) . '" class="pwe-conference-schedule__org-logo">'
                                : '';
                        }

                        $html .= '
                        <tr class="pwe-conference-schedule__row-link" data-href="' . esc_url($href) . '">
                            <td>
                                <div class="pwe-conference-schedule__logos">' . $logo_html . '</div></td>
                            <td>
                                <strong>' . esc_html($conf['title']) . '</strong><br>
                                <small>' . esc_html($conf['organizer']) . '</small>
                            </td>';

                            for ($i = 0; $i < $totalDays; $i++) {

                                if ($i === $conf['start_index']) {

                                    $span = $conf['end_index'] - $conf['start_index'] + 1;

                                    $html .= '
                                    <td colspan="' . $span . '">
                                        <div class="pwe-conference-schedule__timeline-bar"></div>
                                    </td>';

                                    $i = $conf['end_index'];

                                } else {
                                    $html .= '
                                    <td></td>';
                                }
                            }

                        $html .= '
                        </tr>';
                    }


                $html .= '
                </tbody>
            </table>';

            return $html;
        };


        // Rendering groups
        foreach ($groups as $group) {

            if ($useSwiper) {
                $output .= '
                <div class="swiper-slide">';
            }

            $output .= $renderTable($group);

            if ($useSwiper) {
                $output .= '
                </div>';
            }
        }

        if ($useSwiper) {
            $output .= '
                </div>
            </div>
            <div class="swiper-scrollbar"></div>';
        }

        $output .= '
        </div>';

        $output .= '
        <div class="pwe-conference-schedule__mobile-list-wrapper">
            <div class="pwe-conference-schedule__mobile-list">';

            foreach ($processed as $conf) {

                // Counting days
                $days = array_slice(
                    $fairDays,
                    $conf['start_index'],
                    $conf['end_index'] - $conf['start_index'] + 1
                );

                $daysFormatted = implode(', ', array_map(fn($d) => date('d.m', strtotime($d)), $days));

                // Multiple logos fo mobile
                $logo_html = '';

                if (!empty($conf['logo']) && is_array($conf['logo'])) {
                    foreach ($conf['logo'] as $src) {
                        if (!empty($src)) {
                            $logo_html .= '
                            <img src="' . esc_url($src) . '" alt="" class="pwe-conference-schedule__org-logo">';
                        }
                    }
                }

                // Fallback – if the logo were a string
                if (empty($logo_html) && !empty($conf['logo']) && is_string($conf['logo'])) {
                    $logo_html .= '
                    <img src="' . esc_url($conf['logo']) . '" alt="" class="pwe-conference-schedule__org-logo">';
                }

                $output .= '
                    <div class="pwe-conference-schedule__mobile-card">
                        <div class="pwe-conference-schedule__logos">
                            ' . $logo_html . '
                        </div>
                        <h3>' . esc_html($conf['title']) . '</h3>
                        <p><strong>' . esc_html($conf['organizer']) . '</strong></p>
                        <p><em>' . esc_html($daysFormatted) . '</em></p>
                    </div>';
            }

            $output .= '
            </div>
        </div>';

        $output .= '
        <div class="pwe-conference-schedule__buttons">
            <a href="' . PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') . '" class="pwe-main-btn--primary">'
                . PWECommonFunctions::languageChecker('Weź udział', 'Take part') . '</a>

            <a href="' . PWECommonFunctions::languageChecker('/wydarzenia/', '/en/conferences/') . '" class="pwe-main-btn--secondary">'
                . PWECommonFunctions::languageChecker('Dowiedz się więcej', 'Find out more') . '</a>
        </div>
    </div>
</div>';

if ($useSwiper) {
    $output .= PWE_Swiper::swiperScripts('#pweConfSchedule', [0 => ['slidesPerView' => 1]], true);
}

return $output;
