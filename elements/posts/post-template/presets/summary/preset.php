<?php
if (!defined('ABSPATH')) exit;

$output = '';

// Prepare values only. All statistics markup is built below in $output.
$statistics_cards = [];
$statistics = is_array($news['statistics'] ?? null) ? $news['statistics'] : [];
$current = is_array($statistics['current_edition'] ?? null) ? $statistics['current_edition'] : [];
$previous = is_array($statistics['previous_edition'] ?? null) ? $statistics['previous_edition'] : [];
$current_year = !empty($news['year']) ? (int) $news['year'] : '';
$previous_year = $current_year !== '' ? $current_year - 1 : '';
$metrics = [
    'visitors' => ['' . PWE_Functions::multi_translation("visitors") . '', ''],
    'exhibitors' => ['' . PWE_Functions::multi_translation("exhibitors") . '', ''],
    'area' => ['' . PWE_Functions::multi_translation("area") . '', 'm²'],
    'countries' => ['' . PWE_Functions::multi_translation("countries") . '', ''],
];
$number = static function ($value) {
    return is_numeric($value) && is_finite((float) $value) && $value > 0 ? (float) $value : null;
};

foreach ($metrics as $key => [$label, $unit]) {
    $value = $number($current[$key] ?? null);
    $old = $number($previous[$key] ?? null);
    if ($value === null && $old === null) continue;

    $rows = [];
    if ($value !== null) $rows[] = ['edition' => 'current', 'value' => $value, 'year' => $current_year ?: PWE_Functions::multi_translation("current")];
    if ($old !== null) $rows[] = ['edition' => 'previous', 'value' => $old, 'year' => $previous_year ?: PWE_Functions::multi_translation("previous")];
    $delta = $value !== null && $old !== null && $old > 0 ? ($value - $old) / $old * 100 : null;
    $statistics_cards[] = compact('label', 'unit', 'value', 'old', 'rows', 'delta');
}

if ($statistics_cards) {
    wp_enqueue_script('pwe-summary-chart', 'https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js', [], '4.4.8', true);
    $scripts = wp_scripts();
    $handle = 'pwe-post-template-summary-script';
    if (isset($scripts->registered[$handle])) {
        $scripts->registered[$handle]->deps = array_unique(array_merge($scripts->registered[$handle]->deps, ['pwe-summary-chart']));
    }
}

// Prepare the YouTube embed; the section markup is built in $output below.
$video_url = is_string($news['video_url'] ?? null) ? trim($news['video_url']) : '';
$video_title = is_string($news['video_title'] ?? null) ? trim($news['video_title']) : '';
$video_description = is_string($news['video_description'] ?? null) ? trim($news['video_description']) : '';
$video_host = strtolower((string) wp_parse_url($video_url, PHP_URL_HOST));
$video_path = (string) wp_parse_url($video_url, PHP_URL_PATH);
$video_id = '';

if (in_array($video_host, ['youtube.com', 'www.youtube.com', 'm.youtube.com', 'youtube-nocookie.com', 'www.youtube-nocookie.com', 'youtu.be', 'www.youtu.be'], true)) {
    if (in_array($video_host, ['youtu.be', 'www.youtu.be'], true)) {
        $video_id = trim($video_path, '/');
    } elseif (preg_match('~^/(?:embed|shorts|live)/([A-Za-z0-9_-]{11})(?:/|$)~', $video_path, $match)) {
        $video_id = $match[1];
    } else {
        parse_str((string) wp_parse_url($video_url, PHP_URL_QUERY), $query);
        $video_id = is_string($query['v'] ?? null) ? $query['v'] : '';
    }
}

$video_embed = preg_match('/^[A-Za-z0-9_-]{11}$/D', $video_id)
    ? 'https://www.youtube-nocookie.com/embed/' . $video_id
    : '';
$video_layout = mb_strlen(trim(wp_strip_all_tags($video_description)), 'UTF-8') > 900 ? 'long' : 'standard';

$group = do_shortcode('[trade_fair_group]');

