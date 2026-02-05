<?php
class PWE_Swiper
{
    public function __construct() {} 

    public static function swiperScripts($id, $breakpoints = [], $dots = false, $arrows = false, $rows = 1, $centered = false, $gap = null)
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

        if ($gap === null) {
            $gap = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $_SERVER['HTTP_USER_AGENT']) ? 10 : 18;
        }

        $breakpoints_json  = wp_json_encode($breakpoints, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $output = '
        <style>
            .pwe-element-auto-switch ' . $id . ' {
                visibility: hidden;
                opacity: 0;
                transition: opacity 0.5s ease-in-out;
            }
            .pwe-element-auto-switch ' . $id . ' .swiper {
                width: 100%;
            }
            .pwe-element-auto-switch ' . $id . ' .swiper-wrapper {
                display: flex;
            }
            .pwe-element-auto-switch ' . $id . ' .swiper-slide {
                height: auto;
            }
        </style>';
        
        if ($dots) {
            $output .= '
            <style>
                .pwe-element-auto-switch ' . $id . ' .swiper-nav {
                    display: block;
                    position: sticky;
                    overflow-x: auto;
                    overflow-y: hidden;
                    white-space: nowrap;
                    max-width: 120px;
                    scroll-behavior: smooth;
                    -ms-overflow-style: none;
                    scrollbar-width: none;
                    left: 50% !important;
                    transform: translateX(-50%);
                    margin: 18px auto 0;
                    padding: 0;
                }
                .pwe-element-auto-switch ' . $id . ' .swiper-horizontal>.swiper-pagination-bullets {
                    bottom: 3px;
                }
                .pwe-element-auto-switch ' . $id . ' .swiper-pagination-bullet {
                    width: 20px;
                    height: 12px;
                    border-radius: 8px;
                    transition: .3s ease;
                }
                .pwe-element-auto-switch ' . $id . ' .swiper-pagination-bullet-active {
                    background: var(--accent-color);
                    width: 55px;
                    
                }
                .pwe-element-auto-switch ' . $id . ' .swiper-nav::-webkit-scrollbar{
                    width: 0;
                    height: 0;
                    background: transparent;
                }
                    /* kropki w linii */
                .pwe-element-auto-switch ' . $id . ' .swiper-nav-bullet{
                    display: inline-block;
                    margin: 0 3px;
                }
            </style>';
        }

        if ($arrows) {
            $output .= '
            <style>
                .pwe-element-auto-switch ' . $id . ' .swiper-buttons-arrows {
                    display: flex;
                    gap: 10px;
                }
                .pwe-element-auto-switch ' . $id . ' .swiper-button-next,
                .pwe-element-auto-switch ' . $id . ' .swiper-button-prev {
                    background: var(--background-color);
                    position: relative;
                    left: inherit;
                    right: inherit;
                    color: var(--accent-color);
                    padding: 22px;
                    border-radius: 50%;
                    transition: 0.3s;
                    font-size: 26px;
                    line-height: 1;
                    cursor: pointer;
                }
                .pwe-element-auto-switch ' . $id . ' .swiper-button-next:hover,
                .pwe-element-auto-switch ' . $id . ' .swiper-button-prev:hover {
                    box-shadow: 0px 0px 12px #c7c7c7ff;
                }
                .pwe-element-auto-switch ' . $id . ' .swiper-button-next::after,
                .pwe-element-auto-switch ' . $id . ' .swiper-button-prev::after {
                    display: none;
                }
            </style>';
        }

        $output .= '
        <script>
            jQuery(function($){
                if (typeof Swiper === "undefined") return;

                const partnersElAvailable = '. (stripos($id, 'partners-') !== false ? 'true' : 'false') .';

                const breakpoints = ' . $breakpoints_json . ';

                const swiper = new Swiper("' . $id . ' .swiper", {
                    spaceBetween: ' . $gap . ',
                    centeredSlides: ' . ($centered ? 'true' : 'false') . ',
                    grabCursor: true,
                    breakpoints: breakpoints
                    ' . ($rows != 1 ? ',
                    loop: false,
                    grid: {
                        rows: ' . $rows . ',
                        fill: "row"
                    }' : ',
                    loop: true') . ',
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    }' . ($arrows ? ',
                    navigation: {
                        nextEl: "' . $id . ' .swiper-button-next",
                        prevEl: "' . $id . ' .swiper-button-prev"
                    }' : '') . ',
                    pagination: {
                        el: "' . $id . ' .swiper-nav",
                        clickable: true,
                        dynamicBullets: false,
                        dynamicMainBullets: 3
                    }
                });

                const paginationEl = document.querySelector("' . $id . ' .swiper-nav");

                // Centering the active dot always
                function scrollDotsToActive() {
                    if (!paginationEl) return;

                    const active = paginationEl.querySelector(".swiper-pagination-bullet-active");
                    if (!active) return;

                    const target =
                        active.offsetLeft -
                        (paginationEl.clientWidth / 2) +
                        (active.clientWidth / 2);

                    $(paginationEl).stop(true).animate(
                        { scrollLeft: Math.max(0, target) },
                        300
                    );
                }

                swiper.on("slideChangeTransitionEnd", scrollDotsToActive);
                swiper.on("resize", scrollDotsToActive);
                setTimeout(scrollDotsToActive, 0);

                if (partnersElAvailable == false) {

                    // Function to set equal height
                    function setEqualHeight() {
                        let maxHeight = 0;

                        // Reset the heights before calculations
                        $(".pwe-element-auto-switch ' . $id . ' .swiper-slide").css("height", "auto");

                        // Calculate the maximum height
                        $(".pwe-element-auto-switch ' . $id . ' .swiper-slide").each(function() {
                            const thisHeight = $(this).outerHeight();
                            if (thisHeight > maxHeight) {
                                maxHeight = thisHeight;
                            }
                        });

                        // Set the same height for all
                        $(".pwe-element-auto-switch ' . $id . ' .swiper-slide").css("minHeight", maxHeight);
                    }

                    // Call the function after loading the slider
                    $(".pwe-element-auto-switch ' . $id . ' .swiper").on("init", function() {
                        setEqualHeight();
                    });

                    // Call the function when changing the slide
                    $(".pwe-element-auto-switch ' . $id . ' .swiper").on("afterChange", function() {
                        setEqualHeight();
                    });

                    // Call the function at the beginning
                    setTimeout(setEqualHeight, 1000);

                }

                $(".pwe-element-auto-switch ' . $id . '").css("visibility", "visible").animate({ opacity: 1 }, 500);
            });
        </script>';

        return $output;
    }
}