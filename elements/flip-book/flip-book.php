<?php
/**
 * Plugin Name: PWE Flipbook
 * Description: Flipbook PDF in WordPress (render PDF -> canvas + animation of flipping pages). Integration of PDF.js + StPageFlip.
 * Version: 1.9.2
 * Author: Patryk Chodorowski
 */

if (!defined('ABSPATH')) exit;

class Flip_Book {

    public static function init() {
        add_action('wp_enqueue_scripts', [self::class, 'register_assets']);
        add_filter('rocket_delay_js_exclusions', [self::class, 'pwe_exclude_from_wp_rocket']);
        add_filter('script_loader_tag', [self::class, 'pwe_add_attributes'], 10, 2);
    }

    public static function get_data() {
        return ['types' => ['flip-book']];
    }

    // Registers JavaScript libraries.
    public static function register_assets() {
        wp_enqueue_script('pwe-page-flip-browser-js', plugin_dir_url(__FILE__) . 'assets/page-flip.browser.js', [], '2.0.0', true);
    }

    // Prevents WP Rocket from delaying execution of Flipbook scripts.
    // If these are delayed, the PDF worker might fail to initialize.
    public static function pwe_exclude_from_wp_rocket($patterns) {
        $patterns[] = 'page-flip.browser.js';
        $patterns[] = 'pwe-flip-book-script';
        $patterns[] = 'pwe-page-flip-browser-js';
        $patterns[] = 'pdf.js';
        $patterns[] = 'pdf.worker.js';
        return $patterns;
    }

    // Adds 'data-no-optimize' attributes for other optimization plugins (Litespeed, Autoptimize).
    public static function pwe_add_attributes($tag, $handle) {
        if (in_array($handle, ['pwe-flip-book-script', 'pwe-page-flip-browser-js'])) {
            $tag = str_replace(' src', ' data-no-optimize="1" data-no-defer="1" data-rocket-defer="false" src', $tag);
        }
        return $tag;
    }

    // Main Render Method
    public static function render($group = '', $params = [], $atts = []) {
        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = strtolower(str_replace('_', '-', __CLASS__));

        // Global assets
        PWE_Functions::assets_per_element($element_slug, '');

        $pdf_url = $atts['pdf_url'];

        // Validation
        if (empty($pdf_url)) {
            echo '<div class="pwe-flip-book-error">PWE Flipbook Error: Missing PDF URL.</div>';
            return;
        }

        // Unique ID for this instance
        $id = 'pwe-flip-book-' . wp_generate_uuid4();

        // Pass Configuration to JS
        // Note: Using standard .js files instead of .mjs to avoid MIME type issues on some servers.
        $config = [
            'id' => $id,
            'pdf' => esc_url_raw($pdf_url),
            'start' => 1,
            'embed' => false,
            'pdfModuleSrc' => plugin_dir_url(__FILE__) . 'assets/pdf.js',
            'workerSrc'    => plugin_dir_url(__FILE__) . 'assets/pdf.worker.js',
        ];
        
        wp_add_inline_script('pwe-flip-book-script', 'window.PWE_FLIPBOOK = window.PWE_FLIPBOOK || []; window.PWE_FLIPBOOK.push(' . wp_json_encode($config) . ');', 'before');

        $output = '
        <div id="'. $id .'" class="pwe-flip-book-root">
            <button type="button" class="pwe-nav-arrow pwe-nav-prev" data-nav="prev" aria-label="Poprzednia">&#10094;</button>
            <button type="button" class="pwe-nav-arrow pwe-nav-next" data-nav="next" aria-label="Następna">&#10095;</button>
            
            <div class="pwe-flip-book-stage"></div>
            
            <div class="pwe-flip-book-toolbar" style="display:flex;gap:8px;align-items:center; justify-content:center;margin:8px 0; color:#666; font-size:14px;">
                <span data-role="pageinfo">Loading...</span>
            </div>
        </div>';

        echo $output;

    }
}

Flip_Book::init();