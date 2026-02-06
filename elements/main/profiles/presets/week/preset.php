<?php

$output = '
<div class="pwe-profiles">
    <div class="pwe-profiles__wrapper">
        <div class="pwe-profiles__title">
            <h4 class="pwe-main-title">'. PWE_Functions::languageChecker('Profil', 'Profile') .'</h4>
        </div>
        <div class="pwe-profiles__cards">
            <div id="profile_for_visitors" class="pwe-profiles__card" style="background: url('. $profile_for_visitors_img  .')">
                <div class="pwe-profiles__card-content">
                    <p>Profil</p>
                    <h5>'. PWE_Functions::languageChecker('Odwiedzającego', 'Visitor') .'</h5>
                    <div class="pwe-profiles__show-more-btn">
                        <span>'. PWE_Functions::languageChecker('Pokaż więcej', 'See more') .'</span>
                        <span class="pwe-profiles__show-more-arrow">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                            </svg>
                        </span>
                    </div>
                    <div class="pwe-profiles__card-text">'. PWE_Functions::languageChecker($profile_for_visitors_pl, $profile_for_visitors_en) .'</div>
                    
                </div>
            </div>

            <div id="profile_for_exhibitors" class="pwe-profiles__card" style="background: url('. $profile_for_exhibitors_img .')">
                <div class="pwe-profiles__card-content">
                    <p>Profil</p>
                    <h5>'. PWE_Functions::languageChecker('Wystawcy', 'Exhibitor') .'</h5>
                    <div class="pwe-profiles__show-more-btn">
                        <span>'. PWE_Functions::languageChecker('Pokaż więcej', 'See more') .'</span>
                        <span class="pwe-profiles__show-more-arrow">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                            </svg>
                        </span>
                    </div>
                    <div class="pwe-profiles__card-text">'. PWE_Functions::languageChecker($profile_for_exhibitors_pl, $profile_for_exhibitors_en) .'</div>
                    
                </div>
            </div>

            <div id="profile_industry_scope" class="pwe-profiles__card" style="background: url('. $profile_industry_scope_img .')">
                <div class="pwe-profiles__card-content">
                    <p>Profil</p>
                    <h5>'. PWE_Functions::languageChecker('Branżowy', 'Industry') .'</h5>
                    <div class="pwe-profiles__show-more-btn">
                        <span>'. PWE_Functions::languageChecker('Pokaż więcej', 'See more') .'</span>
                        <span class="pwe-profiles__show-more-arrow">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.58266 11.0817C2.19221 11.4721 1.55899 11.472 1.16844 11.0817C0.777921 10.6912 0.777921 10.058 1.16844 9.66747L7.71125 3.12466L1.87486 3.12466C1.32279 3.12441 0.874968 2.6769 0.874968 2.12477C0.874968 1.57264 1.32279 1.12512 1.87486 1.12487L10.1254 1.12487C10.6774 1.12512 11.1253 1.57264 11.1253 2.12477L11.1246 10.3746C11.1244 10.9268 10.6769 11.3745 10.1247 11.3745C9.57257 11.3743 9.1249 10.9267 9.12478 10.3746L9.12478 4.53956L2.58266 11.0817Z" fill="white"/>
                            </svg>
                        </span>
                    </div>
                    <div class="pwe-profiles__card-text">'. PWE_Functions::languageChecker($profile_industry_scope_pl, $profile_industry_scope_en) .'</div>
                </div>
            </div>
        </div>
    </div>
</div>';

return $output;