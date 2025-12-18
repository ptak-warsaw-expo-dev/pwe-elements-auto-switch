<?php

// $output .= '
// <style>
//     .pwe-header__partners {
//         position: absolute;
//         right: 36px;
//         top: 50%;
//         transform: translateY(-50%);
//         z-index: 1;
//         display: flex;
//         align-items: center;
//         opacity: 0; 
//         transition: .3s ease-in;
//     }
//     .pwe-header__partners-section {
//         position: relative;
//         padding: 32px;
//         color: #fff;
//         max-width: 380px; 
//     }
//     .pwe-header__partners-section:before {
//         content: "";
//         position: absolute;
//         top: 0;
//         left: 0;
//         width: 100%;
//         height: 100%;
//         background: rgb(144 153 166 / 11%);
//         z-index: 1;
//         border-radius: 20px;
//         backdrop-filter: blur(8px);
//         -webkit-backdrop-filter: blur(8px);
//     }
//     .pwe-header__partners-section h3 {
//         margin: 0 0 18px;
//         display: flex;
//         justify-content: center;
//         position: relative;
//         z-index: 1;
//         color: white;
//     }
//     /* ogólny swiper rules */
//     .pwe-header__partners .swiper-container {
//         width: 100%;
//         height: 100%;
//     }
//     /* poziomy container */
//     .swiper-container-horizontal {
//         width: 100%;
//         overflow: hidden;
//     }
//     .swiper-container-horizontal .swiper-wrapper {
//         align-items: flex-start;
//     }
//     .swiper-container-horizontal .swiper-slide {
//         width: 150px; /* szerokość pojedynczej kolumny */
//         margin-right: 12px;
//         box-sizing: border-box;
//     }
//     .pwe-header__partners-flex {
//         display: block;
//     }
//     .pwe-header__partners-col {
//         display: flex;
//         flex-direction: column;
//         width: 100%;
//         overflow: hidden;
//     }
//     .pwe-header__partners-thumbs {
//         height: calc(400px - 96px);
//         overflow: hidden;
//     }
//     /* pojedyncze obrazki wewnątrz pionowego slidera */
//     .pwe-header__partners .swiper-slide .pwe-header__partners-image {
//         background: white;
//         padding: 10px;
//         border-radius: 18px;
//         width: 100%;
//         height: 100%;
//         box-sizing: border-box;
//     }
//     .pwe-header__partners-image img {
//         display: block;
//         width: 100%;
//         height: 100%;
//         object-fit: contain;
//     }
// </style>
// ';

// $files = [];
// $grouped_logos = [];

// $meta_data = PWECommonFunctions::get_database_meta_data('header_order');

// if (!empty($meta_data)) {
//     $header_order = $meta_data[0]->meta_data;
// }

// $grouped_logos = [];
// $current_locale = get_locale();
// $current_lang = substr($current_locale, 0, 2);

// foreach ($cap_logotypes_data as $logo_data) {
//     if (strpos($logo_data->logos_type, 'header-') === 0) { 

//         $meta = json_decode($logo_data->meta_data, true);
//         $data = json_decode($logo_data->data ?? '{}', true);

//         $desc_pl = $meta["desc_pl"] ?? '';
//         $desc_en = $meta["desc_en"] ?? '';

//         $url   = 'https://cap.warsawexpo.eu/public' . $logo_data->logos_url;
//         $name  = $data['logos_exh_name'];
//         $order = isset($logo_data->logos_order) ? (int)$logo_data->logos_order : PHP_INT_MAX;

//         $visibilityFlags = array_filter($data, function ($key) {
//             return preg_match('/^logos_[a-z]{2}_[A-Z]{2}$/', $key);
//         }, ARRAY_FILTER_USE_KEY);

//         if (empty($visibilityFlags)) {
//             $showLogo = true;
//         } else {
//             $flagKey = 'logos_' . $current_locale;

//             if (isset($visibilityFlags[$flagKey])) {
//                 $showLogo = ($visibilityFlags[$flagKey] === 'true');
//             } else {
//                 $showLogo = false;
//             }
//         }

//         if (!$showLogo) {
//             continue;
//         }

//         $link_pl = $data['logos_link']     ?? null;
//         $link_en = $data['logos_link_en']  ?? null;

//         if (!empty($link_pl) && empty($link_en)) {
//             $finalLink = $link_pl;

