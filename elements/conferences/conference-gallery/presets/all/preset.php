<?php

$output = '
<div id="pweConferenceGallery" class="pwe-conference-gallery">
    <div class="pwe-conference-gallery__content">
        <div class="pwe-conference-gallery__badge">
            <div class="pwe-conference-gallery__badge-icon-wrapper">
                <span class="pwe-conference-gallery__badge-icon">✦</span>
            </div>
            <span>' . PWE_Functions::multi_translation('badge_label') . '</span>
        </div>
        <h2 class="pwe-conference-gallery__title">
            ' . PWE_Functions::multi_translation('title') . '
        </h2>
        <p class="pwe-conference-gallery__description">
            ' . PWE_Functions::multi_translation('description') . '
        </p>
        <div class="pwe-conference-gallery__btn-wrapper">
            <a href="' . PWE_Functions::multi_translation('gallery_url') . '" class="pwe-conference-gallery__btn">
                ' . PWE_Functions::multi_translation('gallery_button_label') . '
            </a>
            <a href="' . PWE_Functions::multi_translation('registration_url') . '" class="pwe-conference-gallery__btn pwe-conference-gallery__btn--secondary">
                ' . PWE_Functions::multi_translation('registration_button_label') . '
            </a>
        </div>
    </div>
    <div class="pwe-conference-gallery__gallery">
        <div class="pwe-conference-gallery__gallery__item">
            <img src="/wp-content/plugins/pwe-media/media/events-mini/event_4.webp" alt="Conference 1">
        </div>
        <div class="pwe-conference-gallery__gallery__item">
            <img src="/wp-content/plugins/pwe-media/media/events-mini/event_2.webp" alt="Conference 2">
        </div>
        <div class="pwe-conference-gallery__gallery__item">
            <img src="/wp-content/plugins/pwe-media/media/events-mini/event_3.webp" alt="Conference 3">
        </div>
    </div>

</div>';

return $output;