(function () {
    'use strict';

    function init() {
        const root = document.querySelector('.pwe-registration-exhibitors');
        if (!root) return;

        const patronInput = root.querySelector('.patron input');
        if (patronInput) patronInput.value = root.dataset.fairGroup || '';

        const langInput = root.querySelector('.lang input');
        if (langInput) {
            const pageLang = (document.documentElement.lang || '').trim().toLowerCase();
            langInput.value = pageLang ? pageLang.substring(0, 2) : '';
        }

        const utmInput = root.querySelector('.utm-class input');
        if (utmInput) {
            utmInput.value = new URLSearchParams(window.location.search).get('utm_source') || '';
        }

        const countryInput = root.querySelector('.country input');
        const selectedFlag = root.querySelector('.iti__selected-country');
        if (countryInput && selectedFlag) {
            countryInput.value = selectedFlag.getAttribute('title') || '';
        }
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('gform_post_render', init);
})();
