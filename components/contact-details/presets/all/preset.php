<?php

$output = '
<div id="pweContactDetails" class="pwe-contact-details">

        <div class="pwe-contact-details__title">
            <h4>'. PWE_Functions::multi_translation("customer_service") . '</h4>
        </div>

        <div class="pwe-contact-details__items">

            <div class="pwe-contact-details__item">
                <img src="/wp-content/plugins/pwe-media/media/Phone.jpg" alt="grafika słuchawka">
                <p class="pwe-contact-details__item-text">
                    <b>'. PWE_Functions::multi_translation("customer_service_office") . '</b>';
                    if (!empty($service_phone)) {
                        $output .= '<a href="tel:'. $service_phone .'">'. $service_phone .'</a>';
                    }

                    if (!empty($service_emails) && is_array($service_emails)) {
                        foreach ($service_emails as $email) {
                            $output .= '
                            <a href="mailto:'. $email .'">
                                <span>'. str_replace("@warsawexpo.eu", "", $email) .'</span><span>@warsawexpo.eu</span>
                            </a>';
                        }
                    }
                    $output .= '
                </p>
            </div>';

            if (!empty($consultant_email)) {
                $output .= '
                <div class="pwe-contact-details__item">
                    <img src="/wp-content/plugins/pwe-media/media/WystawcyZ.jpg" alt="grafika wystawcy">
                    <p class="pwe-contact-details__item-text">
                        <b>'. PWE_Functions::multi_translation("technical_support") . '</b>
                        <a href="mailto:'. $consultant_email .' ">
                            <span>'. $consultant_email .'</span>
                        </a>
                    </p>
                </div>';
            }

        $output .= '
        </div>

        <div class="pwe-contact-details__title" style="margin-top: 36px;">
            <h4>'. PWE_Functions::multi_translation("media_marketing") . '</h4>
        </div>

        <div class="pwe-contact-details__items">';

            if (!empty($marketing_emails)) {
                $output .= '
                <div class="pwe-contact-details__item">
                    <img src="/wp-content/plugins/pwe-media/media/Marketing.jpg" alt="grafika technicy">
                    <p class="pwe-contact-details__item-text">
                        <b>'. PWE_Functions::multi_translation("media_marketing_service") . '</b>';
                        if (!empty($marketing_emails) && is_array($marketing_emails)) {
                            foreach ($marketing_emails as $email) {
                                $output .= '
                                <a href="mailto:'. $email .'">
                                    <span>'. str_replace("@warsawexpo.eu", "", $email) .'</span><span>@warsawexpo.eu</span>
                                </a>';
                            }
                        }

                    $output .= '
                    </p>
                </div>';
            }

            if (!empty($contact_person_name) && (!empty($contact_person_email) || !empty($contact_person_phone))) {
                $output .= '
                <div class="pwe-contact-details__item">
                    <img src="/wp-content/plugins/pwe-media/media/Person.jpg" alt="grafika osoby">
                    <p class="pwe-contact-details__item-text">
                        <b>'. $contact_person_name .'</b>';
                            if (!empty($contact_person_phone)) {
                                $output .= '<a href="tel:'. $contact_person_phone .'">'. $contact_person_phone .'</a>';
                            }
                            if (!empty($contact_person_email)) {
                                $output .= '<a href="mailto:'. $contact_person_email .'">'. $contact_person_email .'</a>';
                            }
                        $output .= '
                    </p>
                </div>';
            }
            $output .= '
    </div>
</div>';

return $output;
