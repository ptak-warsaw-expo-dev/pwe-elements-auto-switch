<?php
class PWE_Swiper
{
    public function __construct() {} 

    public static function swiperScripts($id, $breakpoints = [], $dots = false, $arrows = false, $rows = 1)
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
        
        if ($dots) {
            $output .= '
            <style>
                #pweElementsAutoSwitch ' . $id . ' .pwe-exhibitors__nav {
                    margin: 18px auto 0;
                    max-width:200px;
                }
                #pweElementsAutoSwitch ' . $id . ' .wc-dots{
                    --dot:10px;
                    --gap:8px;
                    --side:3;
                    display:flex;
                    align-items:center;
                    gap:var(--gap);
                    width:100%;
                }
                #pweElementsAutoSwitch ' . $id . ' .wc-dot{
                    width:var(--dot);
                    height:var(--dot);
                    border-radius:9999px;
                    background:#c9d3ff;
                    flex:0 0 var(--dot);
                    transition:width .35s ease, background-color .2s ease;
                    cursor:pointer;
                }
                #pweElementsAutoSwitch ' . $id . ' .wc-dot--active{
                    background:#bfe8df;
                    width:calc( (100% - (var(--side) * (var(--dot) + var(--gap)))) * 0.9 );
                    flex:0 1 auto;
                    height:8px;
                    border-radius:9999px;
                }
            </style>';
        }

        if ($arrows) {
            $output .= '
            <style>
                #pweElementsAutoSwitch ' . $id . ' .swiper-buttons-arrows {
                    display: flex;
                    gap: 20px;
                }
                #pweElementsAutoSwitch ' . $id . ' .swiper-button-next,
                #pweElementsAutoSwitch ' . $id . ' .swiper-button-prev {
                    background: #dde6ff;
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
                #pweElementsAutoSwitch ' . $id . ' .swiper-button-next:hover,
                #pweElementsAutoSwitch ' . $id . ' .swiper-button-prev:hover {
                    box-shadow: 0px 0px 12px #c7c7c7ff;
                }
                #pweElementsAutoSwitch ' . $id . ' .swiper-button-next::after,
                #pweElementsAutoSwitch ' . $id . ' .swiper-button-prev::after {
                    display: none;
                }
            </style>';
        }

        $output .= '
        <script>
            jQuery(function($){
                if (typeof Swiper === "undefined") return;

                const rootSel = "' . $id . '";
                const $root   = $(rootSel);
                const breakpoints = ' . $breakpoints_json . ';

                function getSPV(){
                    const w = window.innerWidth;
                    const list = Object.entries(breakpoints)
                    .map(([bp,cfg]) => [parseInt(bp,10), cfg.slidesPerView])
                    .sort((a,b)=>a[0]-b[0]);
                    let spv = list[0][1];
                    for (const [bp,val] of list) if (w >= bp) spv = val;
                    return spv;
                }

                const slides = document.querySelectorAll("' . $id . ' .swiper-slide").length;

                const swiper = new Swiper("' . $id . ' .swiper", {
                    spaceBetween: 18,
                    grabCursor: true,';
                    if ($rows != 1) {
                        $output .= '
                        loop: false,
                        grid: {
                            rows: '. $rows .',
                            fill: "row"
                        },';
                    } else {
                        $output .= '
                        loop: true,';
                    }
                    $output .= '
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: true
                    },
                    breakpoints: ' . $breakpoints_json;
                    if ($arrows) {
                        $output .= ',
                        navigation: {
                            nextEl: "' . $id . ' .swiper-button-next",
                            prevEl: "' . $id . ' .swiper-button-prev"
                        }';
                    }
                $output .= '
                });

                $root[0].__wcSwiper = swiper;

                const dotsWrap = $root.find(".wc-dots")[0];
                if (!dotsWrap) return;

                function buildDots(s){
                    s = s || $root[0].__wcSwiper;
                    if (!s) return;

                    const spv = getSPV();
                    const totalPages = Math.ceil(s.slides.length / spv);
                    const navCount   = Math.min(4, totalPages);
                    const groupSize  = Math.ceil(totalPages / navCount);

                    dotsWrap.style.setProperty("--side", Math.max(0, navCount - 1));
                    dotsWrap.innerHTML = "";

                    for (let i=0; i<navCount; i++){
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "wc-dot";
                    btn.addEventListener("click", () => {
                        const targetPage  = i * groupSize;
                        const targetIndex = targetPage * spv;
                        s.slideTo(targetIndex);
                    });
                    dotsWrap.appendChild(btn);
                    }
                    requestAnimationFrame(() => updateDots(s));
                }

                function updateDots(s){
                    const spv = getSPV();
                    const totalPages = Math.ceil(s.slides.length / spv);
                    const navCount = Math.min(4, totalPages);
                    const currentPage = Math.floor(s.activeIndex / spv);
                    const activeIdx = Math.min(navCount - 1, currentPage % navCount);

                    [...dotsWrap.children].forEach((el, idx) =>
                        el.classList.toggle("wc-dot--active", idx === activeIdx)
                    );
                }

                requestAnimationFrame(() => buildDots(swiper));
                swiper.on("slideChangeTransitionEnd", () => updateDots(swiper));
                window.addEventListener("resize", () => buildDots(swiper));

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