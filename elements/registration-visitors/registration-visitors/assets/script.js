(function () {
    'use strict';

    const rootSelector = '.pwe-registration-visitors';

    function getRoot() {
        return document.querySelector(rootSelector);
    }

    function getFieldInput(root, className) {
        return root ? root.querySelector('.' + className + ' input, .' + className + ' select, .' + className + ' textarea') : null;
    }

    function setHiddenFields(root) {
        if (!root) return;

        const patronInput = getFieldInput(root, 'patron');
        if (patronInput) patronInput.value = root.dataset.fairGroup || '';

        const langInput = getFieldInput(root, 'lang');
        if (langInput) {
            const pageLang = (document.documentElement.lang || '').trim().toLowerCase();
            langInput.value = pageLang ? pageLang.substring(0, 2) : '';
        }

        const utmInput = getFieldInput(root, 'utm-class');
        if (utmInput) {
            const params = new URLSearchParams(window.location.search);
            utmInput.value = params.get('utm_source') || '';
        }
    }

    function updateCountry(root) {
        if (!root) return;
        const countryInput = getFieldInput(root, 'country');
        const selectedFlag = root.querySelector('.iti__selected-flag');
        if (countryInput && selectedFlag) {
            countryInput.value = selectedFlag.getAttribute('title') || '';
        }
    }

    function observeCountry(root) {
        const selectedFlag = root ? root.querySelector('.iti__selected-flag') : null;
        if (!selectedFlag) return;

        const observer = new MutationObserver(function () {
            updateCountry(root);
        });
        observer.observe(selectedFlag, {
            attributes: true,
            attributeFilter: ['title', 'aria-expanded']
        });
    }

    function storeVisitorData(root) {
        if (!root) return;
        const form = root.querySelector('form');
        if (!form || form.dataset.pweRegistrationBound === '1') return;
        form.dataset.pweRegistrationBound = '1';

        form.addEventListener('submit', function () {
            const email = root.querySelector('.ginput_container_email input');
            const phone = root.querySelector('.ginput_container_phone input');
            const country = getFieldInput(root, 'country');
            const area = getFieldInput(root, 'input-area');
            const lang = (document.documentElement.lang || '').toLowerCase();

            if (email) localStorage.setItem('user_email', email.value || '');
            if (phone) localStorage.setItem('user_tel', phone.value || '');
            if (country) localStorage.setItem('user_country', country.value || '');
            if (area) localStorage.setItem('user_area', area.value || '');
            localStorage.setItem('user_direction', lang.indexOf('pl') === 0 ? 'rejpl' : 'rejen');
        });
    }

    function removeInternalUtmCookie() {
        const cookie = document.cookie.split('; ').find(function (row) {
            return row.indexOf('utm_params=') === 0;
        });
        if (!cookie) return;

        const value = decodeURIComponent(cookie.substring('utm_params='.length));
        if (/utm_source=(byli|premium|platyna)(?:&|$)/i.test(value)) {
            document.cookie = 'utm_params=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Lax';
        }
    }

    function init() {
        const root = getRoot();
        if (!root) return;
        setHiddenFields(root);
        updateCountry(root);
        observeCountry(root);
        storeVisitorData(root);
        removeInternalUtmCookie();

        root.addEventListener('change', function () { updateCountry(root); });
        root.addEventListener('input', function () { updateCountry(root); });
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('gform_post_render', init);
})();
