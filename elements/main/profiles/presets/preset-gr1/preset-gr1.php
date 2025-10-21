<?php

$output .= '
<div class="pwe-profiles">
    <div class="pwe-profiles__wrapper">
        <div class="pwe-profiles__title">
            <h4 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Profil', 'Profile') .'</h4>
        </div>
        <div class="pwe-profiles__tabs-items">
            <div class="pwe-profiles__tab active" data-tab="profile_for_visitors">
                <span class="pwe-profiles__tab-head">'. PWECommonFunctions::languageChecker('Odwiedzającego', 'Visitor') .'</span>
            </div>
            <div class="pwe-profiles__tab" data-tab="profile_for_exhibitors">
                <span class="pwe-profiles__tab-head">'. PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitor') .'</span>
            </div>
            <div class="pwe-profiles__tab" data-tab="profile_industry_scope">
                <span class="pwe-profiles__tab-head">'. PWECommonFunctions::languageChecker('Branżowy', 'Industry') .'</span>
            </div>
        </div>

        <div id="profile_for_visitors" class="pwe-profiles__tabs-content active">
            <div class="pwe-profiles__tab-text">
                '. PWECommonFunctions::languageChecker($profile_for_visitors_pl, $profile_for_visitors_en) .'
                <button class="pwe-profiles__show-more-btn">'. PWECommonFunctions::languageChecker('więcej', 'more') .' ▼</button>
            </div>
            <div class="pwe-profiles__tab-image">
                <img src="'. $profile_for_visitors_img  .'" alt="'. PWECommonFunctions::languageChecker('Odwiedzającego', 'Visitor') .'">
            </div>
        </div>

        <div id="profile_for_exhibitors" class="pwe-profiles__tabs-content">
            <div class="pwe-profiles__tab-text">
                '. PWECommonFunctions::languageChecker($profile_for_exhibitors_pl, $profile_for_exhibitors_en) .'
                <button class="pwe-profiles__show-more-btn">więcej ▼</button>
            </div>
            <div class="pwe-profiles__tab-image">
                <img src="'. $profile_for_exhibitors_img .'" alt="'. PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitor') .'">
            </div>
        </div>

        <div id="profile_industry_scope" class="pwe-profiles__tabs-content">
            <div class="pwe-profiles__tab-text">
                '. PWECommonFunctions::languageChecker($profile_industry_scope_pl, $profile_industry_scope_en) .'
                <button class="pwe-profiles__show-more-btn">więcej ▼</button>
            </div>
            <div class="pwe-profiles__tab-image">
                <img src="'. $profile_industry_scope_img .'" alt="'. PWECommonFunctions::languageChecker('Branżowy', 'Industry') .'">
            </div>
        </div>
    </div>
</div>';


return $output;