document.addEventListener('DOMContentLoaded', function () {
    const clearBtn = document.querySelector('.exhibitor-catalog__panel-filter-clear');
    if (!clearBtn) return;

    function shouldShowClear() {
        const anyChecked = document.querySelectorAll('input[type="checkbox"]:checked').length > 0;

        const searchInput = document.querySelector('.exhibitor-catalog__search-input');
        const hasSearch = searchInput && searchInput.value.trim().length > 0;

        if (anyChecked || hasSearch) {
            clearBtn.classList.add('is-visible');
        } else {
            clearBtn.classList.remove('is-visible');
        }
    }

    // global listener – działa z AJAX
    document.addEventListener('change', shouldShowClear);
    document.addEventListener('input', shouldShowClear);

    // initial
    shouldShowClear();
});
