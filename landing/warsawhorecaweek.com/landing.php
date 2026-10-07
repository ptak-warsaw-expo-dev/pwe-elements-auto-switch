<?php
if (!defined('ABSPATH')) exit;

$domain = $pwe_landing['domain'];
$lang = $pwe_landing['lang'];
// Media always live in /doc at the domain root. Do not use WPML-filtered home_url(),
// because on EN it would incorrectly produce /en/doc/....
$doc_scheme = is_ssl() ? 'https' : 'http';
$doc_url = $doc_scheme . '://' . $domain . '/doc/';
$static_assets_url = $doc_url . 'pwe-current-static-assets/';
$language_urls = $pwe_landing['language_urls'];
$other_lang = $lang === 'pl' ? 'en' : 'pl';

// Language-specific registration links.
$register_url = $lang === 'pl' ? '/pl/rejestracja/' : '/registration/';
$exhibitor_url = $lang === 'pl' ? '/pl/zostan-wystawca/' : '/become-an-exhibitor/';

$system = class_exists('PWE_System_Functions') ? 'PWE_System_Functions' : null;

$get_sc = static function ($shortcode, $fallback = '') {
    $value = trim(wp_strip_all_tags(do_shortcode($shortcode)));
    return ($value !== '' && $value !== $shortcode) ? $value : $fallback;
};

