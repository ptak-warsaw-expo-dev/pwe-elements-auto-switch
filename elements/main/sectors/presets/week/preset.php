<?php

$is_pl = PWE_Functions::lang_pl();

$audiences = [
    [
        'icon' => '☼',
        'color' => '#ffb800',
        'title' => PWE_Functions::multi_translation('week_title')
    ],
    [
        'icon' => '⚙',
        'color' => '#00d2ff',
        'title' => PWE_Functions::multi_translation('week_title_1')
    ],
    [
        'icon' => '💡',
        'color' => '#9a4dff',
        'title' => PWE_Functions::multi_translation('week_title_2')
    ],
    [
        'icon' => '▥',
        'color' => '#ff4da6',
        'title' => PWE_Functions::multi_translation('week_title_3')
    ],
    [
        'icon' => '♚',
        'color' => '#ff4da6',
        'title' => PWE_Functions::multi_translation('week_title_4')
    ],
    [
        'icon' => '▱',
        'color' => '#8cff5e',
        'title' => PWE_Functions::multi_translation('week_title_5')
    ],
];

$output = '
<div id="pweSectors" class="pwe-element-auto-switch pwe-sectors">
    <div class="pwe-sectors__wrapper">';

        // <div class="pwe-sectors__title">
        //     <p>'. ($is_pl ? do_shortcode('[trade_fair_name]') : do_shortcode('[trade_fair_name_eng]')) .'</p>
        //     <hr>
        //     <h4 class="pwe-sectors__main-title">'. PWE_Functions::multi_translation('title') .'</h4>
        // </div>

        // <div class="pwe-sectors__items">';

        //     foreach ($sectors as $sector) {
        //         $sector_name = $sector['name'];
        //         $sector_image = $sector['image'];

        //         $output .= '
        //         <div class="pwe-sectors__item">
        //             <div class="pwe-sectors__item-wrapper">
        //                 <div class="pwe-sectors__item-icon">
        //                     <img src="' . esc_url($sector_image) . '" alt="' . esc_attr($sector_name) . '">
        //                 </div>
        //                 <div class="pwe-sectors__item-name">
        //                     <p>' . esc_html($sector_name) . '</p>
        //                 </div>
        //             </div>
        //         </div>';
        //     }


        // $output .= '</div>';

        $output .= '


        <div class="pwe-sectors__audience-section">
            <h2 class="pwe-sectors__audience-title">' . PWE_Functions::multi_translation("audience_title") . '</h2>
            <div class="pwe-sectors__audience-grid">';

                foreach ($audiences as $aud) {
                    $style_color = !empty($aud['color']) ? 'style="color:' . $aud['color'] . '"' : '';
                    $output .= '
                    <article class="pwe-sectors__audience-card">
                        <div class="pwe-sectors__audience-icon" ' . $style_color . '>' . esc_html($aud['icon']) . '</div>
                        <div class="pwe-sectors__audience-content">
                            <h3>' . esc_html($aud['title']) . '</h3>
                        </div>
                    </article>';
                }

            $output .= '
            </div>
        </div>
        <div class="pwe-sectors__button-action">
            <div class="pwe-sectors__button pwe-sectors__button--primary">
                <a href="'. PWE_Functions::multi_translation("catalog_btn_url") .'">'. PWE_Functions::multi_translation("catalog_btn") .'</a>
            </div>
        </div>
        <div class="pwe-sectors__cta-section" id="rejestracja">
            <div class="pwe-sectors__cta-box">
                <div class="pwe-sectors__cta-rocket">🚀</div>
                <div class="pwe-sectors__cta-text">
                    <h2>' . PWE_Functions::multi_translation("cta_title") . ' '. do_shortcode('[trade_fair_name]') .' </h2>
                    <p>' . PWE_Functions::multi_translation("cta_sub") . '</p>
                </div>
                <div class="pwe-sectors__cta-actions">
                    <a class="pwe-sectors__btn pwe-sectors__btn--primary" href="' . PWE_Functions::multi_translation("ticket_url") . '">' . PWE_Functions::multi_translation("cta_btn_ticket") . '</a>
                    <a class="pwe-sectors__btn pwe-sectors__btn--secondary" href="' . PWE_Functions::multi_translation("exhibitor_url") . '">' . PWE_Functions::multi_translation("cta_btn_exhibitor") . '</a>
                </div>
            </div>
        </div>

    </div>
</div>';



return $output;