$medal_categories = [
    'gr1' => [
        'categories' => [
            [
                'title' => PWE_Functions::multi_translation("medal_categories_gr1_1"),
            ],
            [
                'title' => PWE_Functions::multi_translation("medal_categories_gr1_2"),
            ],
            [
                'title' => PWE_Functions::multi_translation("medal_categories_gr1_3"),
            ],
            [
                'title' => PWE_Functions::multi_translation("medal_categories_gr1_4"),
            ],
        ],
    ],

    'default' => [
        'categories' => [
            [
                'title' => PWE_Functions::multi_translation("medal_categories_default_1"),
            ],
            [
                'title' => PWE_Functions::multi_translation("medal_categories_default_2"),
            ],
            [
                'title' => PWE_Functions::multi_translation("medal_categories_default_3"),
            ],
            [
                'title' => PWE_Functions::multi_translation("medal_categories_default_4"),
            ],
        ],
    ],
];

$data = $medal_categories[$group] ?? $medal_categories['default'];

$categories = $data['categories'];
$categories_html = '';

foreach ($categories as $category) {
    $categories_html .= '
    <li class="pwe-post-summary__medal-ceremony-category">
        <span aria-hidden="true">✓</span>
        ' . esc_html($category['title']) . '
    </li>';
}

// Next-edition artwork uses the fair's assets.
$fair_url = is_string($news['fair_url'] ?? null)
    ? trim($news['fair_url'])
    : '';

$fair_urls = array_values(array_filter(
    array_map('trim', explode(',', $fair_url)),
    static function ($url) {
        if ($url === '') return false;

        // Normalizacja URL na potrzeby sprawdzenia domeny.
        $normalized_url = preg_match('~^https?://~i', $url)
            ? $url
            : 'https://' . $url;

        $host = strtolower((string) wp_parse_url(
            $normalized_url,
            PHP_URL_HOST
        ));

        // Pomijamy Warsaw Expo.
        return $host !== ''
            && !in_array($host, [
                'warsawexpo.eu',
                'www.warsawexpo.eu',
            ], true);
    }
));

// Liczymy wyłącznie domeny pozostałe po filtrowaniu.
$multiple_fairs = count($fair_urls) > 1;

$next_fairs = [];

foreach ($fair_urls as $url) {
    if (!preg_match('~^https?://~i', $url)) {
        $url = 'https://' . $url;
    }

    $host = wp_parse_url($url, PHP_URL_HOST);
    if (!$host) continue;

    $origin = (wp_parse_url($url, PHP_URL_SCHEME) ?: 'https')
        . '://' . $host;

    $next_fairs[] = [
        'url'  => $origin,
        'host' => $host,
        'logo' => $origin . '/doc/logo.webp',
    ];
}

$fair_url = $fair_urls[0] ?? '';

if ($fair_url !== '' && !preg_match('~^https?://~i', $fair_url)) {
    $fair_url = 'https://' . $fair_url;
}

$fair_host = wp_parse_url($fair_url, PHP_URL_HOST);

$fair_origin = $fair_host
    ? (wp_parse_url($fair_url, PHP_URL_SCHEME) ?: 'https') . '://' . $fair_host
    : '';

$next_image = esc_url(
    is_string($news['next_edition_image_url'] ?? null)
        ? $news['next_edition_image_url']
        : ($fair_origin ? $fair_origin . '/doc/header_mobile.webp' : '')
);

$next_logo = esc_url(
    is_string($news['next_edition_logo_url'] ?? null)
        ? $news['next_edition_logo_url']
        : ($fair_origin ? $fair_origin . '/doc/logo.webp' : '')
);

