<?php
class PWE_Swiper
{
    public function __construct() {}

    public static function swiperScripts($id, $breakpoints = [], $scrollbar = false)
    {
        // domyślne breakpoints jeśli puste
        if (empty($breakpoints)) {
            $breakpoints = [
                0  => ['slidesPerView' => 1],
                650  => ['slidesPerView' => 2],
                960  => ['slidesPerView' => 3],
                1100 => ['slidesPerView' => 4],
            ];
        }

        $breakpoints_json  = wp_json_encode($breakpoints, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $output = '
        <style>
            #pweElementsAutoSwitch ' . $id . ' .swiper {
                width: 100%;
            }
            #pweElementsAutoSwitch ' . $id . ' .swiper-wrapper {
                display: flex;
            }
        </style>';
        
        if ($scrollbar) {
            $output .= '
            <style>
                #pweElementsAutoSwitch ' . $id . ' .swiper-scrollbar {
                    margin-top: 18px;
                    position: inherit;
                    height: 8px;
                }
            </style>';
        }

        $output .= '
        <script>
            jQuery(function($){
                if (typeof Swiper === "undefined") return;

                const slides = document.querySelectorAll("' . $id . ' .swiper-slide").length;
                const breakpoints = ' . $breakpoints_json . ';

                // Find the largest slidesPerView of all breakpoints
                const maxSlidesPerView = Math.max(...Object.values(breakpoints).map(bp => bp.slidesPerView));

                new Swiper("' . $id . ' .swiper", {
                    spaceBetween: 18,
                    grabCursor: true,
                    loop: slides > maxSlidesPerView,';
                  
                    $output .= '
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    },';
                    
                    $output .= '
                    breakpoints: ' . $breakpoints_json;

            if ($scrollbar) {
                $output .= ',
                    scrollbar: {
                        el: "' . $id . ' .swiper-scrollbar",
                        draggable: false
                    }';
            }

            $output .= '
            });

            // Function to set equal height
            function setEqualHeight() {
                let maxHeight = 0;

                // Reset the heights before calculations
                $("#pweElementsAutoSwitch ' . $id . ' .swiper-slide").css("height", "auto");

                // Calculate the maximum height
                $("#pweElementsAutoSwitch ' . $id . ' .swiper-slide").each(function() {
                    const thisHeight = $(this).outerHeight();
                    if (thisHeight > maxHeight) {
                        maxHeight = thisHeight;
                    }
                });

                // Set the same height for all
                $("#pweElementsAutoSwitch ' . $id . ' .swiper-slide").css("minHeight", maxHeight);
            }

            // Call the function after loading the slider
            $("#pweElementsAutoSwitch ' . $id . ' .swiper").on("init", function() {
                setEqualHeight();
            });

            // Call the function when changing the slide
            $("#pweElementsAutoSwitch ' . $id . ' .swiper").on("afterChange", function() {
                setEqualHeight();
            });

            // Call the function at the beginning
            setEqualHeight();

            $("#pweElementsAutoSwitch ' . $id . '").css("visibility", "visible").animate({ opacity: 1 }, 500);
            });
        </script>';

        return $output;
    }
}