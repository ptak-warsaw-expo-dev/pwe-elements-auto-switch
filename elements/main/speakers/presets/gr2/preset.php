<?php

$is_mobile = preg_match('/iPhone|iPad|Android|webOS|BlackBerry|iPod|Opera Mini|IEMobile/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
$speaker_count = count($speakers);
$layout_class = $speaker_count === 1 ? 'pwe-speakers__items--single' : ($speaker_count === 2 ? 'pwe-speakers__items--double' : 'pwe-speakers__items--default');
$limit = $is_mobile ? 3 : 6;

$output = '
<div id="pweSpeakers" class="pwe-speakers">
    <div class="pwe-speakers__wrapper">
        <div class="pwe-speakers__title">
            <h4 class="pwe-speakers__heading pwe-main-title">' . PWE_Functions::multi_translation('title') . '</h4>
        </div>
        <div class="pwe-speakers__items ' . $layout_class . '">';

        foreach (array_slice($speakers, 0, $limit) as $speaker) {
            $name = esc_html($speaker['name'] ?? '');
            $position = esc_html($speaker['position'] ?? '');
            $company = esc_html($speaker['company'] ?? '');
            $img = esc_url($speaker['img'] ?? '');
            $logo = esc_url($speaker['logo'] ?? '');

            $output .= '
            <div class="pwe-speakers__item">
                <div class="pwe-speakers__speaker-img">
                    <img data-no-lazy="1" src="' . $img . '" onerror="this.onerror=null; this.style.display=\'none\';" alt="' . esc_attr($name) . '"/>
                </div>
                <div class="pwe-speakers__item-text">
                    <div class="pwe-speakers__item-top-wrapper">
                        <h3 class="pwe-speakers__item-name">' . $name . '</h3>
                        <p class="pwe-speakers__item-position">' . $position . '</p>
                    </div>
                    <div class="pwe-speakers__item-bottom-wrapper">';
                        if (!empty($company)) $output .= '<p class="pwe-speakers__item-company">' . $company . '</p>';
                        $output .= '
                        <div class="pwe-speakers__company-img">
                            <img data-no-lazy="1" src="' . $logo . '" onerror="this.onerror=null; this.style.display=\'none\';" alt="Company logo"/>
                        </div>
                    </div>
                </div>
                <span class="pwe-speakers__circle"></span>
            </div>';
        }

        if ($speaker_count === 1) {
            $soon = esc_html(PWE_Functions::multi_translation('coming_soon'));
            $output .= '
            <div class="pwe-speakers__upcoming" aria-label="' . esc_attr($soon) . '">';
            for ($i = 0; $i < 2; $i++) {
                $output .= '
                <div class="pwe-speakers__upcoming-card">
                    <span class="pwe-speakers__upcoming-question" aria-hidden="true">?</span>
                    <span class="pwe-speakers__upcoming-divider" aria-hidden="true"></span>
                    <span class="pwe-speakers__upcoming-message">
                        <svg viewBox="0 0 24 24" width="23" height="23" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/></svg>
                        <span>' . $soon . '</span>
                    </span>
                </div>';
            }
            $output .= '
            </div>';
        }

        $output .= '
        </div>';

        if ($speaker_count > 6) {
            $output .= '
            <div class="pwe-speakers__bottom">
                <div class="pwe-speakers__btn">
                    <a class="pwe-main-btn--secondary" href="' . esc_url(PWE_Functions::languageChecker('/prelegenci/', '/en/speakers/')) . '">' . PWE_Functions::multi_translation('all_speakers_btn') . '</a>
                </div>
            </div>';
        }

    $output .= '
    </div>
</div>';

return $output;
