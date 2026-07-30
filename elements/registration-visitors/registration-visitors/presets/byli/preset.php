<?php

$output = '
<div id="pweRegistrationVisitors" class="pwe-registration-visitors byli" data-fair-group="'. esc_attr($fair_group) .'">
    <div class="pwe-registration-visitors__wrapper">
        <div class="pwe-registration-visitors__column pwe-mockup-column">
            '. ($badgevipmockup
                ? '<img src="'. esc_url($badgevipmockup) .'" alt="">'
                : '') .'
        </div>

        <div class="pwe-registration-visitors__column pwe-registration-column">
            <div class="pwe-registration-visitors__step-text">
                <p>'. esc_html(PWE_Functions::multi_translation('step_1_of_2')) .'</p>
            </div>

            <div class="pwe-registration-visitors__title">
                <h4>'. esc_html(PWE_Functions::multi_translation('your_ticket')) .'</h4>
            </div>

            <div class="pwe-registration-visitors__form">
                '. $gravity_form .'
            </div>
        </div>
    </div>
</div>';

return $output;
