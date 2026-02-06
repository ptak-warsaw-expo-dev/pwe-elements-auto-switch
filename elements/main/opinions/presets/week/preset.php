<?php

$output = '
<div id="pweOpinions" class="pwe-opinions">
    <div class="pwe-opinions__wrapper">
        <div class="pwe-opinions__title">
            <h4 class="pwe-main-title">'. PWE_Functions::languageChecker('Rekomendacje', 'Recomendations') .'</h4>
            <div class="swiper-buttons-arrows">
                <div class="swiper-button-prev">⏴</div>
                <div class="swiper-button-next">⏵</div>
            </div>
        </div>
        <div class="pwe-opinions__items swiper">
            <div class="swiper-wrapper">';

                foreach ($opinions_to_render as $opinion_item) {
                    $opinion_person_img = $opinion_item['opinion_person_img'];
                    $opinion_company_img = $opinion_item["opinion_company_img"];
                    $opinion_company_name = PWE_Functions::lang_pl() ? $opinion_item['opinion_company_name_pl'] : $opinion_item['opinion_company_name_en'];
                    $opinion_person_name = $opinion_item['opinion_person_name'];
                    $opinion_person_position = PWE_Functions::lang_pl() ? $opinion_item['opinion_person_position_pl'] : $opinion_item['opinion_person_position_en'];
                    $opinion_text = PWE_Functions::lang_pl() ? $opinion_item['opinion_text_pl'] : $opinion_item['opinion_text_en'];

                    // $words = explode(' ', strip_tags($opinion_text));
                    // if (count($words) > 30) {
                    //     $opinion_text = implode(' ', array_slice($words, 0, 30)) . '...';
                    // }

                    $output .= '
                    <div class="pwe-opinions__item swiper-slide">
                        <div class="pwe-opinions__item-wrapper">
                            <div class="pwe-opinions__item-top">
                                <svg width="74" height="56" viewBox="0 0 74 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M34.4871 0C36.3938 0 37.0871 0.953334 36.5671 2.86L19.4071 53.04C18.8871 54.4267 17.8471 55.12 16.2871 55.12H2.24714C0.340476 55.12 -0.352857 54.1667 0.167143 52.26L12.3871 2.34C12.7338 0.779996 13.6871 0 15.2471 0H34.4871ZM70.8871 0C72.7938 0 73.4872 0.953334 72.9672 2.86L55.5471 53.04C55.0271 54.4267 53.9871 55.12 52.4271 55.12H38.3871C36.4805 55.12 35.7871 54.1667 36.3071 52.26L48.7871 2.34C49.1338 0.779996 50.0871 0 51.6471 0H70.8871Z" fill="var(--accent-color)"/>
                                </svg>
                                <div class="pwe-opinions__item-top-img">
                                    <img data-no-lazy="1" src="' . $opinion_company_img . '">
                                </div>
                            </div>
                            <div class="pwe-opinions__item-opinion">
                                <h5 class="pwe-opinions__item-company-name">' . $opinion_company_name . '</h5>
                                <div class="pwe-opinions__item-opinion-text">' . $opinion_text . '</div>
                            </div>
                            <div class="pwe-opinions__item-bottom">
                                <div class="pwe-opinions__item-bottom-img">
                                    <img data-no-lazy="1" src="' . $opinion_person_img . '">
                                </div>
                                <div class="pwe-opinions__item-bottom-info">
                                    <h4 class="pwe-opinions__item-person-name">' . $opinion_person_name . '</h4>
                                    <p class="pwe-opinions__item-person-position">' . $opinion_person_position . '</p>
                                </div>
                            </div>
                        </div>
                    </div>';
                }

            $output .= '
            </div>

            <div class="swiper-nav">
                <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
            </div>
        </div>
    </div>
</div>';

$output .= PWE_Swiper::swiperScripts('#pweOpinions', [0   => ['slidesPerView' => 1],650 => ['slidesPerView' => 2],960 => ['slidesPerView' => 3],], true, true, 1, true, 36);

return $output;
