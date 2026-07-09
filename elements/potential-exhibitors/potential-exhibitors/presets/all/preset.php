<?php
$output .= '
<div id="pwePotentialExhibitors" class="pwe-potential-exhibitors">

    <div class="pwe-potential-exhibitors__wrapper">
        <div class="pwe-potential-exhibitors__columns">
            <div class="pwe-potential-exhibitors__column">
                <div class="pwe-potential-exhibitors__column-content">
                    <h2>Dziękujemy za aktywację zaproszenia VIP</h2>
                    <p>Na państwa mail wysłaliśmy potwierdzenie wraz z kodem QR upoważniającym do wejścia na targi</p>
                    <div class="pwe-potential-exhibitors__column-content-btn-container"><a href="/">Strona główna</a></div>
                </div>
            </div>
            <div class="pwe-potential-exhibitors__column bg">
                <div class="pwe-potential-exhibitors__column-content">
                    <img class="pwe-potential-exhibitors__column-content-logo" src="/doc/logo.webp">';
                    if (!empty($trade_fair_edition_shortcode)) {
                        $output .= '<p class="pwe-potential-exhibitors__column-content-edition"><span>'. $trade_fair_edition .'</span></p>';
                    } $output .= '
                    <h3 class="pwe-potential-exhibitors__column-content-date">'. $actually_date .'</h3>
                </div>
            </div>
        </div>
        <div class="pwe-potential-exhibitors__footer">
            <div class="pwe-potential-exhibitors__footer-wrapper">
                <div class="pwe-potential-exhibitors__footer-column">
                    <p>PTAK WARSAW EXPO</p>
                    <p>AL. KATOWICKA 62, 05-830 NADARZYN</p>
                </div>
                <div class="pwe-potential-exhibitors__footer-column">
                    <p>INFO@WARSAWEXPO.EU</p>
                    <p>ŚLEDŹ NAS NA <a href="[trade_fair_facebook]" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z"/></svg></a></p>
                </div>
            </div>
        </div>
    </div>
    <div class="pwe-potential-exhibitors__form">
        ' . do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="true"]') . '
    </div>

</div>';

return $output;