// product-modal.js
(function() {
    // ===== 0) Public API (global) =====
    window.ProductModal = {
        initForSingle,
        initForMain,
        openWithData, // ręczne otwarcie: openWithData(productsArray, startIndex, openerEl)
    };

    // ===== 2) Swiper assets (ładujemy raz) =====
    function injectSwiperAssetsOnce() {
        if (document.getElementById("pwe-swiper-css")) return Promise.resolve();
        return new Promise((resolve) => {
            const link = document.createElement("link");
            link.id = "pwe-swiper-css";
            link.rel = "stylesheet";
            link.href = "https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css";

            const script = document.createElement("script");
            script.id = "pwe-swiper-js";
            script.src = "https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js";
            script.defer = true;

            let loaded = 0;

            function done() { if (++loaded === 2) resolve(); }

            link.onload = done;
            script.onload = done;

            document.head.appendChild(link);
            document.head.appendChild(script);
        });
    }

    // ===== 3) Utils =====
    function lockScroll() { document.documentElement.classList.add("product-modal-open"); }

    function unlockScroll() { document.documentElement.classList.remove("product-modal-open"); }

    function trapFocus(container, e) {
        const focusables = container.querySelectorAll(`a,button,textarea,input,select,[tabindex]:not([tabindex="-1"])`);
        if (!focusables.length) return;
        const first = focusables[0],
            last = focusables[focusables.length - 1];
        if (e.shiftKey && document.activeElement === first) { last.focus();
            e.preventDefault(); } else if (!e.shiftKey && document.activeElement === last) { first.focus();
            e.preventDefault(); }
    }

    function extractDataFromTile(tile) {
        const img = tile.querySelector("img");
        const title = tile.querySelector(".exhibitor-single__product-title, .product-title");
        const desc = tile.getAttribute("data-desc") || "";
        return {
            imgSrc: img ? img.src : "",
            imgAlt: img ? (img.alt || "") : "",
            title: title ? title.textContent.trim() : "",
            desc
        };
    }

    function collectAllProductsData(listRoot, tileSelector) {
        return Array.from(listRoot.querySelectorAll(tileSelector))
            .map(extractDataFromTile);
    }

    // ===== 4) Tworzenie i otwieranie modalu (Twoja struktura + Swiper) =====
    function createProductModal(productsData, startIndex, openerEl) {

        const backdrop = document.createElement("div");
        backdrop.className = "exhibitor-single-product-modal__background";
        backdrop.setAttribute("role", "dialog");
        backdrop.setAttribute("aria-modal", "true");
        backdrop.setAttribute("aria-labelledby", "productModalTitle");

        backdrop.innerHTML = `
      <div class="exhibitor-single-product-modal">
        <button class="exhibitor-single-product-modal__close" type="button" aria-label="Zamknij" data-close>&times;</button>

        <div class="swiper" aria-roledescription="carousel">
          <div class="swiper-wrapper">
            ${productsData.map(p => `
              <div class="swiper-slide">
                <div class="exhibitor-single-product-modal__media">
                  <img src="${p.imgSrc || ""}" alt="${p.imgAlt || ""}">
                </div>
                <div class="exhibitor-single-product-modal__text">
                  <h4 id="productModalTitle" class="exhibitor-single-product-modal__title">${p.title || ""}</h4>
                  <p class="exhibitor-single-product-modal__desc">${p.desc || ""}</p>
                </div>
              </div>
            `).join("")}
          </div>

            <button class="swiper-button-prev exhibitor-single-product-modal__arrow exhibitor-single-product-modal__prev" type="button" aria-label="Poprzedni produkt">
                <!-- lewa strzałka -->
                <svg viewBox="0 0 24 24" id="next" data-name="Flat Color"
                    xmlns="http://www.w3.org/2000/svg" class="icon flat-color">
                    <g transform="matrix(-1, 0, 0, 1, 24, 0)">
                        <path id="primary"
                            d="M18.6,11.2l-12-9A1,1,0,0,0,5,3V21a1,1,0,0,0,.55.89,1,1,0,0,0,1-.09l12-9a1,1,0,0,0,0-1.6Z" />
                    </g>
                </svg>
            </button>

            <button class="swiper-button-next exhibitor-single-product-modal__arrow exhibitor-single-product-modal__next" type="button" aria-label="Następny produkt">
                <!-- prawa strzałka -->
                <svg viewBox="0 0 24 24" id="next" data-name="Flat Color" xmlns="http://www.w3.org/2000/svg" class="icon flat-color" transform="matrix(1, 0, 0, 1, 0, 0)"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path id="primary" d="M18.6,11.2l-12-9A1,1,0,0,0,5,3V21a1,1,0,0,0,.55.89,1,1,0,0,0,1-.09l12-9a1,1,0,0,0,0-1.6Z"></path></g></svg>
            </button>
          <div class="swiper-pagination" aria-label="Paginacja produktów"></div>
        </div>
      </div>
    `;

        document.body.appendChild(backdrop);
        requestAnimationFrame(() => backdrop.classList.add("is-open"));
        lockScroll();

        const dialog = backdrop.querySelector(".exhibitor-single-product-modal");
        const closeBtn = backdrop.querySelector("[data-close]");
        if (closeBtn) closeBtn.focus();

        function destroy() {
            backdrop.classList.remove("is-open");
            setTimeout(() => {
                backdrop.remove();
                unlockScroll();
                if (openerEl) openerEl.focus();
                document.removeEventListener("keydown", onKeyDown);
                backdrop.removeEventListener("click", onBackdropClick);
            }, 150);
        }

        function onBackdropClick(e) {
            if (e.target === backdrop || e.target.hasAttribute("data-close")) destroy();
        }

        function onKeyDown(e) {
            if (e.key === "Escape") destroy();
            if (e.key === "Tab" && dialog) trapFocus(dialog, e);
        }
        backdrop.addEventListener("click", onBackdropClick);
        document.addEventListener("keydown", onKeyDown);

        injectSwiperAssetsOnce().then(() => {
            /* global Swiper */
            const swiper = new Swiper(backdrop.querySelector(".swiper"), {
                initialSlide: Number.isInteger(startIndex) ? startIndex : 0,
                speed: 300,
                loop: false,
                slidesPerView: 1,
                spaceBetween: 16,
                keyboard: { enabled: true },
                navigation: {
                    nextEl: backdrop.querySelector(".swiper-button-next"),
                    prevEl: backdrop.querySelector(".swiper-button-prev"),
                },
                pagination: {
                    el: backdrop.querySelector(".swiper-pagination"),
                    clickable: true
                }
            });

            // proste ogłoszenia dla czytników ekranu
            const live = document.createElement("div");
            live.setAttribute("aria-live", "polite");
            live.style.position = "absolute";
            live.style.left = "-9999px";
            dialog.appendChild(live);

            function announce() {
                const idx = swiper.realIndex + 1;
                live.textContent = `Produkt ${idx} z ${productsData.length}: ${productsData[swiper.realIndex].title || ""}`;
            }
            swiper.on("init", announce);
            swiper.on("slideChange", announce);
            announce();
        });

        return destroy;
    }

    function openWithData(productsData, startIndex, openerEl) {
        if (!productsData || !productsData.length) return;
        return createProductModal(productsData, startIndex, openerEl);
    }

    // ===== 5A) SINGLE – podpinamy do istniejącej listy =====
    function initForSingle({
        listSelector = ".exhibitor-single__products-list",
        tileSelector = ".exhibitor-single__product"
    } = {}) {
        const list = document.querySelector(listSelector);
        if (!list) return;

        const tiles = Array.from(list.querySelectorAll(tileSelector));
        if (!tiles.length) return;

        const productsData = collectAllProductsData(list, tileSelector);

        function handleOpen(tile) {
            const index = tiles.indexOf(tile);
            openWithData(productsData, Math.max(0, index), tile);
        }

        list.addEventListener("click", function(e) {
            const tile = e.target.closest(tileSelector);
            if (tile) handleOpen(tile);
        });

        list.addEventListener("keydown", function(e) {
            const tile = e.target.closest(tileSelector);
            if (!tile) return;
            if (e.key === "Enter" || e.key === " ") { e.preventDefault();
                handleOpen(tile); }
        });
    }

    // ===== 5B) MAIN – analogicznie; wskaż selektory kafelków produktów w karcie wystawcy =====
    function initForMain({
        cardSelector = ".exhibitor-catalog__item", // karta wystawcy
        tileSelector = ".exhibitor-catalog__products-list-element" // kafelek produktu w karcie
    } = {}) {
        document.addEventListener("click", function(e) {
            const tile = e.target.closest(tileSelector);
            if (!tile) return;

            const card = tile.closest(cardSelector);
            if (!card) return;

            const exId = Number(card.getAttribute("data-id") || 0);
            if (!exId) return;

            // 1) Spróbuj z danych globalnych (najlepsze na MAIN)
            const all = (window.__EXHIBITORS__ || []);
            const exhibitor = all.find(x => Number(x.id_numeric || x.idNumeric) === exId);

            let productsData = [];
            if (exhibitor && Array.isArray(exhibitor.products)) {
                productsData = exhibitor.products.map(p => ({
                    imgSrc: p.image || p.img || "",
                    imgAlt: p.name || "",
                    title: p.name || "",
                    desc: p.description || p.desc || ""
                }));
            }

            // 2) Fallback: jeśli z jakiegoś powodu brak danych globalnych — zbierz z DOM
            if (!productsData.length) {
                const list = tile.closest(".exhibitor-catalog__products-list");
                const tiles = list ? Array.from(list.querySelectorAll(tileSelector)) : [tile];
                productsData = tiles.map(t => {
                    const img = t.querySelector("img");
                    // jeśli tytuł nie istnieje w DOM, spróbuj z alt
                    const titleEl = t.querySelector(".product-title, .exhibitor-single__product-title");
                    const title = titleEl ? titleEl.textContent.trim() : (img?.alt || "");
                    const desc = t.getAttribute("data-desc") || ""; // zwykle puste na MAIN
                    return { imgSrc: img ? img.src : "", imgAlt: img ? (img.alt || "") : "", title, desc };
                });
            }

            const listForIndex = tile.closest(".exhibitor-catalog__products-list");
            const tilesForIndex = listForIndex ? Array.from(listForIndex.querySelectorAll(tileSelector)) : [];
            const index = Math.max(0, tilesForIndex.indexOf(tile));

            openWithData(productsData, index, tile);
        });

        // klawiatura (Enter/Space)
        document.addEventListener("keydown", function(e) {
            if (!(e.key === "Enter" || e.key === " ")) return;
            const tile = e.target.closest(tileSelector);
            if (!tile) return;
            e.preventDefault();
            tile.click();
        });
    }


    // ===== 6) Auto-init (jeśli chcesz, możesz wyłączyć i odpalać ręcznie) =====
    document.addEventListener("DOMContentLoaded", () => {
        // Spróbuj single z domyślnymi selektorami
        initForSingle();
        // Spróbuj main z domyślnymi selektorami (zmień w razie innej struktury)
        initForMain();
    });
})();