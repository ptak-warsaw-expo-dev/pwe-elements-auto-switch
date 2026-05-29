<?php 

$cap_logotypes_data = PWE_Functions::get_database_logotypes_data();
if (!empty($cap_logotypes_data)) {

    // Output style
    $output .= '<style>' . file_get_contents(plugin_dir_path(__FILE__) . $group . '/assets/style.css') . '</style>';

    $files = [];
    $grouped_logos = [];

    $header_order = PWE_Functions::get_database_meta_data('logos_meta_order', $_SERVER['HTTP_HOST']);
    if (!empty($header_order)) { 
        $header_order = $header_order[0]->meta_data; 
    }

    $grouped_logos = [];
    $locale = get_locale();
    $lang = substr($locale, 0, 2);

    $flagKey = 'logos_' . $locale;
    $fallbackKey = 'logos_en_US';

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

            $flagKey = 'logos_' . $locale;

            if (!empty($visibilityFlags)) {

                if (isset($visibilityFlags[$flagKey])) {
                    $showLogo = $visibilityFlags[$flagKey] === 'true';
                } elseif ($lang !== 'pl' && isset($visibilityFlags[$fallbackKey])) {
                    $showLogo = $visibilityFlags[$fallbackKey] === 'true';
                } else {
                    $showLogo = true; // fallback globalny
                }

            } else {
                $showLogo = true;
            }

            if (!$showLogo) {
                continue;
            }

            $link_pl = $data['logos_link']     ?? null;
            $link_en = $data['logos_link_en']  ?? null;

            if (!empty($link_pl) && empty($link_en)) {
                $finalLink = $link_pl;

            } elseif (!empty($link_pl) && !empty($link_en)) {
                if ($lang === 'pl') {
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
        "Partner Strategiczny" => "Partnerzy Strategiczni",
        "Partner Branżowy" => "Partnerzy Branżowi",
        "Partner targów i konferencji" => "Partnerzy Targów i Konferencji",
        "Patronat Honorowy" => "Patronaty Honorowe",
        "Partner Organizacyjny" => "Partnerzy Organizacyjni",
        "Współorganizator" => "Współorganizatorzy"
    ];

    $plural_map_en = [
        "Speaker" => "Speakers",
        "Fair Partner" => "Fair Partners",
        "Content Partner" => "Content Partners",
        "Strategic Partner" => "Strategic Partners",
        "Industry Partner" => "Industry Partners",
        "Trade and Conference Partner" => "Trade and Conference Partners",
        "Honorary Patronage" => "Honorary Patronages",
        "Organizational partner" => "Organizational partners",
        "Co-organizer" => "Co-organizers"
    ];

    if (count($grouped_logos) > 0) {

        // Apply group based on $header_order
        $ordered_types = [];

        if (!empty($header_order)) {
            // If it is not an array, replace
            if (!is_array($header_order)) {
                $header_order = json_decode($header_order, true);
            }

            if (is_array($header_order)) {
                // Sorting by value ASC
                asort($header_order, SORT_NUMERIC);

                // Extracting keys that start with 'header-'
                foreach ($header_order as $key => $val) {
                    if (strpos($key, 'header-') === 0) {
                        $ordered_types[] = $key;
                    }
                }
            }
        } else {
            // Default: all types in the order they are in $grouped_logos
            $ordered_types = array_keys($grouped_logos);
        }

        // Preset
        require_once plugin_dir_path(__FILE__) . $group . '/preset.php';

        // Output script
        $output .= '<script>' . file_get_contents(plugin_dir_path(__FILE__) . $group . '/assets/script.js') . '</script>';
    }
}