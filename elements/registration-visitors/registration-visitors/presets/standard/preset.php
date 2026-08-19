<?php

$output = '
<div id="pweRegistrationVisitors" class="pwe-registration-visitors standard" data-fair-group="'. esc_attr($fair_group) .'">
    <div class="pwe-registration-visitors__wrapper">
        <div class="pwe-registration-visitors__form">
            <img
                class="pwe-registration-visitors__form-badge-top"
                src="/wp-content/plugins/pwe-media/media/badge_top.png"
                alt=""
            >

            <div class="pwe-registration-visitors__form-container pwe-registration">
                <div class="pwe-registration-visitors__form-badge-header">
                    <h1 class="pwe-registration-visitors__form-header-title">
                        '. $title .'
                    </h1>

                    <a href="https://warsawexpo.eu/" target="_blank" rel="noopener">
                        <img
                            class="pwe-registration-visitors__form-header-image-qr"
                            src="/wp-content/plugins/pwe-media/media/logo_pwe_black.webp"
                            alt="Logo Ptak Warsaw Expo"
                        >
                    </a>
                </div>

                <img
                    class="pwe-registration-visitors__form-badge-left"
                    src="/wp-content/plugins/pwe-media/media/badge_left.png"
                    alt=""
                >

                <img
                    class="pwe-registration-visitors__form-badge-bottom"
                    src="/wp-content/plugins/pwe-media/media/badge_bottom.png"
                    alt=""
                >

                <img
                    class="pwe-registration-visitors__form-badge-right"
                    src="/wp-content/plugins/pwe-media/media/badge_right.png"
                    alt=""
                >

                <a href="https://warsawexpo.eu/" target="_blank" rel="noopener">
                    <img
                        class="pwe-registration-visitors__form-image-qr"
                        src="/wp-content/plugins/pwe-media/media/logo_pwe_black.webp"
                        alt="Logo Ptak Warsaw Expo"
                    >
                </a>

                <div class="pwe-registration-visitors__form-content">
                    <h2 id="main-content" class="pwe-registration-visitors__form-title">
                        '. $title .'
                    </h2>

                    <div class="pwe-registration-visitors__form-fields">
                        '. $gravity_form .'
                    </div>

                    '. $statement .'
                </div>
            </div>
        </div>';

        if (count($exhibitors) >= 12) {
            $output .= PWE_Functions::render_component('exhibitors-top12', 'standard-visitors', []);
        }
        
    $output .= ' 
    </div>
</div>';

return $output;
