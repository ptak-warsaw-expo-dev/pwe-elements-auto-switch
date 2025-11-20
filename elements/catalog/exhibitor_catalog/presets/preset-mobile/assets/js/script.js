// ======== //ANCHOR Rozwijane sekcje filtrów ========

(function ($) {

  // Funkcja ustawiająca max-height dla rozwijania
  function setHeight($el, expand) {
    if (!$el.length) return;

    if (expand) {
      $el
        .css("max-height", $el.prop("scrollHeight") + "px")
        .addClass("catalog-mobile-filters__body--expanded");
    } else {
      $el.css("max-height", $el.prop("scrollHeight") + "px");
      $el[0].getBoundingClientRect();
      $el
        .css("max-height", "0")
        .removeClass("catalog-mobile-filters__body--expanded");
    }
  }

  // Lista grup, które mają być otwarte na start
  const OPEN_TITLES = [
    "Typ",            // PL
    "Type",           // EN
    "Hale",           // PL
    "Halls"           // gdyby była EN
  ];

  // Inicjalne ustawienie stanu grup
  function initGroups() {
    $(".catalog-mobile-filters__group").each(function () {

      const $group = $(this);
      const $body  = $group.children(".catalog-mobile-filters__body");
      const $title = $group.find(".catalog-mobile-filters__title").first();

      if (!$body.length || !$title.length) return;

      const titleText = $title.text().trim();

      // Czy ta grupa ma być rozwinięta?
      const shouldOpen = OPEN_TITLES.some(t => titleText.includes(t));

      if (shouldOpen) {
        // Otwarta
        setHeight($body, true);
        $group.find(".catalog-mobile-filters__arrow")
              .addClass("catalog-mobile-filters__arrow--open");
      } else {
        // Zamknięta
        setHeight($body, false);
        $group.find(".catalog-mobile-filters__arrow")
              .removeClass("catalog-mobile-filters__arrow--open");
      }
    });
  }

  // Kliknięcie nagłówka → toggle
  $(document).on("click", ".catalog-mobile-filters__header", function () {

    const $group = $(this).closest(".catalog-mobile-filters__group");
    const $body  = $group.children(".catalog-mobile-filters__body");

    if (!$body.length) return;

    const expanded = $body.hasClass("catalog-mobile-filters__body--expanded");

    setHeight($body, !expanded);

    $(this)
      .find(".catalog-mobile-filters__arrow")
      .toggleClass("catalog-mobile-filters__arrow--open", !expanded);
  });

  // Aktualizacja po zmianie okna
  $(window).on("resize", function () {
    $(".catalog-mobile-filters__body.catalog-mobile-filters__body--expanded")
      .each(function () {
        $(this).css("max-height", this.scrollHeight + "px");
      });
  });

  // Inicjalizacja na start
  $(initGroups);

  // Inicjalizacja po AJAX
  $(window).on("catalog:updated", initGroups);

})(jQuery);

// ======== //ANCHOR LOAD MORE ========
document.addEventListener("click", async function (e) {
    const btn = e.target.closest("#exhibitorLoadMore");
    if (!btn) return;

    const root = document.getElementById("exhibitorCatalog");
    const spinner = document.querySelector(".exhibitor-catalog__spinner");
    const itemsSelector = ".exhibitor-catalog__items-container";

    const lastUrl = new URL(window.location.href);

    const currentPage = Number(lastUrl.searchParams.get("exh-page") || "1");
    const nextPage = currentPage + 1;

    lastUrl.searchParams.set("exh-page", nextPage);

    if (spinner) spinner.style.display = "flex";

    try {
        const res = await fetch(lastUrl.toString(), { credentials: "same-origin" });
        const html = await res.text();
        const doc = new DOMParser().parseFromString(html, "text/html");

        const newItems = doc.querySelector(itemsSelector);
        const currentItems = root.querySelector(itemsSelector);

        if (!newItems || !newItems.children.length) {
            btn.style.display = "none";
            return;
        }

        [...newItems.children].forEach(el => currentItems.appendChild(el));

        initExhibitorFilesSliders(document);

        history.pushState({}, "", lastUrl.toString());

        window.dispatchEvent(new Event("catalog:updated"));

    } catch (err) {
        console.error("LOAD MORE ERROR", err);
        btn.style.display = "none";

    } finally {
        if (spinner) spinner.style.display = "none";
    }
});

// ======== //ANCHOR  Filtry, wyszukiwarka, czyszczenie filtrów ========

