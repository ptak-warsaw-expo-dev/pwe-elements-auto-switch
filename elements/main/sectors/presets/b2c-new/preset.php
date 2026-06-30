<?php

$output .= '
<section id="pweSectors" class="pwe-sectors-section">
    <div class="pwe-sectors__bg-decor">
        <div class="pwe-sectors__decor-text text-moto">PTAK</div>
        <div class="pwe-sectors__decor-text text-expo">EXPO</div>
    </div>

    <div class="pwe-sectors-container">

        <div class="pwe-sectors__header">
            <span class="pwe-subtitle">' . (PWE_Functions::lang_pl() ? do_shortcode('[trade_fair_name]') : do_shortcode('[trade_fair_name_eng]')) . '</span>
            <h2 class="pwe-title">' . PWE_Functions::multi_translation('title') . '</h2>
        </div>

        <div class="pwe-sectors__grid">';

            foreach ($sectors as $sector) {
                $sector_name  = $sector['name'];
                $sector_image = $sector['image'];
                // Jeśli w bazie przechowujesz opisy kategorii, podstaw zmienną np. $sector['desc']
                $sector_desc  = isset($sector['desc']) ? $sector['desc'] : '';

                $output .= '
                <div class="pwe-sectors__card group">
                    <div class="pwe-sectors__icon-wrapper">
                        <img src="' . esc_url($sector_image) . '" alt="' . esc_attr($sector_name) . '">
                    </div>

                    <div class="pwe-sectors__card-content">
                        <h3 class="pwe-sectors__item-name">' . esc_html($sector_name) . '</h3>
                        ' . (!empty($sector_desc) ? '<p class="pwe-sectors__item-desc">' . esc_html($sector_desc) . '</p>' : '') . '
                    </div>
                </div>';
            }

        $output .= '
        </div>

        <div class="pwe-sectors__footer">
            <div class="pwe-logotypes__button pwe-main-btn--primary">
                <a href="'. PWE_Functions::multi_translation("catalog_btn_url") .'">'. PWE_Functions::multi_translation("catalog_btn") .'</a>
            </div>
        </div>

    </div>
</section>';

return $output;