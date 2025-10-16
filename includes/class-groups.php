<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PWE_Groups {

    private static $groups = [
        'gr1' => ['mr.glasstec.pl',''],
        'gr2' => ['',''],
        'gr3' => ['',''],
        'b2c' => ['',''],
    ];

    public static function init() {

    }

    public static function get_current_group() {
        $host = $_SERVER['HTTP_HOST'] ?? '';

        foreach ( self::$groups as $group => $domains ) {
            if ( in_array( $host, $domains, true ) ) {
                return $group;
            }
        }
        return null;
    }
}
