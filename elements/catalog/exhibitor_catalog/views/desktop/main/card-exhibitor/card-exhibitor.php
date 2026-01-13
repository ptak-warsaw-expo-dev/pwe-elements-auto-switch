<?php
if (!defined('ABSPATH')) exit;

$ex = $context['ex'] ?? [];

$exhibitor_id              = (string)($ex['exhibitor_id'] ?? '');
$exhibitor_name            = $ex['name'] ?? '';
$exhibitor_logo_url        = $ex['logo_url'] ?? '';
$exhibitor_desc            = $ex['description'] ?? '';
$exhibitor_website         = $ex['website'] ?? '';
$exhibitor_phone           = $ex['contact_phone'] ?? '';
$exhibitor_email           = $ex['contact_email'] ?? '';

$exhibitor_brands          = $ex['brands'] ?? [];
$exhibitor_catalog_tags    = $ex['catalog_tags'] ?? [];

$exhibitor_hall_name       = $ex['hall_name'] ?? '';
$exhibitor_stand_number    = $ex['stand_number'] ?? '';
$exhibitor_products_count  = (int)($ex['products_count'] ?? 0);
$exhibitor_documents_count = (int)($ex['documents_count'] ?? 0);

$exhibitor_products        = $ex['products']  ?? [];
$exhibitor_documents       = $ex['documents'] ?? [];

// skrócenie opisu
$limit_words = 40;
$words = explode(' ', strip_tags($exhibitor_desc));
if (count($words) > $limit_words) {
    $exhibitor_desc = implode(' ', array_slice($words, 0, $limit_words)) . '...';
}

$output = '';

