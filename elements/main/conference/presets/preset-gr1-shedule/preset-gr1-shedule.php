<?php

$__getFairDays = function (): array {
    $start_raw = do_shortcode('[trade_fair_datetotimer]'); // "Y/m/d H:i"
    $end_raw   = do_shortcode('[trade_fair_enddata]');     // "Y/m/d H:i"
    $start = DateTime::createFromFormat('Y/m/d H:i', $start_raw);
    $end   = DateTime::createFromFormat('Y/m/d H:i', $end_raw);
    if (!$start || !$end) return [];
    if ($end < $start) [$start, $end] = [$end, $start];
    $start = new DateTime($start->format('Y-m-d'));
    $end   = new DateTime($end->format('Y-m-d'));
    $days = [];
    for ($d = clone $start; $d <= $end; $d->modify('+1 day')) {
        $days[] = $d->format('Y-m-d');
    }
    return $days;
};

$__parseRange = function (?string $range): ?array {
    if (!$range) return null;
    $parts = explode(' to ', trim($range), 2);
    if (count($parts) !== 2) return null;
    $s = DateTime::createFromFormat('Y/m/d', trim($parts[0]));
    $e = DateTime::createFromFormat('Y/m/d', trim($parts[1]));
    if (!$s || !$e) return null;
    if ($e < $s) [$s, $e] = [$e, $s];
    return [new DateTime($s->format('Y-m-d')), new DateTime($e->format('Y-m-d'))];
};

$fair_days  = $__getFairDays();
$total_days = count($fair_days);
if ($total_days === 0) return '';

$all  = self::get_conferences_brief($domain);

