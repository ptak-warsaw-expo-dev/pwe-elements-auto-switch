(function () {
    'use strict';

    const settings = window.PWEEmailValidatorSettings || {};
    const corrections = settings.corrections || {};
    const providers = Array.isArray(settings.providers) ? settings.providers : [];
    const messages = settings.messages || {};

    function language() {
        const lang = (document.documentElement.lang || 'en').slice(0, 2).toLowerCase();
        return messages[lang] ? lang : 'en';
    }

    function message(key) {
        return (messages[language()] && messages[language()][key]) ||
            (messages.en && messages.en[key]) || '';
    }

    function wrapper(input) {
        return input.closest('.gfield') || input.parentElement;
    }

    function removeSuggestion(input) {
        const parent = wrapper(input);
        const box = parent && parent.querySelector('.pwe-email-suggestion');
        if (box) box.remove();
        input.removeAttribute('aria-describedby');
        input.dataset.pweEmailSuggestion = '';
    }

    function splitEmail(value) {
        const at = value.lastIndexOf('@');
        if (at <= 0) return null;
        return {
            local: value.slice(0, at),
            domain: value.slice(at + 1).toLowerCase()
        };
    }

    function levenshtein(a, b) {
        const rows = b.length + 1;
        const cols = a.length + 1;
        const matrix = Array.from({ length: rows }, (_, i) => [i]);
        for (let j = 0; j < cols; j++) matrix[0][j] = j;

        for (let i = 1; i < rows; i++) {
            for (let j = 1; j < cols; j++) {
                matrix[i][j] = b[i - 1] === a[j - 1]
                    ? matrix[i - 1][j - 1]
                    : Math.min(matrix[i - 1][j - 1] + 1, matrix[i][j - 1] + 1, matrix[i - 1][j] + 1);
            }
        }
        return matrix[rows - 1][cols - 1];
    }

    function safeFuzzySuggestion(domain) {
        // Never guess for incomplete or syntactically suspicious TLDs.
        const labels = domain.split('.');
        if (labels.length < 2 || labels.some(label => !label)) return null;

        const tld = labels[labels.length - 1];
        if (tld.length < 2) return null;

        // Only compare a two-label public provider domain. Company domains and
        // subdomains are deliberately left alone.
        if (labels.length !== 2) return null;

        let best = null;
        let bestDistance = Infinity;
        for (const provider of providers) {
            if (provider.split('.').length !== 2) continue;
            const distance = levenshtein(domain, provider);
            const maxDistance = domain.length <= 7 ? 1 : 2;
            if (distance <= maxDistance && distance < bestDistance) {
                best = provider;
                bestDistance = distance;
            }
        }
        return best;
    }

    function getSuggestion(value) {
        const parts = splitEmail(value.trim());
        if (!parts || !parts.domain) return null;

        const exact = corrections[parts.domain];
        if (exact && exact !== parts.domain) {
            return parts.local + '@' + exact;
        }

        const fuzzy = safeFuzzySuggestion(parts.domain);
        return fuzzy && fuzzy !== parts.domain ? parts.local + '@' + fuzzy : null;
    }

    function showSuggestion(input, suggestedEmail) {
        const parent = wrapper(input);
        if (!parent) return;
        removeSuggestion(input);

        const box = document.createElement('div');
        const id = 'pwe-email-suggestion-' + Math.random().toString(36).slice(2);
        box.id = id;
        box.className = 'pwe-email-suggestion';
        box.setAttribute('role', 'status');

        const template = message('suggestion');
        const parts = template.split('%s');
        box.append(document.createTextNode(parts[0] || ''));

        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'pwe-email-suggestion__button';
        button.textContent = suggestedEmail;
        button.addEventListener('click', function () {
            input.value = suggestedEmail;
            removeSuggestion(input);
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            input.focus();
        });

        box.append(button, document.createTextNode(parts.slice(1).join('%s') || ''));
        input.insertAdjacentElement('afterend', box);
        input.setAttribute('aria-describedby', id);
        input.dataset.pweEmailSuggestion = suggestedEmail;
    }

    function check(input) {
        const suggestion = getSuggestion(input.value);
        if (suggestion) showSuggestion(input, suggestion);
        else removeSuggestion(input);
    }

    function initialize(root) {
        const scope = root || document;
        scope.querySelectorAll('.pwe-email-validate input[type="email"], input[type="email"].pwe-email-validate').forEach(function (input) {
            if (input.dataset.pweEmailReady === '1') return;
            input.dataset.pweEmailReady = '1';
            input.addEventListener('blur', function () { check(input); });
            input.addEventListener('input', function () { removeSuggestion(input); });
        });
    }

    function start() { initialize(document); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
    else start();

    document.addEventListener('gform/post_render', function (event) {
        const formId = event.detail && event.detail.formId;
        initialize(formId ? document.getElementById('gform_' + formId) : document);
    });

    if (window.jQuery) {
        window.jQuery(document).on('gform_post_render', function (event, formId) {
            initialize(document.getElementById('gform_' + formId) || document);
        });
    }
})();
