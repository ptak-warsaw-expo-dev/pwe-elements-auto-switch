<?php
if (!defined('ABSPATH')) {
    exit;
}

$benefits = [
    ['icon' => '/wp-content/plugins/pwe-media/media/vip_diament.webp', 'text' => 'Wstępu do strefy VIP'],
    ['icon' => '/wp-content/plugins/pwe-media/media/vip_ludzik.webp', 'text' => 'Uczestnictwa w wydarzeniach towarzyszących targom'],
    ['icon' => '/wp-content/plugins/pwe-media/media/vip_wejscie-vip.webp', 'text' => 'Szybkiego wejścia na teren targów, gdzie czeka na Ciebie ponad 300 wystawców'],
    ['icon' => '/wp-content/plugins/pwe-media/media/vip_ulotka.webp', 'text' => 'Dostępu do materiałów targowych dostępnych wyłącznie w strefie VIP'],
    ['icon' => '/wp-content/plugins/pwe-media/media/vip_wifi.webp', 'text' => 'Skorzystania z darmowego WI-FI i strefy ładowania urządzeń'],
];

$courier_logos = [
    'inpost.png', 'dhl.png', 'ups.png', 'pocztex.png',
    'fedex.png', 'poczta-polska.png', 'gls.png', 'dpd.png',
];

$output = '<script src="https://maps.googleapis.com/maps/api/js?key=' . esc_attr(PWE_Functions::get_database_meta_data('api_key_google_places')) . '&libraries=places"></script>';
$output .= '
<div
    id="pweConfirmationVisitorsRegistration"
    class="pwe-confirmation-visitors-registration pwe-confirmation-visitors-registration--byli"
    data-form-id="' . esc_attr($form_id) . '"
>
    <div class="pwe-confirmation-visitors-registration__before-submit">
        <div class="pwe-confirmation-visitors-registration__wrapper pwe-confirmation-visitors-registration__wrapper--byli">
            <div class="pwe-confirmation-visitors-registration__column pwe-confirmation-visitors-registration__badge-column pwe-confirmation-visitors-registration__badge-column--vip">
                <img src="/doc/badgevipmockup.webp" alt="VIP badge">
            </div>

            <div class="pwe-confirmation-visitors-registration__column pwe-confirmation-visitors-registration__form-column pwe-confirmation-visitors-registration__form-column--vip">
                <p class="pwe-confirmation-visitors-registration__step">Krok 2 z 2</p>
                <h2 class="pwe-confirmation-visitors-registration__form-title">Podaj adres, na który mamy wysłać <span class="pwe-confirmation-visitors-registration__accent">darmowy pakiet <strong>VIP</strong></span></h2>

                <div class="pwe-confirmation-visitors-registration__address-form">
                    <div class="pwe-field">
                        <label for="name">Imię i nazwisko</label>
                        <input type="text" id="name" placeholder="Imię i nazwisko">
                    </div>
                    <div class="pwe-field">
                        <label for="street">Ulica</label>
                        <input type="text" id="street" placeholder="Ulica">
                    </div>
                    <div class="pwe-row">
                        <div class="pwe-field">
                            <label for="house">Numer domu</label>
                            <input type="text" id="house" placeholder="Numer domu">
                        </div>
                        <div class="pwe-field">
                            <label for="local">Numer lokalu</label>
                            <input type="text" id="local" placeholder="Numer lokalu">
                        </div>
                    </div>
                    <div class="pwe-row">
                        <div class="pwe-field">
                            <label>Kod pocztowy</label>
                            <div class="pwe-post">
                                <input id="post1" maxlength="2" inputmode="numeric" placeholder="00">
                                <span>-</span>
                                <input id="post2" maxlength="3" inputmode="numeric" placeholder="000">
                            </div>
                        </div>
                        <div class="pwe-field">
                            <label for="city">Miasto</label>
                            <input type="text" id="city" placeholder="Miasto">
                        </div>
                    </div>
                    <div id="statusMessage" class="status-message" aria-live="polite"></div>
                    <div class="pwe-confirmation-visitors-registration__address-form-button">
                        <button type="button" id="pweSendStepTwo" class="pwe-confirmation-visitors-registration__button">Wyślij</button>
                    </div>
                </div>
            </div>

            <div class="pwe-confirmation-visitors-registration__column pwe-confirmation-visitors-registration__benefits">
                <div class="pwe-confirmation-visitors-registration__benefits-inner">
                    <h3>Pakiet VIP upoważnia do:</h3>';
                    $output .= '
                    <div class="pwe-confirmation-visitors-registration__benefits-list">';

                        foreach ($benefits as $benefit) {
                            $output .= '
                            <div class="pwe-confirmation-visitors-registration__benefit">
                                <img src="' . esc_url($benefit['icon']) . '" alt="">
                                <span>' . esc_html($benefit['text']) . '</span>
                            </div>';
                        }

                    $output .= '
                    </div>
                    <p class="pwe-confirmation-visitors-registration__benefits-footer">Zarezerwuj swoje miejsce już dziś i podnieś swoje doświadczenie targowe na wyższy poziom!</p>
                </div>
            </div>
        </div>
    </div>

    <div class="pwe-confirmation-visitors-registration__success pwe-confirmation-visitors-registration__success--vip">
        <div class="pwe-confirmation-visitors-registration__success-card">
            <div class="pwe-confirmation-visitors-registration__success-content">
                <h3>Dziękujemy za rejestrację na targi <span>' . do_shortcode('[trade_fair_name]') . '</span></h3>
                <p><strong>Niebawem dotrze do Państwa przesyłka, w której znajdzie się:</strong></p>
                <ul>
                    <li>Identyfikator VIP upoważniający do wejścia na teren targów i do dedykowanej strefy</li>
                    <li>Zaproszenie do strefy VIP</li>
                    <li>Karta parkingowa upoważniająca do korzystania z darmowego parkingu</li>
                    <li>Szczegółowe informacje o targach i wydarzeniach towarzyszących</li>
                </ul>';

                $output .= '
                <div class="pwe-confirmation-visitors-registration__couriers">';

                    foreach ($courier_logos as $courier_logo) {
                        $output .= '<img src="/wp-content/plugins/pwe-media/media/firmy-kurierskie/' . esc_attr($courier_logo) . '" alt="">';
                    }

                $output .= '
                </div>';

                $output .= '
            </div>

            <div class="pwe-confirmation-visitors-registration__success-divider"></div>

            <div class="pwe-confirmation-visitors-registration__event">
                <img src="/doc/logo-color.webp" alt="' . do_shortcode('[trade_fair_name]') . '">
                <p class="pwe-confirmation-visitors-registration__edition">' . esc_html($trade_fair_edition) . '</p>
                <h2>' . do_shortcode('[trade_fair_date_custom_format]') . '</h2>
                <h4>Ptak Warsaw Expo</h4>
            </div>
        </div>

        <a class="pwe-confirmation-visitors-registration__button pwe-confirmation-visitors-registration__home-button" href="' . home_url('/') . '">Strona główna</a>
    </div>
</div>';

return $output;
