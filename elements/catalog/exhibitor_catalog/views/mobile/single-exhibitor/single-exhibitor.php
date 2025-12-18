<?php
if (!defined('ABSPATH')) exit;

$single_exhibitor = ec_get_single_exhibitor();

if (!$single_exhibitor) {
    echo "<p>Nie znaleziono wystawcy.</p>";
    return;
}

$name           = $single_exhibitor['name'] ?? '';
$logo           = $single_exhibitor['logo_url'] ?? '';
$desc           = $single_exhibitor['description'] ?? '';
$why_visit      = $single_exhibitor['why_visit'] ?? '';
$website        = $single_exhibitor['website'] ?? '';
$contact_phone  = $single_exhibitor['contact_phone'] ?? '';
$contact_email  = $single_exhibitor['contact_email'] ?? '';

$brands         = $single_exhibitor['brands'] ?? '';
$catalog_tags   = $single_exhibitor['catalog_tags'] ?? '';
$industries     = $single_exhibitor['industries'] ?? '';

$hall_name      = $single_exhibitor['hall_name'] ?? '';
$stand_number   = $single_exhibitor['stand_number'] ?? '';

$products       = $single_exhibitor['products'] ?? '';
$documents      = $single_exhibitor['documents'] ?? '';

$products_count   = count($products);
$documents_count  = count($documents);

function pwe_normalize_url($url) {
    $url = trim((string)$url);
    if ($url === '') return '';
    if (!preg_match('~^https?://~i', $url)) {
        $url = 'https://' . ltrim($url, '/');
    }
    return $url;
}

$platform_fields = ['facebook','instagram','linkedin','youtube','tiktok','x'];
$social_items = [];

foreach ($platform_fields as $platform) {
    $val = $single_exhibitor[$platform] ?? '';
    $val = pwe_normalize_url($val);
    if ($val !== '') {
        $social_items[] = [
            'platform' => $platform,
            'url'      => $val,
        ];
    }
}

if ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu') {
    $output .= '
    <style>
        .single-event__catalog-header, 
        .single-event__exhibitors-fairs {
            display: none !important;
        }
        #exhibitorPage .exhibitor-single-mobile__header {
            background-image: url(https://warsawexpo.eu/wp-content/uploads/2023/06/background_footer.jpg) !important;
        }
        #exhibitorPage .exhibitor-single-mobile__info {
            top: 180px;
        }
    </style>';
}

