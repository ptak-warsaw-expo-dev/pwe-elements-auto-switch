if (typeof window.__onExhibitorsReady === "function") {
  window.__onExhibitorsReady(initPagination);
} else {
  document.addEventListener("exhibitors:dataReady", () => initPagination(window.Exhibitors));
}

function initPagination(X) {
  if (!X) X = window.Exhibitors;
  if (!X) return;

  const { listEl, sentinel, loader, counterEl, PER_PAGE, renderCard } = X;
  if (!listEl || !sentinel || !loader) return;

  function clearListKeepSentinel() {
    listEl.querySelectorAll(".exhibitor-catalog__item").forEach(n => n.remove());
  }

  function renderChunk() {
    if (X.rendered >= X.CURRENT.length) return;
    loader.style.display = "block";
    const next  = Math.min(X.rendered + PER_PAGE, X.CURRENT.length);
    const slice = X.CURRENT.slice(X.rendered, next);
    const html  = slice.map(renderCard).join("");
    const tmp   = document.createElement("div");
    tmp.innerHTML = html;
    while (tmp.firstChild) listEl.insertBefore(tmp.firstChild, sentinel);
    X.rendered = next;
    loader.style.display = "none";
  }

  function reapplyAndReset() {
    if (typeof window.Exhibitors.applyFilters === "function") {
      X.CURRENT = window.Exhibitors.applyFilters(X.DATA_ALL);
    }
    if (typeof window.Exhibitors.updateFacetCounts === "function") {
      window.Exhibitors.updateFacetCounts();
    }
    if (typeof X.updateCounter === "function") X.updateCounter(X.CURRENT.length, "wyszukanie");
    clearListKeepSentinel();
    X.rendered = 0;
    renderChunk();
  }

  function setupIO() {
    if (X.io) X.io.disconnect();
    X.io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) renderChunk();
      });
    }, { rootMargin: "400px 0px" });
    X.io.observe(sentinel);
  }

  // --- eksport do obiektu głównego ---
  X.clearListKeepSentinel = clearListKeepSentinel;
  X.renderChunk = renderChunk;
  X.reapplyAndReset = reapplyAndReset;
  X.setupIO = setupIO;

  // --- inicjalizacja po załadowaniu danych ---
  if (typeof X.updateCounter === "function") X.updateCounter(X.CURRENT.length, "wyszukanie");
  renderChunk();
  setupIO();
}
