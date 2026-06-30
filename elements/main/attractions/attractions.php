<?php
if (!defined('ABSPATH')) exit;

class Attractions {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'b2c-new' => plugin_dir_path(__FILE__) . 'presets/b2c-new/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(__CLASS__);

        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        PWE_Functions::assets_per_element($element_slug, $element_type);
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $current_domain = $_SERVER['HTTP_HOST'];
            $atractions = PWE_Functions::get_database_fairs_data_attractions($current_domain);

            if (empty($atractions)) {
                echo '<style>#pweAttractions {display:none;}</style>';
                return;
            }

            $lang = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : 'en';

            $slides = [];
            $counter = 1;
            foreach ($atractions as $atraction) {
                $data = json_decode($atraction->data, true);
                if (empty($data)) continue;

                $name = !empty($data['name'][$lang]) ? $data['name'][$lang] : ($data['name']['en'] ?? '');
                $desc = !empty($data['short_desc'][$lang]) ? $data['short_desc'][$lang] : ($data['short_desc']['en'] ?? '');

                // Obsługa relatywnych ścieżek dla obrazka głównego
                $image_url = $data['image'] ?? '';
                if (!empty($image_url) && strpos($image_url, 'http') !== 0) {
                    $image_url = 'https://cap.warsawexpo.eu/' . ltrim($image_url, '/');
                }

                // Obsługa relatywnych ścieżek dla logo (na wypadek, gdyby tam też tak było)
                $logo_url = $data['logo'] ?? '';
                if (!empty($logo_url) && strpos($logo_url, 'http') !== 0) {
                    $logo_url = 'https://cap.warsawexpo.eu/' . ltrim($logo_url, '/');
                }

                $slides[] = [
                    'id'    => str_pad($counter, 2, "0", STR_PAD_LEFT),
                    'name'  => $name,
                    'desc'  => nl2br(esc_html($desc)),
                    'img'   => $image_url,
                    'logo'  => $logo_url
                ];
                $counter++;
            }

            if (empty($slides)) {
                echo '<style>#pweAttractions {display:none;}</style>';
                return;
            }

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}