$output .= '
<div id="pwePostSummary" class="pwe-post-summary">

    ' . (!empty($news['hero_url']) || !empty($news['title']) || !empty($news['description']) ? '
    <section class="pwe-post-summary__intro">

        ' . (!empty($news['hero_url']) ? '
            <div class="pwe-post-summary__image-wrapper"> 
                <img class="pwe-post-summary__image"
                    src="' . esc_url($news['hero_url']) . '"
                    alt="' . esc_attr($news['title'] ?? '') . '">
            </div>
        ' : '') . '

        ' . (!empty($news['title']) ? '
            <h1 class="pwe-post-summary__title">
                ' . esc_html($news['title']) . '
            </h1>
        ' : '') . '

        ' . (!empty($news['description']) ? '
            <div class="pwe-post-summary__description">
                ' . $news['description'] . '
            </div>
        ' : '') . '

        
        <div class="pwe-post-summary__post-info">
            <span class="pwe-post-summary__date-year">' . esc_html(get_the_date('Y')) . '</span>
            <span class="pwe-post-summary__divider-line"></span>
            <span class="pwe-post-summary__event-pwe">Ptak Warsaw Expo</span>
        </div>

    </section>
    ' : '') . '';

    $output .= '
    <section class="pwe-post-summary__body">
        <div class="pwe-post-summary__body-wrapper">';

            if ($statistics_cards) {
                $output .= '
                <section class="pwe-post-summary__statistics">
                    ' . (!empty($news['statistics_title']) ? '
                        <h2 class="pwe-post-summary__statistics-title">' . wp_kses_post($news['statistics_title']) . '</h2>
                    ' : '') . '
                    <div class="stats-board">';

                foreach ($statistics_cards as $card) {
                    $value = $card['value'];
                    $old = $card['old'];
                    $unit = $card['unit'];
                    $delta = $card['delta'];

                    $output .= '
                        ' . ($value !== null || $old !== null ? '
                            <article class="chart-stat">
                                <div class="chart-top">
                                    <h3 class="chart-label">' . esc_html($card['label']) . '</h3>
                                </div>
                                <div class="chart-number-line">
                                    <span class="stat-val">' . esc_html(number_format_i18n($value ?? $old)) . '</span>
                                    ' . ($unit ? '<span class="stat-unit">' . esc_html($unit) . '</span>' : '') . '
                                    ' . ($delta !== null ? '<span class="chart-delta" data-trend="' . ($delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'same')) . '">' . esc_html(($delta > 0 ? '+' : '') . number_format_i18n($delta, 1) . '%') . '</span>' : '') . '
                                </div>
                                <div class="stat-chart" data-statistics="' . esc_attr(wp_json_encode($card['rows'])) . '" data-unit="' . esc_attr($unit) . '">
                                    <canvas hidden role="img" aria-label="' . esc_attr($card['label'] . ($value !== null ? ', ' . PWE_Functions::multi_translation("current") . ': ' . $value . ' ' . $unit : '') . ($old !== null ? ', ' . PWE_Functions::multi_translation("previous") . ': ' . $old . ' ' . $unit : '')) . '"></canvas>
                                    <div class="statistics-fallback">
                                        ' . ($value !== null ? '
                                            <div class="pwe-post-summary__statistics-current">
                                                ' . esc_html(($current_year ?: '' . PWE_Functions::multi_translation("current") . '') . ': ' . number_format_i18n($value) . ($unit ? ' ' . $unit : '')) . '
                                            </div>
                                        ' : '') . '
                                        ' . ($old !== null ? '
                                            <div class="pwe-post-summary__statistics-previous">
                                                ' . esc_html(($previous_year ?: '' . PWE_Functions::multi_translation("previous") . '') . ': ' . number_format_i18n($old) . ($unit ? ' ' . $unit : '')) . '
                                            </div>
                                        ' : '') . '
                                    </div>
                                </div>
                                <div class="chart-legend">
                                    ' . ($value !== null ? '<span class="pwe-post-summary__statistics-legend-current">' . esc_html($current_year ?: PWE_Functions::multi_translation("current")) . '</span>' : '') . '
                                    ' . ($old !== null ? '<span class="pwe-post-summary__statistics-legend-previous">' . esc_html($previous_year ?: PWE_Functions::multi_translation("previous")) . '</span>' : '') . '
                                </div>
                            </article>
                        ' : '');
                }

                $output .= '
                    </div>
                </section>';
            }

            $output .= '
            ' . ($video_embed !== '' || $video_title !== '' || $video_description !== '' ? '
                <section class="pwe-post-summary__video pwe-post-summary__video--' . esc_attr($video_layout) . '">
                    ' . ($video_title !== '' || $video_description !== '' ? '
                        <div class="pwe-post-summary__video-content">
                            ' . ($video_title !== '' ? '
                                <h2 class="pwe-post-summary__video-title">' . wp_kses($video_title, ['br' => []]) . '</h2>
                            ' : '') . '
                            ' . ($video_description !== '' ? '
                                <div class="pwe-post-summary__description">' . wp_kses_post($video_description) . '</div>
                            ' : '') . '
                        </div>
                    ' : '') . '
                    ' . ($video_embed !== '' ? '
                        <div class="pwe-post-summary__video-media">
                            <iframe class="pwe-post-summary__video-frame"
                                src="' . esc_url($video_embed) . '"
                                title="' . esc_attr($video_title !== '' ? wp_strip_all_tags($video_title) : PWE_Functions::multi_translation("video_report")) . '"
                                loading="lazy"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                        </div>
                    ' : '') . '
                </section>
            ' : '') . '

            ' . (!empty($news['additional_title']) || !empty($news['additional_description']) ? '
                <section class="pwe-post-summary__additional">
                    ' . (!empty($news['additional_title']) ? '
                        <h2 class="pwe-post-summary__additional-title">
                            ' . esc_html($news['additional_title']) . '
                        </h2>
                    ' : '') . '
                    ' . (!empty($news['additional_description']) ? '
                        <div class="pwe-post-summary__description">
                            ' . $news['additional_description'] . '
                        </div>
                    ' : '') . '
                </section>
            ' : '') . '

            ' . (!empty($news['gallery_title']) || !empty($news['gallery']) ? '
                <section class="pwe-post-summary__gallery">
                    ' . (!empty($news['gallery_title']) ? '
                        <h2 class="pwe-post-summary__gallery-title">
                            ' . esc_html($news['gallery_title']) . '
                        </h2>
                    ' : '') . '
                    ' . (!empty($news['gallery']) ? '
                        <div class="pwe-post-summary__gallery-items">
                            ' . implode('', array_map(function($gallery_item) {
                                return '<div class="pwe-post-summary__gallery-item"><img src="' . esc_url($gallery_item) . '" alt="" /></div>';
                            }, $news['gallery'])) . '
                        </div>
                    ' : '') . '
                </section>
            ' : '') . '

            ' . (!empty($news['conferences_title']) || !empty($news['conferences_text']) ? '
                <section class="pwe-post-summary__conferences">
                    ' . (!empty($news['conferences_title']) ? '
                        <h2 class="pwe-post-summary__conferences-title">
                            ' . esc_html($news['conferences_title']) . '
                        </h2>
                    ' : '') . '
                    ' . (!empty($news['conferences_text']) ? '
                        <p class="pwe-post-summary__description">
                            ' . $news['conferences_text'] . '
                        </p>
                    ' : '') . '
                </section>
            ' : '') . '

            ' . (!empty($news['medal_ceremony_title']) || !empty($news['medal_ceremony_description']) || !empty($news['medal_ceremony_link']) ? '
                <section class="pwe-post-summary__medal-ceremony">
                    <div class="pwe-post-summary__medal-ceremony-content">
                        ' . (!empty($news['medal_ceremony_title']) ? '
                            <h2 class="pwe-post-summary__medal-ceremony-title">
                                ' . $news['medal_ceremony_title'] . '
                            </h2>
                        ' : '') . '
                        ' . (!empty($news['medal_ceremony_description']) ? '
                            <div class="pwe-post-summary__description">
                                ' . $news['medal_ceremony_description'] . '
                            </div>
                        ' : '') . '
                        ' . (!empty($news['medal_ceremony_link']) ? '
                            <a class="pwe-post-summary__button pwe-post-summary__button--accent" href="' . esc_url($news['medal_ceremony_link']) . '" class="pwe-post-summary__medal-ceremony-link">
                                ' . PWE_Functions::multi_translation("laureates") . '
                            </a>
                        ' : '') . '
                    </div>
                    <div class="pwe-post-summary__medal-ceremony-image-wrapper">
                        <img class="pwe-post-summary__medal-ceremony-image" src="/wp-content/plugins/pwe-elements-auto-switch/elements/posts/post-template/presets/summary/assets/pwe-medal.webp" alt="Medal Ceremony" />
                        <ul class="pwe-post-summary__medal-ceremony-categories">
                            ' . $categories_html . '
                        </ul>
                    </div>
                </section>
            ' : '') . '

            ' . (!empty($news['next_edition_title']) || !empty($news['next_edition']) ? '
                <section class="pwe-post-summary__next-edition' . ($multiple_fairs ? ' pwe-post-summary__next-edition--multiple' : '') . '">
                    <div class="pwe-post-summary__next-edition-content">
                    ' . (!empty($news['next_edition_title']) ? '
                        <h2 class="pwe-post-summary__next-edition-title">
                            ' . $news['next_edition_title'] . '
                        </h2>
                    ' : '') . '
                    ' . (!empty($news['next_edition']) ? '
                        <p class="pwe-post-summary__description">
                            ' . $news['next_edition'] . '
                        </p>
                    ' : '') . '
                    ' . (!$multiple_fairs ? '
                    <div class="pwe-post-summary__next-edition-btn-wrapper">
                        <a class="pwe-post-summary__next-edition-btn" target="_blank" href="' . esc_url(rtrim($fair_url, '/') . PWE_Functions::multi_translation("register_url")) . '">
                            ' . PWE_Functions::multi_translation("register") . '
                        </a>
                        <a class="pwe-post-summary__next-edition-btn" target="_blank" href="' . esc_url(rtrim($fair_url, '/') . PWE_Functions::multi_translation("become_exhibitor_url")) . '">
                            ' . PWE_Functions::multi_translation("become_exhibitor") . '
                        </a>
                    </div>
                    ' : '') . '
                    </div>
                    ' . ($multiple_fairs ? '
                        <div class="pwe-post-summary__next-edition-grid">
                            ' . implode('', array_map(static function ($fair) {
                                return '
                                <div class="pwe-post-summary__next-edition-card">
                                    <img class="pwe-post-summary__next-edition-card-logo" src="' . esc_url($fair['logo']) . '" alt="' . esc_attr($fair['host']) . '" loading="lazy" decoding="async">
                                    <div class="pwe-post-summary__next-edition-btn-wrapper">
                                        <a class="pwe-post-summary__next-edition-btn" target="_blank" rel="noopener noreferrer" href="' . esc_url($fair['url'] . PWE_Functions::multi_translation("register_url")) . '">
                                            ' . PWE_Functions::multi_translation("register") . '
                                        </a>
                                        <a class="pwe-post-summary__next-edition-btn" target="_blank" rel="noopener noreferrer" href="' . esc_url($fair['url'] . PWE_Functions::multi_translation("become_exhibitor_url")) . '">
                                            ' . PWE_Functions::multi_translation("become_exhibitor") . '
                                        </a>
                                    </div>
                                </div>';
                            }, $next_fairs)) . '
                        </div>
                    ' : '') . '
                    ' . (!$multiple_fairs && ($next_image !== '' || $next_logo !== '') ? '
                        <div class="pwe-post-summary__next-edition-art" aria-hidden="true">
                            ' . ($next_image !== '' ? '<img class="pwe-post-summary__next-edition-image" src="' . $next_image . '" alt="" loading="lazy" decoding="async">' : '') . '
                            ' . ($next_logo !== '' ? '<img class="pwe-post-summary__next-edition-logo" src="' . $next_logo . '" alt="" loading="lazy" decoding="async">' : '') . '
                        </div>
                    ' : '') . '
                </section>
            ' : '') . '
            ';

            // Permanent editorial footer; publication date belongs to the current WordPress post.
            $post_id = get_the_ID();
            $published_date = $post_id ? get_the_date('j F Y', $post_id) : '';
            $published_iso = $post_id ? get_the_date('c', $post_id) : '';
            $article_url = $post_id ? get_permalink($post_id) : '';
            $share_url = rawurlencode($article_url ?: '');
            $share_title = rawurlencode(is_string($news['title'] ?? null) ? $news['title'] : '');
            $icons = [
                'facebook' => '<path d="M14 21v-8h3l.5-4H14V7c0-1 .3-2 2-2h2V1.5A24 24 0 0 0 15 1c-3 0-5 2-5 5v3H7v4h3v8z"/>',
                'x' => '<path d="M18.9 2H22l-6.8 7.8L23 22h-6.3l-4.9-7.4L5.3 22H2l8.3-9.6L2 2h6.5l4.4 6.8zM17.8 20h1.8L7.5 4H5.6z"/>',
                'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><circle cx="17.5" cy="6.5" r="1.2"/>',
                'linkedin' => '<path d="M3 8h4v13H3zm2-6a2.3 2.3 0 1 1 0 4.6A2.3 2.3 0 0 1 5 2zm5 6h4v1.8C15 8.3 16 8 17.5 8 21 8 22 10 22 13v8h-4v-7c0-2-.4-3-2-3s-2 1-2 3v7h-4z"/>',
                'telegram' => '<path d="M22 3 18.6 21l-6-4.5-3 3 .6-5.8L19 5.5 7 12.7 2 11z"/>',
            ];
            $icon = static function ($name) use ($icons) {
                return '<svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true" focusable="false">' . $icons[$name] . '</svg>';
            };
            $output .= '
            <footer class="pwe-post-summary__editorial">
                <div class="pwe-post-summary__author">
                    <img class="pwe-post-summary__author-photo" src="https://warsawexpo.eu/wp-content/plugins/pwe-media/media/article-authors/nikodem-zygadlo.webp" alt="Nikodem Zygadło" width="144" height="144" loading="lazy" decoding="async">
                    <div class="pwe-post-summary__author-copy">
                        <p class="pwe-post-summary__author-name">Nikodem Zygadło</p>
                        <p class="pwe-post-summary__author-role">' . PWE_Functions::multi_translation("director_role") . ' <span aria-hidden="true">|</span> ' . PWE_Functions::multi_translation("deputy_director_role") . ' <span aria-hidden="true">|</span> Ptak Warsaw Expo</p>
                        <p class="pwe-post-summary__author-bio">' . PWE_Functions::multi_translation("author_bio") . '</p>
                    </div>
                </div>
                <div class="pwe-post-summary__editorial-bottom">
                    <p class="pwe-post-summary__thanks">' . PWE_Functions::multi_translation("thanks_message") . '</p>
                    <a class="pwe-post-summary__button pwe-post-summary__button--accent" href="https://www.linkedin.com/build-relation/newsletter-follow?entityUrn=7185929412658302977" target="_blank" rel="noopener noreferrer">' . $icon('linkedin') . '<span>' . PWE_Functions::multi_translation("linkedin_newsletter") . '</span></a>';

                    if ($published_date !== '') {
                        $output .= '<p class="pwe-post-summary__publication">' . PWE_Functions::multi_translation("publication_date") . ': <time datetime="' . esc_attr($published_iso) . '">' . esc_html($published_date) . '</time></p>';
                    }
                    $social_links = [];
                    if ($article_url) {
                        $social_links['facebook'] = [PWE_Functions::multi_translation("share_facebook"), 'https://www.facebook.com/sharer/sharer.php?u=' . $share_url];
                        $social_links['x'] = [PWE_Functions::multi_translation("share_x"), 'https://twitter.com/intent/tweet?url=' . $share_url . '&text=' . $share_title];
                    }
                    $social_links['instagram'] = [PWE_Functions::multi_translation("instagram_profile"), 'https://www.instagram.com/ptak_warsaw_expo/'];
                    if ($article_url) {
                        $social_links['linkedin'] = [PWE_Functions::multi_translation("share_linkedin"), 'https://www.linkedin.com/sharing/share-offsite/?url=' . $share_url];
                        $social_links['telegram'] = [PWE_Functions::multi_translation("share_telegram"), 'https://t.me/share/url?url=' . $share_url . '&text=' . $share_title];
                    }
                    $output .= '<nav class="pwe-post-summary__social" aria-label="' . esc_attr(PWE_Functions::multi_translation("social_media")) . '">';
                    foreach ($social_links as $name => $link) {
                        $output .= '<a href="' . esc_url($link[1]) . '" aria-label="' . esc_attr($link[0] . ' — ' . PWE_Functions::multi_translation("opens_new_tab")) . '" title="' . esc_attr($link[0]) . '" target="_blank" rel="noopener noreferrer">' . $icon($name) . '</a>';
                    }
                    $output .= '</nav>
                </div>
            </footer>
        </div>
    </section>';

$output .= '</div>';

$output .= include __DIR__ . '/../../assets/photo-viewer.php';

return $output;