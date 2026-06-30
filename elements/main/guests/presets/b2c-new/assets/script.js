document.addEventListener("DOMContentLoaded", function () {
    const sliders = document.querySelectorAll("#pweGuests .embla");

    sliders.forEach((root) => {
        if (!root || typeof EmblaCarousel === "undefined") return;

        const slider = root.closest(".pwe-guests");
        const slides = Array.from(root.querySelectorAll(".embla__slide"));
        const inners = Array.from(root.querySelectorAll(".embla__slide__inner"));

        const CONFIG = {
            maxRotate: 62,
            maxTranslateX: 74,
            maxTranslateZ: 260,
            minScale: 0.68,
            maxBlur: 0,
            visibleRange: 4
        };

        const startIndex = [3, 5].includes(slides.length)
            ? Math.floor(slides.length / 2)
            : 0;

        const autoplay = EmblaCarouselAutoplay({
            delay: 3000,
            stopOnInteraction: true,
            stopOnMouseEnter: true
        });

        const embla = EmblaCarousel(root, {
            loop: true,
            align: "center",
            startIndex: startIndex,
            containScroll: false,
            dragFree: false,
            skipSnaps: false
        }, [autoplay]);

        function clamp(value, min, max) {
            return Math.min(Math.max(value, min), max);
        }

        function updateCoverflow() {
            const engine = embla.internalEngine();
            const scrollProgress = embla.scrollProgress();
            const snaps = embla.scrollSnapList();

            slides.forEach(function (slide, index) {
                const inner = inners[index];
                if (!inner) return;

                let diffToTarget = snaps[index] - scrollProgress;

                if (engine.options.loop) {
                    engine.slideLooper.loopPoints.forEach(function (loopPoint) {
                        const target = loopPoint.target();

                        if (index === loopPoint.index && target !== 0) {
                            if (target < 0) diffToTarget = snaps[index] - (1 + scrollProgress);
                            if (target > 0) diffToTarget = snaps[index] + (1 - scrollProgress);
                        }
                    });
                }

                const distance = diffToTarget * slides.length;
                const abs = Math.abs(distance);
                const sign = Math.sign(distance);
                const limited = clamp(abs, 0, CONFIG.visibleRange);
                const progress = limited / CONFIG.visibleRange;

                const rotateY = -sign * progress * CONFIG.maxRotate;
                const translateX = -sign * progress * CONFIG.maxTranslateX;
                const translateZ = -progress * CONFIG.maxTranslateZ;
                const scale = 1 - progress * (1 - CONFIG.minScale);
                const blur = progress * CONFIG.maxBlur;
                const zIndex = 1000 - Math.round(abs * 100);

                inner.style.transform = `
                    translateX(${translateX}px)
                    translateZ(${translateZ}px)
                    rotateY(${rotateY}deg)
                    scale(${scale})
                `;

                inner.style.filter = `blur(${blur}px)`;
                inner.style.zIndex = String(zIndex);
                slide.style.zIndex = String(zIndex);
            });
        }

        embla.on("init", updateCoverflow);
        embla.on("scroll", updateCoverflow);
        embla.on("select", updateCoverflow);
        embla.on("settle", updateCoverflow);
        embla.on("reInit", updateCoverflow);

        updateCoverflow();
    });

});

function initTabs() {
    const buttons = document.querySelectorAll(".pwe-guests__tab-btn");
    const contents = document.querySelectorAll(".pwe-guests__tab-content");

    if (!buttons.length || !contents.length) return;

    buttons.forEach(btn => {
        btn.addEventListener("click", () => {
            const tab = btn.getAttribute("data-tab");

            buttons.forEach(b => b.classList.remove("is-active"));
            contents.forEach(c => c.classList.remove("is-active"));

            btn.classList.add("is-active");

            const target = document.querySelector(
                `.pwe-guests__tab-content[data-tab="${tab}"]`
            );

            if (target) {
                target.classList.add("is-active");

                // Aktualizacja instancji Swipera wewnątrz aktywowanej zakładki
                const swiperEl = target.querySelector('.swiper');
                if (swiperEl && swiperEl.swiper) {
                    swiperEl.swiper.update();
                }
            }
        });
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initTabs);
} else {
    initTabs();
}
