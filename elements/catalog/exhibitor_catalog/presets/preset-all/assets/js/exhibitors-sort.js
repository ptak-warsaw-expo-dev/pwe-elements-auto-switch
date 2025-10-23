// exhibitors-sort.js
// Czeka na dane z script.js i definiuje X.applyFilters (filtrowanie + sortowanie)

// --- bezpieczny start ---
if (typeof window.__onExhibitorsReady === "function") {
    window.__onExhibitorsReady(initSort);
} else {
    document.addEventListener("exhibitors:dataReady", () => initSort(window.Exhibitors));
}

function initSort(X) {
    if (!X) X = window.Exhibitors;
    if (!X) return;

    const { state, lower, norm, getId } = X;

    // Źródło danych z zachowaniem kolejności z backendu
    const getSource = () => X.DATA_ALL || X.dataAll || X.data || [];

    // Bezpieczny parser metrażu -> liczba (m²)
    function getAreaSafe(x) {
        const raw =
            (typeof X.getArea === "function" ? X.getArea(x) : null) ??
            x.standArea ??
            x.stand_area ??
            x.area ??
            x.area_sqm ??
            0;

        if (typeof raw === "number") return isFinite(raw) ? raw : 0;
        const m = String(raw).replace(",", ".").match(/-?\d+(\.\d+)?/);
        return m ? parseFloat(m[0]) : 0;
    }

    // Główna funkcja: filtruje i sortuje, nie zmieniając oryginalnej kolejności bazowej
    function applyFilters(dataAll) {
        let arr = Array.isArray(dataAll) ? dataAll : getSource();

        // --- 0) Filtr po typie (exhibitor/product) ---
        if (state.types && state.types.size) {
            arr = arr.filter(x => {
                const t = (x && x.type) ? String(x.type) : "exhibitor";
                return state.types.has(t);
            });
        }

        // --- 1) Szukajka (bez zmian) ---
        if (state.q && state.q.trim()) {
            const q = norm(state.q);
            arr = arr.filter((x) => {
                const name = norm(x.name || "");
                const desc = norm(x.description || "");
                const br = norm(x.brands || "");
                const stand = norm(String(x.standNumber || ""));
                return name.includes(q) || desc.includes(q) || br.includes(q) || stand.includes(q);
            });
        }

        // --- 2) Hale (bez zmian) ---
        if (state.halls?.size) {
            arr = arr.filter((x) => state.halls.has(lower(x.hallName || "")));
        }

        // --- 3) Sektory (dla product -> x.sectors; dla exhibitor -> x.catalogTags) ---
        if (state.tags?.size) {
            arr = arr.filter((x) => {
                const isProduct = x?.type === "product";
                const list = isProduct ? (x.sectors || []) : (x.catalogTags || []);
                const set = new Set(
                    (Array.isArray(list) ? list : String(list).split(","))
                    .map(s => lower(String(s).trim()))
                    .filter(Boolean)
                );
                for (const t of state.tags)
                    if (!set.has(t)) return false;
                return true;
            });
        }

        // --- 4) Tagi produktów (działa na obu typach rekordów) ---
        if (state.productTags?.size) {
            arr = arr.filter((x) => {
                const have = new Set();

                if (x?.type === "product") {
                    // bezpośrednie tagi produktu
                    const parts = Array.isArray(x.tags) ? x.tags : (x.tags ? [x.tags] : []);
                    parts
                        .flatMap(s => String(s || "").split(/(?:\s*,\s*|\s{2,})/u))
                        .map(t => lower(String(t).trim()))
                        .filter(Boolean)
                        .forEach(t => have.add(t));
                } else {
                    // agregacja po produktach wystawcy
                    (Array.isArray(x.products) ? x.products : []).forEach((p) => {
                        const raw = p?.tags ?? [];
                        const parts = Array.isArray(raw) ? raw.flat() : [raw];
                        parts
                            .flatMap((s) => String(s || "").split(/(?:\s*,\s*|\s{2,})/u))
                            .map((t) => lower(t.trim()))
                            .filter(Boolean)
                            .forEach((t) => have.add(t));
                    });
                }

                for (const t of state.productTags)
                    if (!have.has(t)) return false;
                return true;
            });
        }

        // --- 5) SORT (jak było) ---
        if (state.onlyBig && state.onlyNew) {
            arr = [...arr].sort((a, b) => {
                const dArea = getAreaSafe(b) - getAreaSafe(a);
                if (dArea) return dArea;
                const dId = getId(b) - getId(a);
                if (dId) return dId;
                const na = (X.getName?.(a) ?? a.name ?? "").toString();
                const nb = (X.getName?.(b) ?? b.name ?? "").toString();
                return na.localeCompare(nb);
            });
        } else if (state.onlyBig) {
            arr = [...arr].sort((a, b) => {
                const dArea = getAreaSafe(b) - getAreaSafe(a);
                if (dArea) return dArea;
                const na = (X.getName?.(a) ?? a.name ?? "").toString();
                const nb = (X.getName?.(b) ?? b.name ?? "").toString();
                const byName = na.localeCompare(nb);
                if (byName) return byName;
                return getId(b) - getId(a);
            });
        } else if (state.onlyNew) {
            arr = [...arr].sort((a, b) => getId(b) - getId(a));
        }

        return arr;
    }



    // Eksport do głównego API – paginacja wywołuje X.applyFilters w reapplyAndReset()
    X.applyFilters = applyFilters;
}