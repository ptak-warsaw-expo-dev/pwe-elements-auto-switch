<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** 
 * Class PWE_Elements_Data
 * 
 * Provides a registry of available elements and components, their file paths, and page composition.
 * 
 * Shortcodes:
 * 
 * Components: [pwe-elements-component-simple-header], [pwe-elements-component-footer] ...
 * Elements: [pwe-elements-auto-switch-header] // [pwe-elements-auto-switch-header b2c="true"] ...
 * Pages: [pwe-elements-auto-switch-page-main], [pwe-elements-auto-switch-page-catalog archive_catalog_id="999"] ...
 * 
 */

class PWE_Elements_Data {

    /** Classes available in the elements directory. Paths are detected automatically. */
    private static $elements = [
        'Header',
        'Countdown',
        'Combined_Events',
        'About',
        'Tickets',
        'Attractions',
        'Sectors',
        'Conference',
        'Speakers',
        'Guests',
        'Premieres',
        'Opinions',
        'Exhibitors',
        'Logotypes',
        'Statistics',
        'Halls',
        'Other_Events',
        'Profiles',
        'Posts',
        'Medals',
        'Summary',

        'Exhibitor_Catalog',
        'Flip_Book',
        'Speakers_Page',
        'Registration_Visitors',
        'Registration_Exhibitors',
        'Contact',
        'Potential_Exhibitors',
        'Medal_Ceremony',
        'Fair_Plan',
        'Exhibitor_Visitor_Generator',
        'Exhibitor_Worker_Generator',
        'Confirmation_Visitors_Registration',
        'Confirmation_Exhibitors_Registration',
    ];

    /** Classes available in the components directory. Paths are detected automatically. */
    private static $components = [
        'Footer',
        'Simple_Header',
        'Exhibitors_Top12',
        'Contact_Details',
        'Organized_Groups',
        'Location_Map',
        'PWE_Address',
    ];

