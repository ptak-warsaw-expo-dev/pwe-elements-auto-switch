<?php

$output = '';

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

        <div class="pwe-video__grid">

            <div class="pwe-video__item group">
                <div class="pwe-video__glow pwe-video__glow--left"></div>
                <div class="pwe-video__wrapper">
                    <iframe
                        class="pwe-video__iframe"
                        src="https://www.youtube-nocookie.com/embed/tjr_kdiU02k"
                        title="MotoShow Highlight 1"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
                <div class="pwe-video__footer">
                    <h4 class="pwe-video__video-title">'. PWE_Functions::multi_translation("fair_summary") .' '.do_shortcode('[trade_fair_name]').'</h4>
                </div>
            </div>

            <div class="pwe-video__item group">
                <div class="pwe-video__glow pwe-video__glow--right"></div>
                <div class="pwe-video__wrapper">
                    <iframe
                        class="pwe-video__iframe"
                        src="https://www.youtube-nocookie.com/embed/zhazT9mB_no"
                        title="MotoShow Highlight 2"
                        frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>
                <div class="pwe-video__footer">
                    <h4 class="pwe-video__video-title">'. PWE_Functions::multi_translation("fair_summary") .' '.do_shortcode('[trade_fair_name]').'</h4>
                </div>
            </div>

        </div>
    </div>
</section>';

return $output;