document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.getElementById('pwe-gf-tool__form-search');
    var rows = Array.prototype.slice.call(document.querySelectorAll('.pwe-gf-tool__form-row'));
    var filterButtons = Array.prototype.slice.call(document.querySelectorAll('.pwe-gf-tool__form-filter'));
    var currentFilter = 'all';

    function getCountsForQuery(query) {
        var counts = { all: 0, active: 0, inactive: 0 };

        rows.forEach(function (row) {
            var text = (row.getAttribute('data-search') || '').toLowerCase();
            var status = (row.getAttribute('data-form-status') || 'active');
            var matchesQuery = query === '' || text.indexOf(query) !== -1;

            if (!matchesQuery) {
                return;
            }

            counts.all += 1;
            if (status === 'active') {
                counts.active += 1;
            } else {
                counts.inactive += 1;
            }
        });

        return counts;
    }

    function renderFilterCounts(query) {
        var counts = getCountsForQuery(query);
        filterButtons.forEach(function (button) {
            var filter = button.getAttribute('data-form-filter') || 'all';
            var label = button.querySelector('span');
            var count = counts[filter] || 0;
            if (label) {
                label.textContent = filter === 'all' ? 'Wszystkie' : (filter === 'active' ? 'Aktywne' : 'Nieaktywne');
            }
            var countEl = button.querySelector('.pwe-gf-tool__form-filter-count');
            if (!countEl) {
                countEl = document.createElement('span');
                countEl.className = 'pwe-gf-tool__form-filter-count';
                button.appendChild(countEl);
            }
            countEl.textContent = count;
        });
    }

    function updateRows() {
        var query = (searchInput ? searchInput.value : '').trim().toLowerCase();
        renderFilterCounts(query);

        rows.forEach(function (row) {
            var text = (row.getAttribute('data-search') || '').toLowerCase();
            var status = (row.getAttribute('data-form-status') || 'active');
            var textMatch = query === '' || text.indexOf(query) !== -1;
            var statusMatch = currentFilter === 'all' || status === currentFilter;
            var match = textMatch && statusMatch;

            row.classList.toggle('is-hidden', !match);
        });
    }

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            currentFilter = button.getAttribute('data-form-filter') || 'all';
            filterButtons.forEach(function (item) {
                item.classList.toggle('is-selected', item === button);
            });
            updateRows();
        });
    });

    rows.forEach(function (row) {
        row.addEventListener('click', function (event) {
            if (event && event.target && (event.target.closest('form') || event.target.closest('button') || event.target.closest('input') || event.target.closest('textarea') || event.target.closest('select') || event.target.closest('label'))) {
                return;
            }

            var isOpen = row.classList.contains('is-open');
            rows.forEach(function (item) {
                item.classList.remove('is-open');
                item.setAttribute('aria-expanded', 'false');
                var panel = item.querySelector('.pwe-gf-tool__form-panel');
                if (panel) {
                    panel.setAttribute('aria-hidden', 'true');
                }
            });

            if (!isOpen) {
                row.classList.add('is-open');
                row.setAttribute('aria-expanded', 'true');
                var panel = row.querySelector('.pwe-gf-tool__form-panel');
                if (panel) {
                    panel.setAttribute('aria-hidden', 'false');
                }
            }
        });

        row.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                row.click();
            }
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            updateRows();
        });
    }

    updateRows();
});
