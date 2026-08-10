<?php

$output = '
<div id="pweRegistrationVisitors" class="pwe-registration-visitors platyna" data-fair-group="'. esc_attr($fair_group) .'">
    <div class="pwe-registration-visitors__column">
        <div class="pwe-registration-visitors__platinum-form">
            <div class="pwe-registration-visitors__platinum-container">
                <div class="pwe-registration-visitors__platinum-fields">
                    <h3>
                        '. PWE_Functions::multi_translation('step_1_of_2') .'
                    </h3>

                    <h2 class="pwe-registration-visitors__platinum-title">
                        '. PWE_Functions::multi_translation('ticket') .'
                    </h2>

                    <div class="pwe-registration-visitors__form">
                        '. $gravity_form .'
                    </div>
                </div>

                <div class="pwe-registration-visitors__benefits">
                    <h2>
                        '. PWE_Functions::multi_translation('vip_invitation') .'
                    </h2>

                    <div class="pwe-registration-visitors__benefit">
                        <img
                            src="/wp-content/plugins/pwe-media/media/platyna/obsluga.webp"
                            alt=""
                        >
                        <p>
                            '. PWE_Functions::multi_translation('concierge_service') .'
                        </p>
                    </div>

                    <div class="pwe-registration-visitors__benefit">
                        <img
                            src="/wp-content/plugins/pwe-media/media/platyna/vip.webp"
                            alt=""
                        >
                        <p>
                            '. PWE_Functions::multi_translation('VIP_zone') .'
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';

return $output;