$output .= '
<div id="exhibitorPage" class="exhibitor-single-mobile" data-exhibitor-id="' . $single_exhibitor['exhibitor_id'] . '">

    <div class="exhibitor-single-mobile__header">
    </div>

    <div class="exhibitor-single-mobile__wrapper">

        <div class="exhibitor-single-mobile__info">

            <img class="exhibitor-single-mobile__logo" src="' . $logo . '" alt="Logotyp ' . $name . '">

            <div class="exhibitor-single-mobile__stand">
                ' . pwe_svg_icon('stand') . '
                ' . PWECommonFunctions::languageChecker('Stoisko', 'Stand') . ' ' . $stand_number . '
            </div>

        </div>
        <div class="exhibitor-single-mobile__content">

            <h1 class="exhibitor-single-mobile__title">' . $name . '</h1>';

            if (!empty($desc)) {
                $output .= '
                <div class="exhibitor-single-mobile__section">
                    <h2 class="exhibitor-single-mobile__section-title">' . PWECommonFunctions::languageChecker('Opis', 'Description') . '</h2>
                    <div class="exhibitor-single-mobile__section-body collapsible-text">' . $desc . '</div>
                    <button class="collapsible-toggle">' . PWECommonFunctions::languageChecker('Pokaż więcej', 'Show more') . '</button>
                </div>';
            }

            if (!empty($why_visit)) {
                $output .= '
                <div class="exhibitor-single-mobile__section">
                    <h2 class="exhibitor-single-mobile__section-title">' . PWECommonFunctions::languageChecker('Dlaczego warto odwiedzić nasze stoisko', 'Why you should visit our booth') . '</h2>
                    <div class="exhibitor-single-mobile__section-body collapsible-text">' . $why_visit . '</div>
                    <button class="collapsible-toggle">' . PWECommonFunctions::languageChecker('Pokaż więcej', 'Show more') . '</button>
                </div>';
            }

            if (!empty($social_items)) {
                $output .= '
                <div class="exhibitor-single-mobile__socials">
                    <h2 class="exhibitor-single-mobile__section-title">Social Media</h2>
                    <div class="exhibitor-single-mobile__socials-list">';
                        foreach ($social_items as $social) {
                            $platform = $social['platform'];
                            $url      = $social['url'];
                            $icon     = pwe_svg_icon($platform);

                            $output .= '
                            <div class="exhibitor-single-mobile__socials-item">
                                <a class="exhibitor-single-mobile__socials-link" href="'. esc_url($url) .'" target="_blank" rel="noopener noreferrer">' . $icon . '</a>
                            </div>';
                        }
                $output .= '
                    </div>
                </div>';
            }

            if (!empty($brands)) {
                $output .= '
                <div class="exhibitor-single-mobile__brands swiper">
                    <h2 class="exhibitor-single-mobile__section-title">' . PWECommonFunctions::languageChecker('Marki, które reprezentujemy', 'Brands we represent') . '</h2>
                    <div class="exhibitor-single-mobile__brands-list swiper-wrapper">';
                        foreach ($brands as $brand) {
                            $output .= '
                            <div class="exhibitor-single-mobile__brands-item swiper-slide">' . $brand . '</div>';
                        }
                $output .= '
                    </div>
                </div>';
            }

            if (!empty($catalog_tags)) {
                $output .= '
                <div class="exhibitor-single-mobile__industries swiper">
                    <h2 class="exhibitor-single-mobile__section-title">' . PWECommonFunctions::languageChecker('Sektory Branżowe', 'Industry Sectors') . '</h2>
                    <div class="exhibitor-single-mobile__industries-list swiper-wrapper">';
                        foreach ($catalog_tags as $catalog_tag) {
                            $output .= '
                            <div class="exhibitor-single-mobile__industries-item swiper-slide">' . $catalog_tag . '</div>';
                        }
                $output .= '
                    </div>
                </div>';
            }

            if (!empty(array_filter(array_column($products, 'tags')))) {
                $output .= '
                <div class="exhibitor-single-mobile__categories swiper">
                    <h2 class="exhibitor-single-mobile__section-title">' . PWECommonFunctions::languageChecker('Kategorie produktów', 'Product categories') . '</h2>
                    <div class="exhibitor-single-mobile__categories-list swiper-wrapper">';
                        foreach ($products as $product) {
                            foreach ($product['tags'] as $single_category) {
                                $output .= '
                                <div class="exhibitor-single-mobile__categories-item swiper-slide">' . $single_category . '</div>';
                            }
                        }
                $output .= '
                    </div>
                </div>';
            }

            if (!empty($products)) {
                $output .= '
                <div class="exhibitor-single-mobile__products swiper">
                    <h2 class="exhibitor-single-mobile__products-title">
                        ' . PWECommonFunctions::languageChecker('Produkty', 'Products') . '
                        <span class="exhibitor-single-mobile__products-count">('. count($products) . ')</span>
                    </h2>
                    <div class="exhibitor-single-mobile__products-list swiper-wrapper">';
                        foreach ($products as $product) {

                            $product_name = $product['name'];
                            $product_img = $product['img'];

                            $output .= '      
                            <div class="exhibitor-single-mobile__product-item swiper-slide">
                                ' . pwe_svg_icon('modal-open') . '
                                <img class="exhibitor-single-mobile__product-img" src="' . $product_img . '" alt="' . $product_name . '">
                                <h4 class="exhibitor-single-mobile__product-title">' . $product_name . '</h4>
                            </div>';

                        }
                $output .= '
                    </div>
                </div>';
            }

            if (!empty($documents)) {
                $output .= '
                <div class="exhibitor-single-mobile__documents swiper">
                    <h2 class="exhibitor-single-mobile__documents-title">
                        ' . PWECommonFunctions::languageChecker('Dokumenty', 'Documents') . '
                        <span class="exhibitor-single-mobile__documents-count">('. count($products) . ')</span>
                    </h2>
                    <div class="exhibitor-single-mobile__documents-list swiper-wrapper">';
                        foreach ($documents as $document) {

                            $document_title = $document['title'];
                            $document_view = $document['viewUrl'];

                            $output .= '      
                            <div class="exhibitor-single-mobile__document-item swiper-slide">
                                ' . pwe_svg_icon('pdf') . '
                                <h4 class="exhibitor-single-mobile__document-title">' . $document_title . '</h4>
                                <a class="exhibitor-single-mobile__document-link" href="' . $document_view . '" target="_blank">
                                    ' . PWECommonFunctions::languageChecker('Więcej', 'More') . '
                                </a>
                            </div>';

                        }
                    $output .= '
                    </div>
                </div>';
            }

        $output .= '
        </div>
    </div>

    <div class="exhibitor-single-mobile__contact">';

        if (!empty($website)) {
            $output .= '
            <div class="exhibitor-single-mobile__contact-item">
                <a class="exhibitor-single-mobile__contact-link" href="' . $website . '" target="_blank">
                    ' . pwe_svg_icon('website') . '
                    www
                </a>
            </div>';
        }

        if (!empty($contact_email)) {
            $output .= '
            <div class="exhibitor-single-mobile__contact-item">
                <a class="exhibitor-single-mobile__contact-link" href="mailto:' . $contact_email . '">
                    ' . pwe_svg_icon('email') . '
                    e-mail
                </a>
            </div>';
        }

        if (!empty($contact_phone)) {
            $output .= '
            <div class="exhibitor-single-mobile__contact-item">
                <a class="exhibitor-single-mobile__contact-link" href="tel:' . $contact_phone . '">
                    ' . pwe_svg_icon('phone') . '
                    ' . PWECommonFunctions::languageChecker('Telefon', 'Phone') . '
                </a>
            </div>';
        }

    $output .= '
    </div>
</div>

<script>
window["EXHIBITOR_' . $single_exhibitor["exhibitor_id"] . '"] = {
  products: ' . json_encode($single_exhibitor["products"]) . '
};
</script>';

echo $output;