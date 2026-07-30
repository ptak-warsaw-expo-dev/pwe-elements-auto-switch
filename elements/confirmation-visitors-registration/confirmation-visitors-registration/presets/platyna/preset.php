<?php
$output = '<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD10_XMpLZxzQT_65E58g0yTq7GQBXUks4&libraries=places"></script>';
$output .= '

    <div id="pweConfirmationVisitorsRegistration" class="pwe-registration pwe-confirmation-visitors-registration--platyna">
        <div class="pwe-registration__column">
            <div id="pweForm" class="pwe-registration__form-wrapper">
                <div class="pwe-registration__container">

                    <!-- Sekcja Formularza -->
                    <div class="pwe-registration__form-section">

                        <!-- Treść widoczna PRZED wysłaniem -->
                        <div class="pwe-registration__before-submit">
                            <h2 class="pwe-registration__title pwe-registration__display-before-submit">
                                ' . PWECommonFunctions::languageChecker(
                                    'Podaj adres, na który mamy wysłać <span style="color:#616161">darmowy pakiet PLATINIUM</span>',
                                    'Enter the address to which we should send<br/>the <span style="color:#616161">free PLATINIUM package</span>'
                                ) . '
                            </h2>

                            <div class="pwe-registration__form-box">
                                <div class="pwe-registration__gravity-form gf_browser_chrome gform_wrapper gravity-theme gform-theme--no-framework">
                                    <form id="addressUpdateForm">
                                        <div class="gform-body gform_body">
                                            <div class="gform_fields">

                                                <div class="gfield gfield--width-full">
                                                    <label class="gfield_label gform-field-label">Imię i Nazwisko</label>
                                                    <input type="text" id="name" placeholder="Imię i Nazwisko" required />
                                                </div>

                                                <div class="gfield gfield--width-full">
                                                    <label class="gfield_label gform-field-label">Ulica</label>
                                                    <input type="text" id="street" placeholder="Ulica" required />
                                                </div>

                                                <div class="pwe-registration__row">
                                                    <div class="gfield gfield--width-full pwe-registration__field-flex">
                                                        <label class="gfield_label gform-field-label">Numer budynku</label>
                                                        <input type="text" id="house" placeholder="Numer budynku" required />
                                                    </div>
                                                    <div class="gfield gfield--width-full pwe-registration__field-flex">
                                                        <label class="gfield_label gform-field-label">' . PWECommonFunctions::languageChecker('Numer lokalu', 'Premises number') . '</label>
                                                        <input type="text" id="local" placeholder="' . PWECommonFunctions::languageChecker('Numer lokalu', 'Premises number') . '" required />
                                                    </div>
                                                </div>

                                                <div class="pwe-registration__row">
                                                    <div class="gfield gfield--width-half pwe-registration__field-flex">
                                                        <label class="gfield_label gform-field-label">Kod pocztowy</label>
                                                        <div class="pwe-registration__post-wrapper">
                                                            <input type="text" id="post1" maxlength="2" placeholder="00" required />
                                                            <span class="pwe-registration__post-separator">-</span>
                                                            <input type="text" id="post2" maxlength="3" placeholder="000" required />
                                                        </div>
                                                    </div>
                                                    <div class="gfield gfield--width-half pwe-registration__field-flex">
                                                        <label class="gfield_label gform-field-label">Miasto</label>
                                                        <input type="text" id="city" placeholder="Miasto" required />
                                                    </div>
                                                </div>

                                                <div id="statusMessage" class="status-message"></div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="pwe-registration__buttons">
                                <button id="pweSendStepTwo" type="button" class="pwe-registration__button update-button btn pwe-btn pwe_reg_visitor" onclick="updateGravityForm()">Zamawiam Bezpłatny identyfikator</button>
                            </div>
                        </div>

                        <!-- Treść widoczna PO wysłaniu (Sukces) -->
                        <div class="pwe-registration__success" style="display: none;">
                            <h2 class="pwe-registration__title pwe-registration__display-after-submit">
                                ' . PWECommonFunctions::languageChecker('Dziękujemy za przesłanie danych!', 'Thank you for submitting your details!') . '
                            </h2>
                            <p class="pwe-registration__text pwe-registration__display-after-submit">
                                ' . PWECommonFunctions::languageChecker('Twój darmowy pakiet PLATINIUM wraz z materiałami zostanie wysłany na podany adres.', 'Your free PLATINIUM package will be sent to the provided address.') . '
                            </p>
                            <div class="pwe-registration__buttons">
                                <a href="/" class="pwe-registration__button btn pwe-btn pwe_reg_visitor">
                                    Powrót do strony głównej
                                </a>
                            </div>
                        </div>

                    </div>

                    <!-- Sekcja Korzyści (Benefits) -->
                    <div class="pwe-registration__benefits">
                        <h2 class="pwe-registration__benefits-title">
                            ' . PWECommonFunctions::languageChecker('Pakiet PLATINIUM upoważnia do:', 'The PLATINIUM package<br/>entitles you to:') . '
                        </h2>

                        <div class="pwe-registration__benefit-item">
                            <img src="/wp-content/plugins/pwe-media/media/platyna/fasttrack.webp" alt="Fast Track" />
                            <p>' . PWECommonFunctions::languageChecker('Wejście bezpłatne', 'Free entry') . '<br/>FAST TRACK</p>
                        </div>

                        <div class="pwe-registration__benefit-item">
                            <img src="/wp-content/plugins/pwe-media/media/platyna/obsluga.webp" alt="Concierge" />
                            <p>' . PWECommonFunctions::languageChecker('Obsługę concierge"a', 'Concierge service') . '</p>
                        </div>

                        <div class="pwe-registration__benefit-item">
                            <img src="/wp-content/plugins/pwe-media/media/platyna/vip.webp" alt="VIP Room" />
                            <p>' . PWECommonFunctions::languageChecker('Strefę VIP ROOM', 'VIP ROOM area') . '</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


';

return $output;