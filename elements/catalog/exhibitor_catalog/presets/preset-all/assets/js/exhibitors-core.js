// exhibitors-core.js
document.addEventListener("DOMContentLoaded", () => {
  (function () {
    const root = document.getElementById("exhibitorCatalog");
    if (!root) return;

    // --- NARZĘDZIA / HELPERY (uzupełnione) ---
    const lower = (s) => (s || "").toString().toLowerCase().trim();
    const uniq  = (arr) => Array.from(new Set(arr));
    const esc   = (s) =>
      (s || "")
        .toString()
        .replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));

    function toArea(v) {
        if (typeof v === "number" && isFinite(v)) return v;
        if (v == null) return 0;
        let s = String(v).trim().toLowerCase();
        s = s.replace(",", ".").replace(/[^0-9.+-]/g, "");
        const n = parseFloat(s);
        return isFinite(n) ? n : 0;
    }

    // normalizacja do wyszukiwania (bez znaków diakrytycznych)
    const norm = (s) =>
      lower(s)
        .normalize("NFKD")
        .replace(/[\u0300-\u036f]/g, "");

    // tablice tagów katalogowych zawsze jako [string, ...] w lowercase
    const toTagArray = (val) => {
      if (Array.isArray(val)) return val.map((t) => lower(String(t))).filter(Boolean);
      return [];
    };

    const getId   = (x) => Number(x?.idNumeric ?? x?.exhibitor_id ?? x?.exhibitorId ?? 0);
    const getArea = (x) => toArea(x?.booth_area ?? 0);   // ← tylko booth_area z PHP

    // zamiast lokalnych pl*, użyj globalnego PL
    const hasPL = !!(window.PL && window.PL.fmtCount);

    // tagi produktów: dziel po przecinku LUB po >=2 spacjach
    const splitProductTags = (raw) => {
      if (!raw) return [];
      if (Array.isArray(raw)) return raw.flatMap(splitProductTags);
      return String(raw)
        .split(/(?:\s*,\s*|\s{2,})/u)
        .map((s) => lower(s))
        .filter(Boolean);
    };

    // normalizacja rekordów exhibitorów (mapowanie pól + fallbacki)
    function normalize(ex) {
      if (!ex || typeof ex !== "object") return ex;
      ex.idNumeric    = ex.idNumeric ?? Number(ex.id_numeric ?? ex.exhibitor_id ?? ex.exhibitorId ?? 0);
      ex.logoUrl      = ex.logoUrl ?? ex.logo_url ?? "";
      ex.contactEmail = ex.contactEmail ?? ex.contact_email ?? "";
      ex.contactInfo  = ex.contactInfo ?? ex.contact_phone ?? "";

      ex.catalogTags  = Array.isArray(ex.catalogTags)
        ? ex.catalogTags
        : (Array.isArray(ex.catalog_tags) ? ex.catalog_tags : []);
      ex.brands       = Array.isArray(ex.brands) ? ex.brands : [];

      ex.hallName     = ex.hallName ?? ex.hall_name ?? "";
      ex.standNumber  = ex.standNumber ?? ex.stand_number ?? "";

      // liczniki
      ex.productsCount = ex.productsCount ?? Number(
        ex.products_count ?? (Array.isArray(ex.products_preview) ? ex.products_preview.length : 0)
      );
      ex.documentsCount = ex.documentsCount ?? Number(
        ex.documents_count ?? (Array.isArray(ex.documents_preview) ? ex.documents_preview.length : 0)
      );

      // preview (do kafelków)
      ex.productsTrim = Array.isArray(ex.productsTrim)
        ? ex.productsTrim
        : (Array.isArray(ex.products_preview) ? ex.products_preview : []);
      ex.documentsTrim = Array.isArray(ex.documentsTrim)
        ? ex.documentsTrim
        : (Array.isArray(ex.documents_preview) ? ex.documents_preview : []);

      // liczenie stałej, parsowanej powierzchni (przyda się dalej)
        ex.totalArea = toArea(
            ex.totalArea ?? ex.total_booth_area ?? ex.boothArea ?? ex.areaSum ?? ex.stand?.boothArea ?? ex.area ?? ex.booth_area ?? 0
        );

        // ignorujemy ewentualne flagi z payloadu – decyduje tylko metraż
        ex.totalArea = toArea(ex.booth_area ?? 0);
        ex.isFeatured = 0;


      return ex;
    }

    // liczniki po filtrach (hale / sektory / tagi produktów)
    function computeDynamicTotals(dataArr) {
      const halls   = new Map();
      const sectors = new Map();
      const prods   = new Map();

      dataArr.forEach((x) => {
        // hala
        const hall = lower(x.hallName || "");
        if (hall) halls.set(hall, (halls.get(hall) || 0) + 1);

        // sektory (katalogowe)
        (Array.isArray(x.catalogTags) ? x.catalogTags : [])
          .map((t) => lower(String(t)))
          .filter(Boolean)
          .forEach((t) => sectors.set(t, (sectors.get(t) || 0) + 1));

        // produkty (sumaryczne tagi po przecinku / >=2 spacje)
        const prodsArr = Array.isArray(x.products) ? x.products : [];
        prodsArr.forEach((p) => {
          splitProductTags(p?.tags ?? []).forEach((t) => {
            prods.set(t, (prods.get(t) || 0) + 1);
          });
        });
      });

      return { halls, sectors, prods };
    }

    // wpisz liczniki do istniejących labeli (UI)
    function applyStaticTotalsToLabels(totals) {
      const applyToGroup = (nameAttr, countsMap) => {
        const inputs = document.querySelectorAll(
          `input.exhibitor-catalog__checkbox-input[name="${nameAttr}"]`
        );
        inputs.forEach((inp) => {
          const key = (inp.value || "").toLowerCase().trim();
          const n = (countsMap && countsMap.get(key)) || 0;

          const optionRoot = inp.closest("label.exhibitor-catalog__checkbox");
          const labelEl = optionRoot?.querySelector(".exhibitor-catalog__checkbox-label");
          if (!labelEl) return;

          // zapamiętaj bazowy tekst (bez licznika)
          if (!labelEl.dataset.baseLabel) {
            labelEl.dataset.baseLabel = (labelEl.textContent || "")
              .replace(/\s*\(\d+\)\s*$/, "")
              .trim();
          }
          const base = labelEl.dataset.baseLabel;

          // usuń stary <span>
          const oldSpan = labelEl.querySelector(".exhibitor-catalog__count");
          if (oldSpan) oldSpan.remove();

          // ustaw bazowy tekst i dołóż aktualną liczbę
          labelEl.textContent = base;
          const countSpan = document.createElement("span");
          countSpan.className = "exhibitor-catalog__count";
          countSpan.textContent = ` (${n})`;
          labelEl.appendChild(countSpan);

          if (optionRoot) {
            const shouldHide = n === 0 && !inp.checked;
            optionRoot.classList.toggle("is-zero", shouldHide);
            inp.disabled = shouldHide ? true : false;
            optionRoot.setAttribute("aria-hidden", shouldHide ? "true" : "false");
          }
        });
      };

      applyToGroup("hall[]", totals?.halls);
      applyToGroup("sector[]", totals?.sectors);
      applyToGroup("products_tag[]", totals?.prods);
    }

    // render karty (identyczny logicznie z oryginałem)
    function renderCard(ex) {
      const productsTrim   = Array.isArray(ex.productsTrim) ? ex.productsTrim : [];
      const documentsTrim  = Array.isArray(ex.documentsTrim) ? ex.documentsTrim : [];
      const productsCount  = Number(ex.productsCount || 0);
      const documentsCount = Number(ex.documentsCount || 0);

      const headingHTML = Number(ex.isFeatured)
        ? `<div class="exhibitor-catalog__item-heading">Wyróżnieni wystawcy</div>`
        : "";

      const productsHTML =
        productsCount > 0
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

      const documentsHTML =
  documentsCount > 0
    ? `
        <div class="exhibitor-catalog__materials">
        <h4 class="exhibitor-catalog__materials-title">MATERIAŁY DO POBRANIA (${documentsCount})</h4>
        <div class="exhibitor-catalog__materials-list exhibitor-catalog__documents-list">
            ${documentsTrim
            .map((d) => {
                const u = d?.viewUrl || "";
                const t = d?.title || "";
                const cat = d?.category || "";
                return `
                <div class="exhibitor-catalog__material exhibitor-catalog__documents-list-element" data-url="${esc(u)}" data-title="${esc(t)}">
                    <p>Dokument</p>
                    <div class="exhibitor-catalog__material-img">
                    <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/document.png"
                        alt="${esc(t || "Dokument")}" />
                    </div>
                </div>`;
            })
            .join("")}
        </div>
        </div>`
    : "";

      return `
<div class="exhibitor-catalog__item" data-id="${esc(getId(ex))}" data-hall="${esc(lower(ex.hallName))}" data-tags="${esc(toTagArray(ex.catalogTags).join(','))}">
  ${headingHTML}
  <div class="exhibitor-catalog__item-container">
    <div class="exhibitor-catalog__info">
      <div class="exhibitor-catalog__company-info">
        <div class="exhibitor-catalog__logo-tile">
          ${ex.logoUrl ? `<img src="${esc(ex.logoUrl)}" alt="${esc(ex.name)}" />` : ""}
          <div class="exhibitor-catalog__stand">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
            <p>Stoisko ${esc(ex.standNumber || "")}</p>
          </div>
        </div>
        <div class="exhibitor-catalog__contact">
          ${ex.website ? `<div class="exhibitor-catalog__contact-item">
            <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.8 9h22.4M1.8 17h22.4M1 13a12 12 0 1 0 24 0 12 12 0 0 0-24 0" stroke="var(--main2-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.333 1a22.67 22.67 0 0 0 0 24m1.333-24a22.67 22.67 0 0 1 0 24" stroke="var(--main2-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <a href="${esc(ex.website)}" target="_blank" rel="noopener">Strona www</a></div>` : ""}
          ${ex.contactEmail ? `<div class="exhibitor-catalog__contact-item">
            <svg width="28" height="22" viewBox="0 0 28 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.2 21.5a2.64 2.64 0 0 1-1.906-.77Q.5 19.96.5 18.875V3.125q0-1.083.794-1.853A2.64 2.64 0 0 1 3.2.5h21.6q1.113 0 1.907.772.795.771.793 1.853v15.75q0 1.083-.793 1.855a2.63 2.63 0 0 1-1.907.77zM14 12.313 24.8 5.75V3.125L14 9.688 3.2 3.125V5.75z" fill="var(--main2-color)"/></svg>
            <a href="mailto:${esc(ex.contactEmail)}">Email</a></div>` : ""}
          ${ex.contactInfo ? `<div class="exhibitor-catalog__contact-item">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22.6 24q-4.167 0-8.233-1.816t-7.4-5.15-5.15-7.4T0 1.4q0-.6.4-1t1-.4h5.4q.467 0 .833.317.368.318.434.75l.866 4.666q.068.534-.033.9a1.4 1.4 0 0 1-.367.634L5.3 10.533a16 16 0 0 0 1.583 2.383q.916 1.149 2.017 2.217a24 24 0 0 0 2.167 1.918 21 21 0 0 0 2.4 1.616l3.133-3.134q.3-.3.784-.449t.95-.084l4.6.933q.465.134.766.484.3.351.3.783v5.4q0 .6-.4 1t-1 .4" fill="var(--main2-color)"/></svg>
            <a href="tel:${esc(ex.contactInfo)}">Telefon</a></div>` : ""}
        </div>
      </div>
      <div class="exhibitor-catalog__details">
        <a class="exhibitor-catalog__open-modal-name" href="?exhibitor_id=${ex.idNumeric}" target="_blank"><h3 class="exhibitor-catalog__name">${esc(ex.name)}</h3></a>
        ${ex.description ? `<p class="exhibitor-catalog__description">${esc(ex.description)}</p>` : ""}
        ${Array.isArray(ex.brands) && ex.brands.length ? `<div class="exhibitor-catalog__brands">${esc(ex.brands.join(", "))}</div>` : ""}
        ${toTagArray(ex.catalogTags).length ? `<div class="exhibitor-catalog__categories">${esc(toTagArray(ex.catalogTags).join(", "))}</div>` : ""}
      </div>
    </div>
  </div>
  <div class="exhibitor-catalog__extra">${productsHTML}${documentsHTML}</div>
  <a class="exhibitor-catalog__open-modal" href="?exhibitor_id=${ex.idNumeric}" target="_blank">Zobacz szczegóły</a>
</div>`;
    }

    // --- ELEMENTY DOM ---
    const listEl      = root.querySelector(".exhibitor-catalog__list");
    const counterEl   = root.querySelector(".exhibitor-catalog__counter");
    const filtersRoot = root.querySelector(".exhibitor-catalog__filters");
    const searchInput = root.querySelector(".exhibitor-catalog__search-input") || root.querySelector(".exhibitor-catalog__search input");
    const sentinel    = document.getElementById("infiniteSentinel");
    const loader      = document.getElementById("infiniteLoader");
    if (!listEl || !filtersRoot || !sentinel || !loader) return;

    // --- BOOT wołane przez script.js po fetchu danych ---
    window.__exhibitorsBoot = function __exhibitorsBoot(DATA_ALL, PER_PAGE) {
      let DATA = (Array.isArray(DATA_ALL) ? DATA_ALL : []).map(normalize);
      console.log(DATA);

      // fallback: wyznacz wyróżnionych gdy backend ich nie zwróci
        (function ensureFeaturedFlag() {
            if (!DATA.length) return;
            const _getId   = (x) => Number(x?.idNumeric ?? x?.exhibitor_id ?? x?.exhibitorId ?? 0);
            const _getArea = (x) => getArea(x);

            const withArea = DATA
                .map((x) => ({ x, a: _getArea(x) }))
                .filter(({ a }) => isFinite(a) && a > 0);

            if (!withArea.length) {
                DATA.forEach((x) => (x.isFeatured = 0));
                return;
            }

            const maxFeatured = Math.min(30, Math.floor(DATA.length * 0.3));
            withArea.sort((A, B) => B.a - A.a);
            const featuredSet = new Set(withArea.slice(0, maxFeatured).map(({ x }) => _getId(x)));
            DATA.forEach((x) => (x.isFeatured = featuredSet.has(_getId(x)) ? 1 : 0));
        })();
        
        (function hydrateSSRFeatured() {
        try {
            const featuredIds = new Set((DATA || []).filter(x => x.isFeatured === 1).map(x => getId(x)));
            if (!featuredIds.size) return;
            listEl.querySelectorAll(".exhibitor-catalog__item").forEach(item => {
            const id = Number(item.getAttribute("data-id") || 0);
            if (!id || !featuredIds.has(id)) return;
            if (!item.querySelector(".exhibitor-catalog__item-heading")) {
                const h = document.createElement("div");
                h.className = "exhibitor-catalog__item-heading";
                h.textContent = "Wyróżnieni wystawcy";
                item.insertBefore(h, item.firstElementChild);
            }
            });
        } catch (e) {
            console.warn("hydrateSSRFeatured failed:", e);
        }
        })();


      const ALL_HALLS = uniq(DATA.map((x) => lower(x.hallName)).filter(Boolean));
      const ALL_TAGS  = uniq(DATA.flatMap((x) => toTagArray(x.catalogTags))).sort();

      // HYDRACJA SSR – ile już jest wyrenderowane serwerowo
      const SSR_COUNT = listEl.querySelectorAll(".exhibitor-catalog__item").length;

      // ustaw licznik jeśli pusty
        if (counterEl) {
            counterEl.textContent = hasPL ? window.PL.fmtCount(DATA.length, "wyszukanie") : String(DATA.length);
        }

      // --- GLOBALNE API (dla pozostałych modułów) ---
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
        toTagArray,
        // udostępnij używane narzędzia i render
        getId,
        getArea,
        ALL_HALLS,
        ALL_TAGS,
        state: { q: "", halls: new Set(), tags: new Set(), onlyNew: false, onlyBig: false },
        CURRENT: DATA,
        rendered: SSR_COUNT,
        renderCard,
        __normalize: normalize,
        __ssrHydrated: true,
      };

      // inicjalne liczniki
      window.Exhibitors.updateFacetCounts = function () {
        try {
          const now = Array.isArray(window.Exhibitors.CURRENT) ? window.Exhibitors.CURRENT : [];
          const totals = computeDynamicTotals(now);
          applyStaticTotalsToLabels(totals);
        } catch (e) {
          console.warn("updateFacetCounts failed:", e);
        }
      };
      window.Exhibitors.updateFacetCounts && window.Exhibitors.updateFacetCounts();
      
        window.Exhibitors.updateCounter = function (n, nounKey = "wyszukanie") {
            const num = Number(n) || 0;
            if (!counterEl) return;
            if (hasPL) counterEl.textContent = window.PL.fmtCount(num, nounKey);
            else counterEl.textContent = String(num);
        };

      // Sygnał dla reszty (na wypadek, gdyby coś nasłuchiwało tu)
      // (emitowany również w script.js po boot)
      // document.dispatchEvent(new CustomEvent("exhibitors:dataReady", { detail: window.Exhibitors }));
    };
  })();
});
