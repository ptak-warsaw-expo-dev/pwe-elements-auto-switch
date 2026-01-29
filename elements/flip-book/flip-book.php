<?php

if (!defined('ABSPATH')) exit;

class PWE_Flipbook {
    public function __construct() {
        add_shortcode('pwe_flipbook', [$this, 'shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'register_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // Bypass WP Rocket Delay JS
        add_filter('rocket_delay_js_exclusions', [$this, 'pwe_exclude_from_wp_rocket']);
        add_filter('script_loader_tag', [$this, 'pwe_add_attributes'], 10, 2);
    }

    public function register_assets() {
        $base = plugin_dir_url(__FILE__);

        // StPageFlip
        wp_register_script(
            'pwe-stpageflip',
            $base . 'assets/js/vendor/stpageflip/page-flip.browser.js',
            [],
            '2.0.0',
            true
        );
        // Viewer script
        wp_register_script(
            'pwe-flipbook-viewer',
            $base . 'assets/js/viewer.js',
            ['pwe-stpageflip'],
            '1.9.1', 
            true 
        );
    }

    public function enqueue_assets() {
        wp_enqueue_script('pwe-stpageflip');
        wp_enqueue_script('pwe-flipbook-viewer');
    }

    public function pwe_exclude_from_wp_rocket($patterns) {
        $patterns[] = 'viewer.js';
        $patterns[] = 'page-flip.browser.js';
        $patterns[] = 'pwe-flipbook-viewer';
        $patterns[] = 'pwe-stpageflip';
        $patterns[] = 'pdf.js';        // Dodano
        $patterns[] = 'pdf.worker.js'; // Dodano
        return $patterns;
    }

    public function pwe_add_attributes($tag, $handle) {
        if (in_array($handle, ['pwe-flipbook-viewer', 'pwe-stpageflip'])) {
            $tag = str_replace(' src', ' data-no-optimize="1" data-no-defer="1" data-rocket-defer="false" src', $tag);
        }
        return $tag;
    }

    // Metoda renderująca dla class-elements.php
    public static function render($group = '', $params = [], $atts = []) {
        $pdf_url = '';

        // 1. Sprawdzamy parametry z bazy/konfiguracji
        if (!empty($params['pdf_url'])) $pdf_url = $params['pdf_url'];
        elseif (!empty($params['pdf'])) $pdf_url = $params['pdf'];
        
        // 2. Sprawdzamy atrybuty shortcode'u (WPBakery wysyła to tutaj)
        elseif (!empty($atts['pdf_url'])) $pdf_url = $atts['pdf_url'];
        elseif (!empty($atts['pdf'])) $pdf_url = $atts['pdf'];

        if (empty($pdf_url)) {
            echo '<div style="padding:20px;border:1px solid #ddd;background:#fff;text-align:center;">PWE Flipbook: Proszę podać link do PDF w ustawieniach elementu.</div>';
            return;
        }

        $width = !empty($atts['width']) ? $atts['width'] : '100%';
        $height = !empty($atts['height']) ? $atts['height'] : '80vh';
        $start_page = !empty($atts['start']) ? max(1, intval($atts['start'])) : 1;
        $embed = !empty($atts['embed']) && intval($atts['embed']) === 1;

        $id = 'pwe-flipbook-' . wp_generate_uuid4();

        // --- ZMIANA: Wskazujemy na pliki .js zamiast .mjs ---
        $config = [
            'id' => $id,
            'pdf' => esc_url_raw($pdf_url),
            'start' => $start_page,
            'embed' => $embed,
            // UWAGA: Tutaj zmieniłem rozszerzenia na .js
            'pdfModuleSrc' => plugin_dir_url(__FILE__) . 'assets/js/vendor/pdfjs/pdf.js',
            'workerSrc'    => plugin_dir_url(__FILE__) . 'assets/js/vendor/pdfjs/pdf.worker.js',
        ];
        
        wp_add_inline_script('pwe-flipbook-viewer', 'window.PWE_FLIPBOOK = window.PWE_FLIPBOOK || []; window.PWE_FLIPBOOK.push(' . wp_json_encode($config) . ');', 'before');

        ?>
        <style>
            .pwe-flipbook-root {
                position: relative; background: #fdfdfd; border-radius: 4px;
                user-select: none; display: flex; flex-direction: column; 
                touch-action: pan-y pinch-zoom !important; 
            }
            .pwe-nav-arrow {
                position: absolute; top: 50%; transform: translateY(-50%);
                width: 50px; height: 50px; background: rgba(255, 255, 255, 0.85);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-radius: 50%;
                border: 1px solid rgba(0, 0, 0, 0.15); cursor: pointer; z-index: 100;
                display: flex; align-items: center; justify-content: center;
                font-size: 24px; color: #333; transition: all 0.2s ease;
                outline: none; touch-action: manipulation; 
            }
            .pwe-nav-arrow:hover {
                background: #fff; box-shadow: 0 6px 16px rgba(0,0,0,0.25);
                border-color: rgba(0,0,0,0.4); transform: translateY(-50%) scale(1.1);
            }
            .pwe-nav-arrow:active { transform: translateY(-50%) scale(0.95); }
            .pwe-nav-arrow.disabled, .pwe-nav-arrow:disabled {
                opacity: 0.2; cursor: default; pointer-events: none; box-shadow: none; border-color: transparent;
            }
            .pwe-nav-prev { left: 15px; }
            .pwe-nav-next { right: 15px; }

            @media (max-width: 768px) {
                .pwe-flipbook-stage { pointer-events: none !important; }
                .pwe-nav-arrow { 
                    pointer-events: auto !important; display: flex !important; 
                    width: 44px; height: 44px; font-size: 20px; left: -15px; 
                }
                .pwe-nav-next { left: auto; right: -15px; }
                .pwe-nav-arrow:hover { transform: translateY(-50%); }
            }
        </style>

        <div id="<?php echo esc_attr($id); ?>" class="pwe-flipbook-root" style="width:<?php echo esc_attr($width); ?>;">
            <button type="button" class="pwe-nav-arrow pwe-nav-prev" data-nav="prev" aria-label="Poprzednia">&#10094;</button>
            <button type="button" class="pwe-nav-arrow pwe-nav-next" data-nav="next" aria-label="Następna">&#10095;</button>
            <div class="pwe-flipbook-stage" style="width:100%;height:<?php echo esc_attr($height); ?>;overflow:hidden;"></div>
            <div class="pwe-flipbook-toolbar" style="display:flex;gap:8px;align-items:center; justify-content:center;margin:8px 0; color:#666; font-size:14px;">
                <span data-role="pageinfo">Ładowanie...</span>
            </div>
        </div>
        <?php
    }

    public function shortcode($atts) {
        ob_start();
        self::render('', [], $atts);
        return ob_get_clean();
    }
}

new PWE_Flipbook();