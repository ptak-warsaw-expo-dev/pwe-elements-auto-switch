<?php

$output = '
<div id="pweOtherEvents" class="pwe-other-events">
    <div class="pwe-other-events__wrapper">
        <div class="pwe-other-events__title">
            <h4 class="pwe-other-events__heading pwe-main-title">'. PWE_Functions::languageChecker('Inne wydarzenia', 'Other events') .'</h4>
            <p>'. PWE_Functions::languageChecker('Podczas targów', 'During the fair') .'</p>
        </div>
        
        <div class="swiper pwe-other-events__items">
            <div class="swiper-wrapper">';
                // Generating HTML
                foreach ($other_events_items_json as $other_events_item) {
                    $other_events_domain = $other_events_item["other_events_domain"];
                    $other_events_short_desc = $other_events_item["other_events_short_desc"];
                    $other_events_text = $other_events_item["other_events_text"];

                    $other_events_text_content = !empty($other_events_text_content) ? $other_events_text_content : '<p>[pwe_desc_' . PWE_Functions::languageChecker('pl', 'en') . ' domain="' . $other_events_domain . '"]</p>';
                    if (strpos($other_events_domain, $current_domain) === false) {
                        $output .= '
                        <div class="pwe-other-events__item swiper-slide">
                            <a href="https://'. $other_events_domain .''. PWE_Functions::languageChecker('/', '/en/') .'" target="_blank">
                                <div class="pwe-other-events__item-logo">
                                    <img data-no-lazy="1" src="https://'. $other_events_domain .'/doc/logo-color.webp"/>
                                </div>
                                <div class="pwe-other-events__item-statistic">
                                    <div class="pwe-other-events__item-text">'. (!empty($other_events_short_desc) ? $other_events_short_desc : $other_events_text) .'</div>
                                    <div class="pwe-other-events__item-statistic-numbers-block">
                                        <div class="pwe-other-events__item-statistic-numbers">
                                            <div class="pwe-other-events__item-statistic-icon">
                                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M26.8 6.4C26.8 4.53 28.33 3 30.2 3C32.07 3 33.6 4.53 33.6 6.4C33.6 8.27 32.07 9.8 30.2 9.8C28.33 9.8 26.8 8.27 26.8 6.4ZM34.926 12.486C33.4353 11.8356 31.8264 11.4999 30.2 11.5C29.061 11.5 27.973 11.67 26.936 11.976C27.922 12.911 28.5 14.22 28.5 15.631V16.6H37V15.631C37 14.254 36.184 13.03 34.926 12.486ZM9.8 9.8C11.67 9.8 13.2 8.27 13.2 6.4C13.2 4.53 11.67 3 9.8 3C7.93 3 6.4 4.53 6.4 6.4C6.4 8.27 7.93 9.8 9.8 9.8ZM13.064 11.976C12.027 11.67 10.939 11.5 9.8 11.5C8.117 11.5 6.519 11.857 5.074 12.486C4.45811 12.7493 3.93317 13.1877 3.56443 13.7469C3.19569 14.306 2.99942 14.9612 3 15.631V16.6H11.5V15.631C11.5 14.22 12.078 12.911 13.064 11.976ZM16.6 6.4C16.6 4.53 18.13 3 20 3C21.87 3 23.4 4.53 23.4 6.4C23.4 8.27 21.87 9.8 20 9.8C18.13 9.8 16.6 8.27 16.6 6.4ZM26.8 16.6H13.2V15.631C13.2 14.254 14.016 13.03 15.274 12.486C16.7647 11.8354 18.3736 11.4996 20 11.4996C21.6264 11.4996 23.2354 11.8354 24.726 12.486C25.3419 12.7493 25.8668 13.1877 26.2356 13.7469C26.6043 14.306 26.8006 14.9612 26.8 15.631V16.6ZM25.1 26.8C25.1 24.93 26.63 23.4 28.5 23.4C30.37 23.4 31.9 24.93 31.9 26.8C31.9 28.67 30.37 30.2 28.5 30.2C26.63 30.2 25.1 28.67 25.1 26.8ZM35.3 37H21.7V36.031C21.7 34.654 22.516 33.43 23.774 32.886C25.2647 32.2354 26.8736 31.8996 28.5 31.8996C30.1264 31.8996 31.7354 32.2354 33.226 32.886C33.8419 33.1492 34.3668 33.5877 34.7356 34.1469C35.1043 34.706 35.3006 35.3612 35.3 36.031V37ZM8.1 26.8C8.1 24.93 9.63 23.4 11.5 23.4C13.37 23.4 14.9 24.93 14.9 26.8C14.9 28.67 13.37 30.2 11.5 30.2C9.63 30.2 8.1 28.67 8.1 26.8ZM18.3 37H4.7V36.031C4.7 34.654 5.516 33.43 6.774 32.886C8.26465 32.2354 9.87357 31.8996 11.5 31.8996C13.1264 31.8996 14.7354 32.2354 16.226 32.886C16.8419 33.1492 17.3668 33.5877 17.7356 34.1469C18.1043 34.706 18.3006 35.3612 18.3 36.031V37ZM21.275 21.7V18.3H18.725V21.7H14.9L20 26.8L25.1 21.7H21.275Z" fill="var(--main2-color)"/>
                                                </svg>
                                            </div>
                                            <div class="pwe-other-events__item-statistic-text">
                                                <div class="pwe-other-events__item-statistic-number">' . do_shortcode('[pwe_visitors domain="'. $other_events_domain .'"]') . '</div>
                                                <div class="pwe-other-events__item-statistic-name">'. PWE_Functions::languageChecker('Odwiedzających', 'Visitors') .'</div>
                                            </div>
                                            
                                        </div>
                                        <div class="pwe-other-events__item-statistic-numbers">
                                            <div class="pwe-other-events__item-statistic-icon">
                                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M20.4957 22.4873C21.7042 22.6113 22.8238 23.179 23.6379 24.0807C24.452 24.9824 24.9027 26.1541 24.9028 27.3689C24.9028 29.8915 23.3651 31.9789 21.2973 33.3564C19.2197 34.7436 16.4386 35.5485 13.4514 35.5485C10.4642 35.5485 7.68318 34.7436 5.60556 33.3564C3.53449 31.9822 2 29.8915 2 27.3689C2 26.0673 2.51707 24.819 3.43745 23.8986C4.35783 22.9782 5.60614 22.4612 6.90775 22.4612H19.9951L20.4957 22.4873ZM33.0922 22.4612C34.3939 22.4612 35.6422 22.9782 36.5626 23.8986C37.4829 24.819 38 26.0673 38 27.3689C38 29.6428 36.5866 31.3311 34.7838 32.3683C32.9908 33.3989 30.6449 33.9126 28.1845 33.9126C27.3556 33.9104 26.5475 33.8493 25.7601 33.7294C27.1768 32.0542 28.178 29.9144 28.178 27.3689C28.1742 25.5976 27.5955 23.8754 26.5289 22.4612H33.0922ZM13.458 4.45298C14.4389 4.43275 15.4139 4.60854 16.326 4.97003C17.2381 5.33152 18.0688 5.87144 18.7695 6.55815C19.4702 7.24485 20.0268 8.06453 20.4066 8.96912C20.7864 9.87372 20.9818 10.845 20.9814 11.8261C20.981 12.8072 20.7847 13.7784 20.4041 14.6826C20.0234 15.5869 19.4661 16.4061 18.7648 17.0922C18.0635 17.7782 17.2323 18.3174 16.3199 18.6781C15.4075 19.0388 14.4323 19.2137 13.4514 19.1926C11.5237 19.152 9.68866 18.3577 8.3397 16.98C6.99074 15.6023 6.23527 13.7509 6.23527 11.8228C6.23527 9.89466 6.99074 8.04327 8.3397 6.66559C9.68866 5.28791 11.5237 4.4936 13.4514 4.45298M28.9926 7.72809C29.7445 7.72809 30.4891 7.87619 31.1838 8.16393C31.8784 8.45168 32.5096 8.87343 33.0413 9.40511C33.573 9.93679 33.9948 10.568 34.2825 11.2627C34.5702 11.9573 34.7183 12.7019 34.7183 13.4538C34.7183 14.2057 34.5702 14.9503 34.2825 15.6449C33.9948 16.3396 33.573 16.9708 33.0413 17.5025C32.5096 18.0342 31.8784 18.4559 31.1838 18.7437C30.4891 19.0314 29.7445 19.1795 28.9926 19.1795C27.4741 19.1795 26.0177 18.5763 24.9439 17.5025C23.8702 16.4287 23.2669 14.9724 23.2669 13.4538C23.2669 11.9352 23.8702 10.4789 24.9439 9.40511C26.0177 8.33133 27.4741 7.72809 28.9926 7.72809Z" fill="var(--main2-color)"/>
                                                </svg>
                                            </div>
                                            <div class="pwe-other-events__item-statistic-text">
                                                <div class="pwe-other-events__item-statistic-number">' . do_shortcode('[pwe_exhibitors domain="'. $other_events_domain .'"]') . '</div>
                                                <div class="pwe-other-events__item-statistic-name">'. PWE_Functions::languageChecker('Wystawców', 'Exhibitors') .'</div>
                                            </div>
                                        </div>
                                        <div class="pwe-other-events__item-statistic-numbers">
                                            <div class="pwe-other-events__item-statistic-icon">
                                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M21.6666 5H5V35H35V18.3334H21.6666V5ZM18.3334 31.6666H8.33336V21.6666H18.3334V31.6666ZM18.3334 18.3334H8.33336V8.33336H18.3334V18.3334ZM31.6666 21.6666V31.6666H21.6666V21.6666H31.6666ZM35 5V15H25V5H35ZM31.6666 8.33336H28.3334V11.6666H31.6666V8.33336Z" fill="var(--main2-color)"/>
                                                </svg>
                                            </div>
                                            <div class="pwe-other-events__item-statistic-text">
                                                <div class="pwe-other-events__item-statistic-number">' . do_shortcode('[pwe_area domain="'. $other_events_domain .'"]') . ' m2</div>
                                                <div class="pwe-other-events__item-statistic-name">'. PWE_Functions::languageChecker('Powierzchni<br>wystawienniczej', 'Exhibition space') .'</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>';
                    }
                }
            $output .= '
            </div>
            <div class="swiper-nav">
                <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
            </div>
        </div>
    </div>
</div>';

$output .= PWE_Swiper::swiperScripts('#pweOtherEvents', null, true, false);

return $output;
