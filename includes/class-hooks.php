<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('plugins_loaded', function() {
    add_filter('pwe_override_menu_output', function($html) {
        ob_start();
        Menu::render('all');
        return ob_get_clean();
    });
});

// add_action('rest_api_init', function () {

//     register_rest_route('pwe/v1', '/clear-cache', [
//         'methods'  => 'POST',
//         'callback' => 'pwe_clear_transients',
//         'permission_callback' => '__return_true'
//     ]);

// });

// function pwe_clear_transients($request) {

//     $token = $request->get_param('token');

//     if ($token !== PWE_API_KEY_5) {
//         return new WP_Error('forbidden', 'Invalid token', ['status'=>403]);
//     }

//     global $wpdb;

//     $wpdb->query("
//         DELETE FROM {$wpdb->options}
//         WHERE option_name LIKE '_transient_pwe_%'
//         OR option_name LIKE '_transient_timeout_pwe_%'
//     ");

//     return [
//         'status' => 'cache cleared'
//     ];
// }