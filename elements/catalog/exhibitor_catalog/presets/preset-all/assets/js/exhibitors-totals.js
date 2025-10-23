// exhibitors-totals.js — facet counters and global counter
// ŁADUJ PO exhibitors-core.js. Nie używa X poza onReady-callbackiem.

(function() {
    const onReady = (fn) => {
        if (typeof window.__onExhibitorsReady === "function") return window.__onExhibitorsReady(fn);
        document.addEventListener(
            "exhibitors:dataReady",
            (e) => fn(e.detail || window.Exhibitors), { once: true }
        );
    };

    onReady(function initTotals(X) {
        if (!X) return;
        const { lower } = X;

        function computeDynamicTotals(arr) {
            const totals = {
                type: { exhibitors: 0, products: 0 },
                halls: Object.create(null),
                tags: Object.create(null), // katalogowe (sector[])
                productTags: Object.create(null), // tagi produktów (products_tag[])
            };
            if (!Array.isArray(arr)) return totals;

            for (const it of arr) {
                const t = (it && it.type) || "exhibitor";
                if (t === "product") totals.type.products++;
                else totals.type.exhibitors++;

                const hall = lower(it?.hallName || it?.hall || "");
                if (hall) totals.halls[hall] = (totals.halls[hall] || 0) + 1;

                // katalogowe na wystawcy; produkty też zabezpieczamy
                const catTags = (Array.isArray(it?.catalogTags) ? it.catalogTags : []).map(lower);
                for (const tag of catTags)
                    if (tag) totals.tags[tag] = (totals.tags[tag] || 0) + 1;

                // tagi produktów
                const pTagsRaw = it?.tags ?? it?.productTags ?? [];
                const pTags = Array.isArray(pTagsRaw) ? pTagsRaw.map(lower) : [];
                for (const pt of pTags)
                    if (pt) totals.productTags[pt] = (totals.productTags[pt] || 0) + 1;
            }
            return totals;
        }

        function ensureCountSpan(labelEl) {
            let span = labelEl.querySelector(".exhibitor-catalog__count");
            if (!span) {
                span = document.createElement("span");
                span.className = "exhibitor-catalog__count";
                labelEl.appendChild(span);
            }
            return span;
        }

        function restoreBaseText(baseEl) {
            if (baseEl.hasAttribute("data-base-label")) {
                const baseText = baseEl.getAttribute("data-base-label") || "";
                if (!baseEl.firstChild || baseEl.firstChild.nodeType !== Node.TEXT_NODE) {
                    baseEl.insertBefore(document.createTextNode(baseText), baseEl.firstChild || null);
                } else {
                    baseEl.firstChild.nodeValue = baseText;
                }
                return;
            }
            const txt = (baseEl.textContent || "").replace(/\s*\(\d+\)\s*$/, "").trim();
            if (!baseEl.firstChild || baseEl.firstChild.nodeType !== Node.TEXT_NODE) {
                baseEl.insertBefore(document.createTextNode(txt), baseEl.firstChild || null);
            } else {
                baseEl.firstChild.nodeValue = txt;
            }
        }

        function setCheckboxCount(checkbox, n) {
            const optionRoot = checkbox.closest(".exhibitor-catalog__checkbox");
            if (!optionRoot) return;
            const base =
                optionRoot.querySelector("[data-base-label]") ||
                optionRoot.querySelector(".exhibitor-catalog__checkbox-label") ||
                optionRoot;

            restoreBaseText(base);
            const span = ensureCountSpan(base);
            span.textContent = ` (${Number(n) || 0})`;

            const shouldHide = Number(n) === 0 && !checkbox.checked;
            optionRoot.classList.toggle("is-zero", shouldHide);
            optionRoot.setAttribute("aria-hidden", shouldHide ? "true" : "false");
            checkbox.disabled = shouldHide;
        }

        function applyStaticTotalsToLabels(totals) {
            const root = X.filtersRoot || document;
            if (!root) return;

            // Typ
            root
                .querySelectorAll('input.exhibitor-catalog__checkbox-input[name="exhibitors"]')
                .forEach((cb) => setCheckboxCount(cb, totals.type.exhibitors));
            root
                .querySelectorAll('input.exhibitor-catalog__checkbox-input[name="products"]')
                .forEach((cb) => setCheckboxCount(cb, totals.type.products));

            // Hale
            root
                .querySelectorAll('input.exhibitor-catalog__checkbox-input[name="hall[]"]')
                .forEach((cb) => {
                    const key = lower(cb.value || "");
                    setCheckboxCount(cb, totals.halls[key] || 0);
                });

            // Sektory (UWAGA: nazwa z UI to sector[])
            root
                .querySelectorAll('input.exhibitor-catalog__checkbox-input[name="sector[]"]')
                .forEach((cb) => {
                    const key = lower(cb.value || "");
                    setCheckboxCount(cb, totals.tags[key] || 0);
                });

            // Tagi produktów (UWAGA: nazwa z UI to products_tag[])
            root
                .querySelectorAll('input.exhibitor-catalog__checkbox-input[name="products_tag[]"]')
                .forEach((cb) => {
                    const key = lower(cb.value || "");
                    setCheckboxCount(cb, totals.productTags[key] || 0);
                });
        }

        // Public API
        X.computeDynamicTotals = computeDynamicTotals;
        X.applyStaticTotalsToLabels = applyStaticTotalsToLabels;

        X.updateFacetCounts = function() {
            try {
                const now = Array.isArray(X.CURRENT) ? X.CURRENT : [];
                const totals = computeDynamicTotals(now);
                applyStaticTotalsToLabels(totals);
            } catch (_) {}
        };

        X.updateCounter = function(n, nounKey = "wyszukanie") {
            const el = X.counterEl;
            if (!el) return;
            const num = Number(n) || 0;
            if (window.PL && typeof window.PL.fmtCount === "function")
                el.textContent = window.PL.fmtCount(num, nounKey);
            else el.textContent = String(num);
        };

        // Inicjał
        X.updateFacetCounts();
        X.updateCounter(X.CURRENT?.length || 0);
    });
})();