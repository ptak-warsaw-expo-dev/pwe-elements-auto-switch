<?php
if (!defined('ABSPATH')) exit;

class Posts {

    public static function get_data() {
        return [
            'types' => ['main'],
            'presets' => [
                'gr1' => plugin_dir_path(__FILE__) . 'presets/preset-gr1/preset-gr1.php',
                'gr2' => plugin_dir_path(__FILE__) . 'presets/preset-gr2/preset-gr2.php',
            ],
        ];
    }

    public static function render($group) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(__CLASS__);

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {
            
        /* <-------------> General code start <-------------> */

            $all_categories = get_categories(['hide_empty' => true]);

            $category_names = [];
            foreach ($all_categories as $category) {
                if (strpos(strtolower($category->name), 'news') !== false) {
                    $category_names[] = $category->slug;
                }
            }
            $category_name = implode(', ', $category_names);

            $args = [
                'posts_per_page' => 5,
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post_status'    => 'publish',
                'category_name'  => $category_name,
            ];

            $query = new WP_Query($args);

            $posts_items = [];
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();

                    $post_id = get_the_ID();
                    $link    = get_permalink($post_id);
                    $image   = has_post_thumbnail($post_id) ? get_the_post_thumbnail_url($post_id, 'full') : '';
                    $date    = get_the_date('d.m.Y', $post_id);

                    $title_word_count = 8;
                    $raw_title = get_the_title($post_id);
                    $title     = trim( wp_trim_words( $raw_title, $title_word_count, '...' ) );

                    $desc_word_count = 10;
                    $post_content = get_post_field('post_content', $post_id);
                    $excerpt = '';
                    $vc_content = '';

                    if (preg_match('/pwe_news_summary_desc="([^"]+)"/', $post_content, $matches)) {
                        $encoded = $matches[1];
                        $decoded = wpb_js_remove_wpautop(urldecode(base64_decode($encoded)), true);
                        $vc_content = $decoded;

                    } elseif (preg_match('/pwe_news_upcoming_desc="([^"]+)"/', $post_content, $matches)) {
                        $encoded = $matches[1];
                        $decoded = wpb_js_remove_wpautop(urldecode(base64_decode($encoded)), true);
                        $vc_content = $decoded;
                    } elseif (preg_match('/news_say_about_us_description="([^"]+)"/', $post_content, $matches)) {
                        $encoded = $matches[1];
                        $decoded = wpb_js_remove_wpautop(urldecode(base64_decode($encoded)), true);
                        $vc_content = $decoded;
                    } elseif (preg_match('/\[vc_column_text.*?\](.*?)\[\/vc_column_text\]/s', $post_content, $matches)) {
                        $vc_content = $matches[1];
                    }

                    $vc_content = wp_strip_all_tags($vc_content);

                    if (!empty($vc_content)) {
                        $words = preg_split('/\s+/', trim($vc_content));
                        $excerpt = implode(' ', array_slice($words, 0, $desc_word_count)) . '...';
                    } else {
                        $excerpt = '';
                    }

                    if (!empty($image) && !empty($link) && !empty($title)) {
                        $posts_items[] = [
                            'img'         => $image,
                            'link'        => $link,
                            'title'       => $title,
                            'excerpt'     => $excerpt,
                            'date'        => $date,
                        ];
                    }
                }
            }
            wp_reset_postdata();

            if (empty($posts_items)) {
                return;
            }

            $pwe_posts = [
                'items'     => $posts_items,
                'has_items' => !empty($posts_items),
            ];

        /* <-------------> General code end <-------------> */

            
            $output = include $preset_file;
            
            if ($output) {
                echo $output;         
            }
        }
    }
}
