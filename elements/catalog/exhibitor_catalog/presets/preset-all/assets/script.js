document.addEventListener("DOMContentLoaded", () => {
  if (!window.Exhibitors) return;
  const X = window.Exhibitors;

  if (window.Exhibitors.buildFiltersUI) window.Exhibitors.buildFiltersUI();

  X.CURRENT = window.Exhibitors.applyFilters(X.DATA_ALL);
  X.listEl.querySelectorAll(".exhibitor-catalog__item").forEach(n=>n.remove());
  X.updateCounter && X.updateCounter();
  X.rendered = 0;
  X.renderChunk && X.renderChunk();
  X.setupIO && X.setupIO();
});
