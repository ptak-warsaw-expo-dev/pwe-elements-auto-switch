<?php

$output .= '
<div id="pweContact" class="pwe-contact">
    <div class="pwe-contact__wrapper">
        <div class="pwe-contact__column">
            <div class="pwe-contact__form">
                <div class="pwe-contact__text">
                    <h2>'. PWE_Functions::multi_translation("write_to_us") . '</h2>
                    <p>'. PWE_Functions::multi_translation("contact_description") . '</p>
                </div>
                <div class="pwe-contact__form-container">
                    ' . do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="false"]') . '
                </div>
            </div>
            ' . PWE_Functions::render_component('organized-groups', 'all', []) . '
        </div>
        <div class="pwe-contact__column">
            ' . PWE_Functions::render_component('contact-details', 'all', []) . '
            ' . PWE_Functions::render_component('location-map', 'all', ['max_width' => '350px']) . '
            ' . PWE_Functions::render_component('pwe-address', 'all', []) . '
        </div>
    </div>
</div>';

return $output;