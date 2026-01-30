(function () {
    const widget = document.querySelector('.pwe-element-auto-switch .catalog-feedback');

    if (!widget) return;

    const toggleBtn = widget.querySelector('.catalog-feedback__toggle');
    const closeBtn = widget.querySelector('.catalog-feedback__close');

    const AUTO_SHOW_DELAY = 120000;
    const CONFIRMATION_CLOSE_DELAY = 10000;

    let confirmationHandled = false;

    /* ===============================
       TOGGLE OPEN / CLOSE
    =============================== */
    const open = () => widget.classList.add('is-open');
    const close = () => widget.classList.remove('is-open');

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        widget.classList.toggle('is-open');
    });

    closeBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        close();
    });

    /* ===============================
       KLIK POZA WIDGET
    =============================== */
    document.addEventListener('click', (e) => {
        if (!widget.contains(e.target)) {
            close();
        }
    });

    /* ===============================
       AUTO WYSUNIĘCIE PO 2 MIN
    =============================== */
    setTimeout(() => {
        if (!confirmationHandled) {
            open();
        }
    }, AUTO_SHOW_DELAY);

    /* ===============================
       RADIO → KLASY is-1 … is-5
    =============================== */
    document.addEventListener('change', (e) => {
        if (!e.target.matches('.pwe-element-auto-switch .catalog-feedback input[type="radio"]')) return;

        widget.classList.remove('is-1', 'is-2', 'is-3', 'is-4', 'is-5');
        widget.classList.add('is-' + e.target.value);
    });

    /* ===============================
       CONFIRMATION (Gravity Forms)
    =============================== */
    const observer = new MutationObserver(() => {
        if (confirmationHandled || !widget.querySelector('.gform_confirmation_wrapper')) return;

        confirmationHandled = true;
        open();

        setTimeout(close, CONFIRMATION_CLOSE_DELAY);
        observer.disconnect();
    });

    observer.observe(widget, {
        childList: true,
        subtree: true,
    });
})();

document.addEventListener('DOMContentLoaded', function () {
    if (typeof EX_FEEDBACK_CONFIG === 'undefined') return;

    const sourceField = document.querySelector('input[name="input_3"]');
    const widthField = document.querySelector('input[name="input_4"]');
    const versionField = document.querySelector('input[name="input_5"]');
    

    if (sourceField) {
        sourceField.value = EX_FEEDBACK_CONFIG.source;
    }

    if (versionField) {
        versionField.value = `vue v${EX_FEEDBACK_CONFIG.version}`;
    }


    if (!widthField) return;

    function getDeviceWidth() {
        return Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
    }

    const width = getDeviceWidth();
    let device = 'desktop';
    if (width <= 762) device = 'mobile';
    else if (width <= 1024) device = 'tablet';

    widthField.value = device + ' ' + width + 'px';

});
