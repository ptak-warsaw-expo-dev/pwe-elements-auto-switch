<?php

$output .= '
<div id="pweRegistrationVisitors" class="pwe-registration-visitors ' . $group . '">
    <div class="pwe-registration-visitors__wrapper">
        <div class="pwe-registration-visitors__column pwe-mockup-column">
            <img src="'. $badgevipmockup .'">
        </div>
        <div class="pwe-registration-visitors__column pwe-registration-column">
            <div class="pwe-registration-visitors__step-text">
                <p>'. PWE_Functions::multi_translation("step_1_of_2").'</p>
            </div>
            <div class="pwe-registration-visitors__title">
                <h4>'. PWE_Functions::multi_translation("ticket").'</h4>
            </div>
            <div class="pwe-registration-visitors__form">
                ' . do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="false"]') . '
            </div>
        </div>
        <div class="pwe-registration-visitors__column pwe-exhibitors-column">
            ' . PWE_Functions::render_component('exhibitors-top12', $group, []) . '
        </div>
    </div>
</div>';

return $output;