// Associated fairs from central DB. Fallback to Week domains if associates table is empty.
$associate_domains = [];
if ($system) {
    $associates = $system::get_database_associates_data($domain);
    if (!empty($associates[0]->fair_associates)) {
        $raw = $associates[0]->fair_associates;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : preg_split('/\s*,\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        }
        if (is_array($raw)) $associate_domains = $raw;
    }
    if (empty($associate_domains) && method_exists($system, 'get_database_week_data')) {
        $associate_domains = $system::get_database_week_data($domain);
    }
}
$associate_domains = array_values(array_unique(array_filter(array_map(static function ($value) {
    $value = strtolower(trim((string)$value));
    $value = preg_replace('#^https?://#', '', $value);
    $value = preg_replace('#^www\\.#', '', $value);
    return trim($value, '/');
}, is_array($associate_domains) ? $associate_domains : []))));

$format_number = static function ($value) {
    $value = trim((string) $value);
    if ($value === '') return '';

    $numeric = preg_replace('/[^0-9]/', '', $value);
    if ($numeric !== '' && is_numeric($numeric)) {
        return number_format((int) $numeric, 0, ',', ' ');
    }

    return $value;
};

$format_date_range = static function ($start, $end, $lang) {
    $start = trim((string) $start);
    $end = trim((string) $end);
    if ($start === '') return '';

    try {
        $start_date = new DateTime($start);
        $end_date = $end !== '' ? new DateTime($end) : clone $start_date;
    } catch (Exception $e) {
        return trim($start . ($end !== '' && $end !== $start ? ' – ' . $end : ''));
    }

    $months_pl = [
        1 => 'stycznia', 2 => 'lutego', 3 => 'marca', 4 => 'kwietnia',
        5 => 'maja', 6 => 'czerwca', 7 => 'lipca', 8 => 'sierpnia',
        9 => 'września', 10 => 'października', 11 => 'listopada', 12 => 'grudnia',
    ];
    $months_en = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    $sd = (int) $start_date->format('j');
    $ed = (int) $end_date->format('j');
    $sm = (int) $start_date->format('n');
    $em = (int) $end_date->format('n');
    $sy = $start_date->format('Y');
    $ey = $end_date->format('Y');

    if ($lang === 'pl') {
        if ($sy === $ey && $sm === $em) {
            return $sd === $ed
                ? $sd . ' ' . $months_pl[$sm] . ' ' . $sy
                : $sd . ' – ' . $ed . ' ' . $months_pl[$sm] . ' ' . $sy;
        }
        return $sd . ' ' . $months_pl[$sm] . ' ' . $sy . ' – ' . $ed . ' ' . $months_pl[$em] . ' ' . $ey;
    }

    if ($sy === $ey && $sm === $em) {
        return $sd === $ed
            ? $months_en[$sm] . ' ' . $sd . ', ' . $sy
            : $sd . ' – ' . $ed . ' ' . $months_en[$sm] . ' ' . $sy;
    }
    return $months_en[$sm] . ' ' . $sd . ', ' . $sy . ' – ' . $months_en[$em] . ' ' . $ed . ', ' . $ey;
};

$edition_label = static function ($edition, $lang) {
    $edition = trim((string) $edition);
    if ($edition === '') return '';

    $number = (int) preg_replace('/[^0-9]/', '', $edition);
    if (!$number) return $edition;

    if ($lang === 'pl') {
        return $number . '. edycja';
    }

    $mod100 = $number % 100;
    $suffix = 'th';
    if ($mod100 < 11 || $mod100 > 13) {
        switch ($number % 10) {
            case 1: $suffix = 'st'; break;
            case 2: $suffix = 'nd'; break;
            case 3: $suffix = 'rd'; break;
        }
    }
    return $number . $suffix . ' edition';
};

$events = [];
foreach ($associate_domains as $event_domain) {
    $name = $get_sc('[pwe_name_' . $lang . ' domain="' . esc_attr($event_domain) . '"]', $event_domain);
    $short_desc = $get_sc('[pwe_short_desc_' . $lang . ' domain="' . esc_attr($event_domain) . '"]', $name);
    $date_start = $get_sc('[pwe_date_start domain="' . esc_attr($event_domain) . '"]', '');
    $date_end = $get_sc('[pwe_date_end domain="' . esc_attr($event_domain) . '"]', '');
    $edition = $get_sc('[pwe_edition domain="' . esc_attr($event_domain) . '"]', '');

    $events[] = [
        'domain' => $event_domain,
        'name' => $name,
        'short_desc' => $short_desc,
        'date' => $format_date_range($date_start, $date_end, $lang),
        'edition' => $edition_label($edition, $lang),
        'visitors' => $format_number($get_sc('[pwe_visitors domain="' . esc_attr($event_domain) . '"]', '')),
        'exhibitors' => $format_number($get_sc('[pwe_exhibitors domain="' . esc_attr($event_domain) . '"]', '')),
        'area' => $format_number($get_sc('[pwe_area domain="' . esc_attr($event_domain) . '"]', '')),
        'image' => 'https://' . $event_domain . '/doc/kafelek_kalendarz.webp',
        'url' => 'https://' . $event_domain . '/',
    ];
}

// Exhibitors in the exact visual catalogue from the supplied HTML.
$exhibitors = $system ? $system::exhibitor_logos(2000, false) : [];
if (!is_array($exhibitors)) $exhibitors = [];
$catalog_rows = [];
foreach ($exhibitors as $item) {
    $stand = (string)($item['stand'] ?? '');
    $hall = $stand !== '' ? strtoupper(substr(trim($stand), 0, 1)) : '';
    $catalog_rows[] = [
        (string)($item['name'] ?? ''),
        $hall,
        $stand,
        (string)($item['logo'] ?? ''),
        (string)($item['www'] ?? ''),
        ''
    ];
}
$catalog_data = ['y26' => $catalog_rows, 'y27' => [], 'l26' => [], 'l27' => []];

// Partners/logotypes from ALL fairs assigned in get_database_associates_data().
$partner_logos = [];
$media_logos = [];
if ($system && method_exists($system, 'get_database_logotypes_data')) {
    $logo_domains = $associate_domains;

    // Avoid duplicate logos when the same partner is assigned to several fairs.
    $seen_logos = [];

    foreach ($logo_domains as $logo_domain) {
        $rows = $system::get_database_logotypes_data($logo_domain);

        foreach ($rows as $row) {
            $type = (string)($row->logos_type ?? '');
            if (
                $type === '' ||
                strpos($type, 'header-') === 0 ||
                in_array($type, ['international-partner', 'miedzynarodowy-patron-medialny', 'europe-event'], true)
            ) {
                continue;
            }

            $meta = json_decode($row->meta_data ?? '{}', true);
            if (!is_array($meta)) $meta = [];
            $data = json_decode($row->data ?? '{}', true);
            if (!is_array($data)) $data = [];

            $url = trim((string)($row->logos_url ?? ''));
            if ($url === '') continue;

            if (strpos($url, '/uploads/domains/') === 0) {
                $url = 'https://cap.warsawexpo.eu/public' . $url;
            } elseif (!preg_match('#^https?://#i', $url)) {
                $domain_server = str_replace('.', '-', strtolower($logo_domain));
                $url = 'https://cap.warsawexpo.eu/public/uploads/domains/' . $domain_server . '/' . ltrim($url, '/');
            }

            $link_key = $lang === 'pl' ? 'logos_link' : 'logos_link_en';
            $alt_key  = $lang === 'pl' ? 'logos_alt' : 'logos_alt_en';

            $link = trim((string)($data[$link_key] ?? $data['logos_link'] ?? ''));
            $alt  = trim((string)($data[$alt_key] ?? $data['logos_alt'] ?? $data['logos_exh_name'] ?? ''));

            if ($alt === '') {
                $alt = trim((string)($meta[$lang === 'pl' ? 'desc_pl' : 'desc_en'] ?? ''));
            }

            // The same physical logo can be assigned to multiple associated fairs.
            // Treat matching filenames as one logo, regardless of source domain or link.
            $logo_path = (string) parse_url($url, PHP_URL_PATH);
            $logo_file = strtolower(rawurldecode(basename($logo_path)));
            $dedupe_key = $logo_file !== '' ? $logo_file : strtolower($url);

            if (isset($seen_logos[$dedupe_key])) {
                continue;
            }

            $seen_logos[$dedupe_key] = true;

            $entry = [
                'url' => $url,
                'link' => $link,
                'alt' => $alt,
                'order' => (int)($row->logos_order ?? 999),
                'type' => $type,
                'domain' => $logo_domain,
            ];

            if ($type === 'patron-medialny') {
                $media_logos[] = $entry;
            } else {
                $partner_logos[] = $entry;
            }
        }
    }

    usort($partner_logos, static fn($a, $b) => $a['order'] <=> $b['order']);
    usort($media_logos, static fn($a, $b) => $a['order'] <=> $b['order']);
}
?>

<div id="pweLandingWeek" class="pwe-landing-week" data-lang="<?php echo esc_attr($lang); ?>">

    <header class="nav" id="nav">
        <a class="brand" href="#top">
            <svg aria-label="Warsaw HoReCa Week" class="logo-a" viewbox="0 0 420 150" xmlns="http://www.w3.org/2000/svg">
                <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="0" y="34">WARSAW</text>
                <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="78" font-weight="900" letter-spacing="1" x="-3" y="104">HORECA</text>
                <rect fill="#c8a24a" height="4" width="44" x="2" y="120">
                </rect>
                <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="58" y="136">WEEK</text>
            </svg>
        </a>
        <nav class="links">
            <a href="#stats">
                <span data-en="Facts">Liczby</span>
            </a>
            <a href="#halls">
                <span data-en="Hall plan">Plan hal</span>
            </a>
            <a href="#events">
                <span data-en="Fairs">Targi</span>
            </a>
            <a href="#catalog">
                <span data-en="Exhibitors">Wystawcy</span>
            </a>
            <a href="#program">
                <span data-en="Programme">Program</span>
            </a>
        </nav>
        <div class="navcta">
            <a class="lang" href="<?= esc_url($language_urls[$other_lang]) ?>" id="langBtn">
                <?= esc_html(strtoupper($other_lang)) ?>
            </a>
            <a class="btn btn-ghost" href="<?= esc_url($register_url) ?>" rel="noopener" target="_blank">
                <span data-en="Register">Zarejestruj się</span>
            </a>
            <a class="btn btn-gold" href="<?= esc_url($exhibitor_url) ?>" rel="noopener" target="_blank">
                <span data-en="Exhibit &amp; sponsor">Zostań wystawcą</span>
            </a>
        </div>
    </header>

    <section class="hero" id="top">
        <video autoplay="" class="hero-video" loop="" muted="" playsinline="" src='<?= esc_url($doc_url . "header.mp4") ?>'>
            <source src='<?= esc_url($doc_url . "header.mp4") ?>' type="video/mp4"/>
        </video>
        <div class="hero-shade">
        </div>
        <div class="hero-content">
            <div class="hero-card">
                <svg aria-label="Warsaw HoReCa Week" class="logo-a" viewbox="0 0 420 150" xmlns="http://www.w3.org/2000/svg">
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="0" y="34">WARSAW</text>
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="78" font-weight="900" letter-spacing="1" x="-3" y="104">HORECA</text>
                    <rect fill="#c8a24a" height="4" width="44" x="2" y="120">
                    </rect>
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="58" y="136">WEEK</text>
                </svg>
            </div>
            <div class="hero-date">9–11 <span data-en="March">marca</span> 2027 <span>|</span>
                <span data-en="Warsaw">Warszawa</span>
            </div>
            <div class="hero-info">
                <b>
                    <span data-en="International HoReCa Industry Trade Fairs">Międzynarodowe Targi Branży HoReCa</span>
                </b>
            </div>
            <div class="hero-info">
                <span data-en="Ptak Warsaw Expo, Warsaw">Ptak Warsaw Expo, Warszawa</span> · <span data-en="halls A–F">hale A–F</span> · <span data-en="18 exhibition pavilions">18 pawilonów wystawienniczych</span>
            </div>
            <div class="hero-cta">
                <a class="btn btn-gold btn-lg" href="<?= esc_url($exhibitor_url) ?>" rel="noopener" target="_blank">
                    <span data-en="Book your stand">Zarezerwuj powierzchnię</span>
                </a>
                <a class="btn btn-white btn-lg" href="<?= esc_url($register_url) ?>" rel="noopener" target="_blank">
                    <span data-en="Register as a visitor">Zarejestruj się jako odwiedzający</span>
                </a>
            </div>
        </div>
        <div class="hero-fairs">
            <img alt="EuroGastro" src="https://eurogastro.com.pl/doc/logo.webp"/>
            <img alt="World Hotel" src="https://worldhotel.pl/doc/logo.webp"/>
            <img alt="HoReCa FoodService Expo" src="https://horecafoodserviceexpo.com/doc/logo.webp"/>
            <img alt="Vending Poland Expo" src="https://vendingpolandexpo.com/doc/logo.webp"/>
            <img alt="Clean-Tech Expo" src="https://cleantechexpo.pl/doc/logo.webp"/>
            <img alt="Beer Warsaw Expo" src="https://beerwarsawexpo.com/doc/logo.webp"/>
            <img alt="Wine Warsaw Expo" src="https://winewarsawexpo.com/doc/logo.webp"/>
        </div>
    </section>

    <section class="stats" id="stats">
        <div class="wrap">
            <h2>
                <span data-en="Warsaw HoReCa Week in numbers">Warsaw HoReCa Week w liczbach</span>
            </h2>
            <div class="stats-grid">
                <div class="stat">
                    <div class="num" data-count="767">767</div>
                    <div class="lbl">
                        <span data-en="exhibitors">wystawców</span>
                    </div>
                    <div class="sub">
                        <span data-en="unique companies in the exhibitor catalogues">unikalne firmy w katalogach wystawców</span>
                    </div>
                </div>
                <div class="stat">
                    <div class="num" data-count="47 tys.">47 tys.</div>
                    <div class="lbl">
                        <span data-en="visitors">odwiedzających</span>
                    </div>
                    <div class="sub">
                        <span data-en="from 41 countries">z 41 krajów</span>
                    </div>
                </div>
                <div class="stat">
                    <div class="num" data-count="150+">150+</div>
                    <div class="lbl">
                        <span data-en="international exhibitors">wystawców zagranicznych</span>
                    </div>
                    <div class="sub">
                        <span data-en="from 31 countries · almost 20% of all">z 31 krajów · blisko 20% wszystkich</span>
                    </div>
                </div>
                <div class="stat">
                    <div class="num" data-count="1 100">1 100</div>
                    <div class="lbl">
                        <span data-en="brands">marek i brandów</span>
                    </div>
                    <div class="sub">
                        <span data-en="in the exhibitor offers">w ofertach wystawców</span>
                    </div>
                </div>
            </div>
            <div class="stats-line">
                <span>
                    <b>3</b>
                    <span data-en="days">dni</span>
                </span>
                <span>
                    <b>18</b>
                    <span data-en="exhibition pavilions · halls A–F">pawilonów wystawienniczych · hale A–F</span>
                </span>
                <span>
                    <b>20</b>
                    <span data-en="programme blocks">bloków programu</span>
                </span>
                <span>
                    <b>95</b>
                    <span data-en="partners, patrons and media">partnerów, patronów i mediów</span>
                </span>
            </div>
        </div>
    </section>

    <section class="venue">
        <div class="wrap venue-grid">
            <div class="venue-txt">
                <h2>
                    <span data-en="One week. The whole HoReCa market.">Jeden tydzień. Cały rynek HoReCa.</span>
                </h2>
                <p class="venue-sub">
                    <span data-en="Warsaw · Ptak Warsaw Expo · 9–11 March 2027">Warszawa · Ptak Warsaw Expo · 9–11 marca 2027</span>
                </p>
                <p>
                    <span data-en="Warsaw HoReCa Week is one event for the whole industry – from professional kitchen equipment, hospitality, food supply, vending and cleaning to beer and wine – over three days at Ptak Warsaw Expo in Warsaw. 767 companies exhibit and 47 thousand professionals visit the halls.">Warsaw HoReCa Week to jedno wydarzenie dla całej branży – od wyposażenia kuchni profesjonalnej, przez hotelarstwo, zaopatrzenie, vending i utrzymanie czystości, po piwo i wino – przez trzy dni w Ptak Warsaw Expo w Warszawie. Stoiska prezentuje 767 firm, a hale odwiedza 47 tysięcy profesjonalistów.</span>
                </p>
                <p>
                    <span data-en="More than half of the visitors are owners and managers of restaurants, hotels and chains – the people who make purchasing decisions.">Ponad połowa odwiedzających to właściciele i kadra zarządzająca lokali, hoteli i sieci – ludzie, którzy podejmują decyzje zakupowe.</span>
                </p>
                <a class="btn btn-navy" href="<?= esc_url($exhibitor_url) ?>" rel="noopener" target="_blank">
                    <span data-en="Become an exhibitor">Zostań wystawcą</span>
                </a>
            </div>
            <div class="venue-media">
                <video autoplay="" class="venue-video" loop="" muted="" playsinline="" poster="<?= esc_url($static_assets_url . 'venue/venue-poster.jpg') ?>">
                    <source src='<?= esc_url($doc_url . "header.mp4") ?>' type="video/mp4"/>
                </video>
                <div class="venue-cap">
                    <span data-en="Warsaw · Ptak Warsaw Expo">Warszawa · Ptak Warsaw Expo</span>
                </div>
            </div>
        </div>
    </section>

    <section class="events" id="events">
        <div class="wrap">
            <h2 class="center">
                <span data-en="One event. The whole industry.">Jedno wydarzenie. Cała branża.</span>
            </h2>
            <p class="po-txt">
                <span data-en="Warsaw HoReCa Week unites EuroGastro, World Hotel, HoReCa FoodService Expo, Vending Poland Expo, Clean-Tech Expo, Beer Warsaw Expo, Warsaw Gift &amp; Deco and Wine Warsaw Expo in one event for the hotel, restaurant and catering industry – 9–11 March 2027 at Ptak Warsaw Expo in Warsaw. One ticket, eighteen pavilions, one week.">Warsaw HoReCa Week łączy EuroGastro, World Hotel, HoReCa FoodService Expo, Vending Poland Expo, Clean-Tech Expo, Beer Warsaw Expo, Warsaw Gift &amp; Deco i Wine Warsaw Expo w jedno wydarzenie dla branży hotelarskiej, gastronomicznej i cateringowej – 9–11 marca 2027 w Ptak Warsaw Expo w Warszawie. Jeden bilet, osiemnaście pawilonów, jeden tydzień.</span>
            </p>
            <div class="btn-row">
                <a class="btn btn-gold" href="#halls">
                    <span data-en="Hall plan">Plan hal</span>
                </a>
                <a class="btn btn-gold" href="#catalog">
                    <span data-en="Exhibitor catalogue">Katalog wystawców</span>
                </a>
            </div>
            <div class="cal-grid">
                <?php foreach ($events as $event): ?>
                    <a class="cal-item" href="<?php echo esc_url($event['url']); ?>" rel="noopener" target="_blank">
                        <div class="cal-tile" style="background-image:url('<?php echo esc_url($event['image']); ?>')">
                            <div class="cal-short">
                                <h4><?php echo esc_html($event['short_desc']); ?></h4>
                            </div>

                            <div class="cal-strip">
                                <div class="cal-check">
                                    <p>
                                        <?php echo esc_html($lang === 'pl' ? 'Sprawdź' : 'Check'); ?> ❯
                                    </p>
                                </div>

                                <div class="cal-ed">
                                    <?php if (!empty($event['edition'])): ?>
                                        <p><?php echo esc_html($event['edition']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($event['date'])): ?>
                            <div class="cal-date">
                                <h5><?php echo esc_html($event['date']); ?></h5>
                            </div>
                        <?php endif; ?>

                        <div class="cal-word">
                            <?php echo esc_html($lang === 'pl' ? 'Statystyki' : 'Statistics'); ?>
                        </div>

                        <div class="cal-stats">
                            <?php if (!empty($event['visitors'])): ?>
                                <div class="cal-row">
                                    <div class="cal-ico" aria-hidden="true" style="width:30px;min-width:30px;flex:0 10 40px;display:flex;align-items:center;justify-content:center;">
                                        <svg width="30" height="30" viewBox="0 -10 40 40" xmlns="http://www.w3.org/2000/svg" style="width:30px;height:30px;display:block;flex:none;">
                                            <path d="M26.8 6.4C26.8 4.53 28.33 3 30.2 3C32.07 3 33.6 4.53 33.6 6.4C33.6 8.27 32.07 9.8 30.2 9.8C28.33 9.8 26.8 8.27 26.8 6.4ZM34.926 12.486C33.4353 11.8356 31.8264 11.4999 30.2 11.5C29.061 11.5 27.973 11.67 26.936 11.976C27.922 12.911 28.5 14.22 28.5 15.631V16.6H37V15.631C37 14.254 36.184 13.03 34.926 12.486ZM9.8 9.8C11.67 9.8 13.2 8.27 13.2 6.4C13.2 4.53 11.67 3 9.8 3C7.93 3 6.4 4.53 6.4 6.4C6.4 8.27 7.93 9.8 9.8 9.8ZM13.064 11.976C12.027 11.67 10.939 11.5 9.8 11.5C8.117 11.5 6.519 11.857 5.074 12.486C4.458 12.749 3.933 13.188 3.564 13.747C3.196 14.306 2.999 14.961 3 15.631V16.6H11.5V15.631C11.5 14.22 12.078 12.911 13.064 11.976ZM16.6 6.4C16.6 4.53 18.13 3 20 3C21.87 3 23.4 4.53 23.4 6.4C23.4 8.27 21.87 9.8 20 9.8C18.13 9.8 16.6 8.27 16.6 6.4ZM26.8 16.6H13.2V15.631C13.2 14.254 14.016 13.03 15.274 12.486C16.765 11.835 18.374 11.5 20 11.5C21.626 11.5 23.235 11.835 24.726 12.486C25.342 12.749 25.867 13.188 26.236 13.747C26.604 14.306 26.801 14.961 26.8 15.631V16.6Z" fill="#7f7f7f"/>
                                        </svg>
                                    </div>
                                    <div class="cal-name">
                                        <p class="cal-lbl"><?php echo esc_html($lang === 'pl' ? 'Odwiedzających' : 'Visitors'); ?></p>
                                        <p class="cal-val"><?php echo esc_html($event['visitors']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($event['exhibitors'])): ?>
                                <div class="cal-row">
                                    <div class="cal-ico" aria-hidden="true" style="width:30px;min-width:30px;flex:0 0 40px;display:flex;align-items:center;justify-content:center;">
                                        <svg width="30" height="30" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" style="width:30px;height:30px;display:block;flex:none;">
                                            <path d="M13.458 4.453C17.614 4.453 20.981 7.752 20.981 11.823C20.981 15.893 17.614 19.193 13.451 19.193C9.289 19.193 5.922 15.893 5.922 11.823C5.922 7.752 9.289 4.453 13.458 4.453ZM6.908 22.461H19.995C22.705 22.461 24.903 24.659 24.903 27.369C24.903 32.437 19.726 35.549 13.451 35.549C7.176 35.549 2 32.437 2 27.369C2 24.659 4.198 22.461 6.908 22.461ZM28.993 7.728C32.155 7.728 34.718 10.291 34.718 13.454C34.718 16.616 32.155 19.18 28.993 19.18C27.656 19.18 26.426 18.721 25.451 17.952C26.038 16.529 26.363 14.971 26.363 13.337C26.363 11.499 25.952 9.756 25.219 8.196C26.271 7.892 27.536 7.728 28.993 7.728ZM28.184 22.461H33.092C35.802 22.461 38 24.659 38 27.369C38 31.621 33.056 33.913 28.184 33.913C27.356 33.91 26.548 33.849 25.76 33.729C27.177 32.054 28.178 29.914 28.178 27.369C28.174 25.598 27.595 23.875 26.529 22.461H28.184Z" fill="#7f7f7f"/>
                                        </svg>
                                    </div>
                                    <div class="cal-name">
                                        <p class="cal-lbl"><?php echo esc_html($lang === 'pl' ? 'Wystawców' : 'Exhibitors'); ?></p>
                                        <p class="cal-val"><?php echo esc_html($event['exhibitors']); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($event['area'])): ?>
                                <div class="cal-row">
                                    <div class="cal-ico" aria-hidden="true" style="width:30px;min-width:30px;flex:0 0 40px;display:flex;align-items:center;justify-content:center;">
                                        <svg width="30" height="30" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" style="width:30px;height:30px;display:block;flex:none;">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M21.667 5H5V35H35V18.333H21.667V5ZM18.333 31.667H8.333V21.667H18.333V31.667ZM18.333 18.333H8.333V8.333H18.333V18.333ZM31.667 21.667V31.667H21.667V21.667H31.667ZM35 5V15H25V5H35ZM31.667 8.333H28.333V11.667H31.667V8.333Z" fill="#7f7f7f"/>
                                        </svg>
                                    </div>
                                    <div class="cal-name">
                                        <p class="cal-lbl">
                                            <?php echo $lang === 'pl' ? 'Powierzchnia<br>wystawiennicza' : 'Exhibition<br>space'; ?>
                                        </p>
                                        <p class="cal-val"><?php echo esc_html($event['area']); ?> m<sup>2</sup></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="po-divider">
                <span>
                    <span data-en="Part of">Część</span>
                </span>
            </div>
            <div class="po-brand">
                <svg aria-label="Warsaw HoReCa Week" class="logo-a" viewbox="0 0 420 150" xmlns="http://www.w3.org/2000/svg">
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="0" y="34">WARSAW</text>
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="78" font-weight="900" letter-spacing="1" x="-3" y="104">HORECA</text>
                    <rect fill="#c8a24a" height="4" width="44" x="2" y="120">
                    </rect>
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="58" y="136">WEEK</text>
                </svg>
                <div class="po-meta">9–11.03.2027 <b>|</b>
                    <span data-en="Ptak Warsaw Expo, Warsaw">Ptak Warsaw Expo, Warszawa</span>
                </div>
            </div>
            <p class="note center">
                <span data-en="Statistics according to the “in numbers” counters on the fair websites (as of 22.09.2026).">Statystyki wg liczników „w Liczbach” na stronach targów (stan 22.09.2026).</span>
            </p>
        </div>
    </section>

    <section class="halls" id="halls">
        <div class="wrap">
            <h2 class="center">
                <span data-en="Hall plan">Plan hal</span>
            </h2>
            <img alt="Plan hal Warsaw HoReCa Week 2027" class="halls-img" src="<?= esc_url($static_assets_url . 'mapa-hale/plan-hal-warsaw-horeca-week-2027.jpg') ?>"/>
        </div>
    </section>

    <section class="eco">
        <div class="wrap">
            <h2>
                <span data-en="The whole HoReCa industry in one place">Cała branża HoReCa w jednym miejscu</span>
            </h2>
            <p class="lead">
                <b>Warsaw HoReCa Week</b>
                <span data-en="brings together the companies that create the offer for gastronomy and hospitality with the people who buy it – in one week, at Ptak Warsaw Expo in Warsaw.">łączy firmy, które tworzą ofertę dla gastronomii i hotelarstwa, z ludźmi, którzy tę ofertę kupują – w jednym tygodniu, w Ptak Warsaw Expo w Warszawie.</span>
            </p>
            <div class="eco-grid">
                <div class="eco-card">
                    <div class="eco-img" style="background-image:url('<?= esc_url($static_assets_url . 'galeria/galeria-09.webp') ?>')">
                    </div>
                    <div class="eco-body">
                        <h3>
                            <span data-en="Exhibitors">Wystawcy</span>
                        </h3>
                        <p>
                            <span data-en="Manufacturers and distributors of kitchen equipment, hospitality, food supply, vending, cleaning technology, beer and wine – from local companies to international brands. Almost every fifth exhibitor comes from abroad.">Producenci i dystrybutorzy wyposażenia kuchni, hotelarstwa, zaopatrzenia żywnościowego, vendingu, technologii czyszczenia oraz piwa i wina – od lokalnych firm po międzynarodowe marki. Blisko co piąty wystawca przyjeżdża z zagranicy.</span>
                        </p>
                        <div class="eco-num">
                            <div>
                                <b>767</b>
                                <span data-en="companies">firm</span>
                            </div>
                            <div>
                                <b>31</b>
                                <span data-en="countries">krajów</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="eco-card">
                    <div class="eco-img" style="background-image:url('<?= esc_url($static_assets_url . 'galeria/galeria-01.webp') ?>')">
                    </div>
                    <div class="eco-body">
                        <h3>
                            <span data-en="Visitors">Odwiedzający</span>
                        </h3>
                        <p>
                            <span data-en="Owners and managers of restaurants, hotels, chains and catering, chefs, purchasing staff – more than half of the visitors make purchasing decisions in their companies.">Właściciele i menedżerowie restauracji, hoteli, sieci i cateringu, szefowie kuchni, kadra zakupowa – ponad połowa gości podejmuje decyzje zakupowe w swoich firmach.</span>
                        </p>
                        <div class="eco-num">
                            <div>
                                <b>47 tys.</b>
                                <span data-en="visitors">gości</span>
                            </div>
                            <div>
                                <b>41</b>
                                <span data-en="countries">krajów</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="eco-card">
                    <div class="eco-img" style="background-image:url('<?= esc_url($static_assets_url . 'galeria/galeria-02.webp') ?>')">
                    </div>
                    <div class="eco-body">
                        <h3>
                            <span data-en="Programme">Program</span>
                        </h3>
                        <p>
                            <span data-en="Conferences, forums, panels, culinary shows, tastings and competitions run by industry partners – twenty programme blocks in four halls.">Konferencje, fora, panele, pokazy kulinarne, degustacje i konkursy prowadzone przez partnerów branżowych – dwadzieścia bloków programu w czterech halach.</span>
                        </p>
                        <div class="eco-num">
                            <div>
                                <b>20</b>
                                <span data-en="blocks">bloków</span>
                            </div>
                            <div>
                                <b>14</b>
                                <span data-en="partners">partnerów</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="catalog" id="catalog">
        <div class="wrap">
            <h2 class="center">
                <span data-en="Warsaw HoReCa Week exhibitors">Wystawcy Warsaw HoReCa Week</span>
            </h2>
            <p class="lead">
                <span id="n26">
                </span>
                <span data-en="companies in the exhibitor catalogue">firm w katalogu wystawców</span>
            </p>
            <div class="cat-tools">
                <input data-ph-en="Search company…" id="catQ" placeholder="Szukaj firmy…" type="search"/>
                <div class="chips" id="hallChips">
                    <button class="chip active" data-h="" type="button">
                        <span data-en="All halls">Wszystkie hale</span>
                    </button>
                    <button class="chip" data-h="F" type="button">F</button>
                    <button class="chip" data-h="E" type="button">E</button>
                    <button class="chip" data-h="D" type="button">D</button>
                    <button class="chip" data-h="C" type="button">C</button>
                    <button class="chip" data-h="B" type="button">B</button>
                    <button class="chip" data-h="A" type="button">A</button>
                </div>
            </div>
            <div class="cat-grid" id="catGrid">
            </div>
            <div class="cat-more">
                <button class="btn btn-line" id="catMore" type="button">
                    <span data-en="Show more">Pokaż więcej</span>
                </button>
                <span id="catCount">
                </span>
            </div>
            <p class="note">
                <span data-en="Exhibitor catalogue according to warsawexpo.eu, duplicates removed.">Katalog wystawców wg warsawexpo.eu, po usunięciu duplikatów.</span>
            </p>
        </div>
    </section>
    
    <section class="brands">
        <div class="wrap">
            <h2 class="center">
                <span data-en="Brands">Marki</span>
            </h2>
            <p class="lead">
                <span data-en="International companies and brands at Warsaw HoReCa Week">Międzynarodowe firmy i marki na Warsaw HoReCa Week</span>
            </p>
            <div class="wall">
                <div class="lg">
                    <img alt="Nestlé Professional" src="<?= esc_url($static_assets_url . 'logotypy/marki/nestle-professional.webp') ?>" title="Nestlé Professional"/>
                </div>
                <div class="lg">
                    <img alt="Starbucks" src="<?= esc_url($static_assets_url . 'logotypy/marki/starbucks.webp') ?>" title="Starbucks"/>
                </div>
                <div class="lg">
                    <img alt="NESCAFÉ" src="<?= esc_url($static_assets_url . 'logotypy/marki/nescafe.webp') ?>" title="NESCAFÉ"/>
                </div>
                <div class="lg">
                    <img alt="LG Professional" src="<?= esc_url($static_assets_url . 'logotypy/marki/lg-professional.webp') ?>" title="LG Professional"/>
                </div>
                <div class="lg">
                    <img alt="Yamaha" src="<?= esc_url($static_assets_url . 'logotypy/marki/yamaha.webp') ?>" title="Yamaha"/>
                </div>
                <div class="lg">
                    <img alt="Miele" src="<?= esc_url($static_assets_url . 'logotypy/marki/miele.webp') ?>" title="Miele"/>
                </div>
                <div class="lg">
                    <img alt="Kärcher" src="<?= esc_url($static_assets_url . 'logotypy/marki/karcher.webp') ?>" title="Kärcher"/>
                </div>
                <div class="lg">
                    <img alt="Electrolux Professional" src="<?= esc_url($static_assets_url . 'logotypy/marki/electrolux-professional.webp') ?>" title="Electrolux Professional"/>
                </div>
                <div class="lg">
                    <img alt="SMEG Professional" src="<?= esc_url($static_assets_url . 'logotypy/marki/smeg-professional.webp') ?>" title="SMEG Professional"/>
                </div>
                <div class="lg">
                    <img alt="Philadelphia" src="<?= esc_url($static_assets_url . 'logotypy/marki/philadelphia.webp') ?>" title="Philadelphia"/>
                </div>
                <div class="lg">
                    <img alt="NESQUIK" src="<?= esc_url($static_assets_url . 'logotypy/marki/nesquik.webp') ?>" title="NESQUIK"/>
                </div>
                <div class="lg">
                    <img alt="Daikin" src="<?= esc_url($static_assets_url . 'logotypy/marki/daikin.webp') ?>" title="Daikin"/>
                </div>
                <div class="lg">
                    <img alt="L'Occitane en Provence" src="<?= esc_url($static_assets_url . 'logotypy/marki/l.webp') ?>" title="L'Occitane en Provence"/>
                </div>
                <div class="lg">
                    <img alt="TP-Link" src="<?= esc_url($static_assets_url . 'logotypy/marki/tp-link.webp') ?>" title="TP-Link"/>
                </div>
                <div class="lg">
                    <img alt="Würth" src="<?= esc_url($static_assets_url . 'logotypy/marki/wurth.webp') ?>" title="Würth"/>
                </div>
                <div class="lg">
                    <img alt="Vileda" src="<?= esc_url($static_assets_url . 'logotypy/marki/vileda.webp') ?>" title="Vileda"/>
                </div>
                <div class="lg">
                    <img alt="BRITA" src="<?= esc_url($static_assets_url . 'logotypy/marki/brita.webp') ?>" title="BRITA"/>
                </div>
                <div class="lg">
                    <img alt="Viessmann" src="<?= esc_url($static_assets_url . 'logotypy/marki/viessmann.webp') ?>" title="Viessmann"/>
                </div>
                <div class="lg">
                    <img alt="Franke" src="<?= esc_url($static_assets_url . 'logotypy/marki/franke.webp') ?>" title="Franke"/>
                </div>
                <div class="lg">
                    <img alt="Melitta Professional" src="<?= esc_url($static_assets_url . 'logotypy/marki/melitta-professional.webp') ?>" title="Melitta Professional"/>
                </div>
                <div class="lg">
                    <img alt="Ecovacs" src="<?= esc_url($static_assets_url . 'logotypy/marki/ecovacs.webp') ?>" title="Ecovacs"/>
                </div>
                <div class="lg">
                    <img alt="Hikvision" src="<?= esc_url($static_assets_url . 'logotypy/marki/hikvision.webp') ?>" title="Hikvision"/>
                </div>
                <div class="lg">
                    <img alt="Dahua Technology" src="<?= esc_url($static_assets_url . 'logotypy/marki/dahua-technology.webp') ?>" title="Dahua Technology"/>
                </div>
                <div class="lg">
                    <img alt="Zepter International" src="<?= esc_url($static_assets_url . 'logotypy/marki/zepter-international.webp') ?>" title="Zepter International"/>
                </div>
                <div class="lg">
                    <img alt="ASKO Professional" src="<?= esc_url($static_assets_url . 'logotypy/marki/asko-professional.webp') ?>" title="ASKO Professional"/>
                </div>
                <div class="lg">
                    <img alt="Tork" src="<?= esc_url($static_assets_url . 'logotypy/marki/tork.webp') ?>" title="Tork"/>
                </div>
                <div class="lg">
                    <img alt="BWT" src="<?= esc_url($static_assets_url . 'logotypy/marki/bwt.webp') ?>" title="BWT"/>
                </div>
                <div class="lg">
                    <img alt="Culligan" src="<?= esc_url($static_assets_url . 'logotypy/marki/culligan.webp') ?>" title="Culligan"/>
                </div>
                <div class="lg">
                    <img alt="SCJ Professional" src="<?= esc_url($static_assets_url . 'logotypy/marki/scj-professional.webp') ?>" title="SCJ Professional"/>
                </div>
                <div class="lg">
                    <img alt="RATIONAL" src="<?= esc_url($static_assets_url . 'logotypy/marki/rational.webp') ?>" title="RATIONAL"/>
                </div>
                <div class="lg">
                    <img alt="UNOX" src="<?= esc_url($static_assets_url . 'logotypy/marki/unox.webp') ?>" title="UNOX"/>
                </div>
                <div class="lg">
                    <img alt="Hobart" src="<?= esc_url($static_assets_url . 'logotypy/marki/hobart.webp') ?>" title="Hobart"/>
                </div>
                <div class="lg">
                    <img alt="Hoshizaki" src="<?= esc_url($static_assets_url . 'logotypy/marki/hoshizaki.webp') ?>" title="Hoshizaki"/>
                </div>
                <div class="lg">
                    <img alt="Winterhalter" src="<?= esc_url($static_assets_url . 'logotypy/marki/winterhalter.webp') ?>" title="Winterhalter"/>
                </div>
                <div class="lg">
                    <img alt="MEIKO" src="<?= esc_url($static_assets_url . 'logotypy/marki/meiko.webp') ?>" title="MEIKO"/>
                </div>
                <div class="lg">
                    <img alt="Robot-Coupe" src="<?= esc_url($static_assets_url . 'logotypy/marki/robot-coupe.webp') ?>" title="Robot-Coupe"/>
                </div>
                <div class="lg">
                    <img alt="Hamilton Beach Commercial" src="<?= esc_url($static_assets_url . 'logotypy/marki/hamilton-beach-commercial.webp') ?>" title="Hamilton Beach Commercial"/>
                </div>
                <div class="lg">
                    <img alt="HENDI" src="<?= esc_url($static_assets_url . 'logotypy/marki/hendi.webp') ?>" title="HENDI"/>
                </div>
                <div class="lg">
                    <img alt="Bartscher" src="<?= esc_url($static_assets_url . 'logotypy/marki/bartscher.webp') ?>" title="Bartscher"/>
                </div>
                <div class="lg">
                    <img alt="Cambro" src="<?= esc_url($static_assets_url . 'logotypy/marki/cambro.webp') ?>" title="Cambro"/>
                </div>
                <div class="lg">
                    <img alt="Evoca Group" src="<?= esc_url($static_assets_url . 'logotypy/marki/evoca-group.webp') ?>" title="Evoca Group"/>
                </div>
                <div class="lg">
                    <img alt="Nayax" src="<?= esc_url($static_assets_url . 'logotypy/marki/nayax.webp') ?>" title="Nayax"/>
                </div>
                <div class="lg">
                    <img alt="Datalogic" src="<?= esc_url($static_assets_url . 'logotypy/marki/datalogic.webp') ?>" title="Datalogic"/>
                </div>
                <div class="lg">
                    <img alt="SATO" src="<?= esc_url($static_assets_url . 'logotypy/marki/sato.webp') ?>" title="SATO"/>
                </div>
                <div class="lg">
                    <img alt="Elavon" src="<?= esc_url($static_assets_url . 'logotypy/marki/elavon.webp') ?>" title="Elavon"/>
                </div>
                <div class="lg">
                    <img alt="British American Tobacco" src="<?= esc_url($static_assets_url . 'logotypy/marki/british-american-tobacco.webp') ?>" title="British American Tobacco"/>
                </div>
                <div class="lg">
                    <img alt="Kopparberg" src="<?= esc_url($static_assets_url . 'logotypy/marki/kopparberg.webp') ?>" title="Kopparberg"/>
                </div>
                <div class="lg">
                    <img alt="Huhtamaki" src="<?= esc_url($static_assets_url . 'logotypy/marki/huhtamaki.webp') ?>" title="Huhtamaki"/>
                </div>
                <div class="lg">
                    <img alt="Sealed Air" src="<?= esc_url($static_assets_url . 'logotypy/marki/sealed-air.webp') ?>" title="Sealed Air"/>
                </div>
                <div class="lg">
                    <img alt="Duni" src="<?= esc_url($static_assets_url . 'logotypy/marki/duni.webp') ?>" title="Duni"/>
                </div>
                <div class="lg">
                    <img alt="PAPSTAR" src="<?= esc_url($static_assets_url . 'logotypy/marki/papstar.webp') ?>" title="PAPSTAR"/>
                </div>
                <div class="lg">
                    <img alt="Aquaphor" src="<?= esc_url($static_assets_url . 'logotypy/marki/aquaphor.webp') ?>" title="Aquaphor"/>
                </div>
                <div class="lg">
                    <img alt="Ferroli" src="<?= esc_url($static_assets_url . 'logotypy/marki/ferroli.webp') ?>" title="Ferroli"/>
                </div>
                <div class="lg">
                    <img alt="Halton" src="<?= esc_url($static_assets_url . 'logotypy/marki/halton.webp') ?>" title="Halton"/>
                </div>
                <div class="lg">
                    <img alt="WIESHEU" src="<?= esc_url($static_assets_url . 'logotypy/marki/wiesheu.webp') ?>" title="WIESHEU"/>
                </div>
                <div class="lg">
                    <img alt="PreGel" src="<?= esc_url($static_assets_url . 'logotypy/marki/pregel.webp') ?>" title="PreGel"/>
                </div>
                <div class="lg">
                    <img alt="MOGUNTIA Food Group" src="<?= esc_url($static_assets_url . 'logotypy/marki/moguntia-food-group.webp') ?>" title="MOGUNTIA Food Group"/>
                </div>
                <div class="lg">
                    <img alt="Haugen-Gruppen" src="<?= esc_url($static_assets_url . 'logotypy/marki/haugen-gruppen.webp') ?>" title="Haugen-Gruppen"/>
                </div>
                <div class="lg">
                    <img alt="Heuschen &amp; Schrouff" src="<?= esc_url($static_assets_url . 'logotypy/marki/heuschen-amp-schrouff.webp') ?>" title="Heuschen &amp; Schrouff"/>
                </div>
                <div class="lg">
                    <img alt="ADA Cosmetics" src="<?= esc_url($static_assets_url . 'logotypy/marki/ada-cosmetics.webp') ?>" title="ADA Cosmetics"/>
                </div>
            </div>
        </div>
    </section>

    <section class="flags">
        <div class="wrap">
            <h2>
                <span data-en="Exhibitors from all over the world">Wystawcy z całego świata</span>
            </h2>
            <div class="fbig">
                <div>
                    <b>150</b>
                    <span data-en="international exhibitors">wystawców zagranicznych</span>
                </div>
                <div>
                    <b>31</b>
                    <span data-en="countries">krajów</span>
                </div>
                <div>
                    <b>≈20%</b>
                    <span data-en="of all exhibitors">wszystkich wystawców</span>
                </div>
            </div>
        </div>
        <div class="mrow">
            <div class="mhead">
                <h3>
                    <span data-en="Europe">Europa</span>
                </h3>
            </div>
            <div class="marquee">
                <div class="mtrack left">
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/de.svg') ?>"/>
                        <span>
                            <span data-en="Germany">Niemcy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/it.svg') ?>"/>
                        <span>
                            <span data-en="Italy">Włochy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cz.svg') ?>"/>
                        <span>
                            <span data-en="Czechia">Czechy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ua.svg') ?>"/>
                        <span>
                            <span data-en="Ukraine">Ukraina</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/lt.svg') ?>"/>
                        <span>
                            <span data-en="Lithuania">Litwa</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/sk.svg') ?>"/>
                        <span>
                            <span data-en="Slovakia">Słowacja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/se.svg') ?>"/>
                        <span>
                            <span data-en="Sweden">Szwecja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/nl.svg') ?>"/>
                        <span>
                            <span data-en="Netherlands">Holandia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/gb.svg') ?>"/>
                        <span>
                            <span data-en="United Kingdom">Wielka Brytania</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/at.svg') ?>"/>
                        <span>
                            <span data-en="Austria">Austria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/es.svg') ?>"/>
                        <span>
                            <span data-en="Spain">Hiszpania</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/fi.svg') ?>"/>
                        <span>
                            <span data-en="Finland">Finlandia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/lv.svg') ?>"/>
                        <span>
                            <span data-en="Latvia">Łotwa</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/fr.svg') ?>"/>
                        <span>
                            <span data-en="France">Francja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/no.svg') ?>"/>
                        <span>
                            <span data-en="Norway">Norwegia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/hr.svg') ?>"/>
                        <span>
                            <span data-en="Croatia">Chorwacja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ch.svg') ?>"/>
                        <span>
                            <span data-en="Switzerland">Szwajcaria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ee.svg') ?>"/>
                        <span>
                            <span data-en="Estonia">Estonia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/bg.svg') ?>"/>
                        <span>
                            <span data-en="Bulgaria">Bułgaria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/be.svg') ?>"/>
                        <span>
                            <span data-en="Belgium">Belgia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/gr.svg') ?>"/>
                        <span>
                            <span data-en="Greece">Grecja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/pt.svg') ?>"/>
                        <span>
                            <span data-en="Portugal">Portugalia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cy.svg') ?>"/>
                        <span>
                            <span data-en="Cyprus">Cypr</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ie.svg') ?>"/>
                        <span>
                            <span data-en="Ireland">Irlandia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/de.svg') ?>"/>
                        <span>
                            <span data-en="Germany">Niemcy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/it.svg') ?>"/>
                        <span>
                            <span data-en="Italy">Włochy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cz.svg') ?>"/>
                        <span>
                            <span data-en="Czechia">Czechy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ua.svg') ?>"/>
                        <span>
                            <span data-en="Ukraine">Ukraina</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/lt.svg') ?>"/>
                        <span>
                            <span data-en="Lithuania">Litwa</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/sk.svg') ?>"/>
                        <span>
                            <span data-en="Slovakia">Słowacja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/se.svg') ?>"/>
                        <span>
                            <span data-en="Sweden">Szwecja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/nl.svg') ?>"/>
                        <span>
                            <span data-en="Netherlands">Holandia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/gb.svg') ?>"/>
                        <span>
                            <span data-en="United Kingdom">Wielka Brytania</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/at.svg') ?>"/>
                        <span>
                            <span data-en="Austria">Austria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/es.svg') ?>"/>
                        <span>
                            <span data-en="Spain">Hiszpania</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/fi.svg') ?>"/>
                        <span>
                            <span data-en="Finland">Finlandia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/lv.svg') ?>"/>
                        <span>
                            <span data-en="Latvia">Łotwa</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/fr.svg') ?>"/>
                        <span>
                            <span data-en="France">Francja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/no.svg') ?>"/>
                        <span>
                            <span data-en="Norway">Norwegia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/hr.svg') ?>"/>
                        <span>
                            <span data-en="Croatia">Chorwacja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ch.svg') ?>"/>
                        <span>
                            <span data-en="Switzerland">Szwajcaria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ee.svg') ?>"/>
                        <span>
                            <span data-en="Estonia">Estonia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/bg.svg') ?>"/>
                        <span>
                            <span data-en="Bulgaria">Bułgaria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/be.svg') ?>"/>
                        <span>
                            <span data-en="Belgium">Belgia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/gr.svg') ?>"/>
                        <span>
                            <span data-en="Greece">Grecja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/pt.svg') ?>"/>
                        <span>
                            <span data-en="Portugal">Portugalia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cy.svg') ?>"/>
                        <span>
                            <span data-en="Cyprus">Cypr</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ie.svg') ?>"/>
                        <span>
                            <span data-en="Ireland">Irlandia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/de.svg') ?>"/>
                        <span>
                            <span data-en="Germany">Niemcy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/it.svg') ?>"/>
                        <span>
                            <span data-en="Italy">Włochy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cz.svg') ?>"/>
                        <span>
                            <span data-en="Czechia">Czechy</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ua.svg') ?>"/>
                        <span>
                            <span data-en="Ukraine">Ukraina</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/lt.svg') ?>"/>
                        <span>
                            <span data-en="Lithuania">Litwa</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/sk.svg') ?>"/>
                        <span>
                            <span data-en="Slovakia">Słowacja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/se.svg') ?>"/>
                        <span>
                            <span data-en="Sweden">Szwecja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/nl.svg') ?>"/>
                        <span>
                            <span data-en="Netherlands">Holandia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/gb.svg') ?>"/>
                        <span>
                            <span data-en="United Kingdom">Wielka Brytania</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/at.svg') ?>"/>
                        <span>
                            <span data-en="Austria">Austria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/es.svg') ?>"/>
                        <span>
                            <span data-en="Spain">Hiszpania</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/fi.svg') ?>"/>
                        <span>
                            <span data-en="Finland">Finlandia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/lv.svg') ?>"/>
                        <span>
                            <span data-en="Latvia">Łotwa</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/fr.svg') ?>"/>
                        <span>
                            <span data-en="France">Francja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/no.svg') ?>"/>
                        <span>
                            <span data-en="Norway">Norwegia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/hr.svg') ?>"/>
                        <span>
                            <span data-en="Croatia">Chorwacja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ch.svg') ?>"/>
                        <span>
                            <span data-en="Switzerland">Szwajcaria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ee.svg') ?>"/>
                        <span>
                            <span data-en="Estonia">Estonia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/bg.svg') ?>"/>
                        <span>
                            <span data-en="Bulgaria">Bułgaria</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/be.svg') ?>"/>
                        <span>
                            <span data-en="Belgium">Belgia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/gr.svg') ?>"/>
                        <span>
                            <span data-en="Greece">Grecja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/pt.svg') ?>"/>
                        <span>
                            <span data-en="Portugal">Portugalia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cy.svg') ?>"/>
                        <span>
                            <span data-en="Cyprus">Cypr</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ie.svg') ?>"/>
                        <span>
                            <span data-en="Ireland">Irlandia</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="mrow">
            <div class="mhead">
                <h3>
                    <span data-en="Rest of the world">Reszta świata</span>
                </h3>
            </div>
            <div class="marquee">
                <div class="mtrack right">
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cn.svg') ?>"/>
                        <span>
                            <span data-en="China">Chiny</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/jp.svg') ?>"/>
                        <span>
                            <span data-en="Japan">Japonia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ge.svg') ?>"/>
                        <span>
                            <span data-en="Georgia">Gruzja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/az.svg') ?>"/>
                        <span>
                            <span data-en="Azerbaijan">Azerbejdżan</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/us.svg') ?>"/>
                        <span>
                            <span data-en="USA">USA</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cn.svg') ?>"/>
                        <span>
                            <span data-en="China">Chiny</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/jp.svg') ?>"/>
                        <span>
                            <span data-en="Japan">Japonia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ge.svg') ?>"/>
                        <span>
                            <span data-en="Georgia">Gruzja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/az.svg') ?>"/>
                        <span>
                            <span data-en="Azerbaijan">Azerbejdżan</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/us.svg') ?>"/>
                        <span>
                            <span data-en="USA">USA</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/cn.svg') ?>"/>
                        <span>
                            <span data-en="China">Chiny</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/jp.svg') ?>"/>
                        <span>
                            <span data-en="Japan">Japonia</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/ge.svg') ?>"/>
                        <span>
                            <span data-en="Georgia">Gruzja</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/az.svg') ?>"/>
                        <span>
                            <span data-en="Azerbaijan">Azerbejdżan</span>
                        </span>
                    </div>
                    <div class="mf">
                        <img alt="" src="<?= esc_url($static_assets_url . 'flagi/us.svg') ?>"/>
                        <span>
                            <span data-en="USA">USA</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="wrap">
            <p class="note fnote">
                <span data-en="International companies in the exhibitor catalogues – by the seat of the company or its group.">Firmy zagraniczne w katalogach wystawców – wg kraju siedziby firmy lub jej grupy.</span>
            </p>
        </div>
    </section>

    <section class="program" id="program">
        <div class="wrap">
            <h2 class="center">
                <span data-en="Programme">Program</span>
            </h2>

            <p class="lead">
                <span data-en="Six congress brands, culinary shows, tastings and competitions – in the halls, next to the stands.">Sześć marek kongresowych, pokazy kulinarne, degustacje i konkursy – w halach, obok stoisk.</span>
            </p>

            <div class="cg-grid">
                <div class="cg-card">
                    <div class="cg-img" style="background-image:url('<?= esc_url($static_assets_url . 'program/program-zdjecie-01.jpg') ?>')"></div>
                    <div class="cg-body">
                        <img alt="EuroGastro" class="cg-logo" src="<?= esc_url($static_assets_url . 'program/logo-eurogastro.webp') ?>"/>
                        <h3>
                            <span data-en="HoReCa Industry Conference">Konferencja Branży HoReCa</span>
                        </h3>
                        <span>EuroGastro</span>
                    </div>
                </div>

                <div class="cg-card">
                    <div class="cg-img" style="background-image:url('<?= esc_url($static_assets_url . 'program/program-zdjecie-02.webp') ?>')"></div>
                    <div class="cg-body">
                        <img alt="World Hotel" class="cg-logo" src="<?= esc_url($static_assets_url . 'program/logo-world-hotel.webp') ?>"/>
                        <h3>
                            <span data-en="Hotel Innovation Forum">Hotel Innovation Forum</span>
                        </h3>
                        <span>World Hotel</span>
                    </div>
                </div>

                <div class="cg-card">
                    <div class="cg-img" style="background-image:url('<?= esc_url($static_assets_url . 'program/program-zdjecie-03.webp') ?>')"></div>
                    <div class="cg-body">
                        <img alt="Vending Poland Expo" class="cg-logo" src="<?= esc_url($static_assets_url . 'program/logo-vending-poland-expo.webp') ?>"/>
                        <h3>
                            <span data-en="Smart Vending Congress">Smart Vending Congress</span>
                        </h3>
                        <span>Vending Poland Expo</span>
                    </div>
                </div>

                <div class="cg-card">
                    <div class="cg-img" style="background-image:url('<?= esc_url($static_assets_url . 'program/program-zdjecie-04.webp') ?>')"></div>
                    <div class="cg-body">
                        <img alt="Clean-Tech Expo" class="cg-logo" src="<?= esc_url($static_assets_url . 'program/logo-clean-tech-expo.webp') ?>"/>
                        <h3>
                            <span data-en="Cleaning Industry Congress">Cleaning Industry Congress</span>
                        </h3>
                        <span>Clean-Tech Expo</span>
                    </div>
                </div>

                <div class="cg-card">
                    <div class="cg-img" style="background-image:url('<?= esc_url($static_assets_url . 'program/program-zdjecie-05.webp') ?>')"></div>
                    <div class="cg-body">
                        <img alt="HoReCa FoodService Expo" class="cg-logo" src="<?= esc_url($static_assets_url . 'program/logo-horeca-foodservice-expo.webp') ?>"/>
                        <h3>
                            <span data-en="FoodSupply Summit">FoodSupply Summit</span>
                        </h3>
                        <span>HoReCa FoodService Expo</span>
                    </div>
                </div>

                <div class="cg-card">
                    <div class="cg-img" style="background-image:url('<?= esc_url($static_assets_url . 'galeria/galeria-11.webp') ?>')"></div>
                    <div class="cg-body">
                        <img alt="Beer Warsaw Expo" class="cg-logo" src="<?= esc_url($static_assets_url . 'program/logo-beer-warsaw-expo.webp') ?>"/>
                        <h3>
                            <span data-en="Innovation in Brewing Forum">Innovation in Brewing Forum</span>
                        </h3>
                        <span>Beer Warsaw Expo</span>
                    </div>
                </div>
            </div>

            <div class="cg-2026">
                <h3>
                    <span data-en="Programme blocks">Bloki programu</span>
                </h3>

                <div class="cg-nums">
                    <div>
                        <b>20</b>
                        <span data-en="programme blocks in 4 halls">bloków programu w 4 halach</span>
                    </div>
                    <div>
                        <b>10</b>
                        <span data-en="conferences, forums and panels">konferencji, forów i paneli</span>
                    </div>
                    <div>
                        <b>10</b>
                        <span data-en="live shows, tastings and a competition">pokazów, degustacji i konkurs</span>
                    </div>
                    <div>
                        <b>14</b>
                        <span data-en="partners running their own block">partnerów z własnym blokiem</span>
                    </div>
                </div>

                <div class="ag-grid">
                    <div class="ag-group">
                        <div class="ag-head">
                            <h4>EuroGastro · World Hotel</h4>
                            <span>13 <span data-en="blocks">bloków</span></span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Conference / forum">Konferencja / forum</span>
                            </span>
                            <b>
                                <span data-en="Polish Gastronomy Forum">Forum Polskiej Gastronomii</span>
                            </b>
                            <span class="ag-org">KRGiC</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Panel / presentations">Panel / prezentacje</span>
                            </span>
                            <b>
                                <span data-en="Trends and Exhibitor Presentations Panel – Hall F">Panel Trendów i Prezentacji Wystawców – hala F</span>
                            </b>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Live cooking">Pokazy</span>
                            </span>
                            <b>
                                <span data-en="Culinary Shows with OSSKiC">Pokazy Kulinarne z OSSKiC</span>
                            </b>
                            <span class="ag-org">OSSKiC</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Live cooking">Pokazy</span>
                            </span>
                            <b>
                                <span data-en="Polish Classics with a Global Twist">Polska klasyka w światowym twiście</span>
                            </b>
                            <span class="ag-org">Dawtona</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Tasting / show">Degustacja / pokaz</span>
                            </span>
                            <b>
                                <span data-en="Tasting of Baked Brioche Buns with Ice Cream">Degustacja pieczonych bułek brioszek z lodami</span>
                            </b>
                            <span class="ag-org">ProChef</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Competition">Konkurs</span>
                            </span>
                            <b>
                                <span data-en="Traditions of Polish Cuisine – Culinary Competition">Tradycje Kuchni Polskiej – konkurs kulinarny</span>
                            </b>
                            <span class="ag-org">OSSKiC</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Live cooking">Pokazy</span>
                            </span>
                            <b>
                                <span data-en="Where Technology Meets Culinary Passion">Miejsce, gdzie technologia spotyka pasję kulinarną</span>
                            </b>
                            <span class="ag-org">GRAFEN</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Live cooking">Pokazy</span>
                            </span>
                            <b>
                                <span data-en="Convenience in a Fine Dining Style">Convenience w wydaniu Fine Dining</span>
                            </b>
                            <span class="ag-org">
                                <span data-en="Chefs' Club">Klub Szefów Kuchni</span>
                            </span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Tastings">Degustacje</span>
                            </span>
                            <b>
                                <span data-en="Coffee Tastings">Degustacje kaw</span>
                            </b>
                            <span class="ag-org">Lavazza</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Live cooking">Pokazy</span>
                            </span>
                            <b>
                                <span data-en="Sempre Culinary Shows">Pokazy kulinarne Sempre</span>
                            </b>
                            <span class="ag-org">Sempre</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Live cooking">Pokazy</span>
                            </span>
                            <b>
                                <span data-en="Culinary Shows Promoting Opole Carp">Pokazy kulinarne z promocją karpia opolskiego</span>
                            </b>
                            <span class="ag-org">
                                <span data-en="Euro-Toques Poland">Euro-Toques Polska</span>
                            </span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Live cooking">Pokazy</span>
                            </span>
                            <b>
                                <span data-en="Tarsmak Culinary Shows">Pokazy kulinarne Tarsmak</span>
                            </b>
                            <span class="ag-org">Tarsmak</span>
                        </div>

                        <div class="ag-item">
                            <span class="ag-type">
                                <span data-en="Panel discussion">Panel dyskusyjny</span>
                            </span>
                            <b>
                                <span data-en="The Social Media Era in Gastronomy">Era Social Mediów w Gastronomii</span>
                            </b>
                            <span class="ag-org">
                                <span data-en="hosted by Jagna Niedzielska">prow. Jagna Niedzielska</span>
                            </span>
                        </div>
                    </div>

                    <div class="ag-col">
                        <div class="ag-group">
                            <div class="ag-head">
                                <h4>Vending Poland Expo</h4>
                                <span>2 <span data-en="blocks">bloki</span></span>
                            </div>

                            <div class="ag-item">
                                <span class="ag-type">
                                    <span data-en="Conference / forum">Konferencja / forum</span>
                                </span>
                                <b>
                                    <span data-en="Vending Leaders Forum: Business in the Age of Digital Transformation">Forum Liderów Vendingu: Biznes w obliczu cyfrowej transformacji</span>
                                </b>
                                <span class="ag-org">Puls Biznesu</span>
                            </div>

                            <div class="ag-item">
                                <span class="ag-type">
                                    <span data-en="Panel / presentations">Panel / prezentacje</span>
                                </span>
                                <b>
                                    <span data-en="Trends and Exhibitor Presentations Panel – Hall C">Panel Trendów i Prezentacji Wystawców – hala C</span>
                                </b>
                            </div>
                        </div>

                        <div class="ag-group">
                            <div class="ag-head">
                                <h4>Clean-Tech Expo</h4>
                                <span>1 <span data-en="block">blok</span></span>
                            </div>

                            <div class="ag-item">
                                <span class="ag-type">
                                    <span data-en="3-day conference">Konferencja 3-dniowa</span>
                                </span>
                                <b>
                                    <span data-en="Smart Cleaning Business – Technology, Education and Sales">Smart Cleaning Business – technologia, edukacja i sprzedaż</span>
                                </b>
                                <span class="ag-org">
                                    <span data-en="Polish Chamber of Cleaning Industry">Polska Izba Gospodarcza Czystości</span>
                                </span>
                            </div>
                        </div>

                        <div class="ag-group">
                            <div class="ag-head">
                                <h4>HoReCa FoodService Expo</h4>
                                <span>1 <span data-en="block">blok</span></span>
                            </div>

                            <div class="ag-item">
                                <span class="ag-type">
                                    <span data-en="Conference">Konferencja</span>
                                </span>
                                <b>
                                    <span data-en="Logistics, New Products and Certification in Gastronomy">Logistyka, nowe produkty i certyfikacja w gastronomii</span>
                                </b>
                                <span class="ag-org">KRGiC</span>
                            </div>
                        </div>

                        <div class="ag-group">
                            <div class="ag-head">
                                <h4>Beer · Wine · FoodService</h4>
                                <span>4 <span data-en="blocks">bloki</span></span>
                            </div>

                            <div class="ag-item">
                                <span class="ag-type">
                                    <span data-en="Panel / presentations">Panel / prezentacje</span>
                                </span>
                                <b>
                                    <span data-en="Trends and Exhibitor Presentations Panel – Hall D">Panel Trendów i Prezentacji Wystawców – hala D</span>
                                </b>
                            </div>

                            <div class="ag-item">
                                <span class="ag-type">
                                    <span data-en="Debate">Debata</span>
                                </span>
                                <b>
                                    <span data-en="The Future of Regional Beers and Polish Hop Varieties">Przyszłość piw regionalnych i polskich odmian chmielu</span>
                                </b>
                                <span class="ag-org">moderator Marek Gogola</span>
                            </div>

                            <div class="ag-item">
                                <span class="ag-type">
                                    <span data-en="Education block">Blok edukacyjny</span>
                                </span>
                                <b>
                                    <span data-en="Polish Wine Zone. Knowledge and Education for the Industry">Strefa Polskiego Wina. Wiedza i edukacja dla branży</span>
                                </b>
                                <span class="ag-org">Wine Me</span>
                            </div>

                            <div class="ag-item">
                                <span class="ag-type">
                                    <span data-en="Education block">Blok edukacyjny</span>
                                </span>
                                <b>
                                    <span data-en="Glass and Beer – a Perfect Pair with More Potential">Szkło i piwo – zgrany duet, który może więcej</span>
                                </b>
                                <span class="ag-org">Tableart</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<section class="moments">
    <div class="wrap">
        <h2 class="center">
            <span data-en="This is what Warsaw HoReCa Week looks like">Tak wygląda Warsaw HoReCa Week</span>
        </h2>
    </div>
    <div class="strip-wrap">
        <button aria-label="Previous" class="strip-btn prev" type="button">‹</button>
        <div class="strip" id="strip">
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-01.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-02.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-03.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-04.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-05.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-06.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-07.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-08.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-09.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-10.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-11.webp') ?>"/>
            </div>
            <div class="pol">
                <img alt="" src="<?= esc_url($static_assets_url . 'galeria/galeria-12.webp') ?>"/>
            </div>
        </div>
        <button aria-label="Next" class="strip-btn next" type="button">›</button>
    </div>
