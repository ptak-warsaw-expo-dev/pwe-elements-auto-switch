<?php

$output = '
<div id="pweRegistrationExhibitors" class="pwe-registration-exhibitors" data-fair-group="'. esc_attr($fair_group) .'">
    <div class="pwe-registration-exhibitors__wrapper">
        <div class="pwe-registration-exhibitors__form">
            <div class="pwe-registration-exhibitors__content">
                <h2 id="main-content" class="pwe-registration-exhibitors__title">
                    '. $registration_title .'
                </h2>

                <div class="pwe-registration-exhibitors__text">
                    '. wp_kses_post(wpautop($registration_text)) .'
                </div>
            </div>

            <div class="pwe-registration-exhibitors__form-fields">
                '. $gravity_form .'
            </div>
        </div>';

        if (count($exhibitors) >= 12) {
            $output .= PWE_Functions::render_component('exhibitors-top12', 'standard-exhibitors', []);
        }

    $output .= '
    </div>
</div>';

return $output;
