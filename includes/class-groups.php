<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class PWE_Groups {

	private static function groups(): array {
        $pwe_groups_data = PWE_Functions::get_database_groups_data();

        $result = [
            'gr1' => [],
            'gr2' => [],
            'gr3' => [],
            'b2c' => [],
            'week' => [],
        ];

        foreach ($pwe_groups_data as $group) {
            if (!isset($result[$group->fair_group])) {

                $result[$group->fair_group] = [];
            }

            $result[$group->fair_group][] = $group->fair_domain;
        }

        return $result;
    }

    public static function get_current_group() {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $groups = self::groups();

        foreach ( $groups as $group => $domains ) {
            if ( in_array( $host, $domains, true ) ) {

                // Temporary <---------------------------------<
                if ($group === 'gr3' || $group === 'b2c') {
                    return 'gr2';
                }
                // Temporary <---------------------------------<

                return $group;
            }

            // Temporary <---------------------------------<
            if ($host === "mr.glasstec.pl") {
                return 'gr2';
            }
            // Temporary <---------------------------------<
        }
        return null;
    }
}
