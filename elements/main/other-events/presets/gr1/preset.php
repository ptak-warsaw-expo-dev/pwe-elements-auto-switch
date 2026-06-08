<?php

$output = '
<div id="pweOtherEvents" class="pwe-other-events">
    <div class="pwe-other-events__wrapper">
        <h4 class="pwe-other-events__heading pwe-main-title">'. PWE_Functions::multi_translation("title") .'</h4>
        <div class="swiper pwe-other-events__items">
            <div class="swiper-wrapper">';
                // Generating HTML
                foreach ($other_events_items_json as $other_events_item) {
                    $event_domain = $other_events_item["event_domain"];
                    $event_desc = $other_events_item["event_desc"];

                    if (strpos($event_domain, $current_domain) === false) {
                        $output .= '
                            <div class="pwe-other-events__item swiper-slide">
                                <a href="https://'. $event_domain .''. PWE_Functions::languageChecker('/', '/'. PWE_Functions::lang() .'/') .'" target="_blank">
                                    <div class="pwe-other-events__item-logo" style="background-image: url(https://'. $event_domain .'/doc/background.webp);">
                                        <img data-no-lazy="1" src="https://'. $event_domain .'/doc/logo.webp"/>
                                    </div>
                                    <div class="pwe-other-events__item-statistic">
                                        <div class="pwe-other-events__item-text">'. $event_desc .'</div>
                                        <div class="pwe-other-events__item-statistic-numbers-block">
                                            <div class="pwe-other-events__item-statistic-numbers">
                                                <div class="pwe-other-events__item-statistic-number">' . do_shortcode('[pwe_visitors domain="'. $event_domain .'"]') . '</div>
                                                <div class="pwe-other-events__item-statistic-name">'. PWE_Functions::multi_translation("visitors") .'</div>
                                            </div>
                                            <div class="pwe-other-events__item-statistic-numbers">
                                                <div class="pwe-other-events__item-statistic-number">' . do_shortcode('[pwe_exhibitors domain="'. $event_domain .'"]') . '</div>
                                                <div class="pwe-other-events__item-statistic-name">'. PWE_Functions::multi_translation("exhibitors") .'</div>
                                            </div>
                                            <div class="pwe-other-events__item-statistic-numbers">
                                                <div class="pwe-other-events__item-statistic-number">' . do_shortcode('[pwe_area domain="'. $event_domain .'"]') . ' m2</div>
                                                <div class="pwe-other-events__item-statistic-name">'. PWE_Functions::multi_translation("exhibition_space") .'</div>
                                            </div>
                                        </div>
                                        <div class="pwe-other-events__show-more-btn">
                                            <span class="pwe-other-events__show-more-text">'. PWE_Functions::multi_translation("more") .'</span>
                                            <span class="pwe-other-events__show-more-arrow">➜</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        ';
                    }
                }
            $output .= '
            </div>
            <div class="swiper-scrollbar"></div>
        </div>
    </div>
</div>';

$output .= PWE_Swiper::swiperScripts('#pweOtherEvents', null, true);

return $output;
