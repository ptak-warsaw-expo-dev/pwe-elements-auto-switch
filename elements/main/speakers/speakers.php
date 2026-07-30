<?php
if (!defined('ABSPATH')) exit;

class Speakers {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/gr1/preset.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/gr2/preset.php',
                'week' => plugin_dir_path(__FILE__) . 'presets/week/preset.php',
            ],
        ];
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        PWE_Functions::assets_per_element($element_slug, $element_type);
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

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

            ksort($speakers_indexed);

            $speakers = [];

            foreach ($speakers_indexed as $order => $items) {
                if ($order != 99) {
                    foreach ($items as $op) {
                        $speakers[] = $op;
                    }
                }
            }

            if (!empty($speakers_indexed[99])) {
                foreach ($speakers_indexed[99] as $op) {
                    $speakers[] = $op;
                }
            }

            self::create_speakers_pages($speakers);

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }

    private static function create_speakers_pages($speakers) {

        if (count($speakers) <= 6) {
            return;
        }

        if (!defined('ICL_SITEPRESS_VERSION')) {
            return;
        }

        $content = '[pwe-elements-auto-switch-page-speakers]';

        $pl_page_id = self::find_or_create_speakers_page(
            'Prelegenci',
            'prelegenci',
            $content
        );

        if (!$pl_page_id) {
            return;
        }

        self::assign_wpml_language(
            $pl_page_id,
            'pl',
            null,
            null
        );

        $element_type = apply_filters(
            'wpml_element_type',
            'page'
        );

        $pl_language_details = apply_filters(
            'wpml_element_language_details',
            null,
            [
                'element_id'   => $pl_page_id,
                'element_type' => $element_type,
            ]
        );

        if (!is_object($pl_language_details) || empty($pl_language_details->trid)) {
            error_log('Nie udało się pobrać WPML trid dla strony Prelegenci.');
            return;
        }

        $translation_group_id = (int) $pl_language_details->trid;

        $en_page_id = apply_filters(
            'wpml_object_id',
            $pl_page_id,
            'page',
            false,
            'en'
        );

        if (!$en_page_id || (int) $en_page_id === $pl_page_id) {
            $en_page_id = self::find_or_create_speakers_page(
                'Speakers',
                'speakers',
                $content
            );
        }

        if (!$en_page_id) {
            return;
        }

        self::assign_wpml_language((int) $en_page_id, 'en', 'pl', $translation_group_id);
    }

    private static function find_or_create_speakers_page(
        string $title,
        string $slug,
        string $content
    ): int {
        $existing_page_id = self::find_page_by_slug($slug);

        if ($existing_page_id) {
            $existing_content = get_post_field(
                'post_content',
                $existing_page_id
            );

            if (trim((string) $existing_content) === '') {
                $updated_page_id = wp_update_post(
                    [
                        'ID'           => $existing_page_id,
                        'post_content' => $content,
                    ],
                    true
                );

                if (is_wp_error($updated_page_id)) {
                    error_log(
                        sprintf(
                            'Nie udało się zaktualizować strony "%s": %s',
                            $title,
                            $updated_page_id->get_error_message()
                        )
                    );
                }
            }

            return $existing_page_id;
        }

        $page_id = wp_insert_post(
            [
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'post_title'     => $title,
                'post_name'      => $slug,
                'post_content'   => $content,
                'comment_status' => 'closed',
                'ping_status'    => 'closed',
            ],
            true
        );

        if (is_wp_error($page_id)) {
            error_log(
                sprintf(
                    'Nie udało się utworzyć strony "%s": %s',
                    $title,
                    $page_id->get_error_message()
                )
            );

            return 0;
        }

        return (int) $page_id;
    }

    private static function find_page_by_slug($slug) {
        $pages = get_posts(
            [
                'name'             => $slug,
                'post_type'        => 'page',
                'post_status'      => [
                    'publish',
                    'draft',
                    'pending',
                    'private',
                    'future',
                ],
                'posts_per_page'   => 1,
                'fields'           => 'ids',
                'suppress_filters' => true,
                'no_found_rows'    => true,
            ]
        );

        if (empty($pages)) {
            return 0;
        }

        return (int) $pages[0];
    }

    private static function assign_wpml_language(
        int $page_id,
        string $language_code,
        ?string $source_language_code = null,
        ?int $translation_group_id = null
    ): void {
        $element_type = apply_filters(
            'wpml_element_type',
            'page'
        );

        $current_details = apply_filters(
            'wpml_element_language_details',
            null,
            [
                'element_id'   => $page_id,
                'element_type' => $element_type,
            ]
        );

        if (
            is_object($current_details) &&
            $current_details->language_code === $language_code &&
            (
                $translation_group_id === null ||
                (int) $current_details->trid === $translation_group_id
            )
        ) {
            return;
        }

        do_action(
            'wpml_set_element_language_details',
            [
                'element_id'           => $page_id,
                'element_type'         => $element_type,
                'trid'                 => $translation_group_id ?: false,
                'language_code'        => $language_code,
                'source_language_code' => $source_language_code,
                'check_duplicates'     => true,
            ]
        );
    }
}