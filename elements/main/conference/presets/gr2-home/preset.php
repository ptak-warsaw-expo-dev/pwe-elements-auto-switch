<?php

$output = '';

// Layout
$output .= '
<div id="pweConference" class="pwe-conference">
    <div class="pwe-conference__wrapper">
        <div class="pwe-conference__info">
            <div class="pwe-conference__left">
                <img src="/wp-content/plugins/pwe-media/media/design-decade-summit.webp" alt="Design Decade Summit Image">
            </div>
            <div class="pwe-conference__right">
                <div class="pwe-conference__right-content">
                    <h4 class="pwe-conference__name">'. PWE_Functions::multi_translation("home_title") .'</h4>
                    <p class="pwe-conference__desc">'. PWE_Functions::multi_translation("home_desc") .'</p>
                    <div class="pwe-conference__btn-container">
                        <a href="' . PWE_Functions::languageChecker('/wydarzenia/?konferencja=warsaw-home-design-decade-summit-2026', '/en/conferences/?konferencja=warsaw-home-design-decade-summit-2026') . '" class="pwe-conference__btn">
                            '. PWE_Functions::multi_translation("home_btn") .'
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="pwe-conference__icons">
            <div class="pwe-conference__icons-item">
                <img src="/wp-content/plugins/pwe-media/media/panel.webp" alt="'. PWE_Functions::multi_translation("home_icon_title_panel") .'">
                <div class="pwe-conference__icons-item-text">
                    <h5>'. PWE_Functions::multi_translation("home_icon_title_panel") .'</h5>
                    <p>'. PWE_Functions::multi_translation("home_icon_desc_panel") .'</p>
                </div>
            </div>
            <div class="pwe-conference__icons-item">
                <img src="/wp-content/plugins/pwe-media/media/trendy.webp" alt="'. PWE_Functions::multi_translation("home_icon_title_networking") .'">
                <div class="pwe-conference__icons-item-text">
                    <h5>'. PWE_Functions::multi_translation("home_icon_title_trends") .'</h5>
                    <p>'. PWE_Functions::multi_translation("home_icon_desc_trends") .'</p>
                </div>
            </div>
            <div class="pwe-conference__icons-item">
                <img src="/wp-content/plugins/pwe-media/media/networking.webp" alt="'. PWE_Functions::multi_translation("home_icon_title_networking") .'">
                <div class="pwe-conference__icons-item-text">
                    <h5>'. PWE_Functions::multi_translation("home_icon_title_networking") .'</h5>
                    <p>'. PWE_Functions::multi_translation("home_icon_desc_networking") .'</p>
                </div>
            </div>
            <div class="pwe-conference__icons-item">
                <img src="/wp-content/plugins/pwe-media/media/jubileusz.webp" alt="'. PWE_Functions::multi_translation("home_icon_title_anniversary") .'">
                <div class="pwe-conference__icons-item-text">
                    <h5>'. PWE_Functions::multi_translation("home_icon_title_anniversary") .'</h5>
                    <p>'. PWE_Functions::multi_translation("home_icon_desc_anniversary") .'</p>
                </div>
            </div>   
        </div>
    </div>
</div>';

return $output;