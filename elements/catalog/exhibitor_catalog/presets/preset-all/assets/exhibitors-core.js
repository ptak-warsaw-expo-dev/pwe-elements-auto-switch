document.addEventListener("DOMContentLoaded", () => {
  (function () {
    const DATA_ALL = Array.isArray(window.__EXHIBITORS__) ? window.__EXHIBITORS__ : [];
    const PER_PAGE = window.__PER_PAGE__ || 20;

    const root = document.getElementById("exhibitorCatalog");
    if (!root) return;

    const listEl = root.querySelector(".exhibitor-catalog__list");
    const counterEl = root.querySelector(".exhibitor-catalog__counter");
    const filtersRoot = root.querySelector(".exhibitor-catalog__filters");
    const searchInput = root.querySelector(".exhibitor-catalog__search-input") || root.querySelector(".exhibitor-catalog__search input");
    const sentinel = document.getElementById("infiniteSentinel");
    const loader = document.getElementById("infiniteLoader");

    if (!listEl || !filtersRoot || !sentinel || !loader) return;

    /* --- Narzędzia i normalizacja danych --- */
    const lower = (s) => (s || "").toString().toLowerCase().trim();
    const uniq = (arr) => Array.from(new Set(arr));
    const esc = (s) =>
      (s || "").toString().replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));
    const norm = (s) => lower(s).normalize("NFKD").replace(/[\u0300-\u036f]/g, "");
    const splitTags = (csv) => (csv ? csv.split(",").map((t) => lower(t)).filter(Boolean) : []);
    const getId = (x) => Number(x?.idNumeric ?? x?.exhibitor_id ?? x?.exhibitorId ?? 0);
    const getArea = (x) => Number(x?.areaSum ?? x?.boothArea ?? x?.total_booth_area ?? 0);

    // tagi produktów: dziel po przecinku LUB po >=2 spacjach
    const splitProductTags = (raw) => {
    if (!raw) return [];
    if (Array.isArray(raw)) return raw.flatMap(splitProductTags);
    return String(raw)
        .split(/(?:\s*,\s*|\s{2,})/u)
        .map(s => lower(s))
        .filter(Boolean);
    };

    function normalize(ex) {
        if (!ex || typeof ex !== "object") return ex;
        ex.idNumeric      = ex.idNumeric ?? Number(ex.id_numeric ?? ex.exhibitor_id ?? ex.exhibitorId ?? 0);
        ex.logoUrl        = ex.logoUrl ?? ex.logo_url ?? "";
        ex.contactEmail   = ex.contactEmail ?? ex.contact_email ?? "";
        ex.contactInfo    = ex.contactInfo ?? ex.contact_phone ?? "";
        ex.catalogTags    = ex.catalogTags ?? ex.catalog_tags ?? "";
        ex.hallName       = ex.hallName ?? ex.hall_name ?? "";
        ex.standNumber    = ex.standNumber ?? ex.stand_number ?? "";

        // ✅ MAPOWANIE LICZNIKÓW
        ex.productsCount  = ex.productsCount  ?? Number(
            ex.products_count ?? (Array.isArray(ex.products_preview)  ? ex.products_preview.length  : 0)
        );
        ex.documentsCount = ex.documentsCount ?? Number(
            ex.documents_count ?? (Array.isArray(ex.documents_preview) ? ex.documents_preview.length : 0)
        );

        // ✅ PREVIEW (może być puste, ale sekcja i tak ma się pokazać przy >0)
        ex.productsTrim   = Array.isArray(ex.productsTrim)
            ? ex.productsTrim
            : (Array.isArray(ex.products_preview)  ? ex.products_preview  : []);
        ex.documentsTrim  = Array.isArray(ex.documentsTrim)
            ? ex.documentsTrim
            : (Array.isArray(ex.documents_preview) ? ex.documents_preview : []);

        ex.isFeatured     = Number(ex.isFeatured || ex.is_featured || 0);
        return ex;
    }

    function computeStaticTotals(dataAll) {
        const halls   = new Map();
        const sectors = new Map();
        const prods   = new Map();

        // H A L E
        dataAll.forEach(x => {
            const k = lower(x.hallName || "");
            if (!k) return;
            halls.set(k, (halls.get(k) || 0) + 1);
        });

        // S E K T O R Y (catalogTags – po przecinku)
        dataAll.forEach(x => {
            String(x.catalogTags || "")
            .split(/\s*,\s*/u)
            .map(s => s.trim())
            .filter(Boolean)
            .map(lower)
            .forEach(tag => sectors.set(tag, (sectors.get(tag) || 0) + 1));
        });

        // P R O D U K T Y (products[].tags – przecinek LUB ≥2 spacje)
        dataAll.forEach(x => {
            const prodsArr = Array.isArray(x.products) ? x.products : [];
            prodsArr.forEach(p => {
            splitProductTags(p?.tags ?? []).forEach(t =>
                prods.set(t, (prods.get(t) || 0) + 1)
            );
            });
        });

        return { halls, sectors, prods };
    }

    function applyStaticTotalsToLabels(totals) {
        const applyToGroup = (nameAttr, countsMap) => {
            const inputs = document.querySelectorAll(
            `input.exhibitor-catalog__checkbox-input[name="${nameAttr}"]`
            );
            inputs.forEach(inp => {
            const key = lower(inp.value || "");
            const n = countsMap.get(key) || 0;

            const labelEl = inp.closest('label.exhibitor-catalog__checkbox')
                ?.querySelector('.exhibitor-catalog__checkbox-label');
            if (!labelEl) return;

            // bazowy tekst bez starego licznika
            if (!labelEl.dataset.baseLabel) {
                labelEl.dataset.baseLabel = (labelEl.textContent || "")
                .replace(/\s*\(\d+\)\s*$/, '')
                .trim();
            }
            const base = labelEl.dataset.baseLabel;

            // usuń stary span, jeśli był
            const oldSpan = labelEl.querySelector('.exhibitor-catalog__count');
            if (oldSpan) oldSpan.remove();

            // ustaw bazowy tekst
            labelEl.textContent = base;

            // dodaj licznik jako osobny <span>
            const countSpan = document.createElement('span');
            countSpan.className = 'exhibitor-catalog__count';
            countSpan.textContent = ` (${n})`;
            labelEl.appendChild(countSpan);
            });
        };

        applyToGroup('hall[]',         totals.halls);
        applyToGroup('sector[]',       totals.sectors);
        applyToGroup('products_tag[]', totals.prods);
    }

    let DATA = DATA_ALL.map(normalize);

    /* --- Fallback: wyróżnieni wystawcy, jeśli backend nie zwróci flagi --- */
    (function ensureFeaturedFlag() {
      if (!Array.isArray(DATA) || !DATA.length) return;
      const hasFeatured = DATA.some((x) => Number(x?.isFeatured) === 1);
      if (!hasFeatured) {
        const maxFeatured = Math.min(30, Math.floor(DATA.length * 0.3));
        const sorted = [...DATA].sort((a, b) => getArea(b) - getArea(a));
        const featuredSet = new Set(sorted.slice(0, maxFeatured).map(getId));
        DATA.forEach((x) => (x.isFeatured = featuredSet.has(getId(x)) ? 1 : 0));
      }
    })();

    const ALL_HALLS = uniq(DATA.map((x) => lower(x.hallName)).filter(Boolean));
    const ALL_TAGS = uniq(DATA.flatMap((x) => splitTags(x.catalogTags))).sort();

    const state = { q: "", halls: new Set(), tags: new Set(), onlyNew: false, onlyBig: false };

    /* --- Render pojedynczej karty (identyczny z PHP) --- */
    function renderCard(ex) {
      const productsTrim = Array.isArray(ex.productsTrim) ? ex.productsTrim : [];
      const documentsTrim = Array.isArray(ex.documentsTrim) ? ex.documentsTrim : [];
      const productsCount = Number(ex.productsCount || 0);
      const documentsCount = Number(ex.documentsCount || 0);

      const headingHTML = Number(ex.isFeatured)
        ? `<div class="exhibitor-catalog__item-heading">Wyróżnieni wystawcy</div>`
        : "";

      const productsHTML = productsCount > 0
        ? `
        <div class="exhibitor-catalog__products">
          <h4 class="exhibitor-catalog__products-title">Produkty (${productsCount})</h4>
          <div class="exhibitor-catalog__products-list">
            ${productsTrim
              .map(
                (p) => `
              <div class="exhibitor-catalog__products-list-element">
                <img src="${esc(p.img || "")}" alt="${esc(p.name || "Product")}" loading="lazy" decoding="async" />
              </div>`
              )
              .join("")}
          </div>
        </div>`
        : "";

      const documentsHTML = documentsCount > 0
        ? `
        <div class="exhibitor-catalog__materials">
          <h4 class="exhibitor-catalog__materials-title">MATERIAŁY DO POBRANIA (${documentsCount})</h4>
          <div class="exhibitor-catalog__materials-list">
            ${documentsTrim
              .map(
                (d) => `
              <div class="exhibitor-catalog__material">
                <p>${esc(d.category || "")}</p>
                <div class="exhibitor-catalog__material-img">
                  <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/document.png" alt="${esc(
                    d.title || "Dokument"
                  )}" />
                </div>
              </div>`
              )
              .join("")}
          </div>
        </div>`
        : "";

      return `
<div class="exhibitor-catalog__item" data-hall="${esc(lower(ex.hallName))}" data-tags="${esc(lower(ex.catalogTags))}">
  ${headingHTML}
  <div class="exhibitor-catalog__item-container">
    <div class="exhibitor-catalog__info">
      <div class="exhibitor-catalog__company-info">
        <div class="exhibitor-catalog__logo-tile">
          ${ex.logoUrl ? `<img src="${esc(ex.logoUrl)}" alt="${esc(ex.name)}" />` : ""}
          <div class="exhibitor-catalog__stand">
            <svg viewBox="0 0 24 24"><circle cx="12" cy="10" r="3" stroke="#fff" stroke-width="2"></circle>
            <path d="M19 9.75C19 15.375 12 21 12 21C12 21 5 15.375 5 9.75C5 6.02208 8.134 3 12 3C15.866 3 19 6.02208 19 9.75Z" stroke="#fff" stroke-width="2"></path></svg>
            <p>Stoisko ${esc(ex.standNumber || "")}</p>
          </div>
        </div>
        <div class="exhibitor-catalog__contact">
          ${ex.website ? `<div class="exhibitor-catalog__contact-item"><a href="${esc(ex.website)}">Strona www</a></div>` : ""}
          ${ex.contactEmail ? `<div class="exhibitor-catalog__contact-item"><a href="mailto:${esc(ex.contactEmail)}">Email</a></div>` : ""}
          ${ex.contactInfo ? `<div class="exhibitor-catalog__contact-item"><a href="tel:${esc(ex.contactInfo)}">Telefon</a></div>` : ""}
        </div>
      </div>
      <div class="exhibitor-catalog__details">
        <h3 class="exhibitor-catalog__name">${esc(ex.name)}</h3>
        ${ex.description ? `<p class="exhibitor-catalog__description">${esc(ex.description)}</p>` : ""}
        ${ex.brands ? `<div class="exhibitor-catalog__brands">${esc(ex.brands)}</div>` : ""}
        ${ex.catalogTags ? `<div class="exhibitor-catalog__categories">${esc(ex.catalogTags)}</div>` : ""}
      </div>
    </div>
  </div>
  <div class="exhibitor-catalog__extra">${productsHTML}${documentsHTML}</div>
  <button type="button" class="exhibitor-catalog__open-modal">Zobacz szczegóły</button>
</div>`;
    }

    /* --- HYDRACJA SSR --- */
    const SSR_COUNT = listEl.querySelectorAll(".exhibitor-catalog__item").length;
    if (counterEl && !/\d/.test(counterEl.textContent || "")) {
      counterEl.textContent = `${DATA.length} Wyszukiwań`;
    }

    /* --- Eksport globalny --- */
    window.Exhibitors = {
      DATA_ALL: DATA,
      PER_PAGE,
      listEl,
      counterEl,
      filtersRoot,
      searchInput,
      sentinel,
      loader,
      lower,
      uniq,
      esc,
      norm,
      splitTags,
      getId,
      getArea,
      ALL_HALLS,
      ALL_TAGS,
      state,
      CURRENT: DATA.slice(0, SSR_COUNT),
      rendered: SSR_COUNT,
      renderCard,
      __normalize: normalize,
      __ssrHydrated: true,
    };
    // --- Liczniki stałe w nawiasach (hala / sektory / produkty) ---
    try {
        const totals = computeStaticTotals(DATA);
        applyStaticTotalsToLabels(totals);
        // (opcjonalnie) eksport do podglądu w konsoli:
        window.Exhibitors.__staticTotals = totals;
        // (opcjonalnie) udostępnij helper, jeśli używasz go też w innych plikach:
        window.Exhibitors.splitProductTags = splitProductTags;
    } catch (e) {
        console.warn('Exhibitor counters failed:', e);
    }

  })();
});