    /** Page composition: class, order and optional params only. */
    private static $pages = [
        'main' => [
            ['class' => 'Header',           'order' => ['gr1' => 1,   'gr2' => 1,   'b2c' => 1,   'b2c-new' => 1,   'week' => 1]],
            ['class' => 'Countdown',        'order' => ['gr1' => 2,   'gr2' => 2,   'b2c' => 2,   'b2c-new' => 2,   'week' => 1]],
            ['class' => 'Combined_Events',  'order' => ['gr1' => 0,   'gr2' => 0,   'b2c' => 0,   'b2c-new' => 0,   'week' => 2]],
            ['class' => 'About',            'order' => ['gr1' => 3,   'gr2' => 3,   'b2c' => 3,   'b2c-new' => 2,   'week' => 5]],
            ['class' => 'Tickets',          'order' => ['gr1' => 0,   'gr2' => 0,   'b2c' => 0,   'b2c-new' => 3,   'week' => 5]],
            ['class' => 'Attractions',      'order' => ['gr1' => 0,   'gr2' => 0,   'b2c' => 0,   'b2c-new' => 4,   'week' => 5]],
            ['class' => 'Sectors',          'order' => ['gr1' => 3,   'gr2' => 3,   'b2c' => 3,   'b2c-new' => 8,   'week' => 4]],
            ['class' => 'Conference',       'order' => ['gr1' => 4,   'gr2' => 5,   'b2c' => 4,   'b2c-new' => 0,   'week' => 5]],
            ['class' => 'Speakers',         'order' => ['gr1' => 4,   'gr2' => 5,   'b2c' => 4,   'b2c-new' => 0,   'week' => 5]],
            ['class' => 'Guests',           'order' => ['gr1' => 4,   'gr2' => 5,   'b2c' => 4,   'b2c-new' => 6,   'week' => 0]],
            ['class' => 'Premieres',        'order' => ['gr1' => 0,   'gr2' => 5,   'b2c' => 0,   'b2c-new' => 8,   'week' => 0]],
            ['class' => 'Opinions',         'order' => ['gr1' => 13,  'gr2' => 12,  'b2c' => 5,   'b2c-new' => 0,   'week' => 0]],
            ['class' => 'Exhibitors',       'order' => ['gr1' => 7,   'gr2' => 4,   'b2c' => 6,   'b2c-new' => 5,   'week' => 3]],
            ['class' => 'Logotypes',        'order' => ['gr1' => 8,   'gr2' => 7,   'b2c' => 0,   'b2c-new' => 0,   'week' => 3],   'params' => ['slug' => 'patrons-partners-international']],
            ['class' => 'Logotypes',        'order' => ['gr1' => 9,   'gr2' => 8,   'b2c' => 8,   'b2c-new' => 6,   'week' => 3],   'params' => ['slug' => 'patrons-partners']],
            ['class' => 'Statistics',       'order' => ['gr1' => 5,   'gr2' => 6,   'b2c' => 10,  'b2c-new' => 7,   'week' => 2]],
            ['class' => 'Logotypes',        'order' => ['gr1' => 6,   'gr2' => 0,   'b2c' => 0,   'b2c-new' => 0,   'week' => 0],   'params' => ['slug' => 'patrons-partners-pwe']],
            ['class' => 'Halls',            'order' => ['gr1' => 11,  'gr2' => 10,  'b2c' => 11,  'b2c-new' => 0,   'week' => 5]],
            ['class' => 'Other_Events',     'order' => ['gr1' => 12,  'gr2' => 11,  'b2c' => 12,  'b2c-new' => 0,   'week' => 0]],
            ['class' => 'Profiles',         'order' => ['gr1' => 10,  'gr2' => 9,   'b2c' => 13,  'b2c-new' => 0,   'week' => 0]],
            ['class' => 'Posts',            'order' => ['gr1' => 14,  'gr2' => 13,  'b2c' => 14,  'b2c-new' => 9,   'week' => 0]],
            ['class' => 'Logotypes',        'order' => ['gr1' => 13,  'gr2' => 0,   'b2c' => 0,   'b2c-new' => 0,   'week' => 0],   'params' => ['slug' => 'europe-event']],
            ['class' => 'Medals',           'order' => ['gr1' => 15,  'gr2' => 15,  'b2c' => 15,  'b2c-new' => 0,   'week' => 0]],
            ['class' => 'Summary',          'order' => ['gr1' => 16,  'gr2' => 16,  'b2c' => 16,  'b2c-new' => 0,   'week' => 0]],
            ['class' => 'Countdown',        'order' => ['gr1' => 0,   'gr2' => 17,  'b2c' => 0,   'b2c-new' => 0,   'week' => 0]],
            ['class' => 'Footer',           'order' => ['gr1' => 999, 'gr2' => 999, 'b2c' => 999, 'b2c-new' => 999, 'week' => 999]],
        ],
        'catalog' => [
            ['class' => 'Exhibitor_Catalog'],
            ['class' => 'Footer'],
        ],
        'flip-book' => [
            ['class' => 'Flip_Book'],
            ['class' => 'Footer'],
        ],
        'speakers' => [
            ['class' => 'Simple_Header'],
            ['class' => 'Speakers_Page'],
            ['class' => 'Footer'],
        ],
        'registration-visitors' => [
            ['class' => 'Registration_Visitors'],
            ['class' => 'Footer'],
        ],
        'registration-exhibitors' => [
            ['class' => 'Registration_Exhibitors'],
            ['class' => 'Footer'],
        ],
        'contact' => [
            ['class' => 'Simple_Header'],
            ['class' => 'Contact'],
            ['class' => 'Footer'],
        ],
        'potential-exhibitors' => [
            ['class' => 'Potential_Exhibitors'],
            ['class' => 'Footer'],
        ],
        'medal-ceremony' => [
            ['class' => 'Medal_Ceremony'],
            ['class' => 'Footer'],
        ],
        'fair-plan' => [
            ['class' => 'Fair_Plan'],
            ['class' => 'Footer'],
        ],
        'exhibitor-visitor-generator' => [
            ['class' => 'Exhibitor_Visitor_Generator'],
            ['class' => 'Footer'],
        ],
        'exhibitor-worker-generator' => [
            ['class' => 'Exhibitor_Worker_Generator'],
            ['class' => 'Footer'],
        ],
        'confirmation-visitors-registration' => [
            ['class' => 'Confirmation_Visitors_Registration'],
            ['class' => 'Footer'],
        ],
        'confirmation-exhibitors-registration' => [
            ['class' => 'Confirmation_Exhibitors_Registration'],
            ['class' => 'Footer'],
        ],
    ];

