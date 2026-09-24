<?php

$output = '';

$data = PWE_Functions::get_database_fairs_data_adds();

$videos_to_display = [
    [
        'title' => PWE_Functions::multi_translation("fair_summary") . ' ' . do_shortcode('[trade_fair_name]'),
        'url'   => 'https://www.youtube-nocookie.com/embed/tjr_kdiU02k'
    ],
    [
        'title' => PWE_Functions::multi_translation("fair_summary") . ' ' . do_shortcode('[trade_fair_name]'),
        'url'   => 'https://www.youtube-nocookie.com/embed/zhazT9mB_no'
    ]
];

// Domyślny opis sekcji z tłumaczenia
$section_description = PWE_Functions::multi_translation("pwe_video_description");

if (!empty($data) && isset($data[0]->videos)) {
    $db_videos = json_decode($data[0]->videos, true);

    if (is_array($db_videos) && !empty($db_videos)) {
        $current_lang = substr(get_locale(), 0, 2);

        // Pobieramy tytuły nadrzędne
        $parent_title_pl = !empty($db_videos['title_pl']) ? trim($db_videos['title_pl']) : '';
        $parent_title_en = !empty($db_videos['title_en']) ? trim($db_videos['title_en']) : '';

        // Ustawiamy opis sekcji z głównego tytułu z bazy danych
        if ($current_lang === 'pl') {
            $db_parent_title = !empty($parent_title_pl) ? $parent_title_pl : $parent_title_en;
        } else {
            $db_parent_title = !empty($parent_title_en) ? $parent_title_en : $parent_title_pl;
        }

        // Jeśli znaleziono tytuł nadrzędny w bazie, nadpisujemy domyślny opis sekcji
        if (!empty($db_parent_title)) {
            $section_description = $db_parent_title;
        }

        // Pobranie listy filmów (dla nowej i starej struktury)
        $items = (isset($db_videos['items']) && is_array($db_videos['items'])) ? $db_videos['items'] : $db_videos;

        if (!empty($items) && is_array($items)) {
            $videos_to_display = [];

            foreach ($items as $v) {
                if (!is_array($v) || empty($v['url'])) {
                    continue;
                }

                $item_title_pl = !empty($v['title_pl']) ? trim($v['title_pl']) : '';
                $item_title_en = !empty($v['title_en']) ? trim($v['title_en']) : '';

                $title = '';

                // Pobieramy tytuł przypisany bezpośrednio do tego filmu
                if ($current_lang === 'pl') {
                    $title = !empty($item_title_pl) ? $item_title_pl : $item_title_en;
                } else {
                    $title = !empty($item_title_en) ? $item_title_en : $item_title_pl;
                }

                // Domyślny tytuł filmu, jeśli żaden nie został zdefiniowany w obiekcie filmu
                if (empty($title)) {
                    $title = PWE_Functions::multi_translation("fair_summary") . ' ' . do_shortcode('[trade_fair_name]');
                }

                $embed_url = $v['url'];
                if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+|/(?:v|e(?:mbed)?)|watchExternal)\?|youtu\.be/)([^"&?/\s]{11})%i', $v['url'], $match)) {
                    $video_id = $match[1];
                    $embed_url = "https://www.youtube-nocookie.com/embed/" . $video_id;
                }

                $videos_to_display[] = [
                    'title' => $title,
                    'url'   => $embed_url
                ];
            }
        }
    }
}

$stats = [
    [
        "label"  => PWE_Functions::multi_translation("visitors"),
        "value"  => do_shortcode('[pwe_visitors]'),
        "suffix" => ""
    ],
    [
        "label"  => PWE_Functions::multi_translation("visitors_abroad"),
        "value"  => do_shortcode('[pwe_visitors_foreign]'),
        "suffix" => ""
    ],
    [
        "label"  => PWE_Functions::multi_translation("exhibitors"),
        "value"  => do_shortcode('[pwe_exhibitors]'),
        "suffix" => ""
    ],
    [
        "label"  => PWE_Functions::multi_translation("area"),
        "value"  => do_shortcode('[pwe_area]'),
        "suffix" => ""
    ]
];

$output .= '
<section id="video" class="pwe-video">
    <div class="pwe-video__container">

        <div class="pwe-video__header">
            <div class="pwe-video__intro">
                <span class="pwe-video__subtitle">'. PWE_Functions::multi_translation("previous_editions") .'</span>
                <h2 class="pwe-video__title">Retro <span class="pwe-video__title--accent">Flashback</span></h2>
                <p class="pwe-video__description">
                    '. esc_html($section_description) .'
                </p>
            </div>

            <div class="pwe-video__stats">';

            foreach ($stats as $stat) {
                $suffix_attr = !empty($stat['suffix']) ? ' data-suffix="' . $stat['suffix'] . '"' : '';

                $output .= '
                <div class="pwe-video__stat-item">
                    <div class="pwe-video__stat-value pwe-statistics__tile-number" data-target="' . $stat['value'] . '"' . $suffix_attr . '>0</div>
                    <div class="pwe-video__stat-label">' . $stat['label'] . '</div>
                </div>';
            }

            $output .= '
            </div>
        </div>

        <div class="pwe-video__grid">';

        foreach ($videos_to_display as $index => $video) {
            $glow_side = ($index % 2 === 0) ? 'left' : 'right';

            $output .= '
            <div class="pwe-video__item group">
                <div class="pwe-video__glow pwe-video__glow--' . $glow_side . '"></div>
                <div class="pwe-video__wrapper">
                    <iframe
                        class="pwe-video__iframe"
                        src="' . esc_url($video['url']) . '"
                        title="' . esc_attr($video['title']) . '"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
                <div class="pwe-video__footer">
                    <h4 class="pwe-video__video-title">' . esc_html($video['title']) . '</h4>
                </div>
            </div>';
        }

        $output .= '
        </div>
    </div>
</section>';

return $output;