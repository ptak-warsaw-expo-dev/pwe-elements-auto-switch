// exhibitor-documents-modal.js
(function () {
  window.DocumentsModal = { initForSingle, initForMain, openWithData };

  // ---------- swiper assets (wspólne z product-modal) ----------
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
      function done(){ if (++loaded === 2) resolve(); }
      link.onload = done; script.onload = done;
      document.head.appendChild(link);
      document.head.appendChild(script);
    });
  }

  // ---------- utils ----------
  function lockScroll(){ document.documentElement.classList.add("product-modal-open"); }
  function unlockScroll(){ document.documentElement.classList.remove("product-modal-open"); }
  function trapFocus(container, e){
    const focusables = container.querySelectorAll(`a,button,textarea,input,select,[tabindex]:not([tabindex="-1"])`);
    if (!focusables.length) return;
    const first = focusables[0], last = focusables[focusables.length - 1];
    if (e.shiftKey && document.activeElement === first) { last.focus(); e.preventDefault(); }
    else if (!e.shiftKey && document.activeElement === last) { first.focus(); e.preventDefault(); }
  }
  function filenameFromUrl(u){
    try{ const p = new URL(u, location.href).pathname; const base = p.split("/").pop() || ""; return decodeURIComponent(base); }catch{ return u; }
  }
  function escapeHtml(str){ return String(str || "").replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s])); }
  const escapeHtmlAttr = escapeHtml;

  // mapowanie rekordu dokumentu (z danych globalnych)
  function mapDocRecord(d){
    const url = d?.downloadUrl || "";
    const viewUrl = d?.viewUrl || "";
    const title = d?.title || filenameFromUrl(url);
    return { url, viewUrl, title };
  }

  // ekstrakcja z kafelka (SINGLE / fallback MAIN)
    function extractDocFromTile(tile){
        const rawUrl = tile.getAttribute("href")
            || tile.getAttribute("data-url")
            || (tile.querySelector("a[href]")?.getAttribute("href") || "");

        const viewUrl = tile.getAttribute("data-view")
            || tile.getAttribute("data-link") // backward-compat
            || "";

        let title = tile.getAttribute("data-title") || "";
        if (!title) {
            const tEl = tile.querySelector(".document-title, .exhibitor-single__document-title, .product-title");
            title = tEl ? tEl.textContent.trim() : "";
        }
        if (!title) title = filenameFromUrl(rawUrl || viewUrl);

        return { url: rawUrl, viewUrl, title };
    }

  // ---------- modal ----------
  function createDocsModal(documents, startIndex, openerEl){
    if (!documents || !documents.length) return;

    const backdrop = document.createElement("div");
    backdrop.className = "exhibitor-docs-modal__backdrop";
    backdrop.setAttribute("role","dialog");
    backdrop.setAttribute("aria-modal","true");
    backdrop.setAttribute("aria-labelledby","docsModalTitle");

    const slidesHTML = documents.map(d => {
        const safeUrl   = d.url || "";                 // do "Pobierz"
        const previewUrl = d.viewUrl || d.url || "";   // do iframe i "Otwórz w nowej karcie"
        const safeTitle = d.title || "";

        const actions = `
            ${ safeUrl   ? `<a class="exhibitor-docs-modal__btn" href="${safeUrl}" download>Pobierz PDF</a>` : "" }
            ${ previewUrl? `<a class="exhibitor-docs-modal__btn" href="${previewUrl}" target="_blank" rel="noopener">Otwórz w nowej karcie</a>` : "" }
        `;

        return `
            <div class="swiper-slide">
            <div class="exhibitor-docs-modal__media">
                ${ previewUrl
                    ? `<iframe class="exhibitor-docs-modal__iframe" src="${previewUrl}#toolbar=0" loading="lazy" title="${escapeHtmlAttr(safeTitle)} (podgląd PDF)"></iframe>`
                    : `<div class="exhibitor-docs-modal__no-preview">Brak podglądu</div>` }
            </div>
            <div class="exhibitor-docs-modal__text">
                <h4 id="docsModalTitle" class="exhibitor-docs-modal__title">${escapeHtml(safeTitle)}</h4>
                <div class="exhibitor-docs-modal__actions">${actions}</div>
            </div>
            </div>
        `;
    }).join("");

    backdrop.innerHTML = `
      <div class="exhibitor-docs-modal">
        <button class="exhibitor-docs-modal__close" type="button" aria-label="Zamknij" data-close>&times;</button>

        <div class="swiper" aria-roledescription="carousel">
          <div class="swiper-wrapper">${slidesHTML}</div>

          <button class="swiper-button-prev exhibitor-docs-modal__arrow" type="button" aria-label="Poprzedni dokument">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" transform="matrix(-1,0,0,1,0,0)"><path d="M18.6,11.2l-12-9A1,1,0,0,0,5,3V21a1,1,0,0,0,.55.89,1,1,0,0,0,1-.09l12-9a1,1,0,0,0,0-1.6Z"></path></svg>
          </button>
          <button class="swiper-button-next exhibitor-docs-modal__arrow" type="button" aria-label="Następny dokument">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.6,11.2l-12-9A1,1,0,0,0,5,3V21a1,1,0,0,0,.55.89,1,1,0,0,0,1-.09l12-9a1,1,0,0,0,0-1.6Z"></path></svg>
          </button>
          <div class="swiper-pagination" aria-label="Paginacja dokumentów"></div>
        </div>
      </div>
    `;

    document.body.appendChild(backdrop);
    requestAnimationFrame(() => backdrop.classList.add("is-open"));
    lockScroll();

    const dialog = backdrop.querySelector(".exhibitor-docs-modal");
    const closeBtn = backdrop.querySelector("[data-close]");
    if (closeBtn) closeBtn.focus();

    function destroy(){
      backdrop.classList.remove("is-open");
      setTimeout(() => {
        backdrop.remove();
        unlockScroll();
        if (openerEl) openerEl.focus();
        document.removeEventListener("keydown", onKeyDown);
        backdrop.removeEventListener("click", onBackdropClick);
      }, 150);
    }
    function onBackdropClick(e){
      if (e.target === backdrop || e.target.hasAttribute("data-close")) destroy();
    }
    function onKeyDown(e){
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
        pagination: { el: backdrop.querySelector(".swiper-pagination"), clickable: true }
      });

      const live = document.createElement("div");
      live.setAttribute("aria-live","polite");
      live.style.position = "absolute";
      live.style.left = "-9999px";
      dialog.appendChild(live);

      function announce(){
        const idx = swiper.realIndex + 1;
        live.textContent = `Dokument ${idx} z ${documents.length}: ${documents[swiper.realIndex].title || ""}`;
      }
      swiper.on("init", announce);
      swiper.on("slideChange", announce);
      announce();
    });

    return destroy;
  }

  function openWithData(documents, startIndex, openerEl){
    if (!documents || !documents.length) return;
    return createDocsModal(documents, startIndex, openerEl);
  }

  // ---------- SINGLE (dopasowane do Twojego pliku) ----------
  function initForSingle({
    sectionSelector = ".exhibitor-single__documents",
    listSelector = ".exhibitor-single__documents-list",
    tileSelector = ".exhibitor-single__documents-element"
  } = {}) {
    const section = document.querySelector(sectionSelector);
    if (!section) return;

    const list = section.querySelector(listSelector);
    if (!list) return;

    const tiles = Array.from(list.querySelectorAll(tileSelector));
    if (!tiles.length) return;

    const documents = tiles.map(extractDocFromTile).filter(d => d.url);

    function handleOpen(tile){
      const index = Math.max(0, tiles.indexOf(tile));
      openWithData(documents, index, tile);
    }

    list.addEventListener("click", (e) => {
      const tile = e.target.closest(tileSelector);
      if (tile) { e.preventDefault(); handleOpen(tile); }
    });

    list.addEventListener("keydown", (e) => {
      const tile = e.target.closest(tileSelector);
      if (!tile) return;
      if (e.key === "Enter" || e.key === " ") { e.preventDefault(); handleOpen(tile); }
    });
  }

  // ---------- MAIN ----------
  function initForMain({
    cardSelector = ".exhibitor-catalog__item",
    tileSelector = ".exhibitor-catalog__material",
    listSelector = ".exhibitor-catalog__materials-list"
  } = {}) {
    document.addEventListener("click", (e) => {
      const tile = e.target.closest(tileSelector);
      if (!tile) return;

      const card = tile.closest(cardSelector);
      if (!card) return;

      const exId = Number(card.getAttribute("data-id") || 0);
      if (!exId) return;

      const all = (window.__EXHIBITORS__ || []);
      const exhibitor = all.find(x => Number(x.id_numeric || x.idNumeric) === exId);

      let docs = [];
      if (exhibitor && Array.isArray(exhibitor.documents)) {
        docs = exhibitor.documents.map(mapDocRecord).filter(d => d.url);
      }

      if (!docs.length) {
        const list = tile.closest(listSelector) || card;
        const tiles = list ? Array.from(list.querySelectorAll(tileSelector)) : [tile];
        docs = tiles.map(extractDocFromTile).filter(d => d.url);
      }

      const listForIndex = tile.closest(listSelector) || card;
      const tilesForIndex = listForIndex ? Array.from(listForIndex.querySelectorAll(tileSelector)) : [];
      const index = Math.max(0, tilesForIndex.indexOf(tile));

      openWithData(docs, index, tile);
    });

    document.addEventListener("keydown", (e) => {
      if (!(e.key === "Enter" || e.key === " ")) return;
      const tile = e.target.closest(tileSelector);
      if (!tile) return;
      e.preventDefault();
      tile.click();
    });
  }

  // ---------- auto-init ----------
  document.addEventListener("DOMContentLoaded", () => {
    initForSingle();
    initForMain();
  });
})();