    /** Runtime cache: class name => relative file path. */
    private static $class_files = null;

    /** Returns all configured page definitions. */
    public static function get_all_elements($current_group = null) {
        return self::$pages;
    }

    /** Returns the element registry with detected file paths. */
    public static function get_all_element_files() {
        return self::get_registry(self::$elements);
    }

    /** Returns the component registry with detected file paths. */
    public static function get_all_components() {
        return self::get_registry(self::$components);
    }

    /** Returns the configuration for a selected page. */
    public static function get_page($page) {
        return self::$pages[$page] ?? [];
    }

    /** Returns compact definitions used by existing shortcode registration code. */
    private static function get_registry($classes) {
        $registry = [];
        foreach ($classes as $class) {
            $definition = self::get_file_for_class($class);
            if ($definition) {
                $registry[self::class_to_slug($class)] = $definition;
            }
        }
        return $registry;
    }

    /** Finds the PHP file by reading class declarations in elements and components. */
    public static function get_file_for_class($class) {
        self::build_class_file_map();
        if (empty(self::$class_files[$class])) return null;
        return ['class' => $class, 'file' => self::$class_files[$class]];
    }

    /** Scans plugin directories and maps classes to PHP files. */
    private static function build_class_file_map() {
        if (self::$class_files !== null) return;

        self::$class_files = [];
        $plugin_root = dirname(__DIR__);
        $allowed = array_fill_keys(array_merge(self::$elements, self::$components), true);

        foreach (['elements', 'components'] as $directory) {
            $base_path = $plugin_root . DIRECTORY_SEPARATOR . $directory;
            if (!is_dir($base_path)) continue;

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_path, FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $file) {
                if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') continue;

                foreach (self::get_classes_from_file($file->getPathname()) as $found_class) {
                    if (!isset($allowed[$found_class]) || isset(self::$class_files[$found_class])) continue;
                    self::$class_files[$found_class] = str_replace('\\', '/', substr($file->getPathname(), strlen($plugin_root) + 1));
                }
            }
        }
    }

    /** Extracts declared class names without executing the scanned file. */
    private static function get_classes_from_file($path) {
        $code = @file_get_contents($path);
        if ($code === false) return [];

        $tokens = token_get_all($code);
        $classes = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            if (!is_array($tokens[$i]) || $tokens[$i][0] !== T_CLASS) continue;

            $previous = self::previous_significant_token($tokens, $i);
            if ($previous === T_NEW || $previous === T_DOUBLE_COLON) continue;

            for ($j = $i + 1; $j < $count; $j++) {
                if (is_array($tokens[$j]) && $tokens[$j][0] === T_STRING) {
                    $classes[] = $tokens[$j][1];
                    break;
                }
                if ($tokens[$j] === '{' || $tokens[$j] === '(') break;
            }
        }

        return $classes;
    }

    /** Returns the previous meaningful PHP token. */
    private static function previous_significant_token($tokens, $index) {
        for ($i = $index - 1; $i >= 0; $i--) {
            if (!is_array($tokens[$i])) return null;
            if (!in_array($tokens[$i][0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) return $tokens[$i][0];
        }
        return null;
    }

    /** Converts a class name into a shortcode-friendly slug. */
    private static function class_to_slug($class) {
        return strtolower(str_replace('_', '-', $class));
    }

    /** Loads the PHP file containing the requested class. */
    public static function require_class($class) {
        if (class_exists($class, false)) return true;

        $definition = self::get_file_for_class($class);
        if (!$definition) {
            error_log("No PHP file found for class {$class}");
            return false;
        }

        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . $definition['file'];
        require_once $path;

        if (!class_exists($class)) {
            error_log("Class {$class} not found after loading {$path}");
            return false;
        }
        return true;
    }

    /** Loads all classes assigned to the selected page. */
    public static function require_elements($type) {
        foreach (self::get_page($type) as $element) self::require_class($element['class']);
    }

    /** Returns the display order for a class in a page group. */
    public static function get_order_for($class, $group, $type) {
        foreach (self::get_page($type) as $element) {
            if ($element['class'] !== $class) continue;
            $order = (int) ($element['order'][$group] ?? 1);
            return $order > 0 ? $order : 999;
        }
        return 999;
    }
}