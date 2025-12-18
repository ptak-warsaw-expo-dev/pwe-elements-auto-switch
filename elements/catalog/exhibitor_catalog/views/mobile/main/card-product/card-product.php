<?php
if (!defined('ABSPATH')) exit;

/* ==========================================================
 *  Pobranie danych z kontekstu — IDENTYCZNIE jak w desktop
 * ========================================================== */
$item = $context['item'] ?? [];

$product   = $item['data']['product']   ?? [];
$exhibitor = $item['data']['exhibitor'] ?? [];

/* dane produktu */
$product_name = $product['name']        ?? '';
$product_img  = $product['img']         ?? '';
$product_desc = $product['description'] ?? '';
$product_tags = $product['tags']        ?? [];

/* dane wystawcy */
$exhibitor_name  = $exhibitor['exhibitor_name']        ?? '';
$exhibitor_stand = $exhibitor['exhibitor_stand_number'] ?? '';

/* parametry routingu */
$product_id   = $item['product_id']   ?? '';
$exhibitor_id = $item['exhibitor_id'] ?? '';

/* skracanie nazwy */
if (mb_strlen($product_name) > 27) {
    $product_name = mb_substr($product_name, 0, 27) . '...';
}

/* skracanie opisu */
$limit_words = 30;
$words       = explode(' ', strip_tags($product_desc));

if (count($words) > $limit_words) {
    $product_desc = implode(' ', array_slice($words, 0, $limit_words)) . '...';
}

/* ==========================================================
 *  RENDER — minimalny layout mobile
 * ========================================================== */

$output = '';

$output .= '
<div class="catalog-mobile-product-card">

    <div class="catalog-mobile-product-card__logo-container">
        <img class="catalog-mobile-product-card__logo"
             src="' . $product_img . '"
             alt="' . htmlspecialchars($product_name, ENT_QUOTES, "UTF-8") . '">
    </div>

    <div class="catalog-mobile-product-card__content">

        <div class="catalog-mobile-product-card__title-wrapper">
            <h3 class="catalog-mobile-product-card__title">' . $product_name . '</h3>
        </div>

        <div class="catalog-mobile-product-card__info">

            <div class="catalog-mobile-product-card__stand">
                <p class="catalog-mobile-product-card__stand-text">'
                    . PWECommonFunctions::languageChecker("Stoisko", "Stand") .
                '</p>
                <p class="catalog-mobile-product-card__stand-number">' . $exhibitor_stand . '</p>
            </div>

            <a class="catalog-mobile-product-card__more"
               href="?exhibitor_id=' . $exhibitor_id .
                    '&product_id=' . $product_id .
                    ($_SERVER["HTTP_HOST"] === "warsawexpo.eu" ? "&catalog" : "") . '"
               target="_blank">'
                    . PWECommonFunctions::languageChecker("Więcej", "More") .
            '</a>

        </div>
';

$output .= '
    </div>
</div>';

echo $output;
