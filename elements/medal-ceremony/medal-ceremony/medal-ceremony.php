<?php
if (!defined('ABSPATH')) exit;

class Medal_Ceremony {

    public static function get_data() {
        return [
            'types' => ['medal-ceremony'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        $group = 'all';

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $form_id = PWE_Functions::get_gf_form_id('Ceremonia medalowa');

            $files = PWE_Functions::get_database_fairs_data_files();

            $language = PWE_Functions::lang_pl() ? 'pl' : 'en';

            // Domyślny link, używany gdy w bazie nie ma odpowiedniego pliku.
            $ceremony_rules = $language === 'pl'
                ? 'https://warsawexpo.eu/docs/Regulamin-Konkursu-Medalowego-Ptak-Warsaw-Expo.pdf'
                : 'https://warsawexpo.eu/docs/Rules-of-the-Medal-Competition-Ptak-Warsaw-Expo.pdf';

            $ceremony_rules_files = array_filter($files, static function ($file) use ($language) {
                return isset(
                    $file->category_slug,
                    $file->language,
                    $file->file_path,
                    $file->is_active
                )
                    && $file->category_slug === 'medal-ceremony-rules'
                    && $file->language === $language
                    && $file->is_active === '1'
                    && $file->file_path !== '';
            });

            // Najnowszy rok jako pierwszy.
            usort($ceremony_rules_files, static function ($a, $b) {
                return (int) $b->year <=> (int) $a->year;
            });

            $latest_ceremony_rules = reset($ceremony_rules_files);

            if ($latest_ceremony_rules) {
                $ceremony_rules = 'https://cap.warsawexpo.eu'
                    . $latest_ceremony_rules->file_path;
            }

            $fair_adds = PWE_Functions::get_database_fairs_data_adds();
            $medal_ceremony_data = !empty($fair_adds) && isset($fair_adds[0]->medal_ceremony) ? $fair_adds[0]->medal_ceremony : '';

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }
}
