<?php
if (!defined('ABSPATH')) exit;

class Speakers {

    public static function get_data() {
        return [
            'types' => ['main'], 
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        if ($_SERVER['HTTP_HOST'] === 'warsawtechweek.com') {
            $group = 'gr2';
        }

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            // Get speakers from the database
            $data = PWE_Functions::get_database_fairs_data_speakers(); 

            $lang = PWE_Functions::lang();

            $speakers_indexed = [];

            if (!empty($data)) {

                foreach ($data as $row) {

                    $positionData = $row->position ?? [];
                    $bioData = $row->bio ?? [];

                    if (is_string($positionData)) {
                        $positionData = json_decode($positionData, true) ?: [];
                    }

                    if (is_string($bioData)) {
                        $bioData = json_decode($bioData, true) ?: [];
                    }

                    if (!is_array($positionData)) {
                        $positionData = [];
                    }

                    if (!is_array($bioData)) {
                        $bioData = [];
                    }

                    $position = $positionData[$lang] ?? '';
                    $bio = $bioData[$lang] ?? '';

                    if (empty($position)) {
                        $position = $positionData['en'] ?? '';
                    }

                    if (empty($bio)) {
                        $bio = $bioData['en'] ?? '';
                    }

                    $speaker = [
                        'slug'        => $row->slug ?? '',
                        'name'        => $row->name ?? '',
                        'img'         => !empty($row->image) ? 'https://cap.warsawexpo.eu/' . $row->image : '',
                        'logo'        => !empty($row->logo) ? 'https://cap.warsawexpo.eu/' . $row->logo : '',
                        'company'     => $row->company ?? '',
                        'position'    => $position,
                        'bio'         => $bio,
                        'order'       => $row->order ?? '',
                    ];

                    $order = (int) $speaker['order'];

                    if ($order !== 0) {
                        if ($order === 99) {
                            $speakers_indexed[99][] = $speaker;
                        } else {
                            $speakers_indexed[$order][] = $speaker;
                        }
                    }
                }
            }

            if (empty($speakers_indexed)) {
                echo '<style>.pwe-element-auto-switch.speakers {display:none;}</style>';
                return; 
            }

            // Sorted by speakers_order (without 99)
            ksort($speakers_indexed);

            // Build a score
            $speakers = [];

            // First, these are sorted out
            foreach ($speakers_indexed as $order => $items) {
                if ($order != 99) {
                    foreach ($items as $op) {
                        $speakers[] = $op;
                    }
                }
            }

            // At the end all with order = 99
            if (!empty($speakers_indexed[99])) {
                foreach ($speakers_indexed[99] as $op) {
                    $speakers[] = $op;
                }
            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
