// ======== //ANCHOR MOBILE FILTER MENU – wersja dostosowana ========

document.addEventListener('DOMContentLoaded', () => {
    const exhibitorCatalog = document.querySelector('#exhibitorCatalog');

    // Panel boczny z filtrami (cały sidebar mobilny)
    const sidebar = document.querySelector('.exhibitor-catalog__sidebar');

    // Przycisk otwierania filtrów
    const filterMenuBtn = document.querySelector('.catalog-mobile-panel__filters-btn');

    // Górny panel wyników
    const stickyPanel = document.querySelector('.catalog-mobile-panel__results');

    // Nagłówek katalogu
    const hero = document.querySelector('.exhibitor-catalog__header');

    // Dolny "pływający" przycisk SZUKAJ
    const bottomSearchBar = document.querySelector('.exhibitor-catalog__panel-filter-search--floating');

    // Minimalny wymóg: przycisk + panel wyników + sidebar
    if (!filterMenuBtn || !stickyPanel || !sidebar) {
        // Możesz na chwilę odkomentować poniższe, żeby zobaczyć co nie istnieje:
        // console.warn("Brakuje któregoś elementu:", { filterMenuBtn, stickyPanel, sidebar });
        return;
    }

    // --- Ikony ---
    const originalIcon = filterMenuBtn.innerHTML;
    const closeIcon = `
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
             xmlns="http://www.w3.org/2000/svg">
            <path d="m12 13.703-5.962 5.962q-.334.336-.852.335-.516 0-.851-.335T4 18.814q0-.517.335-.852L10.297 12 4.335 6.038Q4 5.704 4 5.186q0-.516.335-.851T5.186 4q.518 0 .852.335L12 10.297l5.962-5.962Q18.296 4 18.814 4q.516 0 .851.335.336.335.335.851 0 .518-.335.852L13.703 12l5.962 5.962q.336.334.335.852 0 .516-.335.851-.335.336-.851.335-.517 0-.852-.335z"
                  fill="#2a2b2d"/>
        </svg>
    `;

    // --- Sticky logika jak wcześniej ---
    let forceStuck = false;
    let heroHeight = 0;
    const extraOffset = 150;

    function recalcHeroHeight() {
        if (!hero) return;
        heroHeight = hero.getBoundingClientRect().height;
    }

    function updateStickyPanel() {
        if (forceStuck) {
            stickyPanel.classList.add('is_stucked');
            return;
        }

        if (window.scrollY >= heroHeight + extraOffset) {
            stickyPanel.classList.add('is_stucked');
        } else {
            stickyPanel.classList.remove('is_stucked');
        }
    }

    // inicjalizacja
    setTimeout(() => {
        recalcHeroHeight();
        updateStickyPanel();
    }, 150);

    window.addEventListener('resize', () =>
        setTimeout(() => {
            recalcHeroHeight();
            updateStickyPanel();
        }, 150)
    );

    window.addEventListener('scroll', updateStickyPanel);

    window.addEventListener('catalog:updated', () => {
        setTimeout(() => {
            recalcHeroHeight();
            updateStickyPanel();
        }, 150);
    });

    // ========================================================
    //                OTWIERANIE / ZAMYKANIE FILTRÓW
    // ========================================================

    function openFilters() {
        forceStuck = true;
        stickyPanel.classList.add('is_stucked');

        // klasa globalna dla CSS
        exhibitorCatalog.classList.add('filters-open');

        // dodatkowo klasa na sidebar (na wszelki wypadek)
        sidebar.classList.add('is-open');

        filterMenuBtn.classList.add('is-open');
        filterMenuBtn.innerHTML = closeIcon;
    }

    function closeFilters() {
        exhibitorCatalog.classList.remove('filters-open');
        sidebar.classList.remove('is-open');

        filterMenuBtn.classList.remove('is-open');
        filterMenuBtn.innerHTML = originalIcon;

        forceStuck = false;
        updateStickyPanel();

        if (bottomSearchBar) {
            bottomSearchBar.classList.remove('search-active');
        }
    }

    window.closeFilters = closeFilters;

    // kliknięcie w przycisk FILTRY
    filterMenuBtn.addEventListener('click', () => {
        if (filterMenuBtn.classList.contains('is-open')) {
            closeFilters();
        } else {
            openFilters();
        }
    });
});

document.addEventListener('click', function (e) {
    const header = e.target.closest('.catalog-mobile-filters__header');
    if (!header) return;

    const group = header.closest('.catalog-mobile-filters__group.is-collapsible');
    if (!group) return;

    e.preventDefault();
    group.classList.toggle('is-open');
});