//         } elseif (!empty($link_pl) && !empty($link_en)) {
//             if ($current_lang === 'pl') {
//                 $finalLink = $link_pl;
//             } else {
//                 $finalLink = $link_en;
//             }

//         } else {
//             // brak linków
//             $finalLink = null;
//         }

//         $element = [
//             'url'      => $url,
//             'desc_pl'  => $desc_pl,
//             'desc_en'  => $desc_en,
//             'link'     => $finalLink,
//             'name'     => $name,
//             'order'    => $order,
//         ];

//         $grouped_logos[$logo_data->logos_type][] = $element;
//     }
// }

// $total_logos = array_sum(array_map('count', $grouped_logos));

// // Variation Mapping
// $plural_map_pl = [
//     "Prelegent" => "Prelegenci",
//     "Partner Targów" => "Partnerzy Targów",
//     "Partner Merytoryczny" => "Partnerzy Merytoryczni",
//     "Partner Branżowy" => "Partnerzy Branżowi",
//     "Partner targów i konferencji" => "Partnerzy Targów i Konferencji",
//     "Patronat Honorowy" => "Patronaty Honorowe",
//     "Partner Organizacyjny" => "Partnerzy Organizacyjni"
// ];

// $plural_map_en = [
//     "Speaker" => "Speakers",
//     "Fair Partner" => "Fair Partners",
//     "Content Partner" => "Content Partners",
//     "Industry Partner" => "Industry Partners",
//     "Trade and Conference Partner" => "Trade and Conference Partners",
//     "Honorary Patronage" => "Honorary Patronages",
//     "Organizational partner" => "Organizational partners"
// ];

// if (count($grouped_logos) > 0) {

//     // Apply group based on $header_order
//     $ordered_types = [];

//     if (!empty($header_order)) {
//         // Explode and remove empty elements/spaces
//         $parts = array_filter(array_map('trim', explode(',', $header_order)));

//         // Keep original values, but only those that start with "header-"
//         foreach ($parts as $p) {
//             if ($p !== '') $ordered_types[] = $p;
//         }
//     } else {
//         // Default: all types in the order they are in $grouped_logos
//         $ordered_types = array_keys($grouped_logos);
//     }

//     // unique class for horizontal swiper instance (in case więcej takich modułów)
//     $horizontal_unique = 'swiper-h-' . uniqid();

//     $output .= '
//     <div id="pweHeaderPartners" class="pwe-header__partners">
//         <section class="pwe-header__partners-section">
//             <h3>Partnerzy</h3>
//             <div class="pwe-header__partners-flex">
//                 <!-- POZIOMY SWIPER: MUSI MIEĆ .swiper-wrapper I .swiper-slide -->
//                 <div class="swiper-container swiper-container-horizontal '. $horizontal_unique .'">
//                     <div class="swiper-wrapper">';

//                     // Generating containers for each group
//                     foreach ($ordered_types as $logos_type) {

//                         if (!isset($grouped_logos[$logos_type])) {
//                             continue;
//                         }

//                         $files = $grouped_logos[$logos_type];

//                         // Sort files by logos_order (ascending). If missing, order -> at the end.
//                         usort($files, function($a, $b) {
//                             $oa = $a['order'] ?? PHP_INT_MAX;
//                             $ob = $b['order'] ?? PHP_INT_MAX;
//                             if ($oa === $ob) return 0;
//                             return ($oa < $ob) ? -1 : 1;
//                         });

//                         if (count($files) > 0) {
//                             $unique_id = uniqid();

//                             // Group Title
//                             $title_single = PWECommonFunctions::lang_pl() ? $files[0]["desc_pl"] : $files[0]["desc_en"];

//                             // Automatic pluralization
//                             if (count($files) > 1) {
//                                 // plural
//                                 if (PWECommonFunctions::lang_pl()) {
//                                     $title = $plural_map_pl[$title_single] ?? $title_single;
//                                 } else {
//                                     $title = $plural_map_en[$title_single] ?? $title_single;
//                                 }
//                             } else {
//                                 // singular
//                                 $title = $title_single;
//                             }

                            

