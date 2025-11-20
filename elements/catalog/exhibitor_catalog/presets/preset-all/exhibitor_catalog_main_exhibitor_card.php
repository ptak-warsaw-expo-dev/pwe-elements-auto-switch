<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('exhibitor_catalog_render_exhibitor_card')) {
  /**
   * Renderuje JEDNĄ kartę wystawcy (bez JS).
   * Oczekuje struktury jak w $exhibitors_prepared.
   */
  function exhibitor_catalog_render_exhibitor_card($exhibitor) {
    // Bezpieczne wartości
    $exhibitor_id                  = (string)($exhibitor['exhibitor_id'] ?? '');
    $exhibitor_name                = $exhibitor['name'] ?? '';
    $exhibitor_logo_url            = $exhibitor['logo_url'] ?? '';
    $exhibitor_desc                = $exhibitor['description'] ?? '';
    $exhibitor_website             = $exhibitor['website'] ?? '';
    $exhibitor_phone               = $exhibitor['contact_phone'] ?? '';
    $exhibitor_email               = $exhibitor['contact_email'] ?? '';

    $exhibitor_brands              = $exhibitor['brands'];
    $exhibitor_catalog_tags        = $exhibitor['catalog_tags'];

    $exhibitor_hall_name           = $exhibitor['hall_name'] ?? '';
    $exhibitor_stand_number        = $exhibitor['stand_number'] ?? '';
    $exhibitor_boothArea           = (float)($exhibitor['boothArea'] ?? 0);
    $exhibitor_products_count      = (int)($exhibitor['products_count'] ?? 0);
    $exhibitor_documents_count     = (int)($exhibitor['documents_count'] ?? 0);
    $exhibitor_products            = $exhibitor['products']  ?? [];
    $exhibitor_documents           = $exhibitor['documents'] ?? [];

    $limit_words = 40;

    $words = explode(' ', strip_tags($exhibitor_desc));

    if (count($words) > $limit_words) {
        $exhibitor_desc = implode(' ', array_slice($words, 0, $limit_words)) . '...';
    }

    $output = '
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
                        <p class="exhibitor-catalog__exh-card-info-stand-number">Stoisko ' . $exhibitor_stand_number . '</p>

                    </div>

                </div>';

                $output .= '
                <div class="exhibitor-catalog__exh-card-info-contanct-contianer">';

                    if (!empty($exhibitor_website)) {
                        $output .= '
                        <a class="exhibitor-catalog__exh-card-info-contanct-single" href="' . $exhibitor_website . '" target="_blank">
                            ' . pwe_svg_icon('website') . '
                            <p class="exhibitor-catalog__exh-card-info-contanct-single-text">Strona Internetowa</p>
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
                            <p class="exhibitor-catalog__exh-card-info-contanct-single-text">Telefon</p>
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
                    <h4 class="exhibitor-catalog__exh-card-title-brands">Marki</h4>
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
                            <a class="exhibitor-catalog__exh-card-brand-single-more" href="?exhibitor_id=' . $exhibitor_id . ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu' ? '&catalog' : '') .'" target="_blank">Pokaż wszystkie <span>(' . $total_brands . ')</span></a>';
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
                            Produkty <span class="exhibitor-catalog__exh-card-files-tab-count"><span class="exhibitor-catalog__exh-card-files-tab-count-number">('.$exhibitor_products_count.')</span></span>
                        </button>';
                    }
                    if (!empty($exhibitor_documents)) {
                        $output .= '
                        <button class="exhibitor-catalog__exh-card-files-tab" data-tab="documents" type="button">
                            Dokumenty do pobrania <span class="exhibitor-catalog__exh-card-files-tab-count"><span class="exhibitor-catalog__exh-card-files-tab-count-number">('.$exhibitor_documents_count.')</span></span>
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
                        <button class="exh-files__next" type="button" aria-label="Następny"></button>
                    </div>';
                }
                if (!empty($exhibitor_documents)) {
                    $output .= '
                    <div class="exhibitor-catalog__exh-card-files-documents swiper">
                        <div class="swiper-wrapper">';
                            foreach ($exhibitor_documents as $exhibitor_document) {
                                $output .= '
                                <div class="exhibitor-catalog__exh-card-files-document swiper-slide">
                                    ' . pwe_svg_icon('modal-open') . '
                                    <p class="exhibitor-catalog__exh-card-files-document-name">
                                    ' . shorten_text($exhibitor_document['title'], 40) . '</p>
                                    <a class="exhibitor-catalog__exh-card-files-document-link" href="' . $exhibitor_document['downloadUrl'] . '">Pobierz</a>
                                </div>';
                            }
                        $output .= '
                        </div>
                        <button class="exh-files__next" type="button" aria-label="Następny"></button>
                    </div>';
                }

            $output .= '
            </div>';
        }

        $output .= '
        <div class="exhibitor-catalog__exh-card-website">
            <a class="exhibitor-catalog__exh-card-website-link" href="?exhibitor_id=' . $exhibitor_id . ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu' ? '&catalog' : '') .'" target="_blank">Strona wystawcy</span></a>
        </div>
    </div>
    <script>
        window["EXHIBITOR_' . $exhibitor_id . '"] = {
        products: ' . json_encode($exhibitor_products) . '
        };
    </script>';

    return $output;
  }
}
