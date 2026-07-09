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

if (!empty($data) && isset($data[0]->videos)) {
    $db_videos = json_decode($data[0]->videos, true);

    if (is_array($db_videos) && !empty($db_videos)) {
        $videos_to_display = [];

        $current_lang = substr(get_locale(), 0, 2);

        foreach ($db_videos as $v) {
            if (empty($v['url'])) {
                continue;
            }

            $title = '';
            if ($current_lang === 'pl') {
                $title = !empty($v['title_pl']) ? $v['title_pl'] : $v['title_en'];
            } else {
                $title = !empty($v['title_en']) ? $v['title_en'] : $v['title_pl'];
            }

            if (empty($title)) {
                $title = PWE_Functions::multi_translation("fair_summary") . ' ' . do_shortcode('[trade_fair_name]');
            }

            $embed_url = $v['url'];
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)|watchExternal)\?|youtu\.be/)([^"&?/\s]{11})%i', $v['url'], $match)) {
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

$stats = [
    [
        "label"  => PWE_Functions::multi_translation("visitors"),
        "value"  => do_shortcode('[pwe_visitors_foreign]'),
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
                    '. PWE_Functions::multi_translation("pwe_video_description") .'
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