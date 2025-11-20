<?php
if (!defined('ABSPATH')) exit;

if (!function_exists('exhibitor_catalog_render_exhibitor_card')) {

    function exhibitor_catalog_render_exhibitor_card($exhibitor) {
        $exhibitor_id           = (string)($exhibitor['exhibitor_id'] ?? '');
        $exhibitor_name         = $exhibitor['name'] ?? '';
        $exhibitor_logo_url     = $exhibitor['logo_url'] ?? '';
        $exhibitor_phone        = $exhibitor['contact_phone'] ?? '';
        $exhibitor_email        = $exhibitor['contact_email'] ?? '';
        $exhibitor_stand_number = $exhibitor['stand_number'] ?? '';

        if (mb_strlen($exhibitor_name) > 27) {
            $exhibitor_name = mb_substr($exhibitor_name, 0, 27) . '...';
        }

        $output = '
        <div class="catalog-mobile-exh-card" data-exhibitor-id="' . $exhibitor_id . '">

            <div class="catalog-mobile-exh-card__content">
            
                <div class="catalog-mobile-exh-card__info">
                
                    <div class="catalog-mobile-exh-card__logo-container">';

                        if (!empty($exhibitor_logo_url)) {
                            $output .= '
                            <img class="catalog-mobile-exh-card__logo" 
                                 src="' . $exhibitor_logo_url . '" 
                                 alt="' . htmlspecialchars($exhibitor_name) . '">';
                        }

                        $output .= '
                        <div class="catalog-mobile-exh-card__stand">
                            ' . pwe_svg_icon('stand') . '
                            <p class="catalog-mobile-exh-card__stand-number">Stoisko ' . $exhibitor_stand_number . '</p>
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
                            <a class="catalog-mobile-exh-card__contact--disabled">
                                ' . pwe_svg_icon('email') . '
                            </a>';
                        }

                        if (!empty($exhibitor_phone)) {
                            $output .= '
                            <a class="catalog-mobile-exh-card__contact" href="tel:' . $exhibitor_phone . '">
                                ' . pwe_svg_icon('phone') . '
                            </a>';
                        } else {
                            $output .= '
                            <a class="catalog-mobile-exh-card__contact--disabled">
                                ' . pwe_svg_icon('phone') . '
                            </a>';
                        }

                        $output .= '
                        <a class="catalog-mobile-exh-card__more" 
                           href="?exhibitor_id=' . $exhibitor_id . ($_SERVER['HTTP_HOST'] === 'warsawexpo.eu' ? '&catalog' : '') .'" 
                           target="_blank">
                           Więcej
                        </a>

                    </div>
                </div>

            </div>
        </div>';

        return $output;
    }
}