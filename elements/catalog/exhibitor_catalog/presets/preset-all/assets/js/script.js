// ======== //ANCHOR Rozwijane sekcje filtrów ========

(function ($) {
  function setHeight($el, expand) {
    if (!$el.length) return;

    if (expand) {
      $el.css("max-height", $el.prop("scrollHeight") + "px").addClass("is-expanded");
    } else {
      $el.css("max-height", $el.prop("scrollHeight") + "px");
      $el[0].getBoundingClientRect(); // wymuszenie reflow
      $el.css("max-height", "0").removeClass("is-expanded");
    }
  }

  $(document).on("click", ".exhibitor-catalog__more-btn", function (e) {
    e.preventDefault();

    const $btn = $(this);
    const targetSel = $btn.data("target");
    const $box = $(targetSel);
    if (!$box.length) return;

    const expanded = $box.hasClass("is-expanded");
    setHeight($box, !expanded);

    $btn.attr("aria-expanded", (!expanded).toString());
    $btn.text(expanded ? "Pokaż więcej" : "Pokaż mniej");
  });

  // === Przeliczenie wysokości przy zmianie rozmiaru okna ===
  $(window).on("resize", function () {
    $(".exhibitor-catalog__collapsible.is-expanded").each(function () {
      $(this).css("max-height", this.scrollHeight + "px");
    });
  });

})(jQuery);

// ======== //ANCHOR AJAX paginacja ========

(function ($) {
  const $root    = $("#exhibitorCatalog");
  if (!$root.length) return;

  const itemsSelector  = ".exhibitor-catalog__items-container";
  const pagerSelector  = ".exhibitor-catalog__pagination";
  const $spinner       = $(".exhibitor-catalog__spinner");

  $root.on("click", ".exhibitor-catalog__pagination a[href]", function (e) {
    e.preventDefault();

    const url = $(this).attr("href");
    if (!url) return;

    $spinner.fadeIn(150);

    $.ajax({
      url: url,
      method: "GET",
      cache: false,
      xhrFields: { withCredentials: true },
      success: function (html) {
        const $doc = $(new DOMParser().parseFromString(html, "text/html"));
        const $newItems = $doc.find(itemsSelector);
        const $newPager = $doc.find(pagerSelector);
        const $oldItems = $root.find(itemsSelector);
        const $oldPager = $root.find(pagerSelector);

        if ($newItems.length && $oldItems.length) $oldItems.replaceWith($newItems);

        $newItems.find("script").each(function () {
            const inlineScript = $(this).text();
            if (inlineScript.trim()) {
                try {
                eval(inlineScript);
                } catch (err) {
                console.warn("Błąd wykonania inline scriptu w katalogu:", err);
                }
            }
        });

        if ($newPager.length && $oldPager.length) $oldPager.replaceWith($newPager);

        if ($newItems.length) initExhibitorFilesSliders($newItems[0]);

        window.dispatchEvent(new Event("catalog:updated"));

        window.scrollTo({
            top: $('#exhibitorCatalog').offset().top + 140,
            behavior: 'instant'
        });

        history.pushState({}, "", url);
      },
      error: function (xhr) {
        console.error("Błąd AJAX paginacji:", xhr.statusText);
        window.location.href = url;
      },
      complete: function () {
        $spinner.fadeOut(150);
      }
    });
  });

  $(window).on("popstate", function () {
    $.ajax({
      url: window.location.href,
      method: "GET",
      cache: false,
      xhrFields: { withCredentials: true },
      success: function (html) {
        const $doc = $(new DOMParser().parseFromString(html, "text/html"));
        const $newItems = $doc.find(itemsSelector);
        const $newPager = $doc.find(pagerSelector);
        const $oldItems = $root.find(itemsSelector);
        const $oldPager = $root.find(pagerSelector);

        if ($newItems.length && $oldItems.length) $oldItems.replaceWith($newItems);

        $newItems.find("script").each(function () {
            const inlineScript = $(this).text();
            if (inlineScript.trim()) {
                try {
                eval(inlineScript);
                } catch (err) {
                console.warn("Błąd wykonania inline scriptu w katalogu:", err);
                }
            }
        });

        if ($newPager.length && $oldPager.length) $oldPager.replaceWith($newPager);

        if ($newItems.length) initExhibitorFilesSliders($newItems[0]);

        window.dispatchEvent(new Event("catalog:updated"));

        window.scrollTo({
            top: $('#exhibitorCatalog').offset().top + 140,
            behavior: 'instant'
        });
      },
      complete: function () {
        $spinner.hide();
      }
    });
  });
})(jQuery);

