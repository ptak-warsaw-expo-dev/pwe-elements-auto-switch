<?php
$output = '<script src="https://maps.googleapis.com/maps/api/js?key='. PWE_Functions::get_database_meta_data('api_key_google_places') .'&libraries=places"></script>';
$output .= '
    <div
        id="pweConfirmationVisitorsRegistration"
        class="pwe-confirmation-visitors-registration pwe-confirmation-visitors-registration--' . $group . '"
    >
        <div class="pwe-confirmation-visitors-registration__wrapper">

            <div class="pwe-confirmation-visitors-registration__column pwe-confirmation-visitors-registration__content">
                <h2 class="pwe-confirmation-visitors-registration__display-before-submit">Dziękujemy za rejestrację na targi <span class="very-strong">[trade_fair_name]!</span></h2>
                <h2 class="pwe-confirmation-visitors-registration__display-after-submit">Dziękujemy za zamówienie pakietu na targi <span class="very-strong">[trade_fair_name]!</span></h2>

                <p class="pwe-confirmation-visitors-registration__display-before-submit">Cieszymy się, że dołączasz do naszego wydarzenia, pełnego nowości rynkowych i inspiracji do zastosowania w Twojej firmie.</p><br>
                
                <p class="pwe-confirmation-visitors-registration__display-before-submit"><span class="very-strong">Zachęcamy do wypełnienia</span> ostatniego formularza, dzięki temu będziemy mogli przygotować dla Państwa <span class="very-strong">spersonalizowany identyfikator</span> targowy, który usprawni pobyt na targach.</p>
                <p class="pwe-confirmation-visitors-registration__display-after-submit">Twój <span class="very-strong"> spersonalizowany identyfikator</span> wraz z planem/harmonogramem targów otrzymasz przed wydarzeniem na podany w formularzu adres za pośrednictwem poczty polskiej.</p>
            </div>

            <div class="pwe-confirmation-visitors-registration__column pwe-confirmation-visitors-registration__form-column">
                <div class="pwe-confirmation-visitors-registration__before-submit">
                    <h3 class="pwe-confirmation-visitors-registration__display-before-submit">Podaj adres, na który mamy wysłać <span class="golden-text">darmowy pakiet powitalny VIP</span></h3>
                    <p class="pwe-confirmation-visitors-registration__display-before-submit">Otrzymasz bezpłatny spersonalizowany identyfikator wraz z planem/harmonogramem targów.</p>
                    <div class="pwe-confirmation-visitors-registration__address-form">
                        <div class="pwe-field">
                            <label>Imię i nazwisko</label>
                            <input type="text" id="name" placeholder="Imię i nazwisko"/>
                        </div>

                        <div class="pwe-field">
                            <label>Ulica</label>
                            <input type="text" id="street" placeholder="Ulica" />
                        </div>

                        <div class="pwe-row">
                            <div class="pwe-field">
                                <label>Numer domu</label>
                                <input type="text" id="house" placeholder="Numer domu" />
                            </div>

                            <div class="pwe-field">
                                <label>Numer lokalu</label>
                                <input type="text" id="local" placeholder="Numer lokalu"/>
                            </div>
                        </div>

                        <div class="pwe-row">
                            <div class="pwe-field">
                                <label>Kod pocztowy</label>
                                <div class="pwe-post">
                                    <input id="post1" maxlength="2" placeholder="00"/>
                                    <span>-</span>
                                    <input id="post2" maxlength="3" placeholder="000"/>
                                </div>
                            </div>

                            <div class="pwe-field">
                                <label>Miasto</label>
                                <input type="text" id="city" placeholder="Miasto"/>
                            </div>
                        </div>

                        <div id="statusMessage"></div>

                        <div class="pwe-confirmation-visitors-registration__address-form-button">
                            <button type="button" id="pweSendStepTwo" class="pwe-confirmation-visitors-registration__button">
                                Zamawiam Bezpłatny identyfikator
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pwe-confirmation-visitors-registration__success">
                    <p class="pwe-confirmation-visitors-registration__text">
                        Dane zostały zapisane. Dziękujemy za rejestrację.
                    </p>

                    <a class="pwe-confirmation-visitors-registration__button" href="/">
                        Powrót do strony głównej
                    </a>
                </div>
            </div>

            <div class="pwe-confirmation-visitors-registration__column pwe-confirmation-visitors-registration__badge-column">
                <img src="/doc/badge-mockup.webp">
            </div>
            
        </div>
    </div>';

return $output;
?>