<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// shortcodes
// main: [pwe-elements-auto-switch-page-main]
    // [pwe-elements-auto-switch-header]
    // [pwe-elements-auto-switch-counntdown]
    // [pwe-elements-auto-switch-logotypes slug="patrons-partners"]
    // ...
// catalog: [pwe-elements-auto-switch-page-catalog]
// flip-book: [pwe-elements-auto-switch-page-flip-book]

// components shortcodes
// footer: [pwe-elements-component-footer]

class PWE_Elements_Data {

    /**
     * Retrieves elements order data from the database.
     */
    public static function get_elements_order_from_db() {
        $db_data = PWE_Functions::get_database_elements_order_data();
        
        if (empty($db_data)) {
            return [];
        }

        // Mapping of slug -> class and file path
        $slug_map = [
            'header'        => ['class' => 'Header',        'file' => 'elements/main/header/header.php'],
            'countdown'     => ['class' => 'Countdown',     'file' => 'elements/main/countdown/countdown.php'],
            'about'         => ['class' => 'About',         'file' => 'elements/main/about/about.php'],
            'conference'    => ['class' => 'Conference',    'file' => 'elements/main/conference/conference.php'],
            'premieres'     => ['class' => 'Premieres',     'file' => 'elements/main/premieres/premieres.php'],
            'opinions'      => ['class' => 'Opinions',      'file' => 'elements/main/opinions/opinions.php'],
            'exhibitors'    => ['class' => 'Exhibitors',    'file' => 'elements/main/exhibitors/exhibitors.php'],
            'statistics'    => ['class' => 'Statistics',    'file' => 'elements/main/statistics/statistics.php'],
            'speakers'      => ['class' => 'Speakers',      'file' => 'elements/main/speakers/speakers.php'],
            'halls'         => ['class' => 'Halls',         'file' => 'elements/main/halls/halls.php'],
            'other-events'  => ['class' => 'Other_Events',  'file' => 'elements/main/other-events/other-events.php'],
            'profiles'      => ['class' => 'Profiles',      'file' => 'elements/main/profiles/profiles.php'],
            'posts'         => ['class' => 'Posts',         'file' => 'elements/main/posts/posts.php'],
            'medals'        => ['class' => 'Medals',        'file' => 'elements/main/medals/medals.php'],
            'summary'       => ['class' => 'Summary',       'file' => 'elements/main/summary/summary.php'],
            'footer'        => ['class' => 'Footer',        'file' => 'components/footer/footer.php'],
        ];

        $result = [];

        $grouped = [];

        // Iterate over DB rows to build grouped positions
        foreach ($db_data as $row) {
            $page  = $row->page;
            $group = $row->fair_id;

            $order_data = json_decode($row->page_order_data, true);
            if (!is_array($order_data)) continue;

            $position = 1;

            foreach ($order_data as $item) {
                $slug = $item['slug'] ?? null;
                if (!$slug || !isset($slug_map[$slug])) {
                    $position++;
                    continue;
                }

                $grouped[$page][$slug][$group][] = $position;
                $position++;
            }
        }

        // Build final result array with positions for each element
        foreach ($grouped as $page => $slugs) {

            foreach ($slugs as $slug => $groups) {

                $element = $slug_map[$slug];

                // Find max occurrences of this element across groups
                $max = 0;
                foreach ($groups as $positions) {
                    $max = max($max, count($positions));
                }

                // Merge elements across groups into final result
                for ($i = 0; $i < $max; $i++) {

                    $order = [];

                    foreach ($groups as $group => $positions) {
                        $order[$group] = $positions[$i] ?? 0;
                    }

                    $result[$page][] = [
                        'class' => $element['class'],
                        'file'  => $element['file'],
                        'order' => $order,
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Default elements files with order per group
     * Order 0 means element is skipped for that group
     */
    private static $elements_files = [
        'main' => [
            ['class' => 'Header',           'file' => 'elements/main/header/header.php',                   'order' => ['gr1' => 1, 'gr2' => 1, 'b2c' => 1, 'week' => 1]],
            ['class' => 'Countdown',        'file' => 'elements/main/countdown/countdown.php',             'order' => ['gr1' => 2, 'gr2' => 2, 'b2c' => 2, 'week' => 1]],
            // ['class' => 'Combined_Events',  'file' => 'elements/main/combined-events/combined-events.php', 'order' => ['gr1' => 2, 'gr2' => 0, 'b2c' => 2, 'week' => 1]],
            ['class' => 'About',            'file' => 'elements/main/about/about.php',                     'order' => ['gr1' => 3, 'gr2' => 3, 'b2c' => 3, 'week' => 1]],
            ['class' => 'Conference',       'file' => 'elements/main/conference/conference.php',           'order' => ['gr1' => 4, 'gr2' => 5, 'b2c' => 4, 'week' => 1]],
            // ['class' => 'Speakers',         'file' => 'elements/main/speakers/speakers.php',               'order' => ['gr1' => 4, 'gr2' => 5, 'b2c' => 4, 'week' => 1]], 
            ['class' => 'Premieres',        'file' => 'elements/main/premieres/premieres.php',             'order' => ['gr1' => 0, 'gr2' => 5, 'b2c' => 0, 'week' => 1]],
            ['class' => 'Opinions',         'file' => 'elements/main/opinions/opinions.php',               'order' => ['gr1' => 13, 'gr2' => 12, 'b2c' => 5, 'week' => 1]],
            ['class' => 'Exhibitors',       'file' => 'elements/main/exhibitors/exhibitors.php',           'order' => ['gr1' => 7, 'gr2' => 4, 'b2c' => 6, 'week' => 1]],
            ['class' => 'Logotypes',        'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 8, 'gr2' => 7, 'b2c' => 0, 'week' => 1],        'params' => ['slug' => 'patrons-partners-international']],
            ['class' => 'Logotypes',        'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 9, 'gr2' => 8, 'b2c' => 8, 'week' => 1],        'params' => ['slug' => 'patrons-partners']],
            ['class' => 'Statistics',       'file' => 'elements/main/statistics/statistics.php',           'order' => ['gr1' => 5, 'gr2' => 6, 'b2c' => 10, 'week' => 1]],
            ['class' => 'Logotypes',        'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 6, 'gr2' => 0, 'b2c' => 0, 'week' => 1],     'params' => ['slug' => 'patrons-partners-pwe']],
            ['class' => 'Halls',            'file' => 'elements/main/halls/halls.php',                     'order' => ['gr1' => 11, 'gr2' => 10, 'b2c' => 11, 'week' => 1]],
            ['class' => 'Other_Events',     'file' => 'elements/main/other-events/other-events.php',       'order' => ['gr1' => 12, 'gr2' => 11, 'b2c' => 12, 'week' => 1]],
            ['class' => 'Profiles',         'file' => 'elements/main/profiles/profiles.php',               'order' => ['gr1' => 10, 'gr2' => 9, 'b2c' => 13, 'week' => 1]],
            ['class' => 'Posts',            'file' => 'elements/main/posts/posts.php',                     'order' => ['gr1' => 14, 'gr2' => 13, 'b2c' => 14, 'week' => 1]],
            ['class' => 'Logotypes',        'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 13, 'gr2' => 0, 'b2c' => 0, 'week' => 1],     'params' => ['slug' => 'europe-event']],
            ['class' => 'Medals',           'file' => 'elements/main/medals/medals.php',                   'order' => ['gr1' => 15, 'gr2' => 15, 'b2c' => 15, 'week' => 1]],
            ['class' => 'Summary',          'file' => 'elements/main/summary/summary.php',                 'order' => ['gr1' => 16, 'gr2' => 16, 'b2c' => 16, 'week' => 1]],
            ['class' => 'Countdown',        'file' => 'elements/main/countdown/countdown.php',             'order' => ['gr1' => 0, 'gr2' => 17, 'b2c' => 0, 'week' => 1]],
            ['class' => 'Footer',           'file' => 'components/footer/footer.php',                      'order' => ['gr1' => 999, 'gr2' => 999, 'b2c' => 999, 'week' => 999]],
            // ['class' => 'Logotypes',     'file' => 'elements/main/logotypes/logotypes.php',             'order' => ['gr1' => 9, 'gr2' => 9, 'b2c' => 9, 'week' => 1],        'params' => ['slug' => 'patrons-partners-conference']],
        ],
        'catalog' => [
            ['class' => 'Exhibitor_Catalog',    'file' => 'elements/catalog/exhibitor_catalog_vue/exhibitor_catalog_vue.php',   'order' => ['gr1' => 1, 'gr2' => 1, 'b2c' => 1]],
            ['class' => 'Footer',               'file' => 'components/footer/footer.php',                               'order' => ['gr1' => 999, 'gr2' => 999, 'b2c' => 999]],
        ],
        'flip-book' => [
            ['class' => 'Flip_Book',       'file' => 'elements/flip-book/flip-book.php',   'order' => ['gr1' => 1, 'gr2' => 1, 'b2c' => 1]],
            ['class' => 'Footer',          'file' => 'components/footer/footer.php',       'order' => ['gr1' => 999, 'gr2' => 999, 'b2c' => 999]],
        ],
    ];

    /**
     * Components files (static parts like footer)
     */
    private static $components_files = [
        'footer' => [
            'class' => 'Footer',    'file' => 'components/footer/footer.php',       'order' => ['gr1' => 999, 'gr2' => 999, 'b2c' => 999]
        ],
    ];

    /**
     * Returns all elements.
     * If $current_group is provided, checks if group exists in DB; otherwise returns fallback
     */
    public static function get_all_elements($current_group = null) {

        // $elements_from_db = self::get_elements_order_from_db();

        // // If DB empty - fallback
        // if (empty($elements_from_db)) {
        //     return self::$elements_files;
        // }

        // // If group - check if it exists in data
        // if ($current_group) {

        //     $group_found = false;

        //     foreach ($elements_from_db as $page => $elements) {
        //         foreach ($elements as $element) {
        //             if (isset($element['order'][$current_group])) {
        //                 $group_found = true;
        //                 break 2;
        //             }
        //         }
        //     }

        //     // If the group does not exist in the DB - fallback
        //     if (!$group_found) {
        //         return self::$elements_files;
        //     }
        // }

        // return $elements_from_db;

        return self::$elements_files;
    }

    /**
     * Returns all components
     */
    public static function get_all_components() {
        return self::$components_files;
    }

    /**
     * Includes PHP files for given element type.
     * Uses fallback if DB empty or type not found.
     */
    public static function require_elements($type) {

        $group = PWE_Groups::get_current_group();

        $all_elements = self::get_all_elements($group);

        $elements = $all_elements[$type] ?? [];

        foreach ($elements as $element) {

            $path = plugin_dir_path(__DIR__) . $element['file'];

            if (file_exists($path)) {
                require_once $path;

                if (!class_exists($element['class'])) {
                    error_log("Class {$element['class']} not found in file {$path}");
                }
            } else {
                error_log("File not found: {$path}");
            }
        }
    }

    /**
     * Returns the order of an element for a given group and type
     * Returns 999 if order is 0 or element not found
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