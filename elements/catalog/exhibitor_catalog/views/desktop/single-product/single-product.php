<?php
if (!defined('ABSPATH')) exit;

$single_exhibitor = ec_get_single_exhibitor();
$single_product   = ec_get_single_product();

$name           = $single_exhibitor['name'] ?? '';
$website        = $single_exhibitor['website'] ?? '';
$display_website = preg_replace(['#^https?://#', '#/$#'], '', $website);
$contact_phone  = $single_exhibitor['contact_phone'] ?? '';
$contact_email  = $single_exhibitor['contact_email'] ?? '';
$stand_number   = $single_exhibitor['stand_number'] ?? '';
$product        = $single_product;
$product_name   = $product['name'] ?? '';
$product_img    = $product['img'] ?? '';
$product_desc   = $product['description'] ?? '';
$tags           = $product['tags'] ?? [];

$output .= '
<div id="exhPageProduct" class="exh-product">

    <div class="exh-product-page__header"></div>

    <div class="exh-product__contenet">
        <div class="exh-product__header-container">
            <div class="exh-product__header">
                <div class="exh-product__header-wrapper">
                    <img src="' . $product_img . '" alt="' . $product_name . '" class="exh-product__img">';

                    if (!empty($stand_number)) {
                        $output .= '
                        <div class="exh-product__stand">
                            <span class="exh-product__stand-label">' . PWECommonFunctions::languageChecker('Stoisko', 'Stand') . '</span>
                            <span class="exh-product__stand-number">' . $stand_number . '</span>
                        </div>';
                    }

                $output .= '
                </div>

            </div>';

            if (!empty($website) || !empty($contact_email) || !empty($contact_phone)) {
                $output .= '
                <div class="exh-product__contact">';
                    if (!empty($website)) {
                        $output .= '
                        <div class="exh-product__contact-tile">
                            <a class="exh-product__link" href="' . $website . '" target="_blank">
                                ' . pwe_svg_icon('website') . '
                                ' . $display_website . '
                            </a>
                        </div>';
                    }

                    if (!empty($contact_email)) {
                        $output .= '
                        <div class="exh-product__contact-tile">
                            <a class="exh-product__link" href="mailto:' . $contact_email . '">
                                ' . pwe_svg_icon('email') . '
                                ' . $contact_email . '
                            </a>
                        </div>';
                    }

                    if (!empty($contact_phone)) {
                        $output .= '
                        <div class="exh-product__contact-tile">
                            <a class="exh-product__link" href="tel:' . $contact_phone . '">
                                ' . pwe_svg_icon('phone') . '
                                ' . $contact_phone . '
                            </a>
                        </div>';
                    }

                $output .= '
                </div>';
            }

            $output .= '
            <a href="?exhibitor_id=' . $single_exhibitor['exhibitor_id'] . '" 
                class="exh-product__exh-site">
                ' . PWECommonFunctions::languageChecker('Strona wystawcy', 'Exhibitor page') . '
            </a>

        </div>';
        
        $output .= '
        <div class="exh-product__content-wrapper">

            <div class="exh-product__exhibitor">
                <h4 class="exh-product__exhibitor-name">' . $name . '</h4>
                <h2 class="exh-product__product-title">' . $product_name . '</h2>
            </div>';

            if (!empty($product_desc)) {
                $output .= '
                <div class="exh-product__description">
                    <h3 class="exh-product__description-title">' . PWECommonFunctions::languageChecker('Opis', 'Description') . '</h3>
                    <div class="exh-product__description-text collapsible-text">' . $product_desc . '</div>
                    <button class="exh-product__description-more collapsible-toggle">' . PWECommonFunctions::languageChecker('Pokaż więcej', 'Show more') . '</button>
                </div>';
            }

            if (!empty($tags)) {
                $output .= '
                <div class="exh-product__categories">
                    <h3 class="exh-product__categories-title">' . PWECommonFunctions::languageChecker('Kategorie produktu', 'Product categories') . '</h3>

                    <div class="exhibitor-single-mobile__categories">';

                        foreach ($tags as $tag) {
                            $output .= '
                                <div class="exh-product__category-item">
                                    <span class="exh-product__category">' . $tag . '</span>
                                </div>';
                        }

                        $output .= '
                    </div>
                </div>';
            }

        $output .= '
        </div>
    </div>
</div>';

echo $output;