//                             // === KAŻDA KOLUMNA JEST TERAZ SWIPER-SLIDE (minimalna zmiana) ===
//                             $output .= '
//                             <div class="swiper-slide pwe-header__partners-col">
//                                 <div class="pwe-header__partners-title">
//                                     <h3>'. $title .'</h3>
//                                 </div>  
//                                 <div class="pwe-header__partners-thumbs">
//                                     <div class="swiper-container swiper-container-'. $unique_id .'">
//                                         <div class="swiper-wrapper">';
//                                             foreach ($files as $item) {
//                                                 $output .= '
//                                                 <div class="pwe-header__partners-slide swiper-slide">
//                                                     <div class="pwe-header__partners-image">
//                                                         <img src="'. htmlspecialchars($item["url"], ENT_QUOTES) .'" alt="'. htmlspecialchars($item["name"] ?? "", ENT_QUOTES) .'"/>
//                                                     </div>
//                                                 </div>';
//                                             }
//                                             $output .= '
//                                         </div> <!-- .swiper-wrapper (vertical) -->
//                                     </div> <!-- .swiper-container (vertical) -->
//                                 </div> <!-- .pwe-header__partners-thumbs -->

//                             </div>  <!-- .swiper-slide pwe-header__partners-col -->
                            
//                             <script>
//                                 // inicjalizacja pionowego slidera dla tej kolumny
//                                 document.addEventListener("DOMContentLoaded", function() {
//                                     new Swiper(".swiper-container-'. $unique_id .'", {
//                                         direction: "vertical",
//                                         slidesPerView: 3,
//                                         spaceBetween: 12,
//                                         freeMode: true,
//                                         loop: true,
//                                         autoplay: {
//                                             delay: 2500,
//                                             disableOnInteraction: false,
//                                             pauseOnMouseEnter: true
//                                         }
//                                     });
//                                 });
//                             </script>
//                             ';
                        
//                         }
//                     }

//                     // close horizontal wrapper & container
//                     $output .= '
//                     </div> <!-- .swiper-wrapper poziomy -->
//                 </div> <!-- .swiper-container-horizontal -->';

//                 $output .= '
//             </div>
//         </section>

//     </div>';

//     // Inicjalizacja poziomego slidera (tylko raz)
//     $output .= '
//     <script>
//         document.addEventListener("DOMContentLoaded", function() {
//             const pweHeaderPartners = document.querySelector(".pwe-header__partners");
//             if (pweHeaderPartners) {
//                 setTimeout(function() {
//                     pweHeaderPartners.style.opacity = 1;
//                 }, 300);
//             }

//             new Swiper(".' . $horizontal_unique . '", {
//                 direction: "horizontal",
//                 slidesPerView: 2,
//                 spaceBetween: 12,
//                 freeMode: false,
//                 loop: true,
//                 autoplay: {
//                     delay: 5000,
//                     disableOnInteraction: false,
//                     pauseOnMouseEnter: true
//                 },
//                 breakpoints: {
//                     0: { slidesPerView: 1 },
//                     768: { slidesPerView: 2 }
//                 }
//             });
//         });
//     </script>';
// }


$output .= '
<style>
    .pwe-header__partners {
        position: relative;
        display: flex;
        justify-content: center;
        flex-direction: column;
        border-radius: 18px;
        padding: 10px;
        z-index: 1;
        opacity: 0;
        transition: .3s ease-in;
    }
    .pwe-header__partners-wrapper {
        position: relative;
        padding: 18px;
        color: #fff;
        max-width: 380px; 
    }
    .pwe-header__partners-wrapper:before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgb(144 153 166 / 11%);
        z-index: 1;
        border-radius: 20px;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid #ffffff24;
    }
    .pwe-header__partners-container {
        position: relative;
        width: 100%;
        max-width: 280px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-top: 4px;
        z-index: 1;
    }
    .pwe-header__partners-container:not(:first-child) {
        margin-top: 10px;
    }
    .pwe-header__partners-title {
        margin: 0 auto;
    }
    .pwe-header__partners-title h3 {
        color: white;
        max-width: 280px;
        text-align: center;
        margin: 0;
        font-size: 16px;
    }
    .pwe-header__partners-items {
        width: 100%;
    }
    .pwe-header__partners-item {
        background: white;
        padding: 10px;
        border-radius: 10px;
    }
    .pwe-header__partners-item, 
    .pwe-header__partners-item a {
        text-align: center;
    }
    .pwe-header__partners-item span {
        font-weight: 600;
        color: black;
    }
    .pwe-header__partners-item img {
        aspect-ratio: 3/2;
        object-fit: contain;    
    }

    @media(max-width:960px){
        .pwe-header__partners {
            position: static;
            top: unset;
            right: unset;
            transform: unset;
            flex-direction: row;
            flex-wrap: wrap;
            width: 100%;
            padding: 0;
            margin-top: 20px;
        }
        .pwe-header__partners-wrapper {
            max-width: 100%;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 30px;
        }
        .pwe-header__partners-container {
            margin: 0 !important;
        }
    }
    @media(max-width:570px) {
        .pwe-header__partners {
            margin-bottom: -2px;
            border-radius: 18px 18px 0 0;
        }
    }
    @media(max-width:450px) {
        .pwe-header__partners-container {
            max-width: 340px;
        }
    }
