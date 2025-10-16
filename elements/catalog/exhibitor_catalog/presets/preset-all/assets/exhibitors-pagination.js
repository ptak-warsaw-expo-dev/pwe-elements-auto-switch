document.addEventListener("DOMContentLoaded", () => {
  if (!window.Exhibitors) return;
  const X = window.Exhibitors;
  const { listEl, sentinel, loader, counterEl, PER_PAGE, renderCard } = X;

  function updateCounter(){ if (counterEl) counterEl.textContent = X.CURRENT.length + " Wyszukiwań"; }
  function clearListKeepSentinel(){ listEl.querySelectorAll(".exhibitor-catalog__item").forEach(n => n.remove()); }

  function renderChunk(){
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

  function reapplyAndReset(){
    X.CURRENT = window.Exhibitors.applyFilters(X.DATA_ALL);
    updateCounter();
    clearListKeepSentinel();
    X.rendered = 0;
    renderChunk();
  }

  function setupIO(){
    if (X.io) X.io.disconnect();
    X.io = new IntersectionObserver(entries => {
      entries.forEach(e => { if (e.isIntersecting) renderChunk(); });
    }, { rootMargin: "400px 0px" });
    X.io.observe(sentinel);
  }

  // eksport
  X.updateCounter = updateCounter;
  X.clearListKeepSentinel = clearListKeepSentinel;
  X.renderChunk = renderChunk;
  X.reapplyAndReset = reapplyAndReset;
  X.setupIO = setupIO;
});
