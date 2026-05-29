<?php

$output = '
<div class="pwe-profiles">
    <div class="pwe-profiles__wrapper">
        <div class="pwe-profiles__title">
            <h4 class="pwe-main-title">'. PWE_Functions::multi_translation("profiles_title") .'</h4>
        </div>
        <div class="pwe-profiles__tabs-items">
            <div class="pwe-profiles__tab active" data-tab="profile_for_visitors">
                <span class="pwe-profiles__tab-head">'. PWE_Functions::multi_translation("visitor") .'</span>
            </div>
            <div class="pwe-profiles__tab" data-tab="profile_for_exhibitors">
                <span class="pwe-profiles__tab-head">'. PWE_Functions::multi_translation("exhibitor") .'</span>
            </div>
            <div class="pwe-profiles__tab" data-tab="profile_industry_scope">
                <span class="pwe-profiles__tab-head">'. PWE_Functions::multi_translation("industry") .'</span>
            </div>
        </div>

        <div id="profile_for_visitors" class="pwe-profiles__tabs-content active">
            <div class="pwe-profiles__tab-text">
                '. $profile_for_visitors .'
                <button class="pwe-profiles__show-more-btn">'. PWE_Functions::multi_translation("more_btn") .' ▼</button>
            </div>
            <div class="pwe-profiles__tab-image">
                <img 
                    src="'. $profile_for_visitors_img  .'" 
                    alt="'. PWE_Functions::multi_translation("visitor_alt") .'" 
                    onerror="this.onerror=null; this.src=\''. $default_visitors_img  .'\';"
                >
            </div>
        </div>

        <div id="profile_for_exhibitors" class="pwe-profiles__tabs-content">
            <div class="pwe-profiles__tab-text">
                '. $profile_for_exhibitors .'
                <button class="pwe-profiles__show-more-btn">'. PWE_Functions::multi_translation("more_btn") .' ▼</button>
            </div>
            <div class="pwe-profiles__tab-image">
                <img 
                    src="'. $profile_for_exhibitors_img .'" 
                    alt="'. PWE_Functions::multi_translation("exhibitor_alt") .'"
                    onerror="this.onerror=null; this.src=\''. $default_exhibitors_img  .'\';"
                >
            </div>
        </div>

        <div id="profile_industry_scope" class="pwe-profiles__tabs-content">
            <div class="pwe-profiles__tab-text">
                '. $profile_industry_scope .'
                <button class="pwe-profiles__show-more-btn">'. PWE_Functions::multi_translation("more_btn") .' ▼</button>
            </div>
            <div class="pwe-profiles__tab-image">
                <img 
                    src="'. $profile_industry_scope_img .'" 
                    alt="'. PWE_Functions::multi_translation("industry_alt") .'"
                    onerror="this.onerror=null; this.src=\''. $default_industry_img  .'\';"
                >
            </div>
        </div>
    </div>
</div>

<script>
const pweProfilesTranslations = {
    more: "' . PWE_Functions::multi_translation("more_btn") . '",
    less: "' . PWE_Functions::multi_translation("less_btn") . '"
};
</script>';



return $output;