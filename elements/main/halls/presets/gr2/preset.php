<?php

if (!empty($all_halls)) { 
    $output = '
    <div id="pweHalls" class="pwe-halls"
        data-all-items='. json_encode($json_data_all) .'
        data-active-items='. json_encode($json_data_active) .'>
        <div class="pwe-halls__wrapper">

            <div class="pwe-halls__title">
                <h4 class="pwe-main-title">'. PWE_Functions::languageChecker('Powierzchnia wystawiennicza', 'Exhibition space') .'</h4>
                <p>'. PWE_Functions::languageChecker('Największa powierzchnia wystawiennicza w Polsce', 'The largest exhibition space in Poland') .'</p>
            </div> 

            <div class="pwe-halls__container">
            
                <div class="pwe-halls__info">
                    <div class="pwe-halls__info-wrapper">
                        <img src="'. PWE_Functions::languageChecker('/doc/logo-color.webp', '/doc/logo-color-en.webp') .'"/>
                        <span class="pwe-halls__date-icon desktop">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19 19H5V8H19M16 1V3H8V1H6V3H5C3.89 3 3 3.89 3 5V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19V5C21 4.46957 20.7893 3.96086 20.4142 3.58579C20.0391 3.21071 19.5304 3 19 3H18V1M17 12H12V17H17V12Z" fill="var(--accent-color)"/>
                            </svg>
                        </span>
                        <p class="pwe-halls__date"><strong>'. PWE_Functions::languageChecker(do_shortcode('[trade_fair_date]'), do_shortcode('[trade_fair_date_eng]')) .'</strong></p>
                        <p class="pwe-halls__time">10:00-17:00</p>
                        <span class="pwe-halls__location-icon desktop">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.67206 4.09515C7.35381 2.43802 9.61843 1.50661 11.9794 1.501C14.3404 1.49539 16.6095 2.41603 18.2991 4.06516H18.3011L18.3331 4.09515C21.8781 7.58215 21.8851 13.1832 18.3751 16.6352L12.7041 22.2132C12.517 22.3973 12.265 22.5005 12.0026 22.5005C11.7401 22.5005 11.4881 22.3973 11.3011 22.2132L5.63006 16.6352C4.79749 15.8212 4.13595 14.849 3.6843 13.7758C3.23266 12.7026 3 11.55 3 10.3857C3 9.22129 3.23266 8.06867 3.6843 6.99547C4.13595 5.92227 4.79749 4.95014 5.63006 4.13616L5.67206 4.09515ZM12.0001 6.50015C11.6061 6.50015 11.216 6.57775 10.852 6.72852C10.488 6.87928 10.1573 7.10026 9.87874 7.37883C9.60016 7.65741 9.37919 7.98813 9.22842 8.3521C9.07766 8.71608 9.00006 9.10619 9.00006 9.50015C9.00006 9.89412 9.07766 10.2842 9.22842 10.6482C9.37919 11.0122 9.60016 11.3429 9.87874 11.6215C10.1573 11.9001 10.488 12.121 10.852 12.2718C11.216 12.4226 11.6061 12.5002 12.0001 12.5002C12.7957 12.5002 13.5588 12.1841 14.1214 11.6215C14.684 11.0589 15.0001 10.2958 15.0001 9.50015C15.0001 8.70451 14.684 7.94144 14.1214 7.37883C13.5588 6.81623 12.7957 6.50015 12.0001 6.50015Z" fill="var(--accent-color)"/>
                            </svg>
                        </span>
                        <p class="pwe-halls__halls">'. $halls_word .' '. $all_halls .'</p>
                        <p class="pwe-halls__parking mobile">'. PWE_Functions::languageChecker('Darmowy parking', 'Free parking') .'</p>
                        <p class="pwe-halls__location">
                            <span class="pwe-halls__location-icon mobile">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.67206 4.09515C7.35381 2.43802 9.61843 1.50661 11.9794 1.501C14.3404 1.49539 16.6095 2.41603 18.2991 4.06516H18.3011L18.3331 4.09515C21.8781 7.58215 21.8851 13.1832 18.3751 16.6352L12.7041 22.2132C12.517 22.3973 12.265 22.5005 12.0026 22.5005C11.7401 22.5005 11.4881 22.3973 11.3011 22.2132L5.63006 16.6352C4.79749 15.8212 4.13595 14.849 3.6843 13.7758C3.23266 12.7026 3 11.55 3 10.3857C3 9.22129 3.23266 8.06867 3.6843 6.99547C4.13595 5.92227 4.79749 4.95014 5.63006 4.13616L5.67206 4.09515ZM12.0001 6.50015C11.6061 6.50015 11.216 6.57775 10.852 6.72852C10.488 6.87928 10.1573 7.10026 9.87874 7.37883C9.60016 7.65741 9.37919 7.98813 9.22842 8.3521C9.07766 8.71608 9.00006 9.10619 9.00006 9.50015C9.00006 9.89412 9.07766 10.2842 9.22842 10.6482C9.37919 11.0122 9.60016 11.3429 9.87874 11.6215C10.1573 11.9001 10.488 12.121 10.852 12.2718C11.216 12.4226 11.6061 12.5002 12.0001 12.5002C12.7957 12.5002 13.5588 12.1841 14.1214 11.6215C14.684 11.0589 15.0001 10.2958 15.0001 9.50015C15.0001 8.70451 14.684 7.94144 14.1214 7.37883C13.5588 6.81623 12.7957 6.50015 12.0001 6.50015Z" fill="var(--accent-color)"/>
                                </svg>
                            </span>
                            <span>Al. Katowicka 62, 05-830 Nadarzyn</span>
                        </p>
                        <span class="pwe-halls__parking-icon desktop">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.0001 3.6001C4.67635 3.6001 3.6001 4.67635 3.6001 6.0001V18.0001C3.6001 19.3238 4.67635 20.4001 6.0001 20.4001H18.0001C19.3238 20.4001 20.4001 19.3238 20.4001 18.0001V6.0001C20.4001 4.67635 19.3238 3.6001 18.0001 3.6001H6.0001ZM10.8001 12.0001H12.6001C13.2638 12.0001 13.8001 11.4638 13.8001 10.8001C13.8001 10.1363 13.2638 9.6001 12.6001 9.6001H10.8001V12.0001ZM12.6001 14.4001H10.8001V15.6001C10.8001 16.2638 10.2638 16.8001 9.6001 16.8001C8.93635 16.8001 8.4001 16.2638 8.4001 15.6001V8.7001C8.4001 7.87135 9.07135 7.2001 9.9001 7.2001H12.6001C14.5876 7.2001 16.2001 8.8126 16.2001 10.8001C16.2001 12.7876 14.5876 14.4001 12.6001 14.4001Z" fill="var(--accent-color)"/>
                            </svg>
                        </span>
                        <p class="pwe-halls__parking">'. PWE_Functions::languageChecker('Darmowy parking', 'Free parking') .'</p>
                    </div>
                </div> 

                <div class="pwe-halls__model">';
                    require_once plugin_dir_path(dirname(dirname(__FILE__))) . 'assets/svg.php';
                $output .= '
                </div>

            </div>

        </div>
    </div>';
    
} else { $output = '<style>.row-container:has(#pweHalls) {display: none !important;}</style>'; }

return $output;
