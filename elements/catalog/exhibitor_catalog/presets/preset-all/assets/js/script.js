document.addEventListener("DOMContentLoaded", () => {
    const root = document.getElementById("exhibitorCatalog");
    if (!root) return;

    // 1) prosty broker „czekam na dane”
    window.__onExhibitorsReady = function(cb) {
        if (window.Exhibitors && window.Exhibitors.__ssrHydrated) {
            try { cb(window.Exhibitors); } catch (e) { console.warn(e); }
        } else {
            (window.__exhQueue = window.__exhQueue || []).push(cb);
        }
    };

    // 2) parametry
    const PER_PAGE = Number(root.getAttribute("data-per-page") || 20);
    const DATA_URL = new URL("/doc/pwe-exhibitors-data.json", location.origin).toString();

    // 3) fetch JSON tylko tutaj
    (async function loadAndBoot() {
        try {
            const res = await fetch(DATA_URL, { cache: "no-store" });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            // 4) budowa Ex i sygnał dla reszty
            if (typeof window.__exhibitorsBoot === "function") {
                window.__exhibitorsBoot(data, PER_PAGE);

                // kompatybilne globalki (jeśli jakieś moduły jeszcze na nich polegają)
                window.__EXHIBITORS__ = window.Exhibitors.DATA_ALL;
                window.__PER_PAGE__ = window.Exhibitors.PER_PAGE;

                // event + kolejka
                document.dispatchEvent(new CustomEvent("exhibitors:dataReady", { detail: window.Exhibitors }));
                if (Array.isArray(window.__exhQueue)) {
                    window.__exhQueue.forEach(cb => { try { cb(window.Exhibitors); } catch (e) { console.warn(e); } });
                    window.__exhQueue = [];
                }
            } else {
                console.error("Brak window.__exhibitorsBoot (załaduj exhibitors-core.js przed script.js).");
            }
        } catch (err) {
            //   console.error("[Exhibitors] Fetch error:", err);
        }
    })();
});