async function ajaxReplaceCatalog(url) {
  const root = document.getElementById("exhibitorCatalog");
  if (!root) return;

  const spinner = document.querySelector(".exhibitor-catalog__spinner");
  const itemsSelector = ".exhibitor-catalog__items-container";
  const pagerSelector = ".exhibitor-catalog__pagination";

  /* TU: zmiana selektora kontenera filtrów */
  const filtersSelector = ".catalog-mobile-filters";

  try {
    if (spinner) spinner.style.display = "flex";

    const res = await fetch(url, {
      credentials: "same-origin",
      cache: "no-store"
    });
    if (!res.ok) throw new Error("Błąd sieci");

    const html = await res.text();
    const doc = new DOMParser().parseFromString(html, "text/html");

    const newItems   = doc.querySelector(itemsSelector);
    const newPager   = doc.querySelector(pagerSelector);
    const newFilters = doc.querySelector(filtersSelector);

    const oldItems   = root.querySelector(itemsSelector);
    const oldPager   = root.querySelector(pagerSelector);
    const oldFilters = root.querySelector(filtersSelector);

    if (newItems && oldItems) oldItems.replaceWith(newItems);

    if (newPager) {
      if (oldPager) oldPager.replaceWith(newPager);
      else root.querySelector(".exhibitor-catalog__pagination-container")?.appendChild(newPager);
    } else if (oldPager) {
      oldPager.remove(); 
    }

    const newCount = doc.querySelector(".exhibitor-catalog__panel-items-count");
    const oldCount = root.querySelector(".exhibitor-catalog__panel-items-count");
    if (newCount && oldCount) oldCount.textContent = newCount.textContent;

    // ===== UPDATE MOBILE RESULT COUNT =====
    const newMobileCount = doc.querySelector(".catalog-mobile-panel__results-count");
    const panelMobileCount = document.querySelector(".catalog-mobile-panel__results-count");

    if (newMobileCount && panelMobileCount) {
        panelMobileCount.textContent = newMobileCount.textContent;
    }

    if (newFilters && oldFilters) oldFilters.replaceWith(newFilters);

    if (newItems) initExhibitorFilesSliders?.(newItems);

    history.pushState({}, "", url);
    window.dispatchEvent(new Event("catalog:updated"));

  } catch (err) {
    console.error("AJAX katalog error:", err);
    window.location.href = url;

  } finally {
    if (spinner) spinner.style.display = "none";
  }
}