function admin_log($msg) {
    if (function_exists('current_user_can') && current_user_can('administrator')) {
        $safe = json_encode($msg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
        echo "<script>console.log({$safe});</script>";
    }
}

$processed = [];
foreach ($all as $conf) {
    admin_log("Sprawdzam konf ID={$conf->id}, slug={$conf->conf_slug}");

    if (empty($conf->conf_date_range)) {
        admin_log("Pominięto: brak conf_date_range");
        continue;
    }
    if (!self::conference_overlaps_fair((string)$conf->conf_date_range)) {
        admin_log("Pominięto: brak overlap z targami, range={$conf->conf_date_range}");
        continue;
    }

    $range = $__parseRange((string)$conf->conf_date_range);
    if (!$range) {
        admin_log("Pominięto: nie udało się sparsować range={$conf->conf_date_range}");
        continue;
    }
    [$cStart, $cEnd] = $range;
    admin_log("Range OK: start={$cStart->format('Y-m-d')} end={$cEnd->format('Y-m-d')}");

    $fairStart = new DateTime(reset($fair_days));
    $fairEnd   = new DateTime(end($fair_days));
    if ($cStart < $fairStart) $cStart = clone $fairStart;
    if ($cEnd   > $fairEnd)   $cEnd   = clone $fairEnd;

    $start_index = array_search($cStart->format('Y-m-d'), $fair_days, true);
    $end_index   = array_search($cEnd->format('Y-m-d'),   $fair_days, true);
    if ($start_index === false || $end_index === false) {
        admin_log("Pominięto: nie znaleziono start/end w fair_days: {$cStart->format('Y-m-d')} / {$cEnd->format('Y-m-d')}");
        continue;
    }

    $org = self::getConferenceOrganizer((int)$conf->id, (string)$conf->conf_slug, $lang);
    if (!$org) {
        admin_log("Pominięto: brak organizatora dla ID={$conf->id}");
        continue;
    }

    $title_text = PWECommonFunctions::languageChecker(
        $conf->conf_name_pl ?: ($conf->conf_name_en ?: (string)$conf->conf_slug),
        $conf->conf_name_en ?: ($conf->conf_name_pl ?: (string)$conf->conf_slug)
    );

    $order = isset($conf->conf_order) && is_numeric($conf->conf_order) ? (int)$conf->conf_order : PHP_INT_MAX;

    $processed[] = [
        'title'       => $title_text,
        'logo'        => $org['logo_url'],
        'organizer'   => $org['desc'],
        'start_index' => $start_index,
        'end_index'   => $end_index,
        'slug'        => (string)$conf->conf_slug,
        'order'       => $order,
    ];
    admin_log("Dodano konferencję: " . json_encode(end($processed)));
}

if (empty($processed)) {
    admin_log("Brak konferencji – przełączam na preset-gr1.php i dodaję style"); 

    PWE_Functions::assets_per_group($element_slug, 'gr1', $element_type);

    $output = include_once plugin_dir_path(__DIR__) . 'preset-gr1/preset-gr1.php';
    echo $output;
    return;
}

// Sortowanie po conf_order rosnąco; przy remisie – alfabetycznie po tytule
usort($processed, function($a, $b) {
    if ($a['order'] === $b['order']) {
        return strcasecmp($a['title'], $b['title']);
    }
    return ($a['order'] < $b['order']) ? -1 : 1;
});

// Grupowanie po 5 – do swipera desktopowego
$grouped    = array_chunk($processed, 5);
$use_swiper = count($grouped) > 1;

$output  = '<div id="pweConfSchedule" class="pwe-conf-short-info-gr1-schedule">';
$output .= '<div class="pwe-conf-short-info-gr1-schedule__wrapper">';

/* TOP */
$output .= '<div class="pwe-conf-short-info-gr1-schedule__top">
    <img src="/doc/kongres-color.webp" alt="Congress logo">
    <div class="pwe-conf-short-info-gr1-schedule__title-container">
        <h2 class="pwe-conf-short-info-gr1-schedule__conf-name">' . do_shortcode('[trade_fair_conferance]') . '</h2>
        <h3>' . esc_html($title) . '</h3>
    </div>
</div>';

/* DESKTOP: multi-table (swiper lub pojedyncza) */
$output .= '<div class="pwe-conf-short-info-gr1-schedule__multi-table-wrapper">';
if ($use_swiper) {
    $output .= '<div class="swiper"><div class="swiper-wrapper">';
}

$renderTable = function(array $group) use ($fair_days, $total_days) {
    $html  = '<table class="pwe-conf-short-info-gr1-schedule__table">';
    $html .= '<thead><tr><th>' . PWECommonFunctions::languageChecker('Organizator', 'Organizer') . '</th>';
    $html .= '<th>' . PWECommonFunctions::languageChecker('Temat', 'Subject') . '</th>';
    foreach ($fair_days as $date) {
        $html .= '<th>' . date('d.m', strtotime($date)) . '</th>';
    }
    $html .= '</tr></thead><tbody>';

    foreach ($group as $conf) {
        $href = '/' . PWECommonFunctions::languageChecker('wydarzenia', 'en/conferences') . '/?konferencja=' . esc_attr($conf['slug']);
        $html .= '<tr class="pwe-conf-short-info-gr1-schedule__row-link" data-href="' . esc_url($href) . '">';

        $logo = $conf['logo'] ? '<img src="' . esc_url($conf['logo']) . '" alt="" class="pwe-conf-short-info-gr1-schedule__org-logo">' : '';
        $html .= '<td>' . $logo . '</td>';

        $html .= '<td><strong>' . esc_html(str_replace('<br>', '', $conf['title'])) . '</strong><br><small>' . esc_html($conf['organizer']) . '</small></td>';

        for ($i = 0; $i < $total_days; $i++) {
            if ($i === $conf['start_index']) {
                $colspan = $conf['end_index'] - $conf['start_index'] + 1;
                $html .= '<td colspan="' . (int)$colspan . '"><div class="pwe-conf-short-info-gr1-schedule__timeline-bar" style="width:100%"></div></td>';
                $i = $conf['end_index'];
            } else {
                $html .= '<td></td>';
            }
        }
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    return $html;
};

foreach ($grouped as $group) {
    if ($use_swiper) $output .= '<div class="swiper-slide">';
    $output .= $renderTable($group);
    if ($use_swiper) $output .= '</div>'; // .swiper-slide
}

if ($use_swiper) {
    $output .= '</div></div><div class="swiper-scrollbar"></div>';
}
$output .= '</div>'; // .multi-table-wrapper

/* MOBILE: listy kartowe (render zawsze; ukrywanie/pokazywanie CSS-em) */
$output .= '
<div class="pwe-conf-short-info-gr1-schedule__mobile-list-wrapper">
    <div class="pwe-conf-short-info-gr1-schedule__mobile-list">';

foreach ($processed as $conf) {
    $conf_days = array_slice($fair_days, $conf['start_index'], $conf['end_index'] - $conf['start_index'] + 1);
    $conf_days_formatted = implode(', ', array_map(fn($d) => date('d.m', strtotime($d)), $conf_days));

    $output .= '
        <div class="pwe-conf-short-info-gr1-schedule__mobile-card">'
        . (!empty($conf['logo']) ? '<img src="' . esc_url($conf['logo']) . '" alt="">' : '') .
        '<h3>' . esc_html($conf['title']) . '</h3>
            <p><strong>' . esc_html($conf['organizer']) . '</strong></p>
            <p><em>' . esc_html($conf_days_formatted) . '</em></p>
        </div>';
}

$output .= '
    </div>
</div>';

/* BUTTONS */
$output .= '<div class="pwe-conf-short-info-gr1-schedule__buttons">
    <a href="' . PWECommonFunctions::languageChecker('/rejestracja/', '/en/registration/') . '" class="pwe-main-btn--primary">'
        . PWECommonFunctions::languageChecker('Weź udział', 'Take part') . '</a>
    <a href="' . PWECommonFunctions::languageChecker('/wydarzenia/', '/en/conferences/') . '" class="pwe-main-btn--secondary">'
        . PWECommonFunctions::languageChecker('Dowiedz się więcej', 'Find out more') . '</a>
</div>';

$output .= '</div></div>'; // wrapper, kontener

if ($use_swiper) {
    $output .= PWE_Swiper::swiperScripts('#pweConfSchedule', [0 => ['slidesPerView' => 1]], true);
}

return $output;