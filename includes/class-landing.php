<?php
if (!defined('ABSPATH')) exit;

class PWE_Landing_Auto_Switch {
    const SHORTCODE = 'pwe-landing-auto-switch';

    public static function init() {
        add_shortcode(self::SHORTCODE, [__CLASS__, 'render']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'maybe_enqueue_assets']);
        add_action('wp', [__CLASS__, 'mark_landing_request']);
        add_filter('body_class', [__CLASS__, 'body_class']);

        add_action('vc_before_init', function () {
            if (!function_exists('vc_map')) return;
            vc_map([
                'name' => __('PWE Landing AutoSwitch', 'pwe-elements-auto-switch'),
                'base' => self::SHORTCODE,
                'category' => __('PWE Page Auto Switch', 'pwe-elements-auto-switch'),
                'icon' => 'icon-wpb-layer-shape',
                'params' => [],
            ]);
        });
    }


    public static function is_landing_request() {
        global $post;
        return ($post instanceof WP_Post) && has_shortcode((string)$post->post_content, self::SHORTCODE);
    }

    public static function mark_landing_request() {
        if (self::is_landing_request() && !defined('PWE_LANDING_ACTIVE')) {
            define('PWE_LANDING_ACTIVE', true);
        }
    }

    public static function body_class($classes) {
        if (self::is_landing_request()) $classes[] = 'pwe-landing-active';
        return $classes;
    }

    public static function maybe_enqueue_assets() {
        if (is_admin()) return;

        global $post;
        if (!($post instanceof WP_Post) || !has_shortcode((string) $post->post_content, self::SHORTCODE)) {
            return;
        }

        $domain = self::current_domain();
        $template_dir = PWE_PLUGIN_PATH . 'landing/' . $domain;
        if (is_file($template_dir . '/landing.php')) {
            self::enqueue_assets($domain, $template_dir);
        }
    }

    public static function render($atts = []) {
        $domain = self::current_domain();
        $template_dir = PWE_PLUGIN_PATH . 'landing/' . $domain;
        $template_file = $template_dir . '/landing.php';

        if (!is_file($template_file)) {
            return current_user_can('manage_options')
                ? '<!-- PWE Landing AutoSwitch: missing template for ' . esc_html($domain) . ' -->'
                : '';
        }

        self::enqueue_assets($domain, $template_dir);

        $context = [
            'domain' => $domain,
            'lang' => self::current_language(),
            'doc_url' => self::doc_url(),
            'language_urls' => self::language_urls(),
            'atts' => is_array($atts) ? $atts : [],
        ];

        ob_start();
        $pwe_landing = $context;
        include $template_file;
        return ob_get_clean();
    }

    public static function current_domain() {
        $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? parse_url(home_url('/'), PHP_URL_HOST)));
        $host = preg_replace('/:\d+$/', '', $host);
        return preg_replace('/^www\./', '', $host);
    }

    public static function current_language() {
        $lang = apply_filters('wpml_current_language', null);
        if (!$lang && defined('ICL_LANGUAGE_CODE')) {
            $lang = ICL_LANGUAGE_CODE;
        }
        $lang = strtolower((string) $lang);
        return $lang === 'pl' ? 'pl' : 'en';
    }

    public static function doc_url($path = '') {
        $base = untrailingslashit(home_url('/doc/'));
        if ($path === '') return $base . '/';
        return $base . '/' . ltrim($path, '/');
    }

    public static function language_urls() {
        $urls = [
            'en' => home_url('/'),
            'pl' => home_url('/pl/'),
        ];

        $languages = apply_filters('wpml_active_languages', null, [
            'skip_missing' => 0,
            'orderby' => 'code',
        ]);

        if (is_array($languages)) {
            foreach (['en', 'pl'] as $code) {
                if (!empty($languages[$code]['url'])) {
                    $urls[$code] = $languages[$code]['url'];
                }
            }
        }

        return $urls;
    }

    private static function enqueue_assets($domain, $template_dir) {
        $asset_base = PWE_PLUGIN_PATH . 'landing/' . $domain . '/assets/';
        $asset_url = plugins_url('landing/' . $domain . '/assets/', PWE_PLUGIN_FILE);

        $css = $asset_base . 'style.css';
        if (is_file($css)) {
            wp_enqueue_style('pwe-landing-' . md5($domain), $asset_url . 'style.css', [], filemtime($css));
        }

        $js = $asset_base . 'script.js';
        if (is_file($js)) {
            wp_enqueue_script('pwe-landing-' . md5($domain), $asset_url . 'script.js', [], filemtime($js), true);
        }
    }
}
