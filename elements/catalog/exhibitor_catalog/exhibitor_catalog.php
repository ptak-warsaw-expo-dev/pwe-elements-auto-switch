<?php
if (!defined('ABSPATH')) exit;

/*
|--------------------------------------------------------------------------
| PATH DEFINITIONS (must be loaded before the loader)
|--------------------------------------------------------------------------
*/
define('EX_PATH', plugin_dir_path(__FILE__));
define('EX_URL',  plugin_dir_url(__FILE__));

/*
|--------------------------------------------------------------------------
| FUNCTION AUTOLOADER
| Loads all files from the /functions directory.
|--------------------------------------------------------------------------
*/
require_once EX_PATH . 'functions/loader.php';
ec_load_functions();

/*
|--------------------------------------------------------------------------
| MAIN CATALOG CLASS
|--------------------------------------------------------------------------
*/
class Exhibitor_Catalog {

    public static function get_data() {
        return [
            'types'   => ['catalog'],
            'presets' => [],
        ];
    }

    public static function get_info() {
        return [
            'type' => 'catalog',
            'slug' => strtolower(__CLASS__),
        ];
    }

    public static function render($group, $params, $atts) {

        $info = self::get_info();

        PWE_Functions::assets_per_element($info['slug'], $info['type']);
        PWE_Functions::assets_per_group($info['slug'], $group, $info['type']);

        $catalog_ids_raw = trim(do_shortcode('[trade_fair_catalog_id]'));
        $open_catalog_old = isset($_GET['catalog']) && $_GET['catalog'] === 'old';

        if ($catalog_ids_raw === '' || $open_catalog_old) {
            $old_id = trim(do_shortcode('[trade_fair_catalog]'));

            if ($old_id === '') {
                return;
            }

            $pwe = new PWECatalog();

            echo $pwe->PWECatalogOutput(
                [
                    'format'         => 'PWECatalogFull',
                    'identification' => $old_id,
                ],
                null,
                'PWECatalogFull'
            );

            return;
        }

        // 1. Data retrieval
        $exhibitors = ec_get_all_exhibitors($atts);
        $filters    = ec_prepare_filters($exhibitors);
        $colors     = ec_get_catalog_colors();

        // 2. CSV export request handler
        if (ec_export_csv_requested()) {
            ec_export_exhibitors_csv($exhibitors);
            exit;
        }

        // 3. View routing (desktop/mobile)
        $view = ec_is_mobile() ? 'mobile' : 'desktop';

        $context = [
            'exhibitors' => $exhibitors,
            'filters'    => $filters,
            'colors'     => $colors,
            'view'       => $view,
            'atts'       => $atts,
        ];

        // 4. Render view
        echo ec_render_main_view($context);

    }
}