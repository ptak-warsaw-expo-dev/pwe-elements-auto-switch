/* ============================================================
   ===   EXHIBITOR CATALOG – NOWY SYSTEM FILTRÓW + AJAX     ===
   ===   1) Liczniki dynamiczne w JS                        ===
   ===   2) Tylko jeden AJAX przy „Pokaż wyniki”            ===
   ===   3) CSV parametry w URL                             ===
   ============================================================ */

/* -----------------------------------
   1. ZMIENNE GLOBALNE
----------------------------------- */
let ecFilterData = null; // dane z PHP (JSON)
let ecRoot = null; // #exhibitorCatalog
let ecLastAppliedFilters = '';

let ecAjaxCounter = 0;

/* -----------------------------------
   2. INICJALIZACJA
----------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    ecRoot = document.getElementById('exhibitorCatalog');

    ecInitFilterData();
    ecInitFilterEvents();
    ecRecalculateFilterCounts();

    // baza odniesienia = to co jest na starcie
    ecLastAppliedFilters = ecMakeSelectionKey();

    // ustaw stan przycisku na podstawie porównania
    ecUpdateButtonState();
});

/* -----------------------------------
   3. Wczytanie danych z PHP
----------------------------------- */
function ecNormalizeProductTagsJs(tags) {
    if (!Array.isArray(tags)) return [];
    return tags
        .map((t) => String(t).trim().toLowerCase())
        .map((t) => t.normalize('NFKD').replace(/[\u0300-\u036F]/g, '')) // usuń diakrytyki
        .filter((t) => t.length > 0);
}

function ecInitFilterData() {
    const script = document.getElementById('exhibitorFiltersData');
    if (!script) {
        ecFilterData = [];
        return;
    }

    try {
        const json = JSON.parse(script.textContent || '{}');
        const items = Array.isArray(json.items) ? json.items : [];
        ecFilterData = items;
    } catch (e) {
        console.error('Błąd JSON exhibitorFiltersData:', e);
        ecFilterData = [];
    }
}

/* -----------------------------------
   4. Pobranie aktualnych zaznaczonych filtrów
----------------------------------- */
function ecGetCurrentSelection() {
    const form = document.querySelector('.exhibitor-catalog__filters-form');

    const selection = {
        type: [],
        hall: [],
        sector: [],
        category: [],
    };

    if (!form) return selection;

    form.querySelectorAll('input[type="checkbox"]:checked').forEach((cb) => {
        const key = cb.name;
        const val = cb.value;

        if (!selection[key]) selection[key] = [];
        selection[key].push(val);
    });

    return selection;
}

/* -----------------------------------
   5. Logika dopasowania itemu do filtrów (AND)
----------------------------------- */

function ecItemMatchesForCount(item, sel) {
    // TYPY — OR (jeśli nic nie zaznaczono → wszystkie pasują)
    if (sel.type.length && !sel.type.includes(item.type)) return false;

    // HALE — OR
    if (sel.hall.length && !sel.hall.includes(item.hall)) return false;

    // SEKTORY — OR
    if (sel.sector.length) {
        if (!item.sectors || !item.sectors.length) return false;

        let ok = false;
        for (const s of sel.sector) {
            if (item.sectors.includes(s)) {
                ok = true;
                break;
            }
        }
        if (!ok) return false;
    }

    // KATEGORIE — OR
    if (sel.category.length) {
        if (!item.categories || !item.categories.length) return false;

        let ok = false;
        for (const c of sel.category) {
            if (item.categories.includes(c)) {
                ok = true;
                break;
            }
        }
        if (!ok) return false;
    }

    return true;
}

