if (typeof window.__onExhibitorsReady === "function") {
  window.__onExhibitorsReady(initFilters);
} else {
  document.addEventListener("exhibitors:dataReady", () => initFilters(window.Exhibitors));
}

function initFilters(X) {
  if (!X) X = window.Exhibitors;
  if (!X) return;

  // --- Synchronizacja licznika po każdym filtrowaniu/szukaniu ---
    (function installCounterSync() {
        if (!X) return;

        // bezpieczny fallback, gdyby nie było updateCounter z core:
        const setCounter = (n) => {
            if (typeof X.updateCounter === "function") {
            X.updateCounter(n, "wyszukanie"); // używa pluralizacji: 1 wyszukanie / 2–4 wyszukania / 5+ wyszukań
            } else if (X.counterEl) {
            // awaryjnie tylko liczba
            X.counterEl.textContent = String(n);
            }
        };

        // jeżeli już zainstalowane — nie powtarzaj
        if (X.__counterSyncInstalled) return;
        X.__counterSyncInstalled = true;

        const orig = X.reapplyAndReset;
        if (typeof orig === "function") {
            X.reapplyAndReset = function (...args) {
            const ret = orig.apply(this, args);

            // jeśli reapply jest synchroniczne:
            if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length);

            // a jeśli coś w środku działa asynchronicznie (np. po next tick/rAF),
            // dołóż mikro/makro-zadanie, żeby złapać finalny stan:
            queueMicrotask?.(() => { if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length); });
            setTimeout(() => { if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length); }, 0);

            return ret;
            };
        } else {
            // gdyby ktoś ręcznie zmieniał X.CURRENT i renderował bez reapply:
            // obserwuj zmiany listy i aktualizuj licznik
            try {
            const list = X.listEl;
            if (list) {
                const mo = new MutationObserver(() => {
                if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length);
                });
                mo.observe(list, { childList: true, subtree: false });
                // ustaw początkowo
                if (Array.isArray(X.CURRENT)) setCounter(X.CURRENT.length);
            }
            } catch (_) {}
        }
    })();

  const { filtersRoot, searchInput, state, lower } = X;
  if (!filtersRoot) return;

  // --- Collapsible (opcjonalnie, bez usuwania elementów) ---
  function makeGroupCollapsible(groupEl, keep = 3) {
    if (!groupEl || groupEl.dataset.collapsibleInit === "1") return;
    groupEl.dataset.collapsibleInit = "1";

    const items = Array.from(groupEl.querySelectorAll(".exhibitor-catalog__checkbox"));
    if (items.length <= keep) return;

    // zabezpieczenie na wypadek display:none z innego skryptu
    items.forEach(el => { el.style.display = ""; });

    // 1) Kontener na „resztę”
    const moreWrap = document.createElement("div");
    moreWrap.className = "exhibitor-catalog__collapse";

    // przenieś nadmiar
    items.slice(keep).forEach(el => moreWrap.appendChild(el));

    // wstaw po ostatnim "keep"
    items[keep - 1].after(moreWrap);

    // 2) Przycisk toggle
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "exhibitor-catalog__toggle";
    btn.setAttribute("aria-expanded", "false");
    btn.innerHTML = "Pokaż więcej";
    groupEl.appendChild(btn);

    // 3) Start: zwinięty
    let open = false;
    moreWrap.style.maxHeight = "0px";
    moreWrap.style.overflow = "hidden";

    // 4) Po animacji otwierania zdejmij limit wysokości
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
          void moreWrap.offsetHeight; // reflow
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
        void moreWrap.offsetHeight; // reflow
      }
      setOpen(!open);
    });

    // 5) Reakcja na resize: przelicz tylko kiedy otwarte
    window.addEventListener("resize", () => {
      if (open) {
        if (getComputedStyle(moreWrap).maxHeight === "none") {
          moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
        } else {
          moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
        }
      }
    });

    // schowaj strzałkę z nagłówka (jeśli była)
    const headerArrow = groupEl.querySelector(".exhibitor-catalog__heading-container img");
    if (headerArrow) headerArrow.style.display = "none";
  }

  // --- Bindowanie istniejących filtrów z DOM ---
  function bindExistingFilters() {
    // 0) Szukajka (debounce)
    if (searchInput) {
      const debounced = (fn, ms = 200) => {
        let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
      };
      searchInput.addEventListener("input", debounced(() => {
        state.q = String(searchInput.value || "");
        X.reapplyAndReset && X.reapplyAndReset();
      }, 200));
    }

    // 1) Wyróżnienia: najwięksi / najnowsi
    const featured = filtersRoot.querySelector('input.exhibitor-catalog__checkbox-input[name="featured"]');
    const newest   = filtersRoot.querySelector('input.exhibitor-catalog__checkbox-input[name="newest"]');

    if (featured) {
      state.onlyBig = !!featured.checked;
      featured.addEventListener("change", (e) => {
        state.onlyBig = !!e.target.checked;
        X.reapplyAndReset && X.reapplyAndReset();
      });
    }
    if (newest) {
      state.onlyNew = !!newest.checked;
      newest.addEventListener("change", (e) => {
        state.onlyNew = !!e.target.checked;
        X.reapplyAndReset && X.reapplyAndReset();
      });
    }

    // 2) Hale
    const hallInputs = filtersRoot.querySelectorAll('input.exhibitor-catalog__checkbox-input[name="hall[]"]');
    state.halls.clear();
    hallInputs.forEach(inp => {
      const v = lower(inp.value || "");
      if (inp.checked && v) state.halls.add(v);
    });
    filtersRoot.addEventListener("change", (e) => {
      const input = e.target.closest('input.exhibitor-catalog__checkbox-input[name="hall[]"]');
      if (!input) return;
      const v = lower(input.value || "");
      if (!v) return;
      if (input.checked) state.halls.add(v); else state.halls.delete(v);
      X.reapplyAndReset && X.reapplyAndReset();
    });

    // 3) Sektory
    const tagInputs = filtersRoot.querySelectorAll('input.exhibitor-catalog__checkbox-input[name="sector[]"]');
    state.tags.clear();
    tagInputs.forEach(inp => {
      const v = lower(inp.value || "");
      if (inp.checked && v) state.tags.add(v);
    });
    filtersRoot.addEventListener("change", (e) => {
      const input = e.target.closest('input.exhibitor-catalog__checkbox-input[name="sector[]"]');
      if (!input) return;
      const v = lower(input.value || "");
      if (!v) return;
      if (input.checked) state.tags.add(v); else state.tags.delete(v);
      X.reapplyAndReset && X.reapplyAndReset();
    });

    // 3a) Tagi produktów
    if (!state.productTags) state.productTags = new Set();
    const prodTagInputs = filtersRoot.querySelectorAll('input.exhibitor-catalog__checkbox-input[name="products_tag[]"]');

    state.productTags.clear();
    prodTagInputs.forEach(inp => {
      const v = lower(inp.value || "");
      if (inp.checked && v) state.productTags.add(v);
    });

    filtersRoot.addEventListener("change", (e) => {
      const input = e.target.closest('input.exhibitor-catalog__checkbox-input[name="products_tag[]"]');
      if (!input) return;
      const v = lower(input.value || "");
      if (!v) return;
      if (input.checked) state.productTags.add(v);
      else state.productTags.delete(v);
      X.reapplyAndReset && X.reapplyAndReset();
    });

    // 4) Collapsible tylko wizualnie
    const groups = filtersRoot.querySelectorAll(".exhibitor-catalog__category-group");
    groups.forEach(g => makeGroupCollapsible(g, 3));
  }

    // auto-init po danych
    bindExistingFilters();

    // odsłoń filtry z animacją po zainicjalizowaniu
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            if (filtersRoot) {
            filtersRoot.style.visibility = "visible"; // włącza klikalność
            filtersRoot.classList.add("is-visible");  // odpala animację opacity
            }
        });
    });

  // eksport API (inne moduły wywołują reapplyAndReset i zarządzają renderem)
  X.bindExistingFilters = bindExistingFilters;
  X.makeGroupCollapsible = makeGroupCollapsible;

  // auto-init po danych
  bindExistingFilters();
}
