<?php 

$output .= '
<style>
    .pwe-header__partners {
        position: absolute;
        top: 53%;
        transform: translate(0, -50%);
        right: 18px;
        display: flex;
        justify-content: center;
        flex-direction: column;
        background-color: white;
        border-radius: 18px;
        padding: 10px;
        z-index: 1;
        opacity: 0;
        transition: .3s ease-in;
    }
    .pwe-header__partners-container {
        width: 100%;
        max-width: 280px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .pwe-header__partners-title {
        margin: 0 auto;
    }
    .pwe-header__partners-title h3 {
        color: black;
        text-transform: uppercase;
        max-width: 280px;
        text-align: center;
        margin: 16px auto 0;
        font-size: 18px;
    }
    .pwe-header__partners-items {
        width: 100%;
    }
    .pwe-header__partners-item, 
    .pwe-header__partners-item a {
        text-align: center;
    }
    .pwe-header__partners-item span {
        font-weight: 600;
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

foreach ($cap_logotypes_data as $logo_data) {
    if (strpos($logo_data->logos_type, 'header-') === 0) {
        $meta = json_decode($logo_data->meta_data, true);
        $desc_pl = $meta["desc_pl"] ?? '';
        $desc_en = $meta["desc_en"] ?? '';
        $link    = $logo_data->logos_link;
        $url     = 'https://cap.warsawexpo.eu/public' . $logo_data->logos_url;
        $name    = $logo_data->logos_exh_name;
        $order   = isset($logo_data->logos_order) ? (int)$logo_data->logos_order : PHP_INT_MAX;

        $element = [
            'url' => $url,
            'desc_pl' => $desc_pl,
            'desc_en' => $desc_en,
            'link' => $link,
            'name' => $name,
            'order' => $order,
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
    <div id="pweHeaderPartners" class="pwe-header__partners">';
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

                if ($total_logos > 8 && count($grouped_logos) > 2 && count($files) > 2) {
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
    </div>';

    $output .= '
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pweHeaderPartners = document.querySelector(".pwe-header__partners");
            if (pweHeaderPartners) {
                setTimeout(() => {
                    pweHeaderPartners.style.opacity = 1;
                }, 300);
            }
        }); 
    </script>';

}
