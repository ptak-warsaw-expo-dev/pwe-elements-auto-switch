<?php

$output = '
<div id="pweOpinions" class="pwe-opinions">
    <div class="pwe-opinions__wrapper">
        <div class="pwe-opinions__title">
            <h4 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Opinie wystawców i odwiedzających', '') .'</h4>
        </div>
        <div class="pwe-opinions__items swiper">
            <div class="swiper-wrapper">';

                foreach ($opinions_to_render as $opinion_item) {
                    $opinion_person_img = $opinion_item['opinion_person_img'];
                    $opinion_company_img = $opinion_item["opinion_company_img"];
                    $opinion_company_name = PWECommonFunctions::lang_pl() ? $opinion_item['opinion_company_name_pl'] : $opinion_item['opinion_company_name_en'];
                    $opinion_person_name = $opinion_item['opinion_person_name'];
                    $opinion_person_position = PWECommonFunctions::lang_pl() ? $opinion_item['opinion_person_position_pl'] : $opinion_item['opinion_person_position_en'];
                    $opinion_text = PWECommonFunctions::lang_pl() ? $opinion_item['opinion_text_pl'] : $opinion_item['opinion_text_en'];

                    $words = explode(' ', strip_tags($opinion_text));
                    if (count($words) > 30) {
                        $opinion_text = implode(' ', array_slice($words, 0, 30)) . '...';
                    }

                    $output .= '
                    <div class="pwe-opinions__item swiper-slide">
                        <div class="pwe-opinions__item-wrapper">
                            <div class="pwe-opinions__item-top">
                                <div class="pwe-opinions__item-opinion">
                                    <div class="pwe-opinions__item-opinion-text">' . $opinion_text . '</div>
                                </div>
                            </div>
                            <div class="pwe-opinions__item-bottom">
                                <div class="pwe-opinions__item-person-container">
                                    <img data-no-lazy="1" src="' . $opinion_person_img . '">
                                    <div class="pwe-opinions__item-person-info-container">
                                        <h4 class="pwe-opinions__item-person-info-name">' . $opinion_person_name . '</h4>
                                        <h5 class="pwe-opinions__item-person-info-desc">' . $opinion_company_name . '</h5>
                                        <h5 class="pwe-opinions__item-person-info-desc">' . $opinion_person_position . '</h5>
                                    </div>
                                </div>
                                <div class="pwe-opinions__item-info-container">
                                    <div class="pwe-opinions__item-company-info-container">
                                        <div class="pwe-opinions__item-company_logo">
                                            <img data-no-lazy="1" src="' . $opinion_company_img . '">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                }

            $output .= '
            </div>

            <div class="swiper-scrollbar"></div>
        </div>
    </div>
</div>';

$output .= PWE_Swiper::swiperScripts('#pweOpinions', [0   => ['slidesPerView' => 1],960 => ['slidesPerView' => 2],], true);

return $output;
