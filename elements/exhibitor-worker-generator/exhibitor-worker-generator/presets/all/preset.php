<?php

$output = '
<div id="pweExhibitorGenerator" class="pwe-exhibitor-generator pwe-invite-generator--worker">   
    <div class="pwe-exhibitor-generator__wrapper">
        <div class="pwe-exhibitor-generator__column pwe-exhibitor-generator__column--badge" style="background-image: ' . esc_attr($badge) . ';"></div>
        <div class="pwe-exhibitor-generator__column pwe-exhibitor-generator__column--form">
            <div class="pwe-exhibitor-generator__content">
                <h3 class="pwe-exhibitor-generator__title">' . PWE_Functions::multi_translation('title') . '</h3>
                <div class="pwe-exhibitor-generator__form">
                    ' . $form . '
                </div>
            </div>
        </div>
    </div>
    <div class="pwe-exhibitor-generator__tech-support">
        <h3>
            ' . PWE_Functions::multi_translation('help_text') . '<br>
            ' . PWE_Functions::multi_translation('contact') . ' <a href="mailto:' . $email . '">' . $email . '</a>
        </h3>
    </div>
</div>';

return $output;