<?php
if (!defined('ABSPATH')) exit;

$name = $single_exhibitor['name'] ?? '';
$logo = $single_exhibitor['logo_url'] ?? '';
$desc = $single_exhibitor['description'] ?? '';
$why_visit = $single_exhibitor['why_visit'] ?? '';
$website = $single_exhibitor['website'] ?? '';
$contact_phone = $single_exhibitor['contact_phone'] ?? '';
$contact_email = $single_exhibitor['contact_email'] ?? '';

$brands = $single_exhibitor['brands'] ?? '';
$catalog_tags = $single_exhibitor['catalog_tags'] ?? '';
$industries = $single_exhibitor['industries'] ?? '';

$hall_name = $single_exhibitor['hall_name'] ?? '';
$stand_number = $single_exhibitor['stand_number'] ?? '';

$industries = $single_exhibitor['industries'] ?? '';

$products = $single_exhibitor['products'] ?? '';
$documents = $single_exhibitor['documents'] ?? '';

$products_count = count($products);
$documents_count = count($documents);

$display_website = preg_replace(['#^https?://#', '#/$#'], '', $website);


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
        #exhibitorPage .exhibitor-page__header {
            background-image: url(https://warsawexpo.eu/wp-content/uploads/2023/06/background_footer.jpg) !important;
        }
        #exhibitorPage .exhibitor-page__info {
            top: 180px;
        }
    </style>';
}