</section>

<section class="partners">
    <div class="wrap">
        <h2 class="center">
            <span data-en="Partners">Partnerzy</span>
        </h2>
        <p class="lead">
            <span data-en="Honorary patrons, strategic, content and industry partners and media patrons of Warsaw HoReCa Week">Patroni honorowi, partnerzy strategiczni, merytoryczni i branżowi oraz patroni medialni Warsaw HoReCa Week</span>
        </p>
        <h4>
            <span data-en="Patrons and partners">Patroni i partnerzy</span>
        </h4>
        <div class="wall">
            <?php foreach ($partner_logos as $logo): ?>
            <?php if (!empty($logo['link'])): ?>
            <a class="lg" href="<?php echo esc_url($logo['link']); ?>" rel="noopener" target="_blank">
                <img alt="<?php echo esc_attr($logo['alt']); ?>" loading="lazy" src="<?php echo esc_url($logo['url']); ?>"/>
            </a>
            <?php else: ?>
            <div class="lg">
                <img alt="<?php echo esc_attr($logo['alt']); ?>" loading="lazy" src="<?php echo esc_url($logo['url']); ?>"/>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <h4>
            <span data-en="Media patrons">Patroni medialni</span>
        </h4>
        <div class="wall small">
            <?php foreach ($media_logos as $logo): ?>
            <?php if (!empty($logo['link'])): ?>
            <a class="lg" href="<?php echo esc_url($logo['link']); ?>" rel="noopener" target="_blank">
                <img alt="<?php echo esc_attr($logo['alt']); ?>" loading="lazy" src="<?php echo esc_url($logo['url']); ?>"/>
            </a>
            <?php else: ?>
            <div class="lg">
                <img alt="<?php echo esc_attr($logo['alt']); ?>" loading="lazy" src="<?php echo esc_url($logo['url']); ?>"/>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta">
    <div class="wrap">
        <div class="cta-logo">
            <svg aria-label="Warsaw HoReCa Week" class="logo-a" viewbox="0 0 420 150" xmlns="http://www.w3.org/2000/svg">
                <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="0" y="34">WARSAW</text>
                <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="78" font-weight="900" letter-spacing="1" x="-3" y="104">HORECA</text>
                <rect fill="#c8a24a" height="4" width="44" x="2" y="120">
                </rect>
                <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="58" y="136">WEEK</text>
            </svg>
        </div>
        <h2>
            <span data-en="Book your stand at Warsaw HoReCa Week 2027">Zarezerwuj powierzchnię na Warsaw HoReCa Week 2027</span>
        </h2>
        <p>9–11.03.2027 · <span data-en="Ptak Warsaw Expo, Warsaw">Ptak Warsaw Expo, Warszawa</span> · <span data-en="halls A–F">hale A–F</span>
        </p>
        <div class="hero-cta">
            <a class="btn btn-gold btn-lg" href="<?= esc_url($exhibitor_url) ?>" rel="noopener" target="_blank">
                <span data-en="Book your stand">Zarezerwuj powierzchnię</span>
            </a>
            <a class="btn btn-ghost btn-lg" href="<?= esc_url($register_url) ?>" rel="noopener" target="_blank">
                <span data-en="Register as a visitor">Zarejestruj się jako odwiedzający</span>
            </a>
        </div>
    </div>