/* -----------------------------------
   NOWE: Dopasowanie itemu do WYNIKÓW LISTY (typ ma znaczenie)
----------------------------------- */
function ecItemMatchesForResults(item, sel) {
    if (sel.type.length && !sel.type.includes(item.type)) return false;

    if (sel.hall.length && !sel.hall.includes(item.hall)) return false;

    if (sel.sector.length) {
        if (!item.sectors || !item.sectors.length) return false;
        let ok = false;
        for (const s of sel.sector) {
            if (item.sectors.includes(s)) {
                ok = true;
                break;
            }
        }
        if (!ok) return false;
    }

    if (sel.category.length) {
        if (!item.categories || !item.categories.length) return false;
        let ok = false;
        for (const c of sel.category) {
            if (item.categories.includes(c)) {
                ok = true;
                break;
            }
        }
        if (!ok) return false;
    }

    return true;
}

/* -----------------------------------
   6. PRZELICZANIE LICZB W FILTRACH
----------------------------------- */

// uniwersalna funkcja dopasowania — zgodna z PHP
function ecItemMatches(item, sel) {
    // TYPE
    if (sel.type.length && !sel.type.includes(item.type)) {
        return false;
    }

    // HALL
    if (sel.hall.length && !sel.hall.includes(item.hall)) {
        return false;
    }

    // SEKTORY
    if (sel.sector.length) {
        const sectors = item.sectors || [];
        if (!sectors.length || !sectors.some((s) => sel.sector.includes(s))) {
            return false;
        }
    }

    // KATEGORIE
    if (sel.category.length) {
        const cats = item.categories || [];
        if (!cats.length || !cats.some((c) => sel.category.includes(c))) {
            return false;
        }
    }

    return true;
}

function ecRecalculateFilterCounts() {
    if (!ecFilterData) return;

    const sel = ecGetCurrentSelection();

    function makeSelectionWithout(group) {
        return {
            type: group === 'type' ? [] : [...sel.type],
            hall: group === 'hall' ? [] : [...sel.hall],
            sector: group === 'sector' ? [] : [...sel.sector],
            category: group === 'category' ? [] : [...sel.category],
        };
    }

    function computeCounts(group) {
        const selWithout = makeSelectionWithout(group);

        const filtered = ecFilterData.filter((item) => ecItemMatches(item, selWithout));

        const counts = {};

        filtered.forEach((item) => {
            if (group === 'type') {
                counts[item.type] = (counts[item.type] || 0) + 1;
                return;
            }

            if (group === 'hall') {
                if (!item.hall) return;
                counts[item.hall] = (counts[item.hall] || 0) + 1;
                return;
            }

            if (group === 'sector') {
                (item.sectors || []).forEach((s) => {
                    if (!s) return;
                    counts[s] = (counts[s] || 0) + 1;
                });
                return;
            }

            if (group === 'category') {
                (item.categories || []).forEach((c) => {
                    if (!c) return;
                    counts[c] = (counts[c] || 0) + 1;
                });
                return;
            }
        });

        return counts;
    }

    updateGroup('type', computeCounts('type') || {});
    updateGroup('hall', computeCounts('hall') || {});
    updateGroup('sector', computeCounts('sector') || {});
    updateGroup('category', computeCounts('category') || {});
}

function updateGroup(name, counts) {
    const form = document.querySelector('.exhibitor-catalog__filters-form');
    if (!form) return;

    form.querySelectorAll(`input[name="${name}"]`).forEach((cb) => {
        const label = cb.closest('.exhibitor-catalog__filter-switch');
        const span = label.querySelector('.exhibitor-catalog__filter-label-number');
        const val = cb.value;

        const cnt = counts[val] || 0;

        // liczba przy filtrze
        if (span) span.textContent = `(${cnt})`;

        // --- TYPE: zawsze aktywne ---
        // --- TYPE: wyszarzamy gdy 0 i niezaznaczone (tak samo jak hale) ---
        if (name === 'type') {
            if (cnt === 0 && !cb.checked) {
                label.classList.add('is-disabled');
            } else {
                label.classList.remove('is-disabled');
            }
            label.style.display = '';
            return;
        }

        // --- HALE: nigdy nie chowamy, tylko szarzymy ---
        if (name === 'hall') {
            if (cnt === 0 && !cb.checked) {
                label.classList.add('is-disabled');
            } else {
                label.classList.remove('is-disabled');
            }
            label.style.display = '';
            return;
        }

        // --- SEKTORY/KATEGORIE: chowamy gdy 0 i niezaznaczone ---
        if (cnt === 0 && !cb.checked) {
            label.classList.add('is-disabled');
        } else {
            label.classList.remove('is-disabled');
        }

        label.style.display = '';
    });
}

/* -----------------------------------
   7. INICJALIZACJA ZDARZEŃ FILTRÓW
----------------------------------- */
function ecInitFilterEvents() {
    const form = document.querySelector('.exhibitor-catalog__filters-form');
    const btnShow = document.querySelector('.exhibitor-catalog__panel-filter-search--floating');

    if (!form || !btnShow) return;

    // 🔥 delegacja – działa zawsze, nie duplikuje się
    form.addEventListener(
        'change',
        function (e) {
            if (e.target.matches('input[type="checkbox"]')) {
                ecRecalculateFilterCounts();
                ecUpdateButtonState();
            }
        },
        { passive: true }
    );

    // 🔥 jeden pewny listener – nie duplikuje się
    if (!btnShow.dataset.bound) {
        btnShow.dataset.bound = '1';
        btnShow.addEventListener('click', function (e) {
            e.preventDefault();
            ecRunAjax();
        });
    }
}

/* -----------------------------------
   8. Podświetlenie przycisku „Pokaż wyniki”
----------------------------------- */
function ecMakeSelectionKey() {
    const sel = ecGetCurrentSelection();

    // sortujemy każdą tablicę, aby kolejność nie wpływała na klucz
    const sorted = {
        type: [...sel.type].sort(),
        hall: [...sel.hall].sort(),
        sector: [...sel.sector].sort(),
        category: [...sel.category].sort(),
    };

    return JSON.stringify(sorted);
}

function ecUpdateButtonState() {
    const btn = document.querySelector('.exhibitor-catalog__panel-filter-search--floating');
    if (!btn) return;

    const currentKey = ecMakeSelectionKey();
    const changed = currentKey !== ecLastAppliedFilters;

    // Dodajemy / usuwamy klasę od wysuwania
    btn.classList.toggle('search-active', changed);

    // NIE zmieniamy display — nie jest potrzebny
    // transform / animacja są w CSS

    console.log('current:', currentKey, 'applied:', ecLastAppliedFilters);
}

/* -----------------------------------
   9. Budowanie URL (CSV, reset paginacji) i AJAX
----------------------------------- */
function ecRunAjax() {
    if (typeof window.closeFilters === 'function') {
        window.closeFilters();
    }
    if (!ecRoot) return;

    const form = document.querySelector('.exhibitor-catalog__filters-form');
    const data = new FormData(form);

    const multi = ['type', 'hall', 'sector', 'category', 'brand'];

    const params = new URLSearchParams();

    multi.forEach((key) => {
        const values = data
            .getAll(key)
            .map((v) => v.trim())
            .filter(Boolean);
        if (values.length) {
            params.set(key, values.join(','));
        }
    });

    const url = new URL(window.location.href);
    const search = url.searchParams.get('search');
    const sort = url.searchParams.get('sort');

    if (search) params.set('search', search);
    if (sort) params.set('sort', sort);

    params.delete('exh-page');

    const finalUrl = url.pathname + '?' + params.toString();

    ecAjaxReplaceCatalog(finalUrl);
}