// ======== //ANCHOR Mobile LOAD MORE pagination (ze spinnerem) ========
document.addEventListener("click", async function (e) {
    const btn = e.target.closest("#exhibitorLoadMore");
    if (!btn) return;

    const root = document.getElementById("exhibitorCatalog");
    const itemsSelector = ".exhibitor-catalog__items-container";
    const pagerSelector = ".exhibitor-catalog__pagination";
    const spinner = document.querySelector(".exhibitor-catalog__spinner");

    const items = root.querySelector(itemsSelector);
    const pager = root.querySelector(pagerSelector);

    if (!pager) {
        btn.style.display = "none";
        return;
    }

    const nextLink = pager.querySelector("a.ec-pager__btn:not(.is-disabled)[rel='next']");
    if (!nextLink) {
        btn.style.display = "none";
        return;
    }

    const url = nextLink.href;

    // === SPINNER ON ===
    if (spinner) spinner.style.display = "flex";

    try {
        const res = await fetch(url, { credentials: "same-origin" });
        const html = await res.text();
        const doc = new DOMParser().parseFromString(html, "text/html");

        const newItems = doc.querySelector(itemsSelector);
        const newPager = doc.querySelector(pagerSelector);

        // 🔹 1) ZNAJDŹ SLIDERY ZANIM PRZERZUCISZ DZIECI
        let newSliders = [];
        if (newItems) {
            newSliders = newItems.querySelectorAll(".exhibitor-catalog__exh-card-files");
        }

        // 🔹 2) DOKLEJAMY NOWE ELEMENTY DO ISTNIEJĄCEJ LISTY
        if (newItems && items) {
            [...newItems.children].forEach((el) => items.appendChild(el));
        }

        // 🔹 3) PODMIANA PAGINACJI NA NOWĄ (DLA KOLEJNEGO NEXT)
        if (newPager && pager) {
            pager.replaceWith(newPager);
        } else {
            // OSTATNIA STRONA
            btn.style.display = "none";
        }

        // 4) INITUJEMY NOWE SLIDERY – BEZ DOTYKANIA STARYCH
        newSliders.forEach(sliderRoot => {
            sliderRoot.__exhInited = false;
        });

        // wywołanie W JEDNYM MIEJSCU – BEZPIECZNE
        initExhibitorFilesSliders(document);


    } catch (err) {
        console.error("LOAD MORE error:", err);
        btn.style.display = "none";
    }

    // === SPINNER OFF ===
    if (spinner) spinner.style.display = "none";
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

// ======== //ANCHOR  Filtry, wyszukiwarka, czyszczenie filtrów ========

document.addEventListener("DOMContentLoaded", function () {
  initExhibitorFilesSliders(document);
});

async function ajaxReplaceCatalog(url) {
  const root = document.getElementById("exhibitorCatalog");
  if (!root) return;

  const spinner = document.querySelector(".exhibitor-catalog__spinner");
  const itemsSelector = ".exhibitor-catalog__items-container";
  const pagerSelector = ".exhibitor-catalog__pagination";
  const filtersSelector = ".exhibitor-catalog__filter-container";

  try {
    // show spinner
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

    // replace the list of items
    if (newItems && oldItems) oldItems.replaceWith(newItems);

    // replace or remove pagination
    if (newPager) {
      if (oldPager) oldPager.replaceWith(newPager);
      else root.querySelector(".exhibitor-catalog__pagination-container")?.appendChild(newPager);
    } else if (oldPager) {
      // delete the old one if there is no new one (e.g. only 1 page)
      oldPager.remove(); 
    }

    // number of results
    const newCount = doc.querySelector(".exhibitor-catalog__panel-items-count");
    const oldCount = root.querySelector(".exhibitor-catalog__panel-items-count");
    if (newCount && oldCount) oldCount.textContent = newCount.textContent;

    // filters
    if (newFilters && oldFilters) oldFilters.replaceWith(newFilters);

    // re-initialization of sliders
    if (newItems) initExhibitorFilesSliders?.(newItems);

    // updating the address in the bar
    history.pushState({}, "", url);

    // event trigger for the rest of JS
    window.dispatchEvent(new Event("catalog:updated"));
  } catch (err) {
    console.error("AJAX katalog error:", err);
    window.location.href = url; // fallback
  } finally {
    // hide spinner
    if (spinner) spinner.style.display = "none";
  }
}


document.addEventListener("DOMContentLoaded", function() {

    // GLOBAL LISTENERS — RUNNING ONLY ONCE
    let globalListenersInitialized = false;

    function initGlobalListeners() {
      if (globalListenersInitialized) return;
      globalListenersInitialized = true;

      const searchInput = document.querySelector(".exhibitor-catalog__search-input");
      const clearBtn    = document.querySelector(".exhibitor-catalog__panel-filter-clear");

      // ------------------ SEARCH ------------------
      if (searchInput) {
        let debounceTimer;

        function runTextSearch() {
          const query = searchInput.value.trim();

          // minimum length
          if (query.length > 0 && query.length < 3) return;

          const url = new URL(window.location.href);
          const params = url.searchParams;

          if (query === "") params.delete("search");
          else params.set("search", query);

          params.delete("exh-page");

          ajaxReplaceCatalog(url.pathname + "?" + params.toString());
        }

        // automatic search
        searchInput.addEventListener("input", function () {
          clearTimeout(debounceTimer);
          debounceTimer = setTimeout(runTextSearch, 3000);
        });

        // enter
        searchInput.addEventListener("keypress", function(e) {
          if (e.key === "Enter") {
            e.preventDefault();
            clearTimeout(debounceTimer);
            runTextSearch();
          }
        });
      }

      // ---------------- CLEAR FILTERS ----------------
      if (clearBtn) {
        clearBtn.addEventListener("click", function(e) {
            e.preventDefault();

            const checkboxes = document.querySelectorAll(
                `.exhibitor-catalog__filters-form input[type="checkbox"]`
            );
            checkboxes.forEach(cb => cb.checked = false);

            const searchInput = document.querySelector(".exhibitor-catalog__search-input");
            if (searchInput) searchInput.value = "";

            ajaxReplaceCatalog(window.location.pathname);
        });
      }
    }

    // DYNAMIC LISTENERS — RUN AFTER EACH AJAX
    function initDynamicListeners() {
        
      const filterForm  = document.querySelector(".exhibitor-catalog__filters-form");
      const filterBtn   = document.querySelector(".exhibitor-catalog__panel-filter-search");
      const sortSelect  = document.querySelector(".catalog-custom-select");

      // ------------------ FILTERS ------------------
      if (filterForm) {
        const switches = filterForm.querySelectorAll(`input[type="checkbox"]`);

        function sendFiltersAjax() {
          const params = new URLSearchParams(new FormData(filterForm));
          const url = new URL(window.location.href);

          const currentSearch = url.searchParams.get("search");
          if (currentSearch) params.set("search", currentSearch);

          const currentSort = url.searchParams.get("sort_mode");
          if (currentSort) params.set("sort_mode", currentSort);

          ajaxReplaceCatalog(filterForm.getAttribute("action") + "?" + params.toString());
        }

        if (filterBtn) {
          function updateSearchActive() {
            const anyChecked = Array.from(switches).some(sw => sw.checked);
            filterBtn.classList.toggle("search-active", anyChecked);
          }

          switches.forEach(sw => {
            sw.addEventListener("change", updateSearchActive);
            sw.addEventListener("change", sendFiltersAjax);
          });

          updateSearchActive();
        } else {
          switches.forEach(sw => {
            sw.addEventListener("change", sendFiltersAjax);
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
    initGlobalListeners();   // only once
    initDynamicListeners();  // every time

    // refresh dynamic listeners after AJAX
    window.addEventListener("catalog:updated", initDynamicListeners);
});


// // ======== //ANCHOR  Dopasowanie wysokości kolumn ========

// (function ($) {

//   function syncColumnsHeight() {
//     const $mainCol = $(".exhibitor-catalog__main-columns");
//     const $itemsContainer = $(".exhibitor-catalog__pagination-container");
//     if (!$mainCol.length || !$itemsContainer.length) return;

//     $mainCol.css("height", "auto");
//     $mainCol.height($itemsContainer.outerHeight());
//   }

//   $(window).on("load resize catalog:updated", syncColumnsHeight);

// })(jQuery);

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
