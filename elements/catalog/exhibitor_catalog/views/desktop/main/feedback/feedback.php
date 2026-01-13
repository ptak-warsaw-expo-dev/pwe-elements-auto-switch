<?php
if (!defined('ABSPATH')) exit;

/**
 * Get Gravity Form ID by title with DB cache
 */
if (!function_exists('ec_get_form_id')) {
    function ec_get_form_id($form_title) {
        if (!class_exists('GFAPI')) {
            return null;
        }

        $option_key = 'feedback_gf_form_id';

        $cached_id = get_option($option_key);

        if ($cached_id) {
            $form = GFAPI::get_form($cached_id);

            if ($form && empty($form['is_trash']) && (!isset($form['is_active']) || $form['is_active'] === 1)) {
                return $cached_id;
            }

            delete_option($option_key);
        }

        $forms = GFAPI::get_forms();

        foreach ($forms as $form) {
            if (isset($form['title']) && trim(mb_strtolower($form['title'])) === trim(mb_strtolower($form_title))) {
                update_option($option_key, $form['id'], true);
                return $form['id'];
            }
        }

        return null;
    }
}

$form_title = 'User opinions';
$form_id = ec_get_form_id($form_title);

if (!function_exists('gravity_form') || !$form_id) {
    return;
}

echo '
<div class="catalog-feedback">
    <svg fill="var(--accent-color)" viewBox="4 4 16 16" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g data-name="Layer 2"> <g data-name="arrow-up"> <rect width="24" height="24" transform="rotate(90 12 12)" opacity="0"></rect> <path d="M16.21 16H7.79a1.76 1.76 0 0 1-1.59-1 2.1 2.1 0 0 1 .26-2.21l4.21-5.1a1.76 1.76 0 0 1 2.66 0l4.21 5.1A2.1 2.1 0 0 1 17.8 15a1.76 1.76 0 0 1-1.59 1z"></path> </g> </g> </g></svg>
    <h3>'. (PWECommonFunctions::lang_pl() ? 'Jak oceniasz Katalog?' : 'How do you rate the Catalog?') .'</h3>';

    gravity_form(
        $form_id,
        false,  // title
        false,  // description
        false,  // ajax (true jeśli chcesz)
        null,
        true
    );

    echo '
    <p class="rating-hint">'. (PWECommonFunctions::lang_pl() ? 'Twoja opinia jest anonimowa' : 'Your opinion is anonymous') .'</p>
</div>';