$output .= '
<div id="exhibitorPage" class="exhibitor-page" data-exhibitor-id="' . $single_exhibitor['exhibitor_id'] . '">

    <div class="exhibitor-page__header">

    </div>

    <div class="exhibitor-page__wrapper">

        <div class="exhibitor-page__info-column">

            <div class="exhibitor-page__info-sticky">
                <div class="exhibitor-page__info">
                    <div class="exhibitor-page__info-header">
                        <img class="exhibitor-page__logo" src="' . $logo . '" alt="Logotyp ' . $name . '">
                        <div class="exhibitor-page__stand">
                            ' . pwe_svg_icon('stand') . '
                            Stoisko ' . $stand_number . '
                        </div>
                    </div>

                    <div class="exhibitor-page__contact">';
                        if (!empty($website)) {
                            $output .= '
                            <div class="exhibitor-page__contact-tile">
                                <a class="exhibitor-page__link" href="' . $website . '" target="_blank">
                                    ' . pwe_svg_icon('website') . '
                                    ' . $display_website . '
                                </a>
                            </div>';
                        }

                        if (!empty($contact_email)) {
                            $output .= '
                            <div class="exhibitor-page__contact-tile">
                                <a class="exhibitor-page__link" href="mailto:' . $contact_email . '">
                                    ' . pwe_svg_icon('email') . '
                                    ' . $contact_email . '
                                </a>
                            </div>';
                        }

                        if (!empty($contact_phone)) {
                            $output .= '
                            <div class="exhibitor-page__contact-tile">
                                <a class="exhibitor-page__link" href="tel:' . $contact_phone . '">
                                    ' . pwe_svg_icon('phone') . '
                                    ' . $contact_phone . '
                                </a>
                            </div>';
                        }

                    $output .= '
                    </div>';

                    if (!empty($social_items)) {
                        $output .= '
                        <div class="exhibitor-page__socials">
                            <div class="exhibitor-page__social-list">';

                                foreach ($social_items as $social) {

                                    $platform = $social['platform'];
                                    $url      = $social['url'];
                                    $icon     = pwe_svg_icon($platform);

                                    $output .= '
                                    <div class="exhibitor-page__social-item">
                                        <a class="exhibitor-single__socials-link" href="'. esc_url($url) .'" target="_blank" rel="noopener noreferrer">' . $icon . '</a>
                                    </div>';

                                }

                            $output .= '
                            </div>
                        </div>';
                    }

                $output .= '
                </div>
            </div>

        </div>

        <div class="exhibitor-page__content">

            <h1 class="exhibitor-page__title">' . $name . '</h1>';

            if (!empty($desc)) {
                $output .= '
                <div class="exhibitor-page__desc">
                    <h2 class="exhibitor-page__subtitle">Opis</h2>
                    <div class="exhibitor-page__description">' . $desc . '</div>
                </div>';
            }

            if (!empty($why_visit)) {
                $output .= '
                <div class="exhibitor-page__benefits">
                    <h2 class="exhibitor-page__subtitle">Dlaczego warto odwiedzić nasze stoisko</h2>
                    <div class="exhibitor-page__description">' . $why_visit . '</div>
                </div>';
            }

            if (!empty($brands)) {
                $output .= '
                <div class="exhibitor-page__brands swiper">
                    <h2 class="exhibitor-page__subtitle">Marki, które reprezentujemy</h2>
                    <div class="exhibitor-page__brand-list swiper-wrapper">';
                        foreach ($brands as $brand) {
                            $output .= '
                            <div class="exhibitor-page__brand-item swiper-slide">' . $brand . '</div>';
                        }
                    $output .= '
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>';
            }

            if (!empty($catalog_tags)) {
                $output .= '
                <div class="exhibitor-page__technologies">
                    <h2 class="exhibitor-page__subtitle">Sektory Branżowe</h2>
                    <div class="exhibitor-page__technology-list">';
                        foreach ($catalog_tags as $catalog_tag) {
                            $output .= '
                            <div class="exhibitor-page__technology-item">' . $catalog_tag . '</div>';
                        }
                    $output .= '
                    </div>
                </div>';
            }  

            if (!empty(array_filter(array_column($products, 'tags')))) {
                $output .= '
                <div class="exhibitor-page__products-category">
                    <h2 class="exhibitor-page__subtitle">Kategorie produktów</h2>
                    <div class="exhibitor-page__products-category-list">';
                        foreach ($products as $product) {
                            $product_category = $product['tags'];
                            foreach ($product_category as $single_category) {
                                $output .= '
                                <div class="exhibitor-page__products-category-item">' . $single_category . '</div>';
                            }
                        }
                    $output .= '
                    </div>
                </div>';
            }


            // === PAGINACJA PRODUKTÓW ===
            $per_page_products = 12;
            $current_product_page = isset($_GET['product-page']) ? max(1, (int)$_GET['product-page']) : 1;
            $total_products = count($products);
            $total_product_pages = max(1, ceil($total_products / $per_page_products));
            $product_offset = ($current_product_page - 1) * $per_page_products;
            $page_products = array_slice($products, $product_offset, $per_page_products);

            // === PAGINACJA DOKUMENTÓW ===
            $per_page_docs = 12;
            $current_doc_page = isset($_GET['document-page']) ? max(1, (int)$_GET['document-page']) : 1;
            $total_docs = count($documents);
            $total_doc_pages = max(1, ceil($total_docs / $per_page_docs));
            $doc_offset = ($current_doc_page - 1) * $per_page_docs;
            $page_docs = array_slice($documents, $doc_offset, $per_page_docs);

            function exhibitor_single_build_url($param, $value) {
                $params = $_GET;
                $params[$param] = max(1, (int)$value);
                $base = strtok($_SERVER['REQUEST_URI'], '?');
                return esc_url($base . '?' . http_build_query($params));
            }


            $output .= '
            <div class="exhibitor-page__products-tabs">

                <div class="exhibitor-page__tabs-nav">';
                    if (!empty($products)) {
                        $output .= '
                        <button class="exhibitor-page__tab-btn" data-tab="products">Produkty <span class="exhibitor-page__tab-total">(' . $total_products . ')</span></button>';
                    }
                    if (!empty($documents)) {
                        $output .= '
                        <button class="exhibitor-page__tab-btn" data-tab="documents">Dokumenty <span class="exhibitor-page__tab-total">(' . $total_docs . ')</span></button>';
                    }
                $output .= '
                </div>';

                if (!empty($products)) {
                    $output .= '
                    <div class="exhibitor-page__tab-content" id="tab-products">
                        <div class="exhibitor-page__product-list">';

                            if (!empty($page_products)) {
                                foreach ($page_products as $product) {
                                    $output .= '
                                    <div class="exhibitor-page__product exhibitor-catalog__product-modal">
                                        ' . pwe_svg_icon('modal-open') . '
                                        <img class="exhibitor-page__product-image" src="' . esc_url($product['img']) . '" alt="' . esc_attr($product['name']) . '">
                                        <div class="exhibitor-page__product-name">' . esc_html($product['name']) . '</div>
                                    </div>';
                                }
                            }

                        $output .= '
                        </div>';

                        if ($total_product_pages > 1) {
                            $prev_url = ($current_product_page > 1) ? exhibitor_single_build_url("product-page", $current_product_page - 1) : "";
                            $next_url = ($current_product_page < $total_product_pages) ? exhibitor_single_build_url("product-page", $current_product_page + 1) : "";

                            $output .= '
                            <nav class="ec-pager exhibitor-catalog__pagination" aria-label="Stronicowanie produktów">
                                ' . (
                                    $prev_url
                                    ? '<a class="ec-pager__btn ec-pager__btn--prev" href="'.$prev_url.'" rel="prev" aria-label="Poprzednia strona">'
                                    : '<span class="ec-pager__btn ec-pager__btn--prev is-disabled" aria-disabled="true">'
                                ) . '
                                    <svg class="ec-pager__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M15.5 19l-7-7 7-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                ' . ($prev_url ? '</a>' : '</span>') . '

                                <div class="ec-pager__status" aria-live="polite">
                                    <span class="ec-pager__current">' . $current_product_page . '</span>
                                    <span class="ec-pager__sep">/</span>
                                    <span class="ec-pager__total">' . $total_product_pages . '</span>
                                </div>

                                ' . (
                                    $next_url
                                    ? '<a class="ec-pager__btn ec-pager__btn--next" href="'.$next_url.'" rel="next" aria-label="Następna strona">'
                                    : '<span class="ec-pager__btn ec-pager__btn--next is-disabled" aria-disabled="true">'
                                ) . '
                                    <svg class="ec-pager__icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.5 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                ' . ($next_url ? '</a>' : '</span>') . '
                            </nav>';
                        }

                    $output .= '
                    </div>';
                }

                if (!empty($documents)) {
                    $output .= '
                    <div class="exhibitor-page__tab-content" id="tab-documents">
                        <div class="exhibitor-page__document-list">';

                            if (!empty($page_docs)) {
                                foreach ($page_docs as $doc) {
                                    $title = $doc["title"] ?? basename($doc["file_url"]);
                                    $doc_view = $doc["viewUrl"];
                                    $output .= '
                                    <div class="exhibitor-page__document">
                                        <a href="' . $doc_view . '" target="_blank" class="exhibitor-page__document-link">
                                            ' . pwe_svg_icon('pdf') . '
                                            ' . esc_html($title) . '
                                        </a>
                                    </div>';
                                }
                            }

                        $output .= '
                        </div>';

                        if ($total_doc_pages > 1) {
                            $prev_url = ($current_doc_page > 1) ? exhibitor_single_build_url("document-page", $current_doc_page - 1) : "";
                            $next_url = ($current_doc_page < $total_doc_pages) ? exhibitor_single_build_url("document-page", $current_doc_page + 1) : "";

                            $output .= '
                            <nav class="ec-pager exhibitor-catalog__pagination" aria-label="Stronicowanie dokumentów">
                                ' . (
                                    $prev_url
                                    ? '<a class="ec-pager__btn ec-pager__btn--prev" href="'.$prev_url.'" rel="prev">'
                                    : '<span class="ec-pager__btn ec-pager__btn--prev is-disabled">'
                                ) . '
                                    <svg class="ec-pager__icon" viewBox="0 0 24 24"><path d="M15.5 19l-7-7 7-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                ' . ($prev_url ? '</a>' : '</span>') . '

                                <div class="ec-pager__status">
                                    <span class="ec-pager__current">' . $current_doc_page . '</span>
                                    <span class="ec-pager__sep">/</span>
                                    <span class="ec-pager__total">' . $total_doc_pages . '</span>
                                </div>

                                ' . (
                                    $next_url
                                    ? '<a class="ec-pager__btn ec-pager__btn--next" href="'.$next_url.'" rel="next">'
                                    : '<span class="ec-pager__btn ec-pager__btn--next is-disabled">'
                                ) . '
                                    <svg class="ec-pager__icon" viewBox="0 0 24 24"><path d="M8.5 5l7 7-7 7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                ' . ($next_url ? '</a>' : '</span>') . '
                            </nav>';
                        }

                    $output .= '
                    </div>';
                }
            $output .= '
            </div>

        </div>

    </div>

</div>

<script>
window["EXHIBITOR_' . $single_exhibitor["exhibitor_id"] . '"] = {
  products: ' . json_encode($single_exhibitor["products"]) . '
};
</script>';

echo $output;