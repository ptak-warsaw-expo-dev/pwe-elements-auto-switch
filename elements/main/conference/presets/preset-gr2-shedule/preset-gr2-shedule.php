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
    admin_log("Brak konferencji – wczytuję preset-gr2.php");

    PWE_Functions::assets_per_group($element_slug, 'gr2', $element_type);

    $output = include_once plugin_dir_path(__DIR__) . 'preset-gr2/preset-gr2.php';
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
            <div class="pwe-conference-schedule__title-container">
                <h2 class="pwe-subtitle">KONFERENCJE</h2>
                <h3 class="pwe-main-title">'. do_shortcode('[trade_fair_conferance]') .'</h3>
            </div>
            <img src="/doc/kongres-color.webp" alt="Congress logo">
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
                        <th>
                            <div class="pwe-conference-schedule__column-title">
                                <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M5.33333 5.33333C4.6 5.33333 3.97222 5.07222 3.45 4.55C2.92778 4.02778 2.66667 3.4 2.66667 2.66667C2.66667 1.93333 2.92778 1.30556 3.45 0.783333C3.97222 0.261111 4.6 0 5.33333 0C6.06667 0 6.69444 0.261111 7.21667 0.783333C7.73889 1.30556 8 1.93333 8 2.66667C8 3.4 7.73889 4.02778 7.21667 4.55C6.69444 5.07222 6.06667 5.33333 5.33333 5.33333ZM0 9.33333V8.8C0 8.42222 0.0973335 8.07511 0.292 7.75867C0.486667 7.44222 0.744889 7.20044 1.06667 7.03333C1.75556 6.68889 2.45556 6.43067 3.16667 6.25867C3.87778 6.08667 4.6 6.00044 5.33333 6C6.06667 5.99956 6.78889 6.08578 7.5 6.25867C8.21111 6.43156 8.91111 6.68978 9.6 7.03333C9.92222 7.2 10.1807 7.44178 10.3753 7.75867C10.57 8.07556 10.6671 8.42267 10.6667 8.8V9.33333C10.6667 9.7 10.5362 10.014 10.2753 10.2753C10.0144 10.5367 9.70045 10.6671 9.33333 10.6667H1.33333C0.966667 10.6667 0.652889 10.5362 0.392 10.2753C0.131111 10.0144 0.000444444 9.70045 0 9.33333Z" fill="var(--accent-color)"/>
                                </svg>
                                <p>' . PWECommonFunctions::languageChecker('Organizator', 'Organizer') . '</p>
                            </div>
                        </th>
                        <th>
                            <div class="pwe-conference-schedule__column-title">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M6.01335 6.66699H4.00666V8.00034H6.01335V6.66699ZM12.0067 6.66699H7.32666V8.00034H12.0067V6.66699ZM3.98666 9.33368H8.66666V10.667H3.98666V9.33368ZM12.0067 9.33368H10V10.667H12.0067V9.33368Z" fill="var(--accent-color)"/>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.33334 13.3337V2.66699H14.6667V13.3337H1.33334ZM2.66666 12.0003H13.3333V4.00034H2.66666V12.0003Z" fill="var(--accent-color)"/>
                                </svg>
                                <p>' . PWECommonFunctions::languageChecker('Temat', 'Subject') . '</p>
                            </div>
                        </th>
                        <th>
                            <div class="pwe-conference-schedule__column-title">
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.3333 1.33333H10V0.666667C10 0.489856 9.92976 0.320286 9.80474 0.195262C9.67971 0.0702379 9.51014 0 9.33333 0C9.15652 0 8.98695 0.0702379 8.86193 0.195262C8.73691 0.320286 8.66667 0.489856 8.66667 0.666667V1.33333H4.66667V0.666667C4.66667 0.489856 4.59643 0.320286 4.4714 0.195262C4.34638 0.0702379 4.17681 0 4 0C3.82319 0 3.65362 0.0702379 3.5286 0.195262C3.40357 0.320286 3.33333 0.489856 3.33333 0.666667V1.33333H2C1.46957 1.33333 0.960859 1.54405 0.585787 1.91912C0.210714 2.29419 0 2.8029 0 3.33333V11.3333C0 11.8638 0.210714 12.3725 0.585787 12.7475C0.960859 13.1226 1.46957 13.3333 2 13.3333H11.3333C11.8638 13.3333 12.3725 13.1226 12.7475 12.7475C13.1226 12.3725 13.3333 11.8638 13.3333 11.3333V3.33333C13.3333 2.8029 13.1226 2.29419 12.7475 1.91912C12.3725 1.54405 11.8638 1.33333 11.3333 1.33333ZM12 11.3333C12 11.5101 11.9298 11.6797 11.8047 11.8047C11.6797 11.9298 11.5101 12 11.3333 12H2C1.82319 12 1.65362 11.9298 1.5286 11.8047C1.40357 11.6797 1.33333 11.5101 1.33333 11.3333V6.66667H12V11.3333ZM12 5.33333H1.33333V3.33333C1.33333 3.15652 1.40357 2.98695 1.5286 2.86193C1.65362 2.7369 1.82319 2.66667 2 2.66667H3.33333V3.33333C3.33333 3.51014 3.40357 3.67971 3.5286 3.80474C3.65362 3.92976 3.82319 4 4 4C4.17681 4 4.34638 3.92976 4.4714 3.80474C4.59643 3.67971 4.66667 3.51014 4.66667 3.33333V2.66667H8.66667V3.33333C8.66667 3.51014 8.73691 3.67971 8.86193 3.80474C8.98695 3.92976 9.15652 4 9.33333 4C9.51014 4 9.67971 3.92976 9.80474 3.80474C9.92976 3.67971 10 3.51014 10 3.33333V2.66667H11.3333C11.5101 2.66667 11.6797 2.7369 11.8047 2.86193C11.9298 2.98695 12 3.15652 12 3.33333V5.33333Z" fill="var(--accent-color)"/>
                                </svg>
                                <p>' . PWECommonFunctions::languageChecker('Termin', 'Deadline') . '</p>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody>';
                    foreach ($group as $conf) {
                        $href = '/' . PWECommonFunctions::languageChecker('wydarzenia', 'en/conferences') . '/?konferencja=' . esc_attr($conf['slug']);

                        // Multiple logos (array -> HTML)
                        if (!empty($conf['logo']) && is_array($conf['logo'])) {
                            $logo_html = '';
                            foreach ($conf['logo'] as $src) {
                                if (!empty($src)) {
                                    $logo_html .= '<img src="' . esc_url($src) . '" class="pwe-conference-schedule__org-logo">';
                                }
                            }
                        } else {
                            // Fallback
                            $logo_html = !empty($conf['logo']) ? '<img src="' . esc_url($conf['logo']) . '" class="pwe-conference-schedule__org-logo">': '';
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

                                    // ile dni trwa konferencja
                                    $span = $conf['end_index'] - $conf['start_index'] + 1;

                                    // generujemy bloki dni dla całego okresu targów
                                    $days = array_slice($fairDays, 0, $totalDays);

                                    $dates_html = '<div class="pwe-conference-schedule__dates">';

                                    foreach ($days as $index => $day) {
                                        $dayFormatted = date_i18n('D, d M', strtotime($day));

                                        // aktywny (dzień konferencji) / nieaktywny (dzień targów, ale poza zakresem)
                                        $isActive = ($index >= $conf['start_index'] && $index <= $conf['end_index']);

                                        if ($isActive) {
                                            $dates_html .= '
                                                <div class="pwe-conference-schedule__date active">' . esc_html($dayFormatted) . '</div>';
                                        } else {
                                            $dates_html .= '
                                                <div class="pwe-conference-schedule__date inactive">' . esc_html($dayFormatted) . '</div>';
                                        }
                                    }

                                    $dates_html .= '</div>';

                                    $html .= '
                                    <td colspan="' . $totalDays . '">' . $dates_html . '</td>';

                                    $i = $conf['end_index'];

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
                <div class="pwe-btn-container header-button">
                    <a class="pwe-link pwe-btn btn-visitors" 
                        href="'. PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') .'" 
                        alt="'. PWECommonFunctions::languageChecker('link do rejestracji', 'link to registration') .'">
                            '. PWECommonFunctions::languageChecker('Weź udział', 'Take a part') .'
                            <span class="btn-angle-right">🡲</span>
                    </a>
                </div>
                <div class="pwe-btn-container header-button">
                    <a class="pwe-link pwe-btn btn-more" 
                        href="'. PWECommonFunctions::languageChecker('/wydarzenia/', '/en/conferences/') .'" 
                        alt="'. PWECommonFunctions::languageChecker('Konferencja', 'Conference') .'">
                            '. PWECommonFunctions::languageChecker('Dowiedz się więcej', 'Find out more') .' 
                            <span class="btn-angle-right">🡲</span>
                    </a>
                </div>
            </div>

    </div>
</div>';

if ($useSwiper) {
    $output .= PWE_Swiper::swiperScripts('#pweConfSchedule', [0 => ['slidesPerView' => 1]], true);
}

return $output;
