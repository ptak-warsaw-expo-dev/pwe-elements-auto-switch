<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('exhibitor_catalog_render_product_card')) {

  function exhibitor_catalog_render_product_card($item) {

    $product   = $item['product'] ?? [];
    $exhibitor = $item['exhibitor'] ?? [];

    $product_name   = $product['name'];
    $product_img    = $product['img'];
    $exhibitor_stand = $exhibitor['exhibitor_stand_number'];
    $exhibitor_id    = $exhibitor['exhibitor_id'];

    if (mb_strlen($product_name) > 22) {
        $product_name = mb_substr($product_name, 0, 27) . '...';
    }

    $output = '';
    $output .= '
    <div class="catalog-mobile-product-card">

        <div class="catalog-mobile-product-card__logo-container">
            <img class="catalog-mobile-product-card__logo" src="' . $product_img . '" alt="' . $product_name . '">
        </div>

        <div class="catalog-mobile-product-card__content">

            <div class="catalog-mobile-product-card__title-wrapper">
                <h3 class="catalog-mobile-product-card__title">' . $product_name . '</h3>
            </div>

            <div class="catalog-mobile-product-card__info">
                <div class="catalog-mobile-product-card__stand">
                    <p class="catalog-mobile-product-card__stand-text">' . PWECommonFunctions::languageChecker('Stoisko', 'Stand') . '</p>
                    <p class="catalog-mobile-product-card__stand-number">' . $exhibitor_stand . '</p>
                </div>

                <a class="catalog-mobile-product-card__more" 
                   href="?exhibitor_id=' . $exhibitor_id . ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu' ? '&catalog' : '') .'"
                   target="_blank">
                    ' . PWECommonFunctions::languageChecker('Więcej', 'More') . '
                </a>
            </div>

        </div>

    </div>';

    return $output;
  }
}
