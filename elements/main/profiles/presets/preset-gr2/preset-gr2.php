<?php

$output .= '

<div class="pwe-profiles">
    <div class="pwe-profiles__wrapper">
        <div class="pwe-profiles__title">
            <h4 class="pwe-main-title">'. PWECommonFunctions::languageChecker('Profil', 'Profile') .'</h4>
        </div>
        <div class="pwe-profiles__cards">
            <div id="profile_for_visitors" class="pwe-profiles__card" style="background: url('. $profile_for_visitors_img  .')">
                <div class="pwe-profiles__card-content">
                    <p>Profil</p>
                    <h5>'. PWECommonFunctions::languageChecker('Odwiedzającego', 'Visitor') .'</h5>
                    <div class="pwe-profiles__show-more-btn">
                        <span>'. PWECommonFunctions::languageChecker('Pokaż więcej', 'See more') .'</span>
                        <span>🡲</span>
                    </div>
                    <div class="pwe-profiles__card-text">'. PWECommonFunctions::languageChecker($profile_for_visitors_pl, $profile_for_visitors_en) .'</div>
                    
                </div>
            </div>

            <div id="profile_for_exhibitors" class="pwe-profiles__card" style="background: url('. $profile_for_exhibitors_img .')">
                <div class="pwe-profiles__card-content">
                    <p>Profil</p>
                    <h5>'. PWECommonFunctions::languageChecker('Wystawcy', 'Exhibitor') .'</h5>
                    <div class="pwe-profiles__show-more-btn">
                        <span>'. PWECommonFunctions::languageChecker('Pokaż więcej', 'See more') .'</span>
                        <span>🡲</span>
                    </div>
                    <div class="pwe-profiles__card-text">'. PWECommonFunctions::languageChecker($profile_for_exhibitors_pl, $profile_for_exhibitors_en) .'</div>
                    
                </div>
            </div>

            <div id="profile_industry_scope" class="pwe-profiles__card" style="background: url('. $profile_industry_scope_img .')">
                <div class="pwe-profiles__card-content">
                    <p>Profil</p>
                    <h5>'. PWECommonFunctions::languageChecker('Branżowy', 'Industry') .'</h5>
                    <div class="pwe-profiles__show-more-btn">
                        <span>'. PWECommonFunctions::languageChecker('Pokaż więcej', 'See more') .'</span>
                        <span>🡲</span>
                    </div>
                    <div class="pwe-profiles__card-text">'. PWECommonFunctions::languageChecker($profile_industry_scope_pl, $profile_industry_scope_en) .'</div>
                </div>
            </div>
        </div>
    </div>
</div>';

return $output;