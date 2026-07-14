<?php
$output .= '
<div id="pweMedalCeremony" class="pwe-medal-ceremony">

    <div class="pwe-medal-ceremony__header">
        <img src='. PWE_Functions::multi_translation('ceremony_img') .' alt="pwe-medal-ceremony__header" class="pwe-medal-ceremony___header-img" />
    </div>
    <div class="pwe-medal-ceremony__content">
        <div class="pwe-medal-ceremony__content__color-box"></div>
        <div class="pwe-medal-ceremony__content_container">
        <h1 class="pwe-medal-ceremony__content__title">
            '. PWE_Functions::multi_translation('ceremony_title') .'
        </h1>

        <p class="pwe-medal-ceremony__content__description">
            '. PWE_Functions::multi_translation('ceremony_description') .'
        </p>
        </div>
    </div>
    <div class="pwe-medal-ceremony__form">
        ' . do_shortcode('[gravityform id="'. $form_id .'" title="false" description="false" ajax="false"]') . '
    </div>
    <div class="pwe-medal-ceremony__rules">
        '. PWE_Functions::multi_translation('ceremony_rules') .' :<br/>
        <a href="'. $ceremony_rules .'" target="_blank">
            '. PWE_Functions::multi_translation('ceremony_rules_button') .'
        </a>
    </div>
    <div class="pwe-medal-ceremony__footer">
        <img src="'. PWE_Functions::multi_translation('ceremony_footer_img') .'" alt="pwe-medal-ceremony__footer" class="pwe-medal-ceremony__footer-img" />
    </div>

</div>';

return $output;