document.addEventListener("DOMContentLoaded", function () {

    let globalInitialized = false;

    // ==================================================================
    // GLOBAL LISTENERS
    // ==================================================================
    function initGlobalListeners() {
        if (globalInitialized) return;
        globalInitialized = true;

        // --------------------------
        // CLEAR
        // --------------------------
        document.addEventListener("click", function (e) {
            const btn = e.target.closest(".catalog-mobile-filters__clear");
            if (!btn) return;

            e.preventDefault();

            const form = document.querySelector(".catalog-mobile-filters__form");
            if (!form) return;

            form.querySelectorAll("input[type=checkbox]").forEach(cb => cb.checked = false);

            const input = document.querySelector(".catalog-mobile-panel__search-input");
            if (input) input.value = "";

            const bar = document.querySelector(".catalog-mobile-filters__bottom-bar");
            bar?.classList.remove("is-visible");

            ajaxReplaceCatalog(window.location.pathname);
        });

        // --------------------------
        // CHECKBOX: bottom bar UI
        // --------------------------
        document.addEventListener("change", function (e) {
            if (!e.target.matches(".catalog-mobile-filters__form input[type='checkbox']")) return;

            const form = document.querySelector(".catalog-mobile-filters__form");
            const bar  = document.querySelector(".catalog-mobile-filters__bottom-bar");

            const anyChecked = [...form.querySelectorAll("input[type='checkbox']")].some(cb => cb.checked);
            bar?.classList.toggle("is-visible", anyChecked);
        });
    }

    // ==================================================================
    // DYNAMIC LISTENERS
    // ==================================================================
    function initDynamicListeners() {

        const filterForm  = document.querySelector(".catalog-mobile-filters__form");
        const applyBtn    = document.querySelector(".catalog-mobile-filters__bottom-apply");

        const searchInput = document.querySelector(".catalog-mobile-panel__search-input");
        const searchBtn   = document.querySelector(".exhibitor-catalog__search-icon");

        const sortSelect  = document.querySelector(".catalog-custom-select[data-select='sort']");

        if (!filterForm) return;

        // ==========================================================
        // APPLY – AJAX
        // ==========================================================
        if (applyBtn && !applyBtn.dataset.bound) {
            applyBtn.dataset.bound = "1";

            applyBtn.addEventListener("click", function (e) {
                e.preventDefault();

                const params = new URLSearchParams(new FormData(filterForm));
                const urlObj = new URL(window.location.href);

                const searchVal = urlObj.searchParams.get("search");
                if (searchVal) params.set("search", searchVal);

                const sortVal = urlObj.searchParams.get("sort_mode");
                if (sortVal) params.set("sort_mode", sortVal);

                params.delete("exh-page");

                ajaxReplaceCatalog(window.location.pathname + "?" + params.toString());
                if (window.closeFilters) window.closeFilters();
            });
        }

        // ==========================================================
        // SEARCH – debounce
        // ==========================================================
        if (searchInput && !searchInput.dataset.bound) {

            searchInput.dataset.bound = "1";
            let debounceTimer;

            function runSearch() {
                const query = searchInput.value.trim();
                if (query && query.length < 3) return;

                const url = new URL(window.location.href);

                if (query) url.searchParams.set("search", query);
                else url.searchParams.delete("search");

                url.searchParams.delete("exh-page");

                ajaxReplaceCatalog(url.pathname + "?" + url.searchParams.toString());
            }

            searchInput.addEventListener("input", () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(runSearch, 3000);
            });

            searchInput.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    runSearch();
                }
            });

            if (searchBtn) {
                searchBtn.addEventListener("click", function (e) {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    runSearch();
                });
            }
        }

        // ==========================================================
        // SORT
        // ==========================================================
        if (sortSelect && !sortSelect.dataset.bound) {

            sortSelect.dataset.bound = "1";

            const urlParams = new URL(window.location.href).searchParams;
            sortSelect.dataset.current = urlParams.get("sort_mode") || "default";

            sortSelect.addEventListener("change", function (e) {

                const value = e.detail?.value; // custom select wysyła wartość w detail
                if (!value) return;

                const url = new URL(window.location.href);
                url.searchParams.set("sort_mode", value);
                url.searchParams.delete("exh-page");

                ajaxReplaceCatalog(url.pathname + "?" + url.searchParams.toString());
            });
        }
    }

    // INIT
    initGlobalListeners();
    initDynamicListeners();

    // dynamic listeners po AJAX
    window.addEventListener("catalog:updated", initDynamicListeners);
});



// ======== //ANCHOR MOBILE FILTER MENU ========

document.addEventListener("DOMContentLoaded", () => {

    const filters    = document.querySelector(".catalog-mobile-filters__form");
    const filterMenu = document.querySelector(".catalog-mobile-panel__filters-btn");
    const panelBar   = document.querySelector(".catalog-mobile-panel__results");
    const hero       = document.querySelector(".exhibitor-catalog__header");

    if (!filterMenu || !panelBar || !filters) return;

    const originalIcon = filterMenu.innerHTML;
    const closeIcon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m12 13.703-5.962 5.962q-.334.336-.852.335-.516 0-.851-.335T4 18.814q0-.517.335-.852L10.297 12 4.335 6.038Q4 5.704 4 5.186q0-.516.335-.851T5.186 4q.518 0 .852.335L12 10.297l5.962-5.962Q18.296 4 18.814 4q.516 0 .851.335.336.335.335.851 0 .518-.335.852L13.703 12l5.962 5.962q.336.334.335.852 0 .516-.335.851-.335.336-.851.335-.517 0-.852-.335z" fill="#2a2b2d"/></svg> Filtry';

    let forceStuck = false;
    let heroHeight = 0;
    let extraOffset = 150;

    function recalcHeroHeight() {
        if (!hero) return;
        heroHeight = hero.getBoundingClientRect().height;
    }

    function updateStickyPanel() {
        if (forceStuck) {
            panelBar.classList.add("is_stucked");
            return;
        }

        if (window.scrollY >= heroHeight + extraOffset) {
            panelBar.classList.add("is_stucked");
        } else {
            panelBar.classList.remove("is_stucked");
        }
    }

    setTimeout(() => {
        recalcHeroHeight();
        updateStickyPanel();
    }, 150);

    window.addEventListener("resize", () =>
        setTimeout(() => {
            recalcHeroHeight();
            updateStickyPanel();
        }, 150)
    );

    window.addEventListener("scroll", updateStickyPanel);

    window.addEventListener("catalog:updated", () => {
        setTimeout(() => {
            recalcHeroHeight();
            updateStickyPanel();
        }, 150);
    });

    function openFilters() {
        forceStuck = true;
        panelBar.classList.add("is_stucked");

        document.body.classList.add("mobile-filters-open");
        filterMenu.classList.add("is-open");
        filterMenu.innerHTML = closeIcon;
    }

    function closeFilters() {
        document.body.classList.remove("mobile-filters-open");
        filterMenu.classList.remove("is-open");
        filterMenu.innerHTML = originalIcon;

        forceStuck = false;
        updateStickyPanel();
    }

    window.closeFilters = closeFilters;

    filterMenu.addEventListener("click", () => {
        if (filterMenu.classList.contains("is-open")) {
            closeFilters();
        } else {
            openFilters();
        }
    });

});

