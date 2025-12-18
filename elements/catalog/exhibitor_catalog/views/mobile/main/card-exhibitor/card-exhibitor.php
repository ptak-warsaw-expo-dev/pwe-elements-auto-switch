<?php
if (!defined('ABSPATH')) exit;

// dane przychodzą z main.php jako ['ex' => $item['data']]
$ex = $context['ex'] ?? [];

// mapowanie pól – takie same jak w desktopowej karcie
$exhibitor_id           = (string)($ex['exhibitor_id'] ?? '');
$exhibitor_name         = $ex['name'] ?? '';
$exhibitor_logo_url     = $ex['logo_url'] ?? '';
$exhibitor_phone        = $ex['contact_phone'] ?? '';
$exhibitor_email        = $ex['contact_email'] ?? '';
$exhibitor_stand_number = $ex['stand_number'] ?? '';

$exhibitor_name = ec_limit_labels($exhibitor_name, 6, 27);

$output = '
<div class="catalog-mobile-exh-card" data-exhibitor-id="' . $exhibitor_id . '">

    <div class="catalog-mobile-exh-card__content">
    
        <div class="catalog-mobile-exh-card__info">
        
            <div class="catalog-mobile-exh-card__logo-container">';

                if (!empty($exhibitor_logo_url)) {
                    $output .= '
                    <img class="catalog-mobile-exh-card__logo" 
                         src="' . $exhibitor_logo_url . '" 
                         alt="' . htmlspecialchars($exhibitor_name, ENT_QUOTES, "UTF-8") . '">';
                }

                $output .= '
                <div class="catalog-mobile-exh-card__stand">
                    ' . pwe_svg_icon('stand') . '
                    <p class="catalog-mobile-exh-card__stand-number">' . PWECommonFunctions::languageChecker('Stoisko', 'Stand') . ' ' . $exhibitor_stand_number . '</p>
                </div>

            </div>

        </div>

        <div class="catalog-mobile-exh-card__text">
            <div class="catalog-mobile-exh-card__title-wrapper">
                <h3 class="catalog-mobile-exh-card__title">' . $exhibitor_name . '</h3>
            </div>

            <div class="catalog-mobile-exh-card__contacts">';

                if (!empty($exhibitor_email)) {
                    $output .= '
                    <a class="catalog-mobile-exh-card__contact" href="mailto:' . $exhibitor_email . '">
                        ' . pwe_svg_icon('email') . '
                    </a>';
                } else {
                    $output .= '
                    <span class="catalog-mobile-exh-card__contact catalog-mobile-exh-card__contact--disabled">
                        ' . pwe_svg_icon('email') . '
                    </span>';
                }

                if (!empty($exhibitor_phone)) {
                    $output .= '
                    <a class="catalog-mobile-exh-card__contact" href="tel:' . $exhibitor_phone . '">
                        ' . pwe_svg_icon('phone') . '
                    </a>';
                } else {
                    $output .= '
                    <span class="catalog-mobile-exh-card__contact catalog-mobile-exh-card__contact--disabled">
                        ' . pwe_svg_icon('phone') . '
                    </span>';
                }

                $output .= '
                <a class="catalog-mobile-exh-card__more" 
                   href="?exhibitor_id=' . $exhibitor_id . ($_SERVER["HTTP_HOST"] === "warsawexpo.eu" ? "&catalog" : "") . '" 
                   target="_blank">
                   ' . PWECommonFunctions::languageChecker('Więcej', 'More') . '
                </a>

            </div>
        </div>

    </div>
</div>';

echo $output;
