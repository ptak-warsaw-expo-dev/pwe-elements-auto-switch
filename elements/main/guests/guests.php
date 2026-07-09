<?php
if (!defined('ABSPATH')) exit;

class Guests {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
                'b2c-new' => plugin_dir_path(__FILE__) . 'presets/b2c-new/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {
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

            $rows = PWE_Functions::get_database_fairs_data_guests();
            $lang = PWE_Functions::lang();

            $getLang = function($arr, $lang) {
                return $arr[$lang]
                    ?? $arr['en']
                    ?? $arr['pl']
                    ?? '';
            };

            $settings = [];
            $tabs = [];
            $guests_raw = [];

            foreach ($rows as $row) {
                $decoded = json_decode($row->data, true);

                if ($row->type === 'settings') {
                    $settings = $decoded;
                }

                if ($row->type === 'tab') {
                    $tabs[] = $decoded;
                }

                if ($row->type === 'guest') {
                    $guests_raw[] = $decoded;
                }
            }

            // sort tabs
            usort($tabs, fn($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

            if (empty($rows) || count($guests_raw) < 1) {
                echo '<style>.pwe-element-auto-switch.guests{display:none;}</style>';
                return;
            }

            /* ---------------- GROUPING ---------------- */

            $guests_by_tab = [];

            foreach ($guests_raw as $row) {

                $guest = [
                    'id'    => $row['slug'],
                    'name'  => $row['name'] ?? '',
                    'img'   => !empty($row['image']) ? 'https://cap.warsawexpo.eu' . $row['image'] : '',
                    'label' => $getLang($row['label'] ?? [], $lang),
                    'bio'   => $getLang($row['text'] ?? [], $lang),
                    'order' => (int)($row['order'] ?? 0),
                    'tabs'  => $row['tabs'] ?? [],
                ];

                if (empty($guest['tabs'])) {
                    $guests_by_tab['all'][] = $guest;
                    continue;
                }

                foreach ($guest['tabs'] as $tabSlug) {
                    $guests_by_tab[$tabSlug][] = $guest;
                }
            }

            // deduplicate + sort
            foreach ($guests_by_tab as $slug => &$list) {

                $unique = [];

                foreach ($list as $g) {
                    $unique[$g['id']] = $g;
                }

                $list = array_values($unique);

                usort($list, fn($a, $b) => $a['order'] <=> $b['order']);
            }

            unset($list);

            // fallback: no tabs at all
            if (empty($guests_by_tab)) {
                $guests_by_tab['all'] = $guests_raw;

                foreach ($guests_by_tab['all'] as &$g) {
                    $g['img'] = !empty($g['image']) ? 'https://cap.warsawexpo.eu' . $g['image'] : '';
                    $g['label'] = $getLang($g['label'] ?? [], $lang);
                    $g['bio'] = $getLang($g['text'] ?? [], $lang);
                }
            }

            // settings
            $title = $settings['title_' . $lang]
                ?? $settings['title_en']
                ?? $settings['title_pl']
                ?? '';

            $subtitle = $settings['subtitle_' . $lang]
                ?? $settings['subtitle_en']
                ?? $settings['subtitle_pl']
                ?? '';

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}
