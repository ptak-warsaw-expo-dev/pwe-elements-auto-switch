<?php
$output .= '
<div id="pweHeaderPartners" class="pwe-header__partners">
    <div class="pwe-header__partners-wrapper">';

foreach ($ordered_types as $logos_type) {
    if (!isset($grouped_logos[$logos_type])) {
        continue;
    }

    $files = $grouped_logos[$logos_type];

    usort($files, function($a, $b) {
        $oa = $a['order'] ?? PHP_INT_MAX;
        $ob = $b['order'] ?? PHP_INT_MAX;

        if ($oa === $ob) {
            return 0;
        }

        return ($oa < $ob) ? -1 : 1;
    });

    if (count($files) > 0) {
        $unique_id = uniqid();
        $slider_enabled = true;

        if (isset($slider_settings_data) && is_array($slider_settings_data)) {
            if (array_key_exists($logos_type, $slider_settings_data)) {
                $slider_enabled = filter_var(
                    $slider_settings_data[$logos_type],
                    FILTER_VALIDATE_BOOLEAN
                );
            } else {
                $logos_type_without_header = preg_replace('/^header-/', '', $logos_type);

                if (array_key_exists($logos_type_without_header, $slider_settings_data)) {
                    $slider_enabled = filter_var(
                        $slider_settings_data[$logos_type_without_header],
                        FILTER_VALIDATE_BOOLEAN
                    );
                }
            }
        }

        // Group Title
        $title_single = PWE_Functions::lang_pl()
            ? $files[0]["desc_pl"]
            : $files[0]["desc_en"];

        // Automatic pluralization
        if (count($files) > 1) {
            if (PWE_Functions::lang_pl()) {
                $title = $plural_map_pl[$title_single] ?? $title_single;
            } else {
                $title = $plural_map_en[$title_single] ?? $title_single;
            }
        } else {
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

        $should_use_slider =
            $slider_enabled &&
            class_exists('PWE_Swiper') &&
            (
                ($total_logos >= 7 && count($grouped_logos) >= 2 && count($files) > 2) ||
                (count($grouped_logos) >= 1 && count($files) > 8)
            );

        if ($should_use_slider) {
            $output .= PWE_Swiper::swiperScripts(
                '.partners-'. $unique_id,
                [0 => ['slidesPerView' => 2]],
                true
            );
        } else {
            $output .= '
            <style>
                .partners-'. $unique_id .'.pwe-header__partners-container {
                    max-width: 280px;
                }
                .partners-'. $unique_id .' .pwe-header__partners-items .swiper-wrapper {
                    justify-content: center;
                    flex-wrap: wrap;
                    gap: 18px;
                }
                .partners-'. $unique_id .' .pwe-header__partners-item {
                    max-width: 130px;
                    height: auto;
                }
                @media(max-width:960px) {
                    .partners-'. $unique_id .' .pwe-header__partners-items .swiper-wrapper {
                        gap: 10px;
                    }
                    .partners-'. $unique_id .' .pwe-header__partners-item {
                        max-width: 135px;
                    }
                }
            </style>';

            /*
             * Dotychczasowy wyjątek dla pojedynczego logotypu.
             */
            if (count($files) == 1) {
                $output .= '
                <style>
                    @media(min-width:961px) {
                        .partners-'. $unique_id .' .pwe-header__partners-item {
                            max-width: 100%;
                        }
                        .partners-'. $unique_id .' .pwe-header__partners-item img {
                            max-width: 115px;
                        }
                    }
                </style>';
            }
        }
    }
}

$output .= '
    </div>
</div>';