<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('exhibitor_catalog_render_brand_card')) {
  /**
   * Dopisuje do $output JEDNĄ kartę produktu.
   * @param array  $brand   Oczekuje kluczy: name, permalink, img (opcjonalnie)
   * @param array  $exhibitor Opcjonalnie: ['name' => ...]
   * @param string &$output   Referencja do bufora HTML
   */

    function exhibitor_catalog_render_brand_card($item) {

        $brand_name   = $item['brand'] ?? [];
        $exhibitor = $item['exhibitor'] ?? [];

        $exhibitor_stand = $exhibitor['exhibitor_stand_number'];
        $exhibitor_id = $exhibitor['exhibitor_id'];

        $output = '';
        $output .= '
        <div class="exhibitor-catalog__brand-card">
            
                <div class="exhibitor-catalog__brand-card-info">
                
                    <div class="exhibitor-catalog__brand-card-info-stand-contianer">

                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.672 4.094a9.017 9.017 0 0 1 12.627-.03h.002l.032.03c3.545 3.487 3.552 9.088.042 12.54l-5.67 5.578a1 1 0 0 1-1.404 0l-5.67-5.578a8.74 8.74 0 0 1 0-12.499zM12 6.5a3 3 0 1 0 0 6 3 3 0 0 0 0-6" fill="var(--catalog-accent-color)"/></svg>
                        <p class="exhibitor-catalog__brand-card-info-stand">Stoisko</p>
                        <p class="exhibitor-catalog__brand-card-info-stand-number">' . $exhibitor_stand . '</p>

                    </div>

                    <a class="exhibitor-catalog__brand-card-ehibitor-link" href="?exhibitor_id=' . $exhibitor_id . '" target="_blank">Strona wystawcy</span></a>

                </div>

                <div class="exhibitor-catalog__brand-card-text">
                    <p class="exhibitor-catalog__brand-card-headline">Marka</p>
                    <h3 class="exhibitor-catalog__brand-card-title">' . $brand_name . '</h3>
                </div>

        </div>';

    return $output;
  }
}