</style>';

$files = [];
$grouped_logos = [];

$meta_data = PWECommonFunctions::get_database_meta_data('header_order');

if (!empty($meta_data)) {
    $header_order = $meta_data[0]->meta_data;
}

$grouped_logos = [];
$current_locale = get_locale();
$current_lang = substr($current_locale, 0, 2);

foreach ($cap_logotypes_data as $logo_data) {

    if (strpos($logo_data->logos_type, 'header-') === 0) { 

        $meta = json_decode($logo_data->meta_data, true);
        $data = json_decode($logo_data->data ?? '{}', true);

        $desc_pl = $meta["desc_pl"] ?? '';
        $desc_en = $meta["desc_en"] ?? '';

        $url   = 'https://cap.warsawexpo.eu/public' . $logo_data->logos_url;
        $name  = $data['logos_exh_name'];
        $order = isset($logo_data->logos_order) ? (int)$logo_data->logos_order : PHP_INT_MAX;

        $visibilityFlags = array_filter($data, function ($key) {
            return preg_match('/^logos_[a-z]{2}_[A-Z]{2}$/', $key);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($visibilityFlags)) {
            $showLogo = true;
        } else {
            $flagKey = 'logos_' . $current_locale;

            if (isset($visibilityFlags[$flagKey])) {
                $showLogo = ($visibilityFlags[$flagKey] === 'true');
            } else {
                $showLogo = false;
            }
        }

        if (!$showLogo) {
            continue;
        }

        $link_pl = $data['logos_link']     ?? null;
        $link_en = $data['logos_link_en']  ?? null;

        if (!empty($link_pl) && empty($link_en)) {
            $finalLink = $link_pl;

        } elseif (!empty($link_pl) && !empty($link_en)) {
            if ($current_lang === 'pl') {
                $finalLink = $link_pl;
            } else {
                $finalLink = $link_en;
            }

        } else {
            // brak linków
            $finalLink = null;
        }

        $element = [
            'url'      => $url,
            'desc_pl'  => $desc_pl,
            'desc_en'  => $desc_en,
            'link'     => $finalLink,
            'name'     => $name,
            'order'    => $order,
        ];

        $grouped_logos[$logo_data->logos_type][] = $element;
    }
}

$total_logos = array_sum(array_map('count', $grouped_logos));

// Variation Mapping
$plural_map_pl = [
    "Prelegent" => "Prelegenci",
    "Partner Targów" => "Partnerzy Targów",
    "Partner Merytoryczny" => "Partnerzy Merytoryczni",
    "Partner Branżowy" => "Partnerzy Branżowi",
    "Partner targów i konferencji" => "Partnerzy Targów i Konferencji",
    "Patronat Honorowy" => "Patronaty Honorowe",
    "Partner Organizacyjny" => "Partnerzy Organizacyjni"
];

$plural_map_en = [
    "Speaker" => "Speakers",
    "Fair Partner" => "Fair Partners",
    "Content Partner" => "Content Partners",
    "Industry Partner" => "Industry Partners",
    "Trade and Conference Partner" => "Trade and Conference Partners",
    "Honorary Patronage" => "Honorary Patronages",
    "Organizational partner" => "Organizational partners"
];

if (count($grouped_logos) > 0) {

    // Apply group based on $header_order
    $ordered_types = [];

    if (!empty($header_order)) {
        // Explode and remove empty elements/spaces
        $parts = array_filter(array_map('trim', explode(',', $header_order)));

        // Keep original values, but only those that start with "header-"
        foreach ($parts as $p) {
            if ($p !== '') $ordered_types[] = $p;
        }
    } else {
        // Default: all types in the order they are in $grouped_logos
        $ordered_types = array_keys($grouped_logos);
    }

    $output .= '
    <div id="pweHeaderPartners" class="pwe-header__partners">
        <div class="pwe-header__partners-wrapper">';
        // Generating containers for each group
        foreach ($ordered_types as $logos_type) {

            if (!isset($grouped_logos[$logos_type])) {
                continue;
            }

            $files = $grouped_logos[$logos_type];

            // Sort files by logos_order (ascending). If missing, order -> at the end.
            usort($files, function($a, $b) {
                $oa = $a['order'] ?? PHP_INT_MAX;
                $ob = $b['order'] ?? PHP_INT_MAX;
                if ($oa === $ob) return 0;
                return ($oa < $ob) ? -1 : 1;
            });

            if (count($files) > 0) {
                $unique_id = uniqid();

                // Group Title
                $title_single = PWECommonFunctions::lang_pl() ? $files[0]["desc_pl"] : $files[0]["desc_en"];

                // Automatic pluralization
                if (count($files) > 1) {
                    // plural
                    if (PWECommonFunctions::lang_pl()) {
                        $title = $plural_map_pl[$title_single] ?? $title_single;
                    } else {
                        $title = $plural_map_en[$title_single] ?? $title_single;
                    }
                } else {
                    // singular
                    $title = $title_single;
                }

                

                $output .= '
                <div class="pwe-header__partners-container '. $logos_type .' partners-'. $unique_id .'">
                    <div class="pwe-header__partners-title">
                        <h3>'. $title .'</h3>
                    </div>
                    <div class="pwe-header__partners-items swiper">
                        <div class="swiper-wrapper">';

                        foreach ($files as $item) {
                            if (!empty($item["url"])) {
                                if (!empty($item["link"])) {
                                    $output .= '
                                    <div class="pwe-header__partners-item swiper-slide">
                                        <a href="'. $item["link"] .'" target="_blank">
                                            <img src="'. $item["url"] .'" alt="partner logo">
                                            '. (!empty($item["name"]) ? '<span>'. $item["name"] .'</span>' : '') .'
                                        </a>
                                    </div>';
                                } else {
                                    $output .= '
                                    <div class="pwe-header__partners-item swiper-slide">
                                        <img src="'. $item["url"] .'" alt="partner logo">
                                        '. (!empty($item["name"]) ? '<span>'. $item["name"] .'</span>' : '') .'
                                    </div>';
                                }
                            }
                        }
                    $output .= '
                        </div>
                    </div> 
                </div>';

                if (class_exists('PWE_Swiper') && $total_logos > 8 && count($grouped_logos) > 2 && count($files) > 2) {
                    $output .= '
                    <style>
                        @media(max-width:650px) {
                            .partners-'. $unique_id .'.pwe-header__partners-container {
                                max-width: 600px !important;
                            }
                        }
                    </style>';

                    $output .= PWE_Swiper::swiperScripts('.partners-'. $unique_id, [0   => ['slidesPerView' => 2],450   => ['slidesPerView' => 3],650   => ['slidesPerView' => 2]], true);

                } else {
                    $output .= '
                    <style>
                        .partners-'. $unique_id .'.pwe-header__partners-container {
                            max-width: 280px;
                        }
                        .partners-'. $unique_id .' .pwe-header__partners-items .swiper-wrapper {
                            justify-content: center;
                            flex-wrap: wrap;
                            gap: 8px;
                        }
                        .partners-'. $unique_id .' .pwe-header__partners-item {
                            max-width: 130px;
                            height: auto;
                        }
                        @media(max-width:960px) {
                            .partners-'. $unique_id .'.pwe-header__partners-container {
                                max-width: 100%;
                            }
                        }
                    </style>';
                }
            
            }
        }
        $output .= '
        </div>
    </div>';

    $output .= '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pweHeaderPartners = document.querySelector(".pwe-header__partners");
            const pweHeaderContainer = document.querySelector(".pwe-header__container");

            if (!pweHeaderPartners) {
                return;
            }

            setTimeout(() => {
                pweHeaderPartners.style.opacity = 1;

                const partnersHeight = pweHeaderPartners.offsetHeight;
                const containerHeight = pweHeaderContainer.offsetHeight;

                const diff = Math.abs(partnersHeight - containerHeight);

                if (containerHeight < partnersHeight || diff < 100) {
                    pweHeaderContainer.style.minHeight = partnersHeight + 100 + "px";
                }
            }, 300);
           
        }); 
    </script>';

}
