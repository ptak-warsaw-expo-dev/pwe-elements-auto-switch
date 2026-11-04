<?php
if (!defined('ABSPATH')) exit;

wp_enqueue_style('pwe-post-photo-viewer', plugins_url('photo-viewer.css', __FILE__), [], filemtime(__DIR__ . '/photo-viewer.css'));
wp_enqueue_script('pwe-post-photo-viewer', plugins_url('photo-viewer.js', __FILE__), [], filemtime(__DIR__ . '/photo-viewer.js'), true);

return '
<dialog class="pwe-photo-viewer" aria-label="' . esc_attr(PWE_Functions::multi_translation('photo_viewer_title')) . '" data-open-label="' . esc_attr(PWE_Functions::multi_translation('photo_viewer_open')) . '">
    <div class="pwe-photo-viewer__panel">
        <div class="pwe-photo-viewer__toolbar">
            <span class="pwe-photo-viewer__counter" aria-live="polite" aria-atomic="true"></span>
            <button type="button" class="pwe-photo-viewer__control" data-photo-close aria-label="' . esc_attr(PWE_Functions::multi_translation('photo_viewer_close')) . '"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12M18 6L6 18" /></svg></button>
        </div>
        <div class="pwe-photo-viewer__stage">
            <button type="button" class="pwe-photo-viewer__control pwe-photo-viewer__previous" data-photo-previous aria-label="' . esc_attr(PWE_Functions::multi_translation('photo_viewer_previous')) . '"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M15 5l-7 7 7 7" /></svg></button>
            <img class="pwe-photo-viewer__image" alt="">
            <button type="button" class="pwe-photo-viewer__control pwe-photo-viewer__next" data-photo-next aria-label="' . esc_attr(PWE_Functions::multi_translation('photo_viewer_next')) . '"><svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M9 5l7 7-7 7" /></svg></button>
        </div>
        <p class="pwe-photo-viewer__caption"></p>
    </div>
</dialog>';
