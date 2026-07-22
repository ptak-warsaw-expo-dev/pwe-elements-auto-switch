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
                        $output .= '
                        <a href="' . esc_url('tel:' . self::pwe_phone_href($service_phone)) . '">' . esc_html($service_phone) . '</a>';
                    }

                    $output .= self::pwe_render_email_links($service_emails);
                    
                $output .= '
                </p>
            </div>';

            if (!empty($consultant_email)) {
                $consultant_email = sanitize_email($consultant_email);
                $output .= '
                <div class="pwe-contact-details__item">
                    <img src="/wp-content/plugins/pwe-media/media/WystawcyZ.jpg" alt="grafika wystawcy">
                    <p class="pwe-contact-details__item-text">
                        <b>'. PWE_Functions::multi_translation("technical_support") . '</b>
                        
                        <a href="' . esc_url('mailto:' . $consultant_email) . '">
                            <span>' . esc_html($consultant_email) . '</span>
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
                        
                        if (!empty($marketing_media_phone)) {
                            $output .= '
                            <a href="' . esc_url('tel:' . self::pwe_phone_href($marketing_media_phone)) . '">' . esc_html($marketing_media_phone) . '</a>';
                        }

                        $output .= self::pwe_render_email_links($marketing_emails);

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
                            $output .= '
                            <a href="' . esc_url('tel:' . self::pwe_phone_href($contact_person_phone)) . '">' . esc_html($contact_person_phone) . '</a>';
                        }

                        if (!empty($contact_person_email)) {
                            $contact_person_email = sanitize_email($contact_person_email);

                            if (!empty($contact_person_email)) {
                                $output .= '
                                <a href="' . esc_url('mailto:' . $contact_person_email) . '">' . esc_html($contact_person_email) . '</a>';
                            }
                        }
                        $output .= '
                    </p>
                </div>';
            }

            if (!empty($contact_person_name_2) && (!empty($contact_person_email_2) || !empty($contact_person_phone_2))) {
                $output .= '
                <div class="pwe-contact-details__item">
                    <img src="/wp-content/plugins/pwe-media/media/Person.jpg" alt="grafika osoby">
                    <p class="pwe-contact-details__item-text">
                        <b>'. $contact_person_name_2 .'</b>';
                        
                        if (!empty($contact_person_phone_2)) {
                            $output .= '
                            <a href="' . esc_url('tel:' . self::pwe_phone_href($contact_person_phone_2)) . '">' . esc_html($contact_person_phone_2) . '</a>';
                        }

                        if (!empty($contact_person_email_2)) {
                            $contact_person_email_2 = sanitize_email($contact_person_email_2);

                            if (!empty($contact_person_email_2)) {
                                $output .= '
                                <a href="' . esc_url('mailto:' . $contact_person_email_2) . '">' . esc_html($contact_person_email_2) . '</a>';
                            }
                        }
                        $output .= '
                    </p>
                </div>';
            }

            if (!empty($contact_person_name_3) && (!empty($contact_person_email_3) || !empty($contact_person_phone_3))) {
                $output .= '
                <div class="pwe-contact-details__item">
                    <img src="/wp-content/plugins/pwe-media/media/Person.jpg" alt="grafika osoby">
                    <p class="pwe-contact-details__item-text">
                        <b>'. $contact_person_name_3 .'</b>';
                        
                        if (!empty($contact_person_phone_3)) {
                            $output .= '
                            <a href="' . esc_url('tel:' . self::pwe_phone_href($contact_person_phone_3)) . '">' . esc_html($contact_person_phone_3) . '</a>';
                        }

                        if (!empty($contact_person_email_3)) {
                            $contact_person_email_3 = sanitize_email($contact_person_email_3);

                            if (!empty($contact_person_email_3)) {
                                $output .= '
                                <a href="' . esc_url('mailto:' . $contact_person_email_3) . '">' . esc_html($contact_person_email_3) . '</a>';
                            }
                        }
                        $output .= '
                    </p>
                </div>';
            }
            $output .= '
    </div>
</div>';

return $output;
