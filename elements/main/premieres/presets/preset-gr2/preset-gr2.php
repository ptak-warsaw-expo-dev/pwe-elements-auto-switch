<?php

$current_domain = $_SERVER['HTTP_HOST'];
$premieres = PWECommonFunctions::get_database_premieres_data($current_domain);

if (!empty($premieres[0]->slug)) {

    $slides = [];
    foreach ($premieres as $premiere) {
        $data = json_decode($premiere->data, true);
        if (!isset($data[$premiere->slug])) continue;
        $item = $data[$premiere->slug];

        $slides[] = [
            'name'      => PWECommonFunctions::lang_pl() ? $item['name_pl'] : ($item['name_en'] ?? $item['name_pl']),
            'desc'      => PWECommonFunctions::lang_pl() ? $item['desc_pl'] : ($item['desc_en'] ?? $item['desc_pl']),
            'exhibitor' => $item['exhibitor'] ?? '',
            'stand'     => $item['stand'],
            'img'       => $item['background'] ?? '',
            'logo'      => $item['logo'] ?? ''
        ];
    }

    $output = '
    <div id="pwePremieres" class="pwe-premieres">
        <div class="pwe-premieres__header">
            <div class="pwe-premieres__header-title">
                <div class="pwe-subtitle">PRODUKTY '. do_shortcode('[trade_fair_name]') .'</div>
                <div class="pwe-main-title">'. PWECommonFunctions::languageChecker('Co zobaczysz na targach', 'What will you see at the fair?') .'</div>
            </div>
            <img class="pwe-premieres__header-img" src="/doc/logo-color.webp" alt="">
        </div>

        <div class="swiper">
            <div class="swiper-wrapper">';

                foreach ($slides as $slide) {
                    $output .= '
                            <div class="swiper-slide">
                                <div class="pwe-premieres__slide">
                                    <div class="pwe-premieres__image">
                                        <img src="'.esc_url($slide['img']).'" alt="'.esc_attr($slide['name']).'">
                                    </div>
                                    <div class="pwe-premieres__content">

                                        <div class="pwe-premieres__content-bg">
                                            <div class="pwe-premieres__content-header">
                                                <div class="pwe-premieres__company-logo">
                                                    <img src="'.esc_url($slide['logo']).'" alt="logo">
                                                </div>
                                                <div class="pwe-premieres__company-short-info">
                                                    <div class="pwe-premieres__company-stand">'. (!empty($item['stand']) ? ((PWECommonFunctions::lang_pl() ? 'Stoisko: ' : 'Stand: ') .' <span>'.$slide['stand']) : '') .'</span></div>
                                                    <div class="pwe-premieres__company-name">'.$slide['exhibitor'].'</div>
                                                </div>
                                            </div>
                                        
                                            <h3 class="pwe-premieres__premiere-name">'.$slide['name'].'</h3>
                                            <div class="pwe-premieres__premiere-desc">'.$slide['desc'].'</div>
                                        </div>

                                        <div class="pwe-premieres__content-footer">
                                            <div class="pwe-premieres__counter"><span>'.str_pad(array_search($slide, $slides)+1, 2, "0", STR_PAD_LEFT).'</span>/<span>'.str_pad(count($slides), 2, "0", STR_PAD_LEFT).'</span></div>
                                            <div class="swiper-buttons-arrows">
                                                <div class="swiper-button-prev">⏴</div>
                                                <div class="swiper-button-next">⏵</div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>';
                }

                $output .= '
            </div>

        </div>
    </div>';

    $output .= PWE_Swiper::swiperScripts('#pwePremieres', [0 => ['slidesPerView' => 1]], false, true, 1, false);
}

return $output;