</section>

<footer class="foot">
    <div class="wrap foot-grid">
        <div>
            <div class="foot-logo">
                <svg aria-label="Warsaw HoReCa Week" class="logo-a" viewbox="0 0 420 150" xmlns="http://www.w3.org/2000/svg">
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="0" y="34">WARSAW</text>
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="78" font-weight="900" letter-spacing="1" x="-3" y="104">HORECA</text>
                    <rect fill="#c8a24a" height="4" width="44" x="2" y="120">
                    </rect>
                    <text fill="currentColor" font-family="Inter,'Helvetica Neue',Helvetica,Arial,sans-serif" font-size="22" font-weight="700" letter-spacing="7" x="58" y="136">WEEK</text>
                </svg>
            </div>
            <img alt="Ptak Warsaw Expo" class="pwe" src="<?= esc_url($static_assets_url . 'footer/ptak-warsaw-expo.png') ?>"/>
            <p>
                <span data-en="Organiser: Ptak Warsaw Expo, Warsaw">Organizator: Ptak Warsaw Expo, Warszawa</span>
                <br/>warsawexpo.eu</p>
            </div>
            <div>
                <h5>
                    <span data-en="Fairs">Targi</span>
                </h5>
                <a href="https://eurogastro.com.pl/" rel="noopener" target="_blank">EuroGastro</a>
                <a href="https://worldhotel.pl/" rel="noopener" target="_blank">World Hotel</a>
                <a href="https://horecafoodserviceexpo.com/" rel="noopener" target="_blank">HoReCa FoodService Expo</a>
                <a href="https://vendingpolandexpo.com/" rel="noopener" target="_blank">Vending Poland Expo</a>
                <a href="https://cleantechexpo.pl/" rel="noopener" target="_blank">Clean-Tech Expo</a>
                <a href="https://beerwarsawexpo.com/" rel="noopener" target="_blank">Beer Warsaw Expo</a>
                <a href="https://winewarsawexpo.com/" rel="noopener" target="_blank">Wine Warsaw Expo</a>
                <a href="https://warsawgiftshow.com/" rel="noopener" target="_blank">Warsaw Gift &amp; Deco</a>
            </div>
            <div>
                <h5>
                    <span data-en="For exhibitors">Dla wystawców</span>
                </h5>
                <a href="<?= esc_url($lang === 'pl' ? 'https://warsawexpo.eu/pl/zostan-wystawca/' : 'https://warsawexpo.eu/become-an-exhibitor/') ?>" rel="noopener" target="_blank">
                    <span data-en="Become an exhibitor">Zostań wystawcą</span>
                </a>
                <a href="<?= esc_url($lang === 'pl' ? 'https://warsawexpo.eu/pl/zostan-agentem/' : 'https://warsawexpo.eu/become-an-agent/') ?>" rel="noopener" target="_blank">
                    <span data-en="Become an agent">Zostań agentem</span>
                </a>
                <a href="https://warsawexpo.eu/kalendarz-targowy/european-horeca-week/" rel="noopener" target="_blank">
                    <span data-en="Week page on warsawexpo.eu">Strona tygodnia na warsawexpo.eu</span>
                </a>
            </div>
        </div>
        <div class="wrap foot-note">
            <span data-en="Figures according to post-show reports, counters and catalogues of the fair websites (as of 18.09.2026); total visitors is the sum of six fairs (World Hotel shares its audience with EuroGastro). Hall plan according to warsawexpo.eu (22.09.2026). Warsaw video: stock footage (Pexels).">Liczby wg raportów potargowych, liczników i katalogów stron targów (stan 18.09.2026); suma odwiedzających to suma wyników sześciu targów (World Hotel dzieli publiczność z EuroGastro). Plan hal wg warsawexpo.eu (22.09.2026). Wideo Warszawy: materiał stockowy (Pexels).</span>
        </div>
    </div>
</footer>

    <script type="application/json" id="catData">
        <?php echo wp_json_encode($catalog_data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
    </script>
</div>
