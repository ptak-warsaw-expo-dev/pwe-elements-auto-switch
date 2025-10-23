if (typeof window.__onExhibitorsReady === "function") {
    window.__onExhibitorsReady(initFilters);
} else {
    document.addEventListener("exhibitors:dataReady", () => initFilters(window.Exhibitors));
}

function initFilters(X) {
    if (!X) X = window.Exhibitors;
    if (!X) return;

    (function installCounterSync() {
        if (!X) return;

        const setCounter = (n) => {
            if (typeof X.updateCounter === "function") {
                X.updateCounter(n, "wyszukanie");
            } else if (X.counterEl) {
                X.counterEl.textContent = String(n);
            }
        };

        if (X.__counterSyncInstalled) return;
        X.__counterSyncInstalled = true;

        const orig = X.reapplyAndReset;
        if (typeof orig === "function") {
            X.reapplyAndReset = function(...args) {
                const ret = orig.apply(this, args);

                if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length);

                if (typeof queueMicrotask === "function") {
                    queueMicrotask(() => {
                        if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length);
                    });
                }
                setTimeout(() => {
                    if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length);
                }, 0);

                return ret;
            };
        } else {
            try {
                const list = X.listEl;
                if (list) {
                    const mo = new MutationObserver(() => {
                        if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length);
                    });
                    mo.observe(list, { childList: true, subtree: false });
                    if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length);
                }
            } catch (_) {}
        }
    })();

    const { filtersRoot, searchInput, state } = X;
    if (!filtersRoot) return;

    const lower =
        typeof X.lower === "function" ?
        X.lower :
        (s) => (s || "").toString().toLowerCase().trim();

    if (!state.halls) state.halls = new Set();
    if (!state.tags) state.tags = new Set();
    if (!state.productTags) state.productTags = new Set();
    if (!state.types) state.types = new Set();

    // --- Collapsible ---
    function makeGroupCollapsible(groupEl, keep = 3) {
        if (!groupEl || groupEl.dataset.collapsibleInit === "1") return;
        groupEl.dataset.collapsibleInit = "1";

        const items = Array.from(groupEl.querySelectorAll(".exhibitor-catalog__checkbox"));
        if (items.length <= keep) return;

        const moreWrap = document.createElement("div");
        moreWrap.className = "exhibitor-catalog__collapse";

        items.slice(keep).forEach((el) => moreWrap.appendChild(el));

        items[keep - 1].after(moreWrap);

        // 2) See More
        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "exhibitor-catalog__toggle";
        btn.setAttribute("aria-expanded", "false");
        btn.innerHTML = "Pokaż więcej";
        groupEl.appendChild(btn);

        let open = false;
        moreWrap.style.maxHeight = "0px";
        moreWrap.style.overflow = "hidden";

        moreWrap.addEventListener("transitionend", () => {
            if (open) {
                moreWrap.style.maxHeight = "none";
            }
        });

        const setOpen = (nextOpen) => {
            open = nextOpen;
            if (open) {
                if (getComputedStyle(moreWrap).maxHeight === "none") {
                    moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
                } else {
                    moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
                }
                btn.innerHTML = "Pokaż mniej";
                btn.setAttribute("aria-expanded", "true");
                btn.classList.add("is-open");
            } else {
                if (getComputedStyle(moreWrap).maxHeight === "none") {
                    moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
                    void moreWrap.offsetHeight;
                }
                moreWrap.style.maxHeight = "0px";
                btn.innerHTML = "Pokaż więcej";
                btn.setAttribute("aria-expanded", "false");
                btn.classList.remove("is-open");
            }
        };

        btn.addEventListener("click", () => {
            if (!open) {
                moreWrap.style.maxHeight = "0px";
                void moreWrap.offsetHeight;
            }
            setOpen(!open);
        });

        window.addEventListener("resize", () => {
            if (open) {
                if (getComputedStyle(moreWrap).maxHeight === "none") {
                    moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
                } else {
                    moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
                }
            }
        });

    }

    function bindExistingFilters() {
        if (searchInput && !searchInput.dataset.wired) {
            searchInput.dataset.wired = "1";
            const debounced = (fn, ms = 200) => {
                let t;
                return (...a) => {
                    clearTimeout(t);
                    t = setTimeout(() => fn(...a), ms);
                };
            };
            searchInput.addEventListener(
                "input",
                debounced(() => {
                    state.q = String(searchInput.value || "");
                    X.reapplyAndReset && X.reapplyAndReset();
                }, 200)
            );
        }

        // 1) Wyróżnienia
        const featured = filtersRoot.querySelector(
            'input.exhibitor-catalog__checkbox-input[name="featured"]'
        );
        const newest = filtersRoot.querySelector(
            'input.exhibitor-catalog__checkbox-input[name="newest"]'
        );

        if (featured) {
            state.onlyBig = !!featured.checked;
            if (!featured.dataset.wired) {
                featured.dataset.wired = "1";
                featured.addEventListener("change", (e) => {
                    state.onlyBig = !!e.target.checked;
                    X.reapplyAndReset && X.reapplyAndReset();
                });
            }
        }
        if (newest) {
            state.onlyNew = !!newest.checked;
            if (!newest.dataset.wired) {
                newest.dataset.wired = "1";
                newest.addEventListener("change", (e) => {
                    state.onlyNew = !!e.target.checked;
                    X.reapplyAndReset && X.reapplyAndReset();
                });
            }
        }

        // 2) Hale
        state.halls.clear();
        const hallInputs = filtersRoot.querySelectorAll(
            'input.exhibitor-catalog__checkbox-input[name="hall[]"]'
        );
        hallInputs.forEach((inp) => {
            const v = lower(inp.value || "");
            if (inp.checked && v) state.halls.add(v);
        });

        // 3) Sektory
        state.tags.clear();
        const tagInputs = filtersRoot.querySelectorAll(
            'input.exhibitor-catalog__checkbox-input[name="sector[]"]'
        );
        tagInputs.forEach((inp) => {
            const v = lower(inp.value || "");
            if (inp.checked && v) state.tags.add(v);
        });

        // 4) Tagi produktów
        state.productTags.clear();
        const prodTagInputs = filtersRoot.querySelectorAll(
            'input.exhibitor-catalog__checkbox-input[name="products_tag[]"]'
        );
        prodTagInputs.forEach((inp) => {
            const v = lower(inp.value || "");
            if (inp.checked && v) state.productTags.add(v);
        });

        if (!filtersRoot.dataset.changeWired) {
            filtersRoot.dataset.changeWired = "1";
            filtersRoot.addEventListener("change", (e) => {
                const input = e.target.closest('input.exhibitor-catalog__checkbox-input');
                if (!input) return;

                const name = input.getAttribute("name") || "";
                const v = lower(input.value || "");
                const checked = !!input.checked;

                if (name === "hall[]") {
                    if (!v) return;
                    checked ? state.halls.add(v) : state.halls.delete(v);
                } else if (name === "sector[]") {
                    if (!v) return;
                    checked ? state.tags.add(v) : state.tags.delete(v);
                } else if (name === "products_tag[]") {
                    if (!v) return;
                    checked ? state.productTags.add(v) : state.productTags.delete(v);
                } else if (name === "exhibitors" || name === "products") {
                    return;
                } else {
                    return;
                }

                X.reapplyAndReset && X.reapplyAndReset();
            });
        }

        // 5) Typ: Wystawcy / Produkty
        (function bindTypeFilters() {
            const $exh = filtersRoot.querySelector(
                'input.exhibitor-catalog__checkbox-input[name="exhibitors"]'
            );
            const $prd = filtersRoot.querySelector(
                'input.exhibitor-catalog__checkbox-input[name="products"]'
            );

            function updateTypesFromUI() {
                const next = new Set();
                const exChecked = $exh ? !!$exh.checked : false;
                const prChecked = $prd ? !!$prd.checked : false;

                if (!exChecked && !prChecked) {
                    next.add("exhibitor");
                    next.add("product");
                } else {
                    if (exChecked) next.add("exhibitor");
                    if (prChecked) next.add("product");
                }

                state.types = next;
                X.reapplyAndReset && X.reapplyAndReset();
            }

            updateTypesFromUI();

            if ($exh && !$exh.dataset.wired) {
                $exh.dataset.wired = "1";
                $exh.addEventListener("change", updateTypesFromUI);
            }
            if ($prd && !$prd.dataset.wired) {
                $prd.dataset.wired = "1";
                $prd.addEventListener("change", updateTypesFromUI);
            }
        })();

        const groups = filtersRoot.querySelectorAll(".exhibitor-catalog__category-group");
        groups.forEach((g) => makeGroupCollapsible(g, 3));
    }

    bindExistingFilters();

    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            if (filtersRoot) {
                filtersRoot.style.visibility = "visible";
                filtersRoot.classList.add("is-visible");
            }
        });
    });

    X.bindExistingFilters = bindExistingFilters;
    X.makeGroupCollapsible = makeGroupCollapsible;
}