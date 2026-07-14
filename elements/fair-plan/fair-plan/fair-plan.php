<?php
if (!defined('ABSPATH')) exit;

class Fair_Plan {

    public static function get_data() {
        return [
            'types' => ['fair-plan'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    private static function set_featured_image_by_url($post_id, $image_path) {
        if (!$post_id || empty($image_path)) {
            return false;
        }

        $relative_url = '/' . ltrim($image_path, '/');
        $full_url     = home_url($relative_url);

        // 1. Szukaj po pełnym URL
        $attachment_id = attachment_url_to_postid($full_url);

        // 2. Szukaj po metadanych załącznika
        if (!$attachment_id) {
            $existing = get_posts([
                'post_type'      => 'attachment',
                'post_status'    => 'inherit',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_query'     => [
                    'relation' => 'OR',
                    [
                        'key'     => '_wp_attached_file',
                        'value'   => $relative_url,
                        'compare' => '=',
                    ],
                    [
                        'key'     => '_wp_attached_file',
                        'value'   => ltrim($relative_url, '/'),
                        'compare' => '=',
                    ],
                    [
                        'key'     => '_source_url',
                        'value'   => $relative_url,
                        'compare' => '=',
                    ],
                    [
                        'key'     => '_source_url',
                        'value'   => $full_url,
                        'compare' => '=',
                    ],
                ],
            ]);

            if (!empty($existing)) {
                $attachment_id = (int) $existing[0];
            }
        }

        // 3. Jeśli nie istnieje — dopiero wtedy importuj
        if (!$attachment_id) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';

            $attachment_id = media_sideload_image($full_url, 0, null, 'id');

            if (!is_wp_error($attachment_id) && $attachment_id) {
                update_post_meta($attachment_id, '_source_url', $relative_url);
            }
        }

        if (!is_wp_error($attachment_id) && $attachment_id) {
            return set_post_thumbnail($post_id, (int) $attachment_id);
        }

        return false;
    }

    /**
     * Szuka wpisu po metadanych, również w koszu.
     * Dla starych wpisów bez metadanych używa slugu.
     */
    private static function find_fair_plan_post($year, $language, $slug) {
        $posts = get_posts([
            'post_type'        => 'post',
            'post_status'      => [
                'publish',
                'draft',
                'pending',
                'private',
                'future',
                'trash',
            ],
            'posts_per_page'   => 1,
            'suppress_filters' => true,
            'meta_query'       => [
                'relation' => 'AND',
                [
                    'key'     => '_pwe_auto_fair_plan',
                    'value'   => '1',
                    'compare' => '=',
                ],
                [
                    'key'     => '_pwe_fair_plan_year',
                    'value'   => (int) $year,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ],
                [
                    'key'     => '_pwe_fair_plan_lang',
                    'value'   => $language,
                    'compare' => '=',
                ],
            ],
        ]);

        if (!empty($posts)) {
            return $posts[0];
        }

        // Fallback dla wcześniej utworzonych wpisów bez metadanych
        return get_page_by_path($slug, OBJECT, 'post');
    }

    /**
     * Przenosi do kosza wpisy, których planów nie ma już w bazie
     * albo zostały oznaczone jako nieaktywne.
     */
    private static function remove_news_without_active_plan($active_years) {
        $active_years = array_unique(array_map('intval', $active_years));

        $posts = get_posts([
            'post_type'        => 'post',
            'post_status'      => [
                'publish',
                'draft',
                'pending',
                'private',
                'future',
            ],
            'posts_per_page'   => -1,
            'fields'           => 'ids',
            'suppress_filters' => true,
            'meta_key'         => '_pwe_auto_fair_plan',
            'meta_value'       => '1',
        ]);

        foreach ($posts as $post_id) {
            $year = (int) get_post_meta(
                $post_id,
                '_pwe_fair_plan_year',
                true
            );

            if (!$year || !in_array($year, $active_years, true)) {
                wp_trash_post($post_id);
            }
        }
    }

    public static function pwe_create_fair_plan_news($year) {
        $year = (int) $year;

        if (!$year) {
            return false;
        }

        $post_type = 'post';

        $pl_slug = 'plan-targow-' . $year;
        $en_slug = 'fair-plan-' . $year;

        $news_cat = get_category_by_slug('news');
        $news_cat_id = $news_cat ? (int) $news_cat->term_id : 0;

        $pl_post = self::find_fair_plan_post($year, 'pl', $pl_slug);
        $en_post = self::find_fair_plan_post($year, 'en', $en_slug);

        /*
         * Polski wpis
         */
        if (!$pl_post) {
            $pl_post_id = wp_insert_post([
                'post_title'    => 'Plan targów ' . $year,
                'post_name'     => $pl_slug,
                'post_content'  => '[pwe-elements-auto-switch-page-fair-plan]',
                'post_status'   => 'publish',
                'post_type'     => $post_type,
                'post_category' => $news_cat_id ? [$news_cat_id] : [],
                'meta_input'    => [
                    '_pwe_auto_fair_plan' => '1',
                    '_pwe_fair_plan_year' => $year,
                    '_pwe_fair_plan_lang' => 'pl',
                ],
            ], true);

            if (is_wp_error($pl_post_id) || !$pl_post_id) {
                return false;
            }
        } else {
            $pl_post_id = (int) $pl_post->ID;

            if ($pl_post->post_status === 'trash') {
                if (!wp_untrash_post($pl_post_id)) {
                    return false;
                }
            }

            $result = wp_update_post([
                'ID'           => $pl_post_id,
                'post_title'   => 'Plan targów ' . $year,
                'post_name'    => $pl_slug,
                'post_content' => '[pwe-elements-auto-switch-page-fair-plan]',
                'post_status'  => 'publish',
            ], true);

            if (is_wp_error($result)) {
                return false;
            }

            update_post_meta($pl_post_id, '_pwe_auto_fair_plan', '1');
            update_post_meta($pl_post_id, '_pwe_fair_plan_year', $year);
            update_post_meta($pl_post_id, '_pwe_fair_plan_lang', 'pl');

            if ($news_cat_id) {
                wp_set_post_categories($pl_post_id, [$news_cat_id]);
            }
        }

        self::set_featured_image_by_url(
            $pl_post_id,
            '/doc/plan.webp'
        );

        /*
         * Angielski wpis
         */
        if (!$en_post) {
            $en_post_id = wp_insert_post([
                'post_title'    => 'Fair plan ' . $year,
                'post_name'     => $en_slug,
                'post_content'  => '[pwe-elements-auto-switch-page-fair-plan]',
                'post_status'   => 'publish',
                'post_type'     => $post_type,
                'post_category' => $news_cat_id ? [$news_cat_id] : [],
                'meta_input'    => [
                    '_pwe_auto_fair_plan' => '1',
                    '_pwe_fair_plan_year' => $year,
                    '_pwe_fair_plan_lang' => 'en',
                ],
            ], true);

            if (is_wp_error($en_post_id) || !$en_post_id) {
                return false;
            }
        } else {
            $en_post_id = (int) $en_post->ID;

            if ($en_post->post_status === 'trash') {
                if (!wp_untrash_post($en_post_id)) {
                    return false;
                }
            }

            $result = wp_update_post([
                'ID'           => $en_post_id,
                'post_title'   => 'Fair plan ' . $year,
                'post_name'    => $en_slug,
                'post_content' => '[pwe-elements-auto-switch-page-fair-plan]',
                'post_status'  => 'publish',
            ], true);

            if (is_wp_error($result)) {
                return false;
            }

            update_post_meta($en_post_id, '_pwe_auto_fair_plan', '1');
            update_post_meta($en_post_id, '_pwe_fair_plan_year', $year);
            update_post_meta($en_post_id, '_pwe_fair_plan_lang', 'en');

            if ($news_cat_id) {
                wp_set_post_categories($en_post_id, [$news_cat_id]);
            }
        }

        self::set_featured_image_by_url(
            $en_post_id,
            '/doc/plan-en.webp'
        );

        $pl_post_id = (int) $pl_post_id;
        $en_post_id = (int) $en_post_id;

        if (!$pl_post_id || !$en_post_id) {
            return false;
        }

        /*
         * Połączenie wersji językowych przez WPML
         */
        if (function_exists('icl_object_id')) {
            $element_type = 'post_' . $post_type;

            $trid = apply_filters(
                'wpml_element_trid',
                null,
                $pl_post_id,
                $element_type
            );

            do_action('wpml_set_element_language_details', [
                'element_id'           => $pl_post_id,
                'element_type'         => $element_type,
                'trid'                 => $trid,
                'language_code'        => 'pl',
                'source_language_code' => null,
            ]);

            $trid = apply_filters(
                'wpml_element_trid',
                null,
                $pl_post_id,
                $element_type
            );

            do_action('wpml_set_element_language_details', [
                'element_id'           => $en_post_id,
                'element_type'         => $element_type,
                'trid'                 => $trid,
                'language_code'        => 'en',
                'source_language_code' => 'pl',
            ]);
        }

        return [
            'pl' => $pl_post_id,
            'en' => $en_post_id,
        ];
    }

    public static function create_missing_news_for_files($files) {
        if (!is_array($files) || empty($files)) {
            return false;
        }

        $years = [];

        foreach ($files as $file) {
            if (
                ($file->category_slug ?? '') === 'trade-fair-plan'
                && ($file->is_active ?? '') == '1'
                && !empty($file->year)
            ) {
                $years[] = (int) $file->year;
            }
        }

        $years = array_values(
            array_unique(
                array_filter($years)
            )
        );

        /*
         * Tworzenie albo przywracanie aktualnych wpisów.
         */
        foreach ($years as $year) {
            self::pwe_create_fair_plan_news($year);
        }

        /*
         * Usuwanie wpisów planów, których nie ma już w bazie.
         */
        self::remove_news_without_active_plan($years);

        return true;
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

            $base_url = 'https://cap.warsawexpo.eu';

            $build_file_url = function ($path) use ($base_url) {
                if (empty($path)) {
                    return '';
                }

                if (preg_match('~^https?://~', $path)) {
                    return $path;
                }

                return rtrim($base_url, '/') . '/' . ltrim($path, '/');
            };

            $files = PWE_Functions::get_database_fairs_data_files();

            if (!is_array($files)) {
                $files = [];
            }

            $uri = trim(
                parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
                '/'
            );

            // Domyślnie brak roku
            $selected_year = null;

            // Archiwum PL i EN
            if (
                preg_match(
                    '~(?:plan-targow|fair-plan)-(\d{4})/?$~',
                    $uri,
                    $matches
                )
            ) {
                $selected_year = (int) $matches[1];
            } else {
                // Aktualna strona - wybierz najnowszy rok
                $years = [];

                foreach ($files as $file) {
                    if (
                        ($file->category_slug ?? '') === 'trade-fair-plan'
                        && ($file->is_active ?? '') == '1'
                    ) {
                        $years[] = (int) $file->year;
                    }
                }

                if (!empty($years)) {
                    $selected_year = max($years);
                }
            }

            $plan_imgs = [];
            $plan_pdf = '';

            foreach ($files as $file) {
                if (
                    ($file->category_slug ?? '') !== 'trade-fair-plan'
                    || (int) ($file->year ?? 0) !== $selected_year
                    || ($file->is_active ?? '') != '1'
                ) {
                    continue;
                }

                if (!empty($file->gallery_files)) {
                    $gallery_files = json_decode($file->gallery_files);

                    if (is_array($gallery_files)) {
                        foreach ($gallery_files as $gallery_file) {
                            if (!empty($gallery_file->file_path)) {
                                $plan_imgs[] = $build_file_url(
                                    $gallery_file->file_path
                                );
                            }
                        }
                    }
                }

                if (
                    !empty($file->file_type)
                    && strtolower($file->file_type) === 'pdf'
                ) {
                    $plan_pdf = $build_file_url($file->file_path);
                }
            }

            $plan_imgs = array_filter(
                array_unique($plan_imgs)
            );

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo $output;
            }
        }
    }
}