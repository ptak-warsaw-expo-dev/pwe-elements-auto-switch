<?php

$output = '
<div id="pweLayout" class="pwe-layout">
    <div class="pwe-layout__wrapper">
        <div class="pwe-layout__column">
            <div class="pwe-layout__header">
                <h2>'. PWE_Functions::multi_translation("check_fair_plan") . '</h2>
                <a href="'. (PWE_Functions::lang_pl() ? '/plan-targow/' : '/en/fair-plan/') . '">
                    <img src="'. (PWE_Functions::lang_pl() ? '/doc/plan.webp' : '/doc/plan-en.webp') . '" alt="Trade fair plan banner">
                </a>
            </div>
        </div>
        <div class="pwe-layout__contact">
            <div class="pwe-layout__column">
                <div class="pwe-layout__form">
                    <div class="pwe-layout__text">
                        <h2>'. PWE_Functions::multi_translation("write_to_us") . '</h2>
                        <p>'. PWE_Functions::multi_translation("contact_description") . '</p>
                    </div>
                    <div class="pwe-layout__form-container">
                        ' . do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="false"]') . '
                    </div>
                </div>
                ' . PWE_Functions::render_component('organized-groups', 'all', []) . '
            </div>
            <div class="pwe-layout__column">
                ' . PWE_Functions::render_component('contact-details', 'all', []) . '
                ' . PWE_Functions::render_component('location-map', 'all', ['max_width' => '350px']) . '
                ' . PWE_Functions::render_component('pwe-address', 'all', []) . '
            </div>
        </div>
    </div>
</div>';

return $output;