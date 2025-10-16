// filters.js — bind do istniejących kontrolek z PHP (bez generowania HTML)
document.addEventListener("DOMContentLoaded", () => {
  if (!window.Exhibitors) return;
  const { filtersRoot, searchInput, state, lower } = window.Exhibitors;
  if (!filtersRoot) return;

  // --- Collapsible (opcjonalnie, bez usuwania elementów) ---
    function makeGroupCollapsible(groupEl, keep = 3) {
    if (!groupEl || groupEl.dataset.collapsibleInit === "1") return;
    groupEl.dataset.collapsibleInit = "1";

    const items = Array.from(groupEl.querySelectorAll(".exhibitor-catalog__checkbox"));
    if (items.length <= keep) return;

    // 0) zabezpieczenie na wypadek wcześniejszego display:none w innym skrypcie
    items.forEach(el => { el.style.display = ""; });

    // 1) Kontener na „resztę”
    const moreWrap = document.createElement("div");
    moreWrap.className = "exhibitor-catalog__collapse";

    // przenieś nadmiarowe elementy do kontenera
    items.slice(keep).forEach(el => moreWrap.appendChild(el));

    // wstaw po ostatnim „keep”
    items[keep - 1].after(moreWrap);

    // 2) Przycisk toggle
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "exhibitor-catalog__toggle";
    btn.setAttribute("aria-expanded", "false");
    btn.innerHTML = 'Pokaż więcej';
    groupEl.appendChild(btn);

    // 3) Start: zwinięty
    let open = false;
    moreWrap.style.maxHeight = "0px";
    moreWrap.style.overflow = "hidden";

    // 4) Po zakończeniu animacji otwierania zdejmij ograniczenie,
    //    żeby wnętrze mogło rosnąć (np. gdy zmieni się layout)
    moreWrap.addEventListener("transitionend", () => {
        if (open) {
        moreWrap.style.maxHeight = "none";
        }
    });

    const setOpen = (nextOpen) => {
        open = nextOpen;
        if (open) {
        // jeżeli poprzednio było 'none', najpierw przywróć konkretną wysokość
        if (getComputedStyle(moreWrap).maxHeight === "none") {
            moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
        } else {
            // od 0 do naturalnej wysokości
            moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
        }
        btn.innerHTML = 'Pokaż mniej';
        btn.setAttribute("aria-expanded", "true");
        btn.classList.add("is-open");
        } else {
        // jeśli było 'none', zrób „ustaw -> reflow -> 0”, żeby animacja zadziałała
        if (getComputedStyle(moreWrap).maxHeight === "none") {
            moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
            void moreWrap.offsetHeight; // wymuś reflow
        }
        moreWrap.style.maxHeight = "0px";
        btn.innerHTML = 'Pokaż więcej';
        btn.setAttribute("aria-expanded", "false");
        btn.classList.remove("is-open");
        }
    };

    btn.addEventListener("click", () => {
        // przy otwieraniu z 0 -> potrzebny reflow jeśli zaraz wcześniej było 0
        if (!open) {
        moreWrap.style.maxHeight = "0px";
        void moreWrap.offsetHeight;
        }
        setOpen(!open);
    });

    // 5) Reakcja na zmianę rozmiaru: przelicz wysokość tylko, gdy otwarte
    window.addEventListener("resize", () => {
        if (open) {
        // jeśli zdejmęliśmy limit (none), najpierw tymczasowo go przywróć
        if (getComputedStyle(moreWrap).maxHeight === "none") {
            moreWrap.style.maxHeight = moreWrap.scrollHeight + "px";
            // po następnym reflow znów możesz zdjąć limit, ale to już zrobi transitionend
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
    // 0) Szukajka (jeśli chcesz, żeby była w tym pliku)
    if (searchInput) {
      const debounced = (fn, ms = 200) => {
        let t; return (...a) => { clearTimeout(t); t = setTimeout(() => fn(...a), ms); };
      };
      searchInput.addEventListener("input", debounced(() => {
        state.q = String(searchInput.value || "");
        window.Exhibitors.reapplyAndReset && window.Exhibitors.reapplyAndReset();
      }, 200));
    }

    // 1) Wyróżnienia: najwięksi / najnowsi (PHP: name="featured", name="newest")
    const featured = filtersRoot.querySelector('input.exhibitor-catalog__checkbox-input[name="featured"]');
    const newest   = filtersRoot.querySelector('input.exhibitor-catalog__checkbox-input[name="newest"]');

    if (featured) {
      // stan startowy
      state.onlyBig = !!featured.checked;
      featured.addEventListener("change", (e) => {
        state.onlyBig = !!e.target.checked;
        window.Exhibitors.reapplyAndReset && window.Exhibitors.reapplyAndReset();
      });
    }
    if (newest) {
      state.onlyNew = !!newest.checked;
      newest.addEventListener("change", (e) => {
        state.onlyNew = !!e.target.checked;
        window.Exhibitors.reapplyAndReset && window.Exhibitors.reapplyAndReset();
      });
    }

    // 2) Hale (PHP: name="hall[]", value="<nazwa hali>")
    const hallInputs = filtersRoot.querySelectorAll('input.exhibitor-catalog__checkbox-input[name="hall[]"]');
    // inicjalizacja z DOM
    state.halls.clear();
    hallInputs.forEach(inp => {
      const v = lower(inp.value || "");
      if (inp.checked && v) state.halls.add(v);
    });
    // nasłuch
    filtersRoot.addEventListener("change", (e) => {
      const input = e.target.closest('input.exhibitor-catalog__checkbox-input[name="hall[]"]');
      if (!input) return;
      const v = lower(input.value || "");
      if (!v) return;
      if (input.checked) state.halls.add(v); else state.halls.delete(v);
      window.Exhibitors.reapplyAndReset && window.Exhibitors.reapplyAndReset();
    });

    // 3) Sektory (PHP: name="sector[]", value="<nazwa sektora>")
    const tagInputs = filtersRoot.querySelectorAll('input.exhibitor-catalog__checkbox-input[name="sector[]"]');
    // inicjalizacja z DOM
    state.tags.clear();
    tagInputs.forEach(inp => {
      const v = lower(inp.value || "");
      if (inp.checked && v) state.tags.add(v);
    });
    // nasłuch
    filtersRoot.addEventListener("change", (e) => {
      const input = e.target.closest('input.exhibitor-catalog__checkbox-input[name="sector[]"]');
      if (!input) return;
      const v = lower(input.value || "");
      if (!v) return;
      if (input.checked) state.tags.add(v); else state.tags.delete(v);
      window.Exhibitors.reapplyAndReset && window.Exhibitors.reapplyAndReset();
    });

    // 3a) Tagi produktów (PHP: name="products_tag[]", value="<tag>")
    // upewnij się, że stan istnieje
    if (!state.productTags) state.productTags = new Set();

    const prodTagInputs = filtersRoot.querySelectorAll(
      'input.exhibitor-catalog__checkbox-input[name="products_tag[]"]'
    );

    // inicjalizacja z DOM
    state.productTags.clear();
    prodTagInputs.forEach(inp => {
      const v = lower(inp.value || "");
      if (inp.checked && v) state.productTags.add(v);
    });

    // nasłuch zmian
    filtersRoot.addEventListener("change", (e) => {
      const input = e.target.closest(
        'input.exhibitor-catalog__checkbox-input[name="products_tag[]"]'
      );
      if (!input) return;
      const v = lower(input.value || "");
      if (!v) return;
      if (input.checked) state.productTags.add(v);
      else state.productTags.delete(v);
      window.Exhibitors.reapplyAndReset && window.Exhibitors.reapplyAndReset();
    });

    // 4) Collapsible tylko wizualnie (bez (de)mount elementów)
    const groups = filtersRoot.querySelectorAll(".exhibitor-catalog__category-group");
    groups.forEach(g => makeGroupCollapsible(g, 3));
  }

  // eksport API (inne moduły wywołują reapplyAndReset i zarządzają renderem)
  window.Exhibitors.bindExistingFilters = bindExistingFilters;
  window.Exhibitors.makeGroupCollapsible = makeGroupCollapsible;

  // auto-init
  bindExistingFilters();
});