$output .= '
    <div class="exhibitor-catalog__exh-card" data-exhibitor-id="' . $exhibitor_id . '">

        <div class="exhibitor-catalog__exh-card-content">
        
            <div class="exhibitor-catalog__exh-card-info">
            
                <div class="exhibitor-catalog__exh-card-info-logo-contianer">';
                
                    if (!empty($exhibitor_logo_url)) {
                        $output .= '
                        <img class="exhibitor-catalog__exh-card-info-logo" src="' . $exhibitor_logo_url . '" alt="' . htmlspecialchars($exhibitor_name) . '">';
                    }

                    $output .= '
                    <div class="exhibitor-catalog__exh-card-info-stand">
                    
                        ' . pwe_svg_icon('stand') . '
                        <p class="exhibitor-catalog__exh-card-info-stand-number">' . PWECommonFunctions::languageChecker('Stoisko', 'Stand') . ' ' . $exhibitor_stand_number . '</p>

                    </div>

                </div>';

                $output .= '
                <div class="exhibitor-catalog__exh-card-info-contanct-contianer">';

                    if (!empty($exhibitor_website)) {
                        $output .= '
                        <a class="exhibitor-catalog__exh-card-info-contanct-single" href="' . esc_html($exhibitor_website) . '" target="_blank">
                            ' . pwe_svg_icon('website') . '
                            <p class="exhibitor-catalog__exh-card-info-contanct-single-text">' . PWECommonFunctions::languageChecker('Strona Internetowa', 'Website') . '</p>
                        </a>';
                    }

                    if (!empty($exhibitor_email)) {
                        $output .= '
                        <a class="exhibitor-catalog__exh-card-info-contanct-single" href="mailto:' . $exhibitor_email . '">
                            ' . pwe_svg_icon('email') . '
                            <p class="exhibitor-catalog__exh-card-info-contanct-single-text">e–mail</p>
                        </a>';
                    }

                    if (!empty($exhibitor_phone)) {
                        $output .= '
                        <a class="exhibitor-catalog__exh-card-info-contanct-single" href="tel:' . $exhibitor_phone . '">
                            ' . pwe_svg_icon('phone') . '
                            <p class="exhibitor-catalog__exh-card-info-contanct-single-text">' . PWECommonFunctions::languageChecker('Telefon', 'Phone') . '</p>
                        </a>';
                    }

                $output .= '
                </div>';

            $output .= '
            </div>

            <div class="exhibitor-catalog__exh-card-text">
                <h3 class="exhibitor-catalog__exh-card-title">' . $exhibitor_name . '</h3>
                <p class="exhibitor-catalog__exh-card-desc">' . $exhibitor_desc . '</p>';

                if (!empty($exhibitor_brands)) {
                    $output .= '
                    <h4 class="exhibitor-catalog__exh-card-title-brands">' . PWECommonFunctions::languageChecker('Marki', 'Brands') . '</h4>
                    <div class="exhibitor-catalog__exh-card-brand-container">';

                        $total_brands = count($exhibitor_brands);
                        $brands_limit = 5;
                        

                        foreach ($exhibitor_brands as $index => $exhibitor_brand) {
                            if ($index >= $brands_limit) break;
                            $output .= '
                            <p class="exhibitor-catalog__exh-card-brand-single">' . $exhibitor_brand . '</p>';
                        }

                        if ($total_brands > $brands_limit) {
                            $output .= '
                            <a class="exhibitor-catalog__exh-card-brand-single-more" href="?exhibitor_id=' . $exhibitor_id . ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu' ? '&catalog' : '') .'" target="_blank">' . PWECommonFunctions::languageChecker('Pokaż wszystkie', 'Show all') . ' <span>(' . $total_brands . ')</span></a>';
                        }

                    $output .= '
                    </div>';
                }
            $output .= '
            </div>


        </div>';

        if ((!empty($exhibitor_products) || !empty($exhibitor_documents))) {
            $output .= '
            <div class="exhibitor-catalog__exh-card-files">
                <div class="exhibitor-catalog__exh-card-files-nav">';
                    if (!empty($exhibitor_products)) {
                        $output .= '
                        <button class="exhibitor-catalog__exh-card-files-tab is-active" data-tab="products" type="button">
                            ' . PWECommonFunctions::languageChecker('Produkty', 'Products') . ' <span class="exhibitor-catalog__exh-card-files-tab-count"><span class="exhibitor-catalog__exh-card-files-tab-count-number">('.$exhibitor_products_count.')</span></span>
                        </button>';
                    }
                    if (!empty($exhibitor_documents)) {
                        $output .= '
                        <button class="exhibitor-catalog__exh-card-files-tab" data-tab="documents" type="button">
                            ' . PWECommonFunctions::languageChecker('Dokumenty do pobrania', 'Documents to download') . ' <span class="exhibitor-catalog__exh-card-files-tab-count"><span class="exhibitor-catalog__exh-card-files-tab-count-number">('.$exhibitor_documents_count.')</span></span>
                        </button>';
                    }
                $output .= '
                </div>';
                if (!empty($exhibitor_products)) {
                    $output .= '
                    <div class="exhibitor-catalog__exh-card-files-products swiper" style="opacity:0; visibility:hidden; height:162px;">
                        <div class="swiper-wrapper">';
                            foreach ($exhibitor_products as $exhibitor_product) {

                                $output .= '
                                <div class="exhibitor-catalog__exh-card-files-product swiper-slide exhibitor-catalog__product-modal">
                                    ' . pwe_svg_icon('modal-open') . '
                                    <img src="' . $exhibitor_product['img'] . '"alt="' . $exhibitor_product['name'] . '">
                                </div>';
                            }
                        $output .= '
                        </div>
                        <button class="exh-files__next" type="button" aria-label="' . PWECommonFunctions::languageChecker('Następny', 'Next') . '"></button>
                    </div>';
                }
                if (!empty($exhibitor_documents)) {
                    $output .= '
                    <div class="exhibitor-catalog__exh-card-files-documents swiper">
                        <div class="swiper-wrapper">';
                            foreach ($exhibitor_documents as $exhibitor_document) {
                                $output .= '
                                <div class="exhibitor-catalog__exh-card-files-document swiper-slide">
                                    ' . pwe_svg_icon('pdf') . '
                                    <p class="exhibitor-catalog__exh-card-files-document-name">
                                    ' . ec_limit_labels($exhibitor_document['title'], 5, 38) . '</p>
                                    <a class="exhibitor-catalog__exh-card-files-document-link" href="' . $exhibitor_document['viewUrl'] . '" target="_blank">' . PWECommonFunctions::languageChecker('Zobacz', 'Check it out') . '</a>
                                </div>';
                            }
                        $output .= '
                        </div>
                        <button class="exh-files__next" type="button" aria-label="' . PWECommonFunctions::languageChecker('Następny', 'Next') . '"></button>
                    </div>';
                }

            $output .= '
            </div>';
        }

        $output .= '
        <div class="exhibitor-catalog__exh-card-website">
            <a class="exhibitor-catalog__exh-card-website-link" href="?exhibitor_id=' . $exhibitor_id . ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu' ? '&catalog' : '') .'" target="_blank">' . PWECommonFunctions::languageChecker('Strona wystawcy', 'Exhibitor page') . '</span></a>
        </div>
        <script>
            window["EXHIBITOR_' . $exhibitor_id . '"] = {
            products: ' . json_encode($exhibitor_products) . '
            };
        </script>
    </div>';

echo $output;
