if (typeof window.__onExhibitorsReady === "function") {
    window.__onExhibitorsReady(initSearch);
} else {
    document.addEventListener("exhibitors:dataReady", () => initSearch(window.Exhibitors));
}

function initSearch(X) {
    if (!X) X = window.Exhibitors;
    if (!X) return;

    const { searchInput, state } = X;
    if (!searchInput) return;

    // unikamy wielokrotnego podpinania eventu
    if (searchInput.__wired) return;
    searchInput.__wired = true;

    let t = null;
    searchInput.addEventListener("input", (e) => {
        clearTimeout(t);
        t = setTimeout(() => {
            state.q = e.target.value || "";
            if (typeof window.Exhibitors.reapplyAndReset === "function") {
                window.Exhibitors.reapplyAndReset();
            }
        }, 180);
    });
}