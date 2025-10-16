document.addEventListener("DOMContentLoaded", () => {
  if (!window.Exhibitors) return;

  const API = window.Exhibitors;
  const { state, lower, norm, getId } = API;

  // Źródło danych z kolejnością z PHP
  const getSource = () => API.dataAll || API.data || [];

  // Bezpieczny parser metrażu -> liczba (m²)
  function getAreaSafe(x) {
    // Jeżeli masz własny getArea i na pewno zwraca liczbę, możesz go tu użyć:
    const raw =
      (typeof API.getArea === "function" ? API.getArea(x) : null) ??
      x.standArea ??
      x.stand_area ??
      x.area ??
      x.area_sqm ??
      0;

    if (typeof raw === "number") return isFinite(raw) ? raw : 0;
    const m = String(raw).replace(",", ".").match(/-?\d+(\.\d+)?/);
    return m ? parseFloat(m[0]) : 0;
  }

  function applyFilters(dataAll) {
    // NIE sortujemy bazowo — zachowujemy kolejność z PHP.
    let arr = dataAll;

    // Szukajka
    if (state.q && state.q.trim()) {
      const q = norm(state.q);
      arr = arr.filter((x) => {
        const name = norm(x.name || "");
        const desc = norm(x.description || "");
        const br = norm(x.brands || "");
        const stand = norm(String(x.standNumber || ""));
        return (
          name.includes(q) || desc.includes(q) || br.includes(q) || stand.includes(q)
        );
      });
    }

    // Filtr hal
    if (state.halls?.size) {
      arr = arr.filter((x) => state.halls.has(lower(x.hallName || "")));
    }

    // Filtr tagów (wszystkie zaznaczone)
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

        // 3a) Kategorie produktów (PHP: products_tag[]) — wymagaj wszystkich zaznaczonych
    if (state.productTags?.size) {
      arr = arr.filter((x) => {
        // zbierz WSZYSTKIE tagi produktów z danego wystawcy
        const set = new Set();

        (Array.isArray(x.products) ? x.products : []).forEach((p) => {
          const raw = p?.tags ?? [];
          const parts = Array.isArray(raw) ? raw.flat() : [raw];

          parts
            .flatMap(s => String(s || "").split(/(?:\s*,\s*|\s{2,})/u)) // przecinek LUB ≥2 spacje
            .map(t => t.trim())
            .filter(Boolean)
            .forEach(t => set.add(lower(t)));
        });

        // sprawdź, czy ma wszystkie zaznaczone tagi produktów
        for (const t of state.productTags) if (!set.has(t)) return false;
        return true;
      });
    }

    // SORTOWANIE SPECJALNE — tylko zmiana kolejności, nic nie wycinamy
    if (state.onlyBig && state.onlyNew) {
      // Najpierw największe, przy remisie nowsze wyżej, potem alfabetycznie
      arr = [...arr].sort((a, b) => {
        const d = getAreaSafe(b) - getAreaSafe(a);
        if (d) return d;
        const id = getId(b) - getId(a);
        if (id) return id;
        const na = (API.getName?.(a) ?? a.name ?? "").toString();
        const nb = (API.getName?.(b) ?? b.name ?? "").toString();
        return na.localeCompare(nb);
      });
    } else if (state.onlyBig) {
      // Tylko „Najwięksi” — sort po metrażu malejąco + tiebreakery
      arr = [...arr].sort((a, b) => {
        const d = getAreaSafe(b) - getAreaSafe(a);
        if (d) return d;
        // jeśli metraż równy lub brak danych — pokaż zmianę porządku deterministycznie:
        const na = (API.getName?.(a) ?? a.name ?? "").toString();
        const nb = (API.getName?.(b) ?? b.name ?? "").toString();
        const byName = na.localeCompare(nb);
        if (byName) return byName;
        return getId(b) - getId(a); // ostatni remis po ID (nowsze wyżej)
      });
    } else if (state.onlyNew) {
      // Tylko „Nowi” — malejąco po ID
      arr = [...arr].sort((a, b) => getId(b) - getId(a));
    }

    return arr;
  }

  // Domyślna funkcja renderująca (jeśli projekt nie ma własnej)
  function renderFallback(arr) {
    const list =
      document.querySelector("[data-exhibitors-list]") ||
      document.querySelector(".exhibitors-list");
    if (!list) return;

    // Minimalny przykład — dostosuj do swojego templatu
    list.innerHTML = arr
      .map(
        (x) => `
        <li class="exhibitor" data-id="${getId(x)}" data-area="${getAreaSafe(x)}">
          <span class="exhibitor__name">${(x.name ?? "Bez nazwy")}</span>
          <span class="exhibitor__meta">${getAreaSafe(x)} m²</span>
        </li>`
      )
      .join("");
  }

  function updateView() {
    const arr = applyFilters(getSource());
    if (typeof API.render === "function") {
      API.render(arr); // użyj istniejącego renderera projektu
    } else {
      renderFallback(arr); // awaryjnie, gdy brak renderera
    }
  }

  // === PODPIĘCIE CHECKBOXÓW ===
  const $onlyBig =
    document.querySelector('#onlyBig') ||
    document.querySelector('[name="onlyBig"]') ||
    document.querySelector('[data-filter="onlyBig"]');

  if ($onlyBig) {
    // ustaw stan startowy
    state.onlyBig = !!($onlyBig.checked || $onlyBig.getAttribute("aria-checked") === "true");
    $onlyBig.addEventListener("change", (e) => {
      // wspiera input[type=checkbox] i aria-checked
      const el = e.currentTarget;
      const checked = "checked" in el ? el.checked : el.getAttribute("aria-checked") === "true";
      state.onlyBig = !!checked;
      updateView();
    });
  }

  const $onlyNew =
    document.querySelector('#onlyNew') ||
    document.querySelector('[name="onlyNew"]') ||
    document.querySelector('[data-filter="onlyNew"]');

  if ($onlyNew) {
    state.onlyNew = !!($onlyNew.checked || $onlyNew.getAttribute("aria-checked") === "true");
    $onlyNew.addEventListener("change", (e) => {
      const el = e.currentTarget;
      const checked = "checked" in el ? el.checked : el.getAttribute("aria-checked") === "true";
      state.onlyNew = !!checked;
      updateView();
    });
  }

  // Eksport + pierwszy render
  API.applyFilters = applyFilters;
  API.updateView = updateView;
  updateView();
});
