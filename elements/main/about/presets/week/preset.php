<?php

$dir    = $_SERVER['DOCUMENT_ROOT'] . '/doc/galeria/';
$webDir = '/doc/galeria/';

$files = is_dir($dir) ? scandir($dir) : [];
$allImages = array_values(array_filter($files, function ($file) use ($dir) {
    return is_file($dir . $file) && preg_match('/\.(jpe?g|png|webp)$/i', $file);
}));
$allImages = array_map(fn($f) => $webDir . $f, $allImages);

$maxImages = 10;
shuffle($allImages);
$mediaImages = array_slice($allImages, 0, $maxImages);

$video = 'https://mr.glasstec.pl/wp-content/uploads/2025/12/roof-expo-intro-test-video-4.mp4';


$output .= '
<div id="pweAbout" class="pwe-about" data-images=\''. json_encode($mediaImages) .'\'>
    <div class="event-about">

        <div class="event-about__header is-active" data-target="about">
            <span class="icon"></span>
            <span>O wydarzeniu</span>
            <span class="arrow">↑</span>
        </div>

        <div class="event-about__content is-active" id="about">

            <div class="event-about__grid">
                <div class="event-about__left">
                    <span class="event-about__place">Ptak Warsaw Expo</span>
                    <h2>Warszawski Tydzień Home & Contract Week</h2>

                    <p>
                        Warszawski Tydzień Home & Contract Week skupia specjalistyczne targi
                        łączące kluczowe sektory wyposażenia wnętrz oraz rynku home i contract.
                        To wydarzenie, w którym w jednym miejscu spotykają się producenci mebli
                        i wyposażenia, projektanci wnętrz i architekci, firmy fit-out i wykonawcy,
                        deweloperzy, sieci handlowe, inwestorzy, przedstawiciele sektora hotelowego
                        oraz dystrybutorzy i dostawcy rozwiązań dla przestrzeni prywatnych i komercyjnych.
                    </p>

                    <div class="event-about__tags">
                        <span>Meble i wyposażenie wnętrz</span>
                        <span>Design i dekoracje</span>
                        <span>Architekci i projektanci wnętrz</span>
                        <span>Firmy fit-out i wykonawcy</span>
                        <span>Sektor hotelowy (HoReCa)</span>
                        <span>Deweloperzy i inwestorzy</span>
                    </div>
                </div>

                <div class="event-about__right">
                    <div class="image image--big"><img src="'. ($mediaImages[0] ?? '') .'"/></div>
                    <div class="image image--small"><img src="'. ($mediaImages[1] ?? '') .'"/></div>
                    <div class="image image--small"><img src="'. ($mediaImages[2] ?? '') .'"/></div>
                </div>
            </div>

        </div>

        <div class="event-about__header" data-target="why">
            <span class="icon"></span>
            <span>Dlaczego warto</span>
            <span class="arrow">↓</span>
        </div>

        <div class="event-about__content" id="why">

            <div class="event-about__grid">
                <div class="event-about__left">
                    <h2>Dlaczego warto wziąć udział?</h2>

                    <p>
                        To unikalna okazja do nawiązania kontaktów biznesowych, poznania trendów
                        rynkowych oraz zapoznania się z innowacyjnymi rozwiązaniami dla sektora
                        wnętrzarskiego i kontraktowego.
                    </p>

                    <div class="event-about__tags">
                        <span>Networking</span>
                        <span>Nowe trendy</span>
                        <span>Innowacje</span>
                        <span>Biznes</span>
                    </div>
                </div>

                <div class="event-about__right">
                    <div class="image image--big"><img src="'. ($mediaImages[3] ?? '') .'"/></div>
                    <div class="image image--small"><img src="'. ($mediaImages[4] ?? '') .'"/></div>
                    <div class="image image--small"><img src="'. ($mediaImages[5] ?? '') .'"/></div>
                </div>
            </div>

        </div>
    </div>
    <div class="event-about_bottom">
        <div class="event-about_bottom__left">
            <h2>Weź udział w Warszawskim Home & Contract Week i spotkaj liderów rynku w jednym miejscu.</h2>
            <p>Zarejestruj się i bądź częścią najważniejszego wydarzenia branży home & contract w roku.</p>
        </div>
        <div class="event-about_bottom__right">
            <a href="/rejestracja">
                <span>
                    <strong>Zarejestruj się</strong>
                    Odbierz darmowy bilet
                </span>
                <span class="arrow">→</span>
            </a>

        </div>
    </div>

</div>
';


return $output;
