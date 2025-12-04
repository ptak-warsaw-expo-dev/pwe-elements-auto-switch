<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('plugins_loaded', function() {
    add_filter('pwe_override_menu_output', function($html) {
        ob_start();
        Menu::render('all');
        return ob_get_clean();
    });
});
