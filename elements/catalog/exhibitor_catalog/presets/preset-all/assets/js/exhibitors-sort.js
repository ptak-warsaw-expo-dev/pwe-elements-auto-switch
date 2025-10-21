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

    // Szukajka
    if (state.q && state.q.trim()) {
      const q = norm(state.q);
      arr = arr.filter((x) => {
        const name  = norm(x.name || "");
        const desc  = norm(x.description || "");
        const br    = norm(x.brands || "");
        const stand = norm(String(x.standNumber || ""));
        return name.includes(q) || desc.includes(q) || br.includes(q) || stand.includes(q);
      });
    }

    // Filtr hal
    if (state.halls?.size) {
      arr = arr.filter((x) => state.halls.has(lower(x.hallName || "")));
    }

    // Filtr tagów katalogowych (wszystkie zaznaczone)
    if (state.tags?.size) {
      arr = arr.filter((x) => {
        const tags = new Set(
          lower(x.catalogTags || "")
            .split(",")
            .map((s) => s.trim())
            .filter(Boolean)
        );
        for (const t of state.tags) if (!tags.has(t)) return false;
        return true;
      });
    }

    // Filtr tagów produktów (wszystkie zaznaczone)
    if (state.productTags?.size) {
      arr = arr.filter((x) => {
        const set = new Set();
        (Array.isArray(x.products) ? x.products : []).forEach((p) => {
          const raw = p?.tags ?? [];
          const parts = Array.isArray(raw) ? raw.flat() : [raw];
          parts
            .flatMap((s) => String(s || "").split(/(?:\s*,\s*|\s{2,})/u))
            .map((t) => t.trim())
            .filter(Boolean)
            .forEach((t) => set.add(lower(t)));
        });
        for (const t of state.productTags) if (!set.has(t)) return false;
        return true;
      });
    }

    // SORTOWANIE (tylko zmiana kolejności wyników, bez wycinania)
    if (state.onlyBig && state.onlyNew) {
      arr = [...arr].sort((a, b) => {
        const dArea = getAreaSafe(b) - getAreaSafe(a);
        if (dArea) return dArea;
        const dId = getId(b) - getId(a); // nowsze wyżej
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
        return getId(b) - getId(a); // nowsze wyżej przy pełnym remisie
      });
    } else if (state.onlyNew) {
      arr = [...arr].sort((a, b) => getId(b) - getId(a)); // nowsze wyżej
    }

    return arr;
  }

  // Eksport do głównego API – paginacja wywołuje X.applyFilters w reapplyAndReset()
  X.applyFilters = applyFilters;

  // (opcjonalnie) jeżeli chcesz też zbindować przełączniki onlyBig/onlyNew tutaj,
  // upewnij się, że nie dublujesz tego z filters.js. Przykład poniżej jest wyłączony:
  //
  // if (!X.__sortWired) {
  //   X.__sortWired = true;
  //   const $onlyBig = document.querySelector('#onlyBig,[name="onlyBig"],[data-filter="onlyBig"]');
  //   const $onlyNew = document.querySelector('#onlyNew,[name="onlyNew"],[data-filter="onlyNew"]');
  //   if ($onlyBig && !$onlyBig.dataset.wired) {
  //     $onlyBig.dataset.wired = "1";
  //     state.onlyBig = !!($onlyBig.checked || $onlyBig.getAttribute("aria-checked") === "true");
  //     $onlyBig.addEventListener("change", (e) => {
  //       const el = e.currentTarget;
  //       const checked = "checked" in el ? el.checked : el.getAttribute("aria-checked") === "true";
  //       state.onlyBig = !!checked;
  //       X.reapplyAndReset && X.reapplyAndReset();
  //     });
  //   }
  //   if ($onlyNew && !$onlyNew.dataset.wired) {
  //     $onlyNew.dataset.wired = "1";
  //     state.onlyNew = !!($onlyNew.checked || $onlyNew.getAttribute("aria-checked") === "true");
  //     $onlyNew.addEventListener("change", (e) => {
  //       const el = e.currentTarget;
  //       const checked = "checked" in el ? el.checked : el.getAttribute("aria-checked") === "true";
  //       state.onlyNew = !!checked;
  //       X.reapplyAndReset && X.reapplyAndReset();
  //     });
  //   }
  // }
}
