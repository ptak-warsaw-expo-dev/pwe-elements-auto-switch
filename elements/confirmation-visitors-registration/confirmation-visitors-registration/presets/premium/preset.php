<?php

$output = '
<div
    id="pweConfirmationVisitorsRegistration"
    class="pwe-confirmation-visitors-registration pwe-confirmation-visitors-registration--premium"
    data-fair-group="'. esc_attr($fair_group) .'"
    data-form-id="'. esc_attr($form_id) .'"
    data-group="premium"
    data-lang="'. esc_attr($lang) .'"
>
    <div class="pwe-confirmation-visitors-registration__wrapper">
        <div class="pwe-confirmation-visitors-registration__column pwe-confirmation-visitors-registration__content">
            <p class="pwe-confirmation-visitors-registration__step">
                '. wp_kses_post(PWE_Functions::multi_translation('step')) .'
            </p>

            <h1 class="pwe-confirmation-visitors-registration__title">
                '. wp_kses_post(PWE_Functions::multi_translation('your_ticket')) .'
            </h1>

            <p class="pwe-confirmation-visitors-registration__text">
                '. wp_kses_post(PWE_Functions::multi_translation('receive_e-mail')) .'
            </p>

            <div class="pwe-confirmation-visitors-registration__fair-meta">
                <strong>'. esc_html($trade_fair_edition) .'</strong>
                '. ($trade_fair_date ? '<span>'. esc_html($trade_fair_date) .'</span>' : '') .'
            </div>
        </div>

        <div class="pwe-confirmation-visitors-registration__column pwe-confirmation-visitors-registration__form-column">
            <div class="pwe-confirmation-visitors-registration__before-submit">
                <h2 class="pwe-confirmation-visitors-registration__title">
                    '. esc_html(PWE_Functions::multi_translation('form_title')) .'
                </h2>
            </div>

            <div class="pwe-confirmation-visitors-registration__success">
                <p class="pwe-confirmation-visitors-registration__text">
                    '. esc_html(PWE_Functions::multi_translation('success')) .'
                </p>

                <a class="pwe-confirmation-visitors-registration__button" href="'. esc_url(PWE_Functions::multi_translation('back_link')) .'">
                    '. esc_html(PWE_Functions::multi_translation('back_to_main_page')) .'
                </a>
            </div>
        </div>
    </div>
</div>';

return $output;
