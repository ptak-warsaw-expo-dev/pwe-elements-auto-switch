<?php

$output = '
<div id="pweRegistrationVisitors" class="pwe-registration-visitors premium" data-fair-group="'. esc_attr($fair_group) .'">
    <div class="pwe-registration-visitors__wrapper">
        <div class="pwe-registration-visitors__column pwe-mockup-column">
            '. ($badgevipmockup ? '<img src="'. esc_url($badgevipmockup) .'" alt="">' : '') .'
        </div>

        <div class="pwe-registration-visitors__column pwe-registration-column">
            <div class="pwe-registration-visitors__step-text">
                <p>'. PWE_Functions::multi_translation('step_1_of_2') .'</p>
            </div>

            <div class="pwe-registration-visitors__title">
                <h4>'. $title .'</h4>
            </div>

            <div class="pwe-registration-visitors__form">
                '. $gravity_form .'
            </div>

            '. $statement .'
        </div>';

        if (count($exhibitors) >= 12) {
            $output .= PWE_Functions::render_component('exhibitors-top12', 'byli-premium-visitors', []);
        }

    $output .= '
    </div>
</div>';

return $output;
 