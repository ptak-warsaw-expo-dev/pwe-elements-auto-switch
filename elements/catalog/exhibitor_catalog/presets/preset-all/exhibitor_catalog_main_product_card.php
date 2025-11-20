<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('exhibitor_catalog_render_product_card')) {
  /**
   * Dopisuje do $output JEDNĄ kartę produktu.
   * @param array  $product   Oczekuje kluczy: name, permalink, img (opcjonalnie)
   * @param array  $exhibitor Opcjonalnie: ['name' => ...]
   * @param string &$output   Referencja do bufora HTML
   */
  function exhibitor_catalog_render_product_card($item) {

    $product   = $item['product'] ?? [];
    $exhibitor = $item['exhibitor'] ?? [];

    $product_name = $product['name'];
    $product_img = $product['img'];
    $product_desc = $product['description'];
    $product_tags = $product['tags'];

    $exhibitor_name = $exhibitor['exhibitor_name'];
    $exhibitor_stand = $exhibitor['exhibitor_stand_number'];
    $exhibitor_id = $exhibitor['exhibitor_id'];

    $limit_words = 40; // maksymalna liczba słów
    $words = explode(' ', strip_tags($product_desc));

    if (count($words) > $limit_words) {
        $product_desc = implode(' ', array_slice($words, 0, $limit_words)) . '...';
    }

    $output = '';
    $output .= '
    <div class="exhibitor-catalog__product-card">
        
            <div class="exhibitor-catalog__product-card-info">
            
                <div class="exhibitor-catalog__product-card-info-logo-contianer">
                
                    <img class="exhibitor-catalog__product-card-info-logo" src="' . $product_img . '" alt="' . $product_name . '">
                    <p class="exhibitor-catalog__product-card-info-stand">' . PWECommonFunctions::languageChecker('Stoisko', 'Stand') . '</p>
                    <p class="exhibitor-catalog__product-card-info-stand-number">' . $exhibitor_stand . '</p>

                </div>

                <a class="exhibitor-catalog__product-card-ehibitor-link" href="?exhibitor_id=' . $exhibitor_id . ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu' ? '&catalog' : '') .'" target="_blank">' . PWECommonFunctions::languageChecker('Strona wystawcy', 'Exhibitor website') . '</span></a>

            </div>

            <div class="exhibitor-catalog__product-card-text">
                <h3 class="exhibitor-catalog__product-card-title">' . $product_name . '</h3>
                <h4 class="exhibitor-catalog__product-card-subtitle">' . $exhibitor_name . '</h4>
                <p class="exhibitor-catalog__product-card-desc">' . $product_desc . '</p>';
                if (!empty($product_tags)) {
                    $output .='
                    <h4 class="exhibitor-catalog__product-card-subtitle">' . PWECommonFunctions::languageChecker('Kategorie produktów', 'Product categories') . '</h4>
                    <div class="exhibitor-catalog__product-card-category-container">';


                            $product_tags_total = count($product_tags);
                            $product_tags_limit = 5;
                            

                            foreach ($product_tags as $index => $product_tag) {
                                if ($index >= $product_tags_limit) break;
                                $output .= '
                                <p class="exhibitor-catalog__product-card-category-single">' . $product_tag . '</p>';
                            }

                            if ($product_tags_total > $product_tags_limit) {
                                $output .= '
                                <a class="exhibitor-catalog__product-card-category-single-more" href="?exhibitor_id=' . $exhibitor_id . ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu' ? '&catalog' : '') .'" target="_blank">' . PWECommonFunctions::languageChecker('Pokaż wszystkie', 'Show all') . ' <span>(' . $product_tags_total . ')</span></a>';
                            }

                    $output .= '
                    </div>';
                }
            $output .= '
            </div>

    </div>';

    return $output;
  }
}
