// =============================================================
//  SWIPER – inicjalizacja dla produktów i dokumentów w kartach
// =============================================================

function ecInitExhibitorCardSliders(scope = document) {

    scope.querySelectorAll(".exhibitor-catalog__exh-card-files").forEach(function (root) {
        if (root.__exhInited) return;
        root.__exhInited = true;

        const productsEl  = root.querySelector(".exhibitor-catalog__exh-card-files-products");
        const documentsEl = root.querySelector(".exhibitor-catalog__exh-card-files-documents");
        const tabs        = Array.from(root.querySelectorAll(".exhibitor-catalog__exh-card-files-tab"));

        const hasProducts  = !!productsEl;
        const hasDocuments = !!documentsEl;

        if (!hasProducts && !hasDocuments) return;

        // ------------------------------------------
        // TWORZENIE POJEDYNCZEGO SWIPERA
        // ------------------------------------------
        function makeSwiper(el) {
            if (!el) return null;

            const nextBtn = el.querySelector(".exh-files__next");

            return new Swiper(el, {
                wrapperClass: "swiper-wrapper",
                slideClass:   "swiper-slide",
                watchOverflow: true,
                autoplay: false,
                navigation: nextBtn ? { nextEl: nextBtn } : undefined,

                breakpoints: {
                    480:  { slidesPerView: 1.6, spaceBetween: 6 },
                    640:  { slidesPerView: 2.2, spaceBetween: 8 },
                    768:  { slidesPerView: 2.6, spaceBetween: 10 },
                    1024: { slidesPerView: 3.5, spaceBetween: 12 },
                },

                on: {
                    init() {
                        el.style.opacity = "1";
                        el.style.visibility = "visible";
                        el.style.height = "auto";
                        el.style.transition = "opacity 0.3s ease";
                    }
                }
            });
        }

        const productsSwiper  = makeSwiper(productsEl);
        const documentsSwiper = makeSwiper(documentsEl);

        // ------------------------------------------
        // PRZEŁĄCZANIE TABÓW
        // ------------------------------------------
        function showPanel(which) {
            const showProducts = which === "products";

            if (hasProducts)  productsEl.toggleAttribute("hidden", !showProducts);
            if (hasDocuments) documentsEl.toggleAttribute("hidden", showProducts);

            tabs.forEach(btn => {
                btn.classList.toggle("is-active", btn.dataset.tab === which);
            });

            const sw = showProducts ? productsSwiper : documentsSwiper;
            if (sw) setTimeout(() => sw.update(), 40);
        }

        // ------------------------------------------
        // LOGIKA TABÓW
        // ------------------------------------------
        if (hasProducts && hasDocuments) {

            showPanel("products");

            tabs.forEach(btn =>
                btn.addEventListener("click", () => showPanel(btn.dataset.tab))
            );

        } else {
            const single = hasProducts ? "products" : "documents";
            showPanel(single);
        }
    });
}

// =============================================================
//  AUTO-INIT DLA WCZYTANEGO DOMU
// =============================================================

document.addEventListener("DOMContentLoaded", () => {
    ecInitExhibitorCardSliders();
});