// ======== //ANCHOR  Inicjalizacja Swiperów ========

function initExhibitorFilesSliders(scope = document) {
  scope.querySelectorAll(".exhibitor-catalog__exh-card-files").forEach(function (root) {
    if (root.__exhInited) return;
    root.__exhInited = true;

    const productsEl  = root.querySelector(".exhibitor-catalog__exh-card-files-products");
    const documentsEl = root.querySelector(".exhibitor-catalog__exh-card-files-documents");
    const tabs        = Array.from(root.querySelectorAll(".exhibitor-catalog__exh-card-files-tab"));
    const nav         = root.querySelector(".exhibitor-catalog__exh-card-files-nav");

    const hasProducts  = !!productsEl;
    const hasDocuments = !!documentsEl;

    // nic do roboty
    if (!hasProducts && !hasDocuments) return;

    function makeSwiper(el) {
      if (!el) return null;
      const nextBtn = el.querySelector(".exh-files__next");
      return new Swiper(el, {
        wrapperClass: "swiper-wrapper",
        slideClass:   "swiper-slide",
        watchOverflow: true,
        autoplay: true,
        navigation: nextBtn ? { nextEl: nextBtn } : undefined,
        breakpoints: {
          480:  { slidesPerView: 1.6, spaceBetween: 16 },
          640:  { slidesPerView: 2.2, spaceBetween: 20 },
          768:  { slidesPerView: 2.6, spaceBetween: 20 },
          1024: { slidesPerView: 3.5, spaceBetween: 12 },
        },
        on: {
            init() {
                // 🔹 Po pełnej inicjalizacji Swipera odkrywamy go
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

    function showPanel(which) {
      const showProducts = which === "products";

      if (hasProducts)  productsEl.toggleAttribute("hidden", !showProducts);
      if (hasDocuments) documentsEl.toggleAttribute("hidden", showProducts);

      tabs.forEach(btn => btn.classList.toggle("is-active", btn.dataset.tab === which));

      const sw = showProducts ? productsSwiper : documentsSwiper;
      if (sw) setTimeout(() => sw.update(), 40);
    }

    if (hasProducts && hasDocuments) {
      // dwa panele – normalne taby
      showPanel("products");
      tabs.forEach(btn => btn.addEventListener("click", () => showPanel(btn.dataset.tab)));
    } else {
      // jeden panel – ukryj nawigację zakładek, jeśli jest zbędna
      const single = hasProducts ? "products" : "documents";
      showPanel(single);
    }
  });
}

// ======== //ANCHOR Custom Select ========

document.addEventListener("click", (e) => {

    const isSelectClick = e.target.closest(".catalog-custom-select");

    // klik poza selectem → zamknij wszystkie
    if (!isSelectClick) {
        document.querySelectorAll(".catalog-custom-select").forEach(s => s.classList.remove("open"));
        return;
    }

    const select = isSelectClick;

    const selected = e.target.closest(".catalog-custom-select__selected");
    const option = e.target.closest(".catalog-custom-select__option");

    // otwieranie/zamykanie
    if (!option && !select.classList.contains("open")) {
        select.classList.add("open");
        return;
    }

    // kliknięcie opcji
    if (option) {
        const value = option.dataset.value;

        // aktualizacja
        const selectedEl = select.querySelector(".catalog-custom-select__selected");

        // oznacz aktywną opcję
        select.querySelectorAll(".catalog-custom-select__option").forEach(o => o.classList.remove("active"));
        option.classList.add("active");

        // ustaw atrybut
        select.dataset.current = value;

        // zamknij dropdown
        select.classList.remove("open");

        // trigger zmiany (jeśli potrzebne)
        select.dispatchEvent(new CustomEvent("change", { detail: { value } }));
    }
});
