(function () {
    'use strict';

    const FIELD_SELECTOR = '.pwe-phone-validate';
    const INPUT_SELECTOR = 'input[type="tel"], input[type="text"]';
    const instances = new WeakMap();
    const messages = window.pwePhoneValidatorConfig || {
        invalidMessage: 'Wpisz prawidłowy numer telefonu.',
        requiredMessage: 'Numer telefonu jest wymagany.',
        libraryErrorMessage: 'Nie udało się załadować walidatora telefonu.'
    };

    function getInput(field) {
        if (field.matches && field.matches(INPUT_SELECTOR)) {
            return field;
        }
        return field.querySelector ? field.querySelector(INPUT_SELECTOR) : null;
    }

    function getDefaultCountry(field, input) {
        const configured = input.dataset.defaultCountry || field.dataset.defaultCountry;
        if (configured && /^[a-z]{2}$/i.test(configured)) {
            return configured.toLowerCase();
        }

        const language = (document.documentElement.lang || navigator.language || 'pl')
            .slice(0, 2)
            .toLowerCase();

        const countries = {
            pl: 'pl', en: 'gb', de: 'de', fr: 'fr', es: 'es', it: 'it',
            pt: 'pt', nl: 'nl', cs: 'cz', sk: 'sk', uk: 'ua', ru: 'ru',
            lt: 'lt', lv: 'lv', et: 'ee', sv: 'se', no: 'no', da: 'dk',
            fi: 'fi', ro: 'ro', hu: 'hu', bg: 'bg', hr: 'hr', sl: 'si',
            el: 'gr', tr: 'tr', ar: 'sa', he: 'il', ja: 'jp', ko: 'kr',
            zh: 'cn'
        };

        return countries[language] || 'pl';
    }

    function getMessageElement(field, input) {
        let message = field.querySelector && field.querySelector('.pwe-phone-validation-message');
        if (!message) {
            message = document.createElement('div');
            message.className = 'pwe-phone-validation-message';
            message.hidden = true;
            message.setAttribute('aria-live', 'polite');

            const container = input.closest('.ginput_container') || input.parentElement;
            container.insertAdjacentElement('afterend', message);
        }
        return message;
    }

    function isRequired(field, input) {
        return input.required ||
            input.getAttribute('aria-required') === 'true' ||
            field.classList.contains('gfield_contains_required');
    }

    function clearState(field, input) {
        const message = getMessageElement(field, input);
        input.classList.remove('pwe-phone-invalid', 'pwe-phone-valid');
        input.removeAttribute('aria-invalid');
        message.textContent = '';
        message.hidden = true;
    }

    function setInvalid(field, input, text) {
        const message = getMessageElement(field, input);
        input.classList.add('pwe-phone-invalid');
        input.classList.remove('pwe-phone-valid');
        input.setAttribute('aria-invalid', 'true');
        message.textContent = text;
        message.hidden = false;
    }

    function setValid(field, input) {
        const message = getMessageElement(field, input);
        input.classList.remove('pwe-phone-invalid');
        input.classList.add('pwe-phone-valid');
        input.setAttribute('aria-invalid', 'false');
        message.textContent = '';
        message.hidden = true;
    }

    function getFieldId(field) {
        const match = (field.id || '').match(/(?:^|_)field_?\d*_(\d+)$/) || (field.id || '').match(/_(\d+)$/);
        if (match) {
            return match[1];
        }

        const inputMatch = (getInput(field)?.name || '').match(/^input_(\d+)/);
        return inputMatch ? inputMatch[1] : '';
    }

    function getValidityMarker(field, input) {
        const fieldId = getFieldId(field);
        if (!fieldId) {
            return null;
        }

        const form = input.closest('form');
        if (!form) {
            return null;
        }

        const name = 'pwe_phone_valid_' + fieldId;
        let marker = form.querySelector('input[type="hidden"][name="' + name + '"]');
        if (!marker) {
            marker = document.createElement('input');
            marker.type = 'hidden';
            marker.name = name;
            marker.value = '0';
            form.appendChild(marker);
        }
        return marker;
    }

    function setValidityMarker(field, input, valid) {
        const marker = getValidityMarker(field, input);
        if (marker) {
            marker.value = valid ? '1' : '0';
        }
    }

    function getMaxNationalDigits(iti) {
        const data = iti.getSelectedCountryData ? iti.getSelectedCountryData() : null;
        const dialCodeDigits = data && data.dialCode ? String(data.dialCode).replace(/\D/g, '').length : 0;

        // E.164 permits at most 15 digits including the country calling code.
        return Math.max(4, 15 - dialCodeDigits);
    }

    function enforceDigitLimit(input, iti) {
        const maxDigits = getMaxNationalDigits(iti);
        const value = input.value;
        let digitCount = 0;
        let limited = '';
        let changed = false;

        for (const char of value) {
            if (/\d/.test(char)) {
                if (digitCount >= maxDigits) {
                    changed = true;
                    continue;
                }
                digitCount += 1;
            }
            limited += char;
        }

        input.dataset.pwePhoneMaxDigits = String(maxDigits);
        if (changed) {
            input.value = limited;
        }
    }

    function validate(field, input, iti, formatValue) {
        const value = input.value.trim();

        if (!value) {
            if (isRequired(field, input)) {
                setValidityMarker(field, input, false);
                setInvalid(field, input, messages.requiredMessage);
                return false;
            }
            setValidityMarker(field, input, true);
            clearState(field, input);
            return true;
        }

        const valid = typeof iti.isValidNumberPrecise === 'function'
            ? iti.isValidNumberPrecise()
            : iti.isValidNumber();

        if (!valid) {
            setValidityMarker(field, input, false);
            setInvalid(field, input, messages.invalidMessage);
            return false;
        }

        if (formatValue) {
            const number = iti.getNumber();
            if (number) {
                input.value = number;
            }
        }

        setValidityMarker(field, input, true);
        setValid(field, input);
        return true;
    }

    function syncInputOffset(input) {
        const itiWrapper = input.closest('.iti');
        const countryContainer = itiWrapper && itiWrapper.querySelector('.iti__country-container');

        if (!countryContainer) {
            return;
        }

        const offset = Math.ceil(countryContainer.getBoundingClientRect().width) + 12;
        const isRtl = window.getComputedStyle(input).direction === 'rtl';

        if (isRtl) {
            input.style.setProperty('padding-right', offset + 'px', 'important');
        } else {
            input.style.setProperty('padding-left', offset + 'px', 'important');
        }
    }

    function scheduleInputOffset(input) {
        window.requestAnimationFrame(function () {
            syncInputOffset(input);
            window.requestAnimationFrame(function () {
                syncInputOffset(input);
            });
        });
    }

    function initialiseField(field) {
        const input = getInput(field);
        if (!input || instances.has(input)) {
            return;
        }

        if (typeof window.intlTelInput !== 'function') {
            console.error('[PWE Phone Validator] intl-tel-input nie został załadowany. Sprawdź blokowanie CDN i konsolę przeglądarki.');
            field.dataset.pwePhoneError = 'library-not-loaded';
            return;
        }

        input.setAttribute('inputmode', 'tel');
        input.setAttribute('autocomplete', 'tel');

        const iti = window.intlTelInput(input, {
            initialCountry: getDefaultCountry(field, input),
            separateDialCode: true,
            nationalMode: true,
            autoPlaceholder: 'aggressive',
            formatAsYouType: true,
            countrySearch: true,
            fixDropdownWidth: false,
            useFullscreenPopup: false
        });

        instances.set(input, iti);
        input.dataset.pwePhoneInitialized = 'true';
        setValidityMarker(field, input, false);
        enforceDigitLimit(input, iti);
        clearState(field, input);
        scheduleInputOffset(input);

        const countryContainer = input.closest('.iti') && input.closest('.iti').querySelector('.iti__country-container');
        if (countryContainer && typeof ResizeObserver === 'function') {
            const resizeObserver = new ResizeObserver(function () {
                syncInputOffset(input);
            });
            resizeObserver.observe(countryContainer);
        }

        input.addEventListener('blur', function () {
            validate(field, input, iti, true);
        });

        input.addEventListener('input', function () {
            enforceDigitLimit(input, iti);
            setValidityMarker(field, input, false);
            if (input.classList.contains('pwe-phone-invalid')) {
                validate(field, input, iti, false);
            }
        });

        input.addEventListener('countrychange', function () {
            enforceDigitLimit(input, iti);
            setValidityMarker(field, input, false);
            scheduleInputOffset(input);
            if (input.value.trim()) {
                validate(field, input, iti, false);
            }
        });
    }

    function initialise(root) {
        const scope = root && root.querySelectorAll ? root : document;

        if (scope.matches && scope.matches(FIELD_SELECTOR)) {
            initialiseField(scope);
        }

        scope.querySelectorAll(FIELD_SELECTOR).forEach(initialiseField);
    }

    function validateForm(form) {
        let firstInvalid = null;

        form.querySelectorAll(FIELD_SELECTOR).forEach(function (field) {
            const input = getInput(field);
            const iti = input ? instances.get(input) : null;
            if (input && iti && !validate(field, input, iti, true) && !firstInvalid) {
                firstInvalid = input;
            }
        });

        if (!firstInvalid) {
            return true;
        }

        firstInvalid.focus();
        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }

    let gravityFormsFilterRegistered = false;

    function registerGravityFormsSubmissionFilter() {
        if (gravityFormsFilterRegistered || !window.gform || !gform.utils || typeof gform.utils.addAsyncFilter !== 'function') {
            return;
        }

        gform.utils.addAsyncFilter('gform/submission/pre_submission', async function (data) {
            const form = data && data.form;
            if (!(form instanceof HTMLFormElement) || !form.querySelector(FIELD_SELECTOR)) {
                return data;
            }

            if (!validateForm(form)) {
                data.abort = true;
            }

            return data;
        });

        gravityFormsFilterRegistered = true;
    }

    function boot() {
        initialise(document);
        registerGravityFormsSubmissionFilter();
        document.addEventListener('gform/theme/scripts_loaded', registerGravityFormsSubmissionFilter);

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (form instanceof HTMLFormElement && form.querySelector(FIELD_SELECTOR) && !validateForm(form)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);

        document.addEventListener('gform/post_render', function (event) {
            initialise(event.target || document);
        });

        if (window.jQuery) {
            window.jQuery(document).on('gform_post_render', function (_event, formId) {
                initialise(document.getElementById('gform_' + formId) || document);
            });
        }

        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                mutation.addedNodes.forEach(function (node) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        initialise(node);
                    }
                });
            });
        });

        observer.observe(document.body, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot, { once: true });
    } else {
        boot();
    }
})();
