<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PWE_Elements_Data {

    /**
     * Definition of all elements and their files + order
     * 
     * If the order for a given group is 0, skip this element!!!
     */
    private static $elements_files = [
        'main' => [
            ['class' => 'Header',           'file' => 'elements/main/header/header.php',                   'order' => ['gr1' => 1, 'gr2' => 1, 'b2c' => 1]],
            ['class' => 'Countdown',        'file' => 'elements/main/countdown/countdown.php',             'order' => ['gr1' => 2, 'gr2' => 2, 'b2c' => 2]],
            // ['class' => 'Combined_Events',  'file' => 'elements/main/combined-events/combined-events.php', 'order' => ['gr1' => 2, 'gr2' => 0, 'b2c' => 2]],
            ['class' => 'About',            'file' => 'elements/main/about/about.php',                     'order' => ['gr1' => 3, 'gr2' => 3, 'b2c' => 3]],
            ['class' => 'Conference',       'file' => 'elements/main/conference/conference.php',           'order' => ['gr1' => 4, 'gr2' => 5, 'b2c' => 4]],
            ['class' => 'Opinions',         'file' => 'elements/main/opinions/opinions.php',               'order' => ['gr1' => 13, 'gr2' => 12, 'b2c' => 5]],
            ['class' => 'Exhibitors',       'file' => 'elements/main/exhibitors/exhibitors.php',           'order' => ['gr1' => 7, 'gr2' => 4, 'b2c' => 6]],
            ['class' => 'Logotypes',        'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 8, 'gr2' => 7, 'b2c' => 0],        'params' => ['slug' => 'patrons-partners-international']],
            ['class' => 'Logotypes',        'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 9, 'gr2' => 8, 'b2c' => 8],        'params' => ['slug' => 'patrons-partners']],
            ['class' => 'Statistics',       'file' => 'elements/main/statistics/statistics.php',           'order' => ['gr1' => 5, 'gr2' => 6, 'b2c' => 10]],
            ['class' => 'Logotypes',        'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 6, 'gr2' => 0, 'b2c' => 0],     'params' => ['slug' => 'patrons-partners-pwe']],
            ['class' => 'Halls',            'file' => 'elements/main/halls/halls.php',                     'order' => ['gr1' => 11, 'gr2' => 10, 'b2c' => 11]],
            ['class' => 'Other_Events',     'file' => 'elements/main/other-events/other-events.php',       'order' => ['gr1' => 12, 'gr2' => 11, 'b2c' => 12]],
            ['class' => 'Profiles',         'file' => 'elements/main/profiles/profiles.php',               'order' => ['gr1' => 10, 'gr2' => 9, 'b2c' => 13]],
            ['class' => 'Posts',            'file' => 'elements/main/posts/posts.php',                     'order' => ['gr1' => 14, 'gr2' => 13, 'b2c' => 14]],
            ['class' => 'Premieres',        'file' => 'elements/main/premieres/premieres.php',             'order' => ['gr1' => 0, 'gr2' => 14, 'b2c' => 0]],
            ['class' => 'Logotypes',        'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 13, 'gr2' => 0, 'b2c' => 0],     'params' => ['slug' => 'europe-event']],
            ['class' => 'Medals',           'file' => 'elements/main/medals/medals.php',                   'order' => ['gr1' => 15, 'gr2' => 15, 'b2c' => 15]],
            ['class' => 'Summary',          'file' => 'elements/main/summary/summary.php',                 'order' => ['gr1' => 16, 'gr2' => 16, 'b2c' => 16]],
            ['class' => 'Countdown',        'file' => 'elements/main/countdown/countdown.php',             'order' => ['gr1' => 0, 'gr2' => 17, 'b2c' => 0]],
            ['class' => 'Footer',           'file' => 'elements/main/footer/footer.php',                   'order' => ['gr1' => 17, 'gr2' => 18, 'b2c' => 17]],
            // ['class' => 'Logotypes',     'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 9, 'gr2' => 9, 'b2c' => 9],        'params' => ['slug' => 'patrons-partners-conference']],
        ],
        'catalog' => [
            ['class' => 'Exhibitor_Catalog',    'file' => 'elements/catalog/exhibitor_catalog/exhibitor_catalog.php',   'order' => ['gr1' => 1, 'gr2' => 1, 'b2c' => 1]],
            ['class' => 'Footer',               'file' => 'elements/main/footer/footer.php',                            'order' => ['gr1' => 999, 'gr2' => 999, 'b2c' => 999]],
        ],
    ];

    /**
     * Returns all elements
     */
    public static function get_all_elements() {
        return self::$elements_files;
    }

    /**
     * Loads item files for a given type
     */
    public static function require_elements($type) {
        if (empty(self::$elements_files[$type])) {
            return;
        }

        foreach (self::$elements_files[$type] as $element) {
            $path = plugin_dir_path(__DIR__) . $element['file'];

            if (file_exists($path)) {
                require_once $path;

                // Check if the class exists
                if (!class_exists($element['class'])) {
                    error_log("Class {$element['class']} not found in file {$path}");
                }
            } else {
                error_log("File not found: {$path}");
            }
        }
    }

    /**
     * Helper – Gets the order for an element
     */
    public static function get_order_for($class, $group, $type) {
        foreach (self::$elements_files[$type] ?? [] as $el) {
            if ($el['class'] === $class) {
                $order = (int)($el['order'][$group] ?? 0);
                return $order > 0 ? $order : 999;
            }
        }
        return 999;
    }

}