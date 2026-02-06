<?php
if (!defined('ABSPATH')) exit;

define('EX_PATH', plugin_dir_path(__FILE__));
define('EX_URL', plugin_dir_url(__FILE__));

class Exhibitor_Catalog {

    public static function get_data() {
            return [
                'types'   => ['catalog'],
                'presets' => [],
            ];
        }

    public static function get_info() {
        return [
            'type' => 'catalog',
            'slug' => strtolower(__CLASS__),
        ];
    }

    public static function render($group, $params, $atts) {

        self::enqueue_assets();
        self::enqueue_feedback_assets();
        self::inject_config($atts);

        echo '
        <style>
            .catalog__loading {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                gap: 24px;
            }
            .catalog__loading-spinner {
                --uib-size: 60px;
                --uib-speed: .9s;
                position: relative;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                height: var(--uib-size);
                width: var(--uib-size);
            }

            .spinner__dot {
                position: absolute;
                top: 0;
                left: 0;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                height: 100%;
                width: 100%;
            }

            .spinner__dot::before {
                content: "";
                height: 20%;
                width: 20%;
                border-radius: 50%;
                background-color: var(--main2-color);
                transform: scale(0);
                opacity: 0.5;
                animation: pulse0112 calc(var(--uib-speed) * 1.111) ease-in-out infinite;
                box-shadow: 0 0 20px rgba(18, 31, 53, 0.3);
            }

            .spinner__dot:nth-child(2) {
                transform: rotate(45deg);
            }

            .spinner__dot:nth-child(2)::before {
                animation-delay: calc(var(--uib-speed) * -0.875);
            }

            .spinner__dot:nth-child(3) {
                transform: rotate(90deg);
            }

            .spinner__dot:nth-child(3)::before {
                animation-delay: calc(var(--uib-speed) * -0.75);
            }

            .spinner__dot:nth-child(4) {
                transform: rotate(135deg);
            }

            .spinner__dot:nth-child(4)::before {
                animation-delay: calc(var(--uib-speed) * -0.625);
            }

            .spinner__dot:nth-child(5) {
                transform: rotate(180deg);
            }

            .spinner__dot:nth-child(5)::before {
                animation-delay: calc(var(--uib-speed) * -0.5);
            }

            .spinner__dot:nth-child(6) {
                transform: rotate(225deg);
            }

            .spinner__dot:nth-child(6)::before {
                animation-delay: calc(var(--uib-speed) * -0.375);
            }

            .spinner__dot:nth-child(7) {
                transform: rotate(270deg);
            }

            .spinner__dot:nth-child(7)::before {
                animation-delay: calc(var(--uib-speed) * -0.25);
            }

            .spinner__dot:nth-child(8) {
                transform: rotate(315deg);
            }

            .spinner__dot:nth-child(8)::before {
                animation-delay: calc(var(--uib-speed) * -0.125);
            }

            @keyframes pulse0112 {
            0%,
            100% {
                transform: scale(0);
                opacity: 0.5;
            }

            50% {
                transform: scale(1);
                opacity: 1;
            }
            }
        </style>
        <div class="catalog__loading">
            <h2 class="catalog__loading-title">Katalog Wystawców</h2>
            <div class="catalog__loading-spinner">
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
                <div class="spinner__dot"></div>
            </div>
        </div>
        <div id="vue-catalog" style="min-height: 130vh;"></div>';

        $feedback = EX_PATH . 'addons/feedback/feedback.php';
        if (file_exists($feedback)) {
            require_once $feedback;
        }

        $translates = EX_PATH . 'addons/translates.php';
        if (file_exists($translates)) {
            require_once $translates;
        }
    }

    private static function enqueue_assets() {

        $manifest_path = EX_PATH . 'dist/.vite/manifest.json';
        if (!file_exists($manifest_path)) {
            return;
        }

        $manifest = json_decode(file_get_contents($manifest_path), true);
        if (!isset($manifest['src/main.js'])) {
            return;
        }

        $entry = $manifest['src/main.js'];

        // CSS
        if (!empty($entry['css'][0])) {
            wp_enqueue_style(
                'vue-catalog',
                EX_URL . 'dist/' . $entry['css'][0],
                [],
                null
            );
        }

        // JS
        wp_enqueue_script(
            'vue-catalog',
            EX_URL . 'dist/' . $entry['file'],
            [],
            null,
            true
        );
    }

    private static function enqueue_feedback_assets() {

        $base_path = EX_PATH . 'addons/feedback/';
        $base_url  = EX_URL  . 'addons/feedback/';

        if (file_exists($base_path . 'feedback-style.css')) {
            wp_enqueue_style(
                'exhibitor-feedback-style',
                $base_url . 'feedback-style.css',
                [],
                filemtime($base_path . 'feedback-style.css')
            );
        }

        if (file_exists($base_path . 'feedback-script.js')) {
            wp_enqueue_script(
                'exhibitor-feedback-script',
                $base_url . 'feedback-script.js',
                [],
                filemtime($base_path . 'feedback-script.js'),
                true
            );

            wp_localize_script(
                'exhibitor-feedback-script',
                'EX_FEEDBACK_CONFIG',
                [
                    'source'  => 'catalog',
                    'version' => self::get_plugin_version(),
                ]
            );
        }
    }

    private static function get_plugin_version() {
        static $version = null;

        if ($version === null) {
            $data = get_file_data(PWE_PLUGIN_FILE, ['Version' => 'Version']);
            $version = $data['Version'] ?: 'unknown';
        }

        return $version;
    }

    private static function inject_config($atts) {        
        wp_add_inline_script(
            'vue-catalog',
            'window.VUE_CATALOG_CONFIG = ' . json_encode([
                'dataUrl' => site_url('/doc/pwe-exhibitors-data.json'),
                'atts'    => $atts,
                'locale'  => get_locale(),
            ]) . ';',
            'before'
        );
    }
}