/* -----------------------------------
   10. AJAX – wymiana listy, paginacji, filtrów, JSON
----------------------------------- */
async function ecAjaxReplaceCatalog(url, options = {}) {
    const append = options.append === true;

    ecAjaxCounter++;
    console.log(`AJAX #${ecAjaxCounter}: ${url} (append=${append})`);

    const spinner = document.querySelector('.exhibitor-catalog__spinner');
    if (spinner) spinner.style.display = 'flex';

    const timeStart = performance.now();

    try {
        const res = await fetch(url, { cache: 'no-store' });
        const html = await res.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');

        const itemsNew = doc.querySelector('.exhibitor-catalog__items');
        const pagerNew = doc.querySelector('.exhibitor-catalog__pagination');
        const countNew = doc.querySelector('.exhibitor-catalog__panel-items-count');

        if (!itemsNew) throw new Error('Brak listy w odpowiedzi');

        const itemsOld = ecRoot.querySelector('.exhibitor-catalog__items');
        const pagerOld = ecRoot.querySelector('.exhibitor-catalog__pagination');
        const countOld = ecRoot.querySelector('.exhibitor-catalog__panel-items-count');

        requestAnimationFrame(() => {
            if (append) {
                // --- pobieramy REALNE elementy, które będą doklejone ---
                const appendedElements = [...itemsNew.children];

                // --- doklejamy ---
                appendedElements.forEach((el) => itemsOld.appendChild(el));

                // --- inicjalizacja swiperów tylko na nowych kartach ---
                ecInitExhibitorCardSliders({
                    querySelectorAll: (sel) => appendedElements.flatMap((el) => [...el.querySelectorAll(sel)]),
                });

                if (window.ecUpdateSidebar) window.ecUpdateSidebar();
            } else {
                // standardowy tryb wymiany
                if (itemsOld) itemsOld.replaceWith(itemsNew);

                // inicjalizacja swiperów po pełnej wymianie
                ecInitExhibitorCardSliders(itemsNew);
            }

            // -------------------------------------
            // PAGINACJA
            // -------------------------------------
            if (pagerNew) {
                if (pagerOld) {
                    pagerOld.replaceWith(pagerNew);
                }

                const hasNext = pagerNew.querySelector('a[href*="exh-page"]');

                if (!hasNext) pagerNew.style.display = 'none';
            } else if (pagerOld) {
                pagerOld.remove();
            }

            // licznik wyników
            if (countNew && countOld) {
                countOld.textContent = countNew.textContent;
            }
        });

        // przy append NIE resetujemy filtrów ani stanu UI
        if (!append) {
            ecLastAppliedFilters = ecMakeSelectionKey();
            ecUpdateButtonState();
            ecRecalculateFilterCounts();

            if (window.ecUpdateSidebar) window.ecUpdateSidebar();
        }

        history.pushState({}, '', url);
    } catch (err) {
        console.error('Błąd AJAX:', err);
        window.location.href = url;
    }

    if (spinner) spinner.style.display = 'none';

    const timeEnd = performance.now();
    console.log('AJAX + DOM:', (timeEnd - timeStart).toFixed(1), 'ms');
}

/* -----------------------------------
   11. Obsługa wstecz / history back
----------------------------------- */
window.addEventListener('popstate', () => {
    ecRunAjax(window.location.href);
});

/* -----------------------------------
   12. Obsługa custom selecta (otwieranie / wybór)
----------------------------------- */
document.addEventListener('click', function (e) {
    const select = e.target.closest('.catalog-custom-select');

    // klik poza selectem — zamykamy wszystkie
    if (!select) {
        document.querySelectorAll('.catalog-custom-select.open').forEach((s) => s.classList.remove('open'));
        return;
    }

    const selected = e.target.closest('.catalog-custom-select');
    const option = e.target.closest('.catalog-custom-select__option');

    // kliknięcie w wybrany → otwórz/zamknij
    if (selected) {
        select.classList.toggle('open');
        return;
    }

    // kliknięcie opcji
    if (option) {
        const value = option.dataset.value;
        if (!value) return;

        // oznacz aktywną
        select.querySelectorAll('.catalog-custom-select__option').forEach((o) => o.classList.remove('active'));
        option.classList.add('active');

        // aktualizacja atrybutu
        select.dataset.current = value;
        select.classList.remove('open');

        // event zmiany
        select.dispatchEvent(new CustomEvent('change', { detail: { value } }));
    }
});

