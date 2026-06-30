<?php

$output = '
<section id="pwePremieres" class="pwe-element-auto-switch pwe-premieres-section">
    <div class="pwe-premieres-container">

        <div class="pwe-premieres__header">
            <div class="pwe-premieres__header-title">
                <span class="pwe-subtitle">' . PWE_Functions::multi_translation("premieres_subtitle") . ' ' . do_shortcode('[trade_fair_name]') . '</span>
                <h2 class="pwe-title">' . PWE_Functions::multi_translation("premieres_title") . '</h2>
            </div>

            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                </div>
                <div class="swiper-button-next">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
            </div>
        </div>

        <div class="pwe-premieres__slider swiper" role="group" aria-roledescription="carousel" aria-live="polite">
            <div class="swiper-wrapper">';

                foreach ($slides as $slide) {
                    $output .= '
                    <div class="swiper-slide pwe-premieres__card group">

                        <div class="pwe-premieres__image-wrapper">
                            <img src="'.esc_url($slide['img']).'" alt="'.esc_attr($slide['name']).'" class="pwe-premieres__image">
                        </div>

                        <div class="pwe-premieres__card-body">

                            <div class="pwe-premieres__meta">
                                ' . (!empty($slide['logo']) ? '
                                <div class="pwe-premieres__vendor-logo">
                                    <img src="'.esc_url($slide['logo']).'" alt="logo">
                                </div>' : '') . '
                                <div class="pwe-premieres__vendor-info">
                                    <div class="pwe-premieres__company-name">'.$slide['exhibitor'].'</div>
                                    <div class="pwe-premieres__company-stand">
                                        ' . (!empty($slide['stand']) ? ((PWE_Functions::lang_pl() ? 'Stoisko: ' : 'Stand: ') . '<span>'.$slide['stand'].'</span>') : '') . '
                                    </div>
                                </div>
                            </div>

                            <h3 class="pwe-premieres__premiere-name">'.$slide['name'].'</h3>
                            <div class="pwe-premieres__premiere-desc">'.$slide['desc'].'</div>

                            <div class="pwe-premieres__action">
                                <span>' . (PWE_Functions::lang_pl() ? 'Szczegóły' : 'Details') . '</span>
                                <svg class="pwe-premieres__arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </div>

                        </div>
                    </div>';
                }

            $output .= '
            </div>
        </div>

        <div class="swiper-nav">
            <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
        </div>

    </div>
</section>';

$output .= PWE_Swiper::swiperScripts('#pwePremieres', [0 => ['slidesPerView' => 1], 650 => ['slidesPerView' => 2], 1024 => ['slidesPerView' => 3]], true, true, 1, false);

return $output;