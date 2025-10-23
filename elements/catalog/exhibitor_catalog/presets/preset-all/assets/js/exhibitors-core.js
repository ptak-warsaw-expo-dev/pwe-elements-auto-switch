// exhibitors-core.js
document.addEventListener("DOMContentLoaded", () => {
    (function() {
        const root = document.getElementById("exhibitorCatalog");
        if (!root) return;

        // --- NARZĘDZIA / HELPERY (uzupełnione) ---
        const lower = (s) => (s || "").toString().toLowerCase().trim();
        const uniq = (arr) => Array.from(new Set(arr));
        const esc = (s) =>
            (s || "")
            .toString()
            .replace(/[&<>"]/g, (c) => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" } [c]));

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

        const getId = (x) =>
            x?.type === "product" ? Number(x.id || 0) : Number(x?.idNumeric ?? x?.exhibitor_id ?? x?.exhibitorId ?? 0);

        const getArea = (x) => (x?.type === "product" ? 0 : toArea(x?.booth_area ?? 0));

        // zamiast lokalnych pl*, użyj globalnego PL
        const hasPL = !!(window.PL && window.PL.fmtCount);

        // tagi produktów: dziel po przecinku LUB po >=2 spacjach
        const splitProductTags = (raw) => {
            if (!raw) return [];
            if (Array.isArray(raw)) return raw.flatMap(splitProductTags);
            return String(raw).split(/(?:\s*,\s*|\s{2,})/u).map((s) => lower(s)).filter(Boolean);
        };

        // z jednego wystawcy -> tablica rekordów produktów (flatten)
        function extractProductsFromExhibitor(ex) {
            const exhibitorId = Number(ex.exhibitorId ?? ex.exhibitor_id ?? ex.idNumeric ?? 0);
            const exhibitorName = String(ex.name || "");
            const hallName = String(ex.hallName || "");
            const sectorsArr = Array.isArray(ex.catalogTags) ? ex.catalogTags.map(String) : [];
            const brandSet = Array.isArray(ex.brands) ? ex.brands : [];

            const arr = Array.isArray(ex.products) ? ex.products : [];
            return arr.map((p, idx) => {
                const id = exhibitorId * 1e5 + idx; // stabilne i rosnące „nowsze”
                const name = String(p?.name || "").trim();
                const img = String(p?.img || p?.image || "");
                const desc = String(p?.description || "");
                const brand = String(p?.brand || brandSet[0] || "");
                const tags = splitProductTags(p?.tags ?? []);
                return {
                    type: "product",
                    id,
                    exhibitorId,
                    exhibitorName,
                    hallName,
                    standNumber: String(ex.standNumber || ""),
                    name,
                    description: desc,
                    img,
                    brand,
                    tags, // do filtra products_tag[]
                    sectors: sectorsArr, // do filtra sector[]
                };
            });
        }

        // normalizacja rekordów exhibitorów (mapowanie pól + fallbacki)
        function normalize(ex) {
            if (!ex || typeof ex !== "object") return ex;
            ex.idNumeric = ex.idNumeric ?? Number(ex.id_numeric ?? ex.exhibitor_id ?? ex.exhibitorId ?? 0);
            ex.logoUrl = ex.logoUrl ?? ex.logo_url ?? "";
            ex.contactEmail = ex.contactEmail ?? ex.contact_email ?? "";
            ex.contactInfo = ex.contactInfo ?? ex.contact_phone ?? "";

            ex.catalogTags = Array.isArray(ex.catalogTags) ?
                ex.catalogTags :
                (Array.isArray(ex.catalog_tags) ? ex.catalog_tags : []);
            ex.brands = Array.isArray(ex.brands) ? ex.brands : [];

            ex.hallName = ex.hallName ?? ex.hall_name ?? "";
            ex.standNumber = ex.standNumber ?? ex.stand_number ?? "";

            // liczniki
            ex.productsCount = ex.productsCount ?? Number(
                ex.products_count ?? (Array.isArray(ex.products_preview) ? ex.products_preview.length : 0)
            );
            ex.documentsCount = ex.documentsCount ?? Number(
                ex.documents_count ?? (Array.isArray(ex.documents_preview) ? ex.documents_preview.length : 0)
            );

            // preview (do kafelków)
            ex.productsTrim = Array.isArray(ex.productsTrim) ?
                ex.productsTrim :
                (Array.isArray(ex.products_preview) ? ex.products_preview : []);
            ex.documentsTrim = Array.isArray(ex.documentsTrim) ?
                ex.documentsTrim :
                (Array.isArray(ex.documents_preview) ? ex.documents_preview : []);

            // liczenie stałej, parsowanej powierzchni (przyda się dalej)
            ex.totalArea = toArea(
                ex.totalArea ?? ex.total_booth_area ?? ex.boothArea ?? ex.areaSum ?? ex.stand?.boothArea ?? ex.area ?? ex.booth_area ?? 0
            );

            ex.isFeatured = 0;


            return ex;
        }

        // --- ELEMENTY DOM ---
        const listEl = root.querySelector(".exhibitor-catalog__list");
        const counterEl = root.querySelector(".exhibitor-catalog__counter");
        const filtersRoot = root.querySelector(".exhibitor-catalog__filters");
        const searchInput = root.querySelector(".exhibitor-catalog__search-input") || root.querySelector(".exhibitor-catalog__search input");
        const sentinel = document.getElementById("infiniteSentinel");
        const loader = document.getElementById("infiniteLoader");
        if (!listEl || !filtersRoot || !sentinel || !loader) return;

        // --- BOOT wołane przez script.js po fetchu danych ---
        window.__exhibitorsBoot = function __exhibitorsBoot(DATA_ALL, PER_PAGE) {
            let DATA = (Array.isArray(DATA_ALL) ? DATA_ALL : []).map(normalize);
            DATA.forEach(x => { if (!x.type) x.type = "exhibitor"; });
            const PRODUCTS = DATA.flatMap(extractProductsFromExhibitor);
            const MERGED = [...DATA, ...PRODUCTS]; // najpierw wystawcy, potem produkty


            // fallback: wyznacz wyróżnionych gdy backend ich nie zwróci
            (function ensureFeaturedFlag() {
                if (!DATA.length) return;
                const _getId = (x) => Number(x?.idNumeric ?? x?.exhibitor_id ?? x?.exhibitorId ?? 0);
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
            const ALL_TAGS = uniq(DATA.flatMap((x) => toTagArray(x.catalogTags))).sort();

            // HYDRACJA SSR – ile już jest wyrenderowane serwerowo
            const SSR_COUNT = listEl.querySelectorAll(".exhibitor-catalog__item").length;

            // ustaw licznik jeśli pusty
            if (counterEl) {
                const total = Array.isArray(MERGED) ? MERGED.length : 0;
                counterEl.textContent = hasPL ? window.PL.fmtCount(total, "wyszukanie") : String(total);
            }

            // --- GLOBALNE API (dla pozostałych modułów) ---
            window.Exhibitors = {
                DATA_ALL: MERGED,
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
                getId,
                getArea,
                ALL_HALLS,
                ALL_TAGS,
                state: { q: "", halls: new Set(), tags: new Set(), productTags: new Set(), onlyNew: false, onlyBig: false, types: new Set(["exhibitor", "product"]) },
                CURRENT: MERGED,
                rendered: SSR_COUNT, // SSR to tylko wystawcy
                renderCard: function() { return ""; },
                __normalize: normalize,
                __ssrHydrated: true,
            };

            // powiadom moduły feature’owe, że dane są gotowe
            document.dispatchEvent(new CustomEvent("exhibitors:dataReady", { detail: window.Exhibitors }));

        };
    })();
});