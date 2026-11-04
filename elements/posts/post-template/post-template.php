<?php
if (!defined('ABSPATH')) exit;

class Post_Template {

    public static function get_data() {
        return [
            'types' => ['posts'],
            'presets' => [
                'summary' => plugin_dir_path(__FILE__) . 'presets/summary/preset.php',
                'preview' => plugin_dir_path(__FILE__) . 'presets/preview/preset.php',
                'medal-ceremony' => plugin_dir_path(__FILE__) . 'presets/medal-ceremony/preset.php',
            ],
            'templates' => ['summary', 'preview', 'medal-ceremony'],
        ];
    }

    /** Read the native array saved by api/news/templates.php for this post. */
    public static function load_data($template, $post_id = 0) {
        if (!is_string($template) || !in_array($template, self::get_data()['templates'], true)) {
            return [];
        }

        $document = get_post_meta($post_id ?: get_the_ID(), '_pwe_post_data', true);
        if (!is_array($document)) {
            return [];
        }

        $stored_template = $document['template'] ?? '';
        if ($stored_template === 'summary_v2') {
            $stored_template = 'summary';
        }

        $news = $document['data'] ?? null;
        if (
            $stored_template !== $template ||
            !is_array($news) ||
            !is_string($news['title'] ?? null) ||
            trim($news['title']) === ''
        ) {
            return [];
        }

        // The envelope selects the preset; normalize legacy summary data as well.
        $news['type'] = $template;
        return $news;
    }

    public static function render($group = '', $params = [], $atts = []) {
        $options = array_merge(is_array($params) ? $params : [], is_array($atts) ? $atts : []);
        $post_id = get_the_ID();
        $document = get_post_meta($post_id, '_pwe_post_data', true);
        $template = $options['template'] ?? (is_array($document) ? ($document['template'] ?? '') : '');

        if ($template === '') {
            $template = get_post_meta($post_id, '_pwe_post_template', true);
        }

        if ($template === 'summary_v2') {
            $template = 'summary';
        }

        $news = self::load_data($template, $post_id);
        if (!$news) {
            return;
        }

        PWE_Functions::set_translation_context('post-template', $template, 'posts');
        PWE_Functions::assets_per_element('post-template', 'posts');
        PWE_Functions::assets_per_group('post-template', $template, 'posts');

        // API text must not execute shortcodes.
        $output = include self::get_data()['presets'][$template];

        if (is_string($output)) {
            echo $output;
        }
    }
}
