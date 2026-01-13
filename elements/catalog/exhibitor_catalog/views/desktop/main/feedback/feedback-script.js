(function () {
    const widget = document.querySelector('#exhibitorCatalog .catalog-feedback');
    if (!widget) return;

    const AUTO_SHOW_DELAY = 120000; // 2 min
    const CONFIRMATION_CLOSE_DELAY = 10000; // 10 s

    let autoTimer = null;
    let isInteracting = false;
    let confirmationHandled = false;

    /* ===============================
       AUTO WYSUNIĘCIE PO 2 MIN
    =============================== */
    autoTimer = setTimeout(() => {
        if (!isInteracting && !confirmationHandled) {
            widget.classList.add('is-open');
        }
    }, AUTO_SHOW_DELAY);

    /* ===============================
       INTERAKCJA UŻYTKOWNIKA
    =============================== */
    widget.addEventListener('mouseenter', () => {
        if (confirmationHandled) return;
        isInteracting = true;
        widget.classList.add('is-open');
    });

    widget.addEventListener('mouseleave', () => {
        if (confirmationHandled) return;
        isInteracting = false;
        widget.classList.remove('is-open');
    });

    widget.addEventListener('focusin', () => {
        if (confirmationHandled) return;
        isInteracting = true;
        widget.classList.add('is-open');
    });

    widget.addEventListener('focusout', () => {
        if (confirmationHandled) return;
        isInteracting = false;
        widget.classList.remove('is-open');
    });

    /* ===============================
       RADIO → KLASY is-1 … is-5
    =============================== */
    document.addEventListener('change', function (e) {
        if (!e.target.matches('#exhibitorCatalog .catalog-feedback input[type="radio"]')) return;

        widget.classList.remove('is-1', 'is-2', 'is-3', 'is-4', 'is-5');
        widget.classList.add('is-' + e.target.value);
    });

    /* ===============================
       OBSŁUGA CONFIRMATION (GF)
    =============================== */
    const observer = new MutationObserver(() => {
        if (confirmationHandled || !widget.querySelector('.gform_confirmation_wrapper')) {
            return;
        }

        confirmationHandled = true;

        // utrzymujemy widget otwarty
        widget.classList.add('is-open');

        setTimeout(() => {
            widget.classList.remove('is-open');
        }, CONFIRMATION_CLOSE_DELAY);

        // kończymy obserwację – tylko raz
        observer.disconnect();
    });

    observer.observe(widget, {
        childList: true,
        subtree: true,
    });
})();

(function () {
    function isPolish() {
        const lang = document.documentElement.lang || '';
        return lang.toLowerCase().startsWith('pl');
    }

    function replaceCatalogFeedbackTexts() {
        if (isPolish()) return;

        // Label
        document.querySelectorAll('.catalog-feedback .gform-field-label').forEach((el) => {
            if (el.textContent.trim() === 'Jeśli masz dodatkowe uwagi, daj nam znać') {
                el.textContent = 'If you have any additional comments, please let us know.';
            }
        });

        // Submit button
        document.querySelectorAll('.catalog-feedback input.gform_button').forEach((btn) => {
            if (btn.value === 'Wyślij') {
                btn.value = 'Send';
            }
        });
    }

    function replaceConfirmation() {
        if (isPolish()) return;

        document.querySelectorAll('.catalog-feedback .gform_confirmation_message').forEach((conf) => {
            if (conf.textContent.trim() === 'Dziękujemy za przesłanie opinii.') {
                conf.textContent = 'Thank you for submitting your feedback.';
            }
        });
    }

    // DOM ready
    document.addEventListener('DOMContentLoaded', replaceCatalogFeedbackTexts);

    // AJAX submit confirmation
    jQuery(document).on('gform_confirmation_loaded', function (event, formId) {
        replaceConfirmation();
    });
})();

document.addEventListener('DOMContentLoaded', function () {
    const sourceField = document.querySelector('input[name="input_3"]');
    if (!sourceField) return;

    if (window.location.pathname.includes('/katalog') || window.location.pathname.includes('/exhibitors-catalog')) {
        sourceField.value = 'catalog';
    } else if (window.location.pathname.includes('/sklep') || window.location.pathname.includes('/store')) {
        sourceField.value = 'shop';
    }
});