/* -----------------------------------
   13. GLOBALNY listener sortowania – pewny, działa ZAWSZE
----------------------------------- */
document.addEventListener('click', function (e) {
    const option = e.target.closest('.catalog-custom-select__option');
    if (!option) return;

    const select = option.closest('.catalog-custom-select');
    if (!select) return;

    const value = option.dataset.value;
    if (!value) return;

    // update ikon/klasy
    select.querySelectorAll('.catalog-custom-select__option').forEach((o) => o.classList.remove('active'));
    option.classList.add('active');

    // zapis bieżącej wartości
    select.dataset.current = value;

    // zamknij dropdown
    select.classList.remove('open');

    // ——— AJAX SORTOWANIA ———
    const url = new URL(window.location.href);
    url.searchParams.set('sort', value);
    url.searchParams.delete('exh-page');

    ecAjaxReplaceCatalog(url.pathname + '?' + url.searchParams.toString());
});

/* -----------------------------------
   14. AJAX paginacja + loader
----------------------------------- */
document.addEventListener('click', function (e) {
    const link = e.target.closest('.exhibitor-catalog__pagination a[href]');
    if (!link) return;

    e.preventDefault();

    const url = link.href;

    const spinner = document.querySelector('.exhibitor-catalog__spinner');
    if (spinner) spinner.style.display = 'flex';

    // 🔥 nowy tryb: append = true (doklejanie, nie wymiana)
    ecAjaxReplaceCatalog(url, { append: true });
});

/* -----------------------------------
   15. Sticky Sidebar — wersja stabilna, bez skoków
----------------------------------- */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.exhibitor-catalog__sidebar');
    const container = document.querySelector('.exhibitor-catalog__content');

    if (!sidebar || !container) return;

    const offsetTop = 160;

    // rodzic absolutny
    if (getComputedStyle(container).position === 'static') {
        container.style.position = 'relative';
    }

    // --- udostępniamy funkcję globalnie ---
    window.ecUpdateSidebar = function () {
        const scrollY = window.scrollY;

        const containerTop = container.getBoundingClientRect().top + scrollY;
        const containerH = container.offsetHeight;
        const sidebarH = sidebar.offsetHeight;

        const fixedStart = containerTop - offsetTop;
        const fixedEnd = containerTop + containerH - sidebarH - offsetTop;

        if (scrollY < fixedStart) {
            sidebar.style.position = 'absolute';
            sidebar.style.top = offsetTop + 'px';
        } else if (scrollY > fixedEnd) {
            sidebar.style.position = 'absolute';
            sidebar.style.top = containerH - sidebarH + 'px';
        } else {
            sidebar.style.position = 'fixed';
            sidebar.style.top = offsetTop + 'px';
        }
    };

    window.addEventListener('scroll', window.ecUpdateSidebar, {
        passive: true,
    });
    window.addEventListener('resize', window.ecUpdateSidebar);

    window.ecUpdateSidebar();
});

window.addEventListener('load', () => {
    const interval = setInterval(() => {
        const aside = document.querySelector('aside#usercentrics-cmp-ui');
        const contact = document.querySelector('.exhibitor-single-mobile__contact');

        if (!aside) return;

        const shadow = aside.shadowRoot;
        if (!shadow) return;

        // Funkcja, która ustawia pozycję przycisku i odkrywa aside
        const adjustButton = () => {
            const btn = shadow.querySelector('#uc-main-dialog.privacyButton');
            if (!btn) return;

            if (window.innerWidth < 960 && contact) {
                // MOBILE
                btn.style.left = '6px';
                btn.style.right = 'unset';
                btn.style.bottom = '70px';
                btn.style.width = '38px';
                btn.style.height = '38px';
            } else {
                btn.style.left = 'unset';
                btn.style.right = '17px';
                btn.style.bottom = '10px';
                btn.style.width = '44px';
                btn.style.height = '44px';
            }

            aside.style.display = 'block';
            aside.style.opacity = '1';
            aside.style.visibility = 'visible';
        };

        // Uruchamiamy na start
        adjustButton();

        // Obserwujemy shadowRoot, żeby reagować na ponowne renderowanie przycisku
        const observer = new MutationObserver(() => {
            adjustButton();
        });

        observer.observe(shadow, { childList: true, subtree: true });

        clearInterval(interval);
    }, 500);
});
