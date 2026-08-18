(function () {
    'use strict';

    const rootSelector = '.pwe-registration-visitors';

    function getRoot() {
        return document.querySelector(rootSelector);
    }

    function getFieldInput(root, className) {
        return root ? root.querySelector('.' + className + ' input, .' + className + ' select, .' + className + ' textarea') : null;
    }

    function getEmailInput(root) {
        return root ? root.querySelector('input[type="email"]') : null;
    }

    function getPhoneInput(root) {
        if (!root) return null;
        return root.querySelector(
            '.pwe-phone-validate input[type="tel"], ' +
            '.pwe-phone-validate input[type="text"], ' +
            '.ginput_container_phone input, ' +
            'input[type="tel"]'
        );
    }

    function getLocationPath() {
        const params = new URLSearchParams(window.location.search);
        const registrationParam = params.get('reg');
        const utmSource = params.get('utm_source');

        if (registrationParam) return registrationParam;
        if (utmSource === 'byli') return 'vip';
        if (utmSource === 'premium') return 'platinum';
        if (utmSource === 'platyna') return 'platyna';

        let path = window.location.pathname || '';
        path = path.replace(/^\/en\//, '').replace(/^\/|\/$/g, '');
        return path || 'header';
    }

    function setLocation(root) {
        const locationInput = getFieldInput(root, 'location');
        if (locationInput) {
            locationInput.value = getLocationPath();
            locationInput.dispatchEvent(new Event('change', { bubbles: true }));
        }
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

        setLocation(root);
    }

    function updateCountry(root) {
        if (!root) return;

        const countryInput = getFieldInput(root, 'country');
        const phoneInput = getPhoneInput(root);
        const itiButton = root.querySelector('.iti__selected-country');

        if (!countryInput) return;

        let countryValue = '';

        if (phoneInput && window.intlTelInput && typeof window.intlTelInput.getInstance === 'function') {
            const instance = window.intlTelInput.getInstance(phoneInput);
            if (instance && typeof instance.getSelectedCountryData === 'function') {
                const data = instance.getSelectedCountryData();
                countryValue = data && (data.name || data.iso2) ? (data.name || data.iso2) : '';
            }
        }

        if (!countryValue && itiButton) {
            countryValue = itiButton.getAttribute('title') || itiButton.getAttribute('aria-label') || '';
        }

        if (countryValue) {
            countryInput.value = countryValue;
        }
    }

    function observeCountry(root) {
        const selected = root ? root.querySelector('.iti__selected-country, .iti__selected-flag') : null;
        if (!selected || selected.dataset.pweCountryObserved === '1') return;

        selected.dataset.pweCountryObserved = '1';
        const observer = new MutationObserver(function () {
            updateCountry(root);
        });
        observer.observe(selected, {
            attributes: true,
            attributeFilter: ['title', 'aria-label', 'aria-expanded']
        });
    }

    function persistRegistrationData(root) {
        if (!root) return;

        updateCountry(root);
        setLocation(root);

        const email = getEmailInput(root);
        const phone = getPhoneInput(root);
        const country = getFieldInput(root, 'country');
        const area = getFieldInput(root, 'input-area');
        const lang = (document.documentElement.lang || '').toLowerCase();

        if (email) localStorage.setItem('user_email', email.value || '');
        if (phone) localStorage.setItem('user_tel', phone.value || '');
        if (country) localStorage.setItem('user_country', country.value || '');
        if (area) localStorage.setItem('user_area', area.value || '');
        localStorage.setItem('user_direction', lang.indexOf('pl') === 0 ? 'rejpl' : 'rejen');
    }

    function bindStorage(root) {
        if (!root) return;
        const form = root.querySelector('form');
        if (!form || form.dataset.pweRegistrationBound === '1') return;
        form.dataset.pweRegistrationBound = '1';

        // Capture phase: dane zapisujemy także wtedy, gdy inny skrypt przejmie submit.
        form.addEventListener('submit', function () {
            persistRegistrationData(root);
        }, true);

        const submit = form.querySelector('input[type="submit"], button[type="submit"]');
        if (submit) {
            submit.addEventListener('click', function () {
                persistRegistrationData(root);
            }, true);
        }

        const phone = getPhoneInput(root);
        if (phone && phone.dataset.pweLocalStorageBound !== '1') {
            phone.dataset.pweLocalStorageBound = '1';
            phone.addEventListener('blur', function () {
                localStorage.setItem('user_tel', phone.value || '');
                updateCountry(root);
            });
            phone.addEventListener('countrychange', function () {
                updateCountry(root);
            });
        }
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
        bindStorage(root);
        removeInternalUtmCookie();

        if (root.dataset.pweRegistrationRootBound !== '1') {
            root.dataset.pweRegistrationRootBound = '1';
            root.addEventListener('change', function () {
                updateCountry(root);
                setLocation(root);
            });
            root.addEventListener('input', function () {
                updateCountry(root);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', init);
    document.addEventListener('gform_post_render', init);
    document.addEventListener('gform/post_render', init);
})();
