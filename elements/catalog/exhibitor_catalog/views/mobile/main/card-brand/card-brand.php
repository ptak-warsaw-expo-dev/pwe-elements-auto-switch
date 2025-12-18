<?php
if (!defined('ABSPATH')) exit;

/* ==========================================
 * Pobranie danych — identycznie jak desktop
 * ========================================== */
$item      = $context['item'] ?? [];
$brand     = $item['data']['brand']     ?? '';
$exhibitor = $item['data']['exhibitor'] ?? [];

/* dane wystawcy */
$exhibitor_stand = $exhibitor['exhibitor_stand_number'] ?? '';
$exhibitor_id    = $exhibitor['exhibitor_id']           ?? '';

/* skracanie nazwy */
$brand_name = $brand;
if (mb_strlen($brand_name) > 27) {
    $brand_name = mb_substr($brand_name, 0, 27) . '...';
}

/* ==========================================
 * RENDER – wersja mobile
 * ========================================== */
$output = '';

$output .= '
<div class="catalog-mobile-brand-card">

    <div class="catalog-mobile-brand-card__text">
        <h3 class="catalog-mobile-brand-card__title">' . $brand_name . '</h3>
    </div>
        
    <div class="catalog-mobile-brand-card__info">
    
        <div class="catalog-mobile-brand-card__stand-container">

            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" 
                    d="M5.672 4.094a9.017 9.017 0 0 1 12.627-.02l.002.02c3.545 3.487 3.552 9.088.042 12.54l-5.67 5.578a1 1 0 0 1-1.404 0l-5.67-5.578a8.74 8.74 0 0 1 0-12.499zM12 6.5a3 3 0 1 0 0 6 3 3 0 0 0 0-6"
                    fill="var(--accent-color)"/>
            </svg>

            <p class="catalog-mobile-brand-card__stand">' . PWECommonFunctions::languageChecker('Stoisko', 'Stand') . ' ' . $exhibitor_stand . '</p>

        </div>

        <a class="catalog-mobile-brand-card__more" 
            href="?exhibitor_id=' . $exhibitor_id . ($_SERVER["HTTP_HOST"] === "warsawexpo.eu" ? "&catalog" : "") . '" 
            target="_blank">
            ' . PWECommonFunctions::languageChecker('Więcej', 'More') . '
        </a>

    </div>

</div>';

echo $output;
