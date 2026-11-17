(function () {
    'use strict';
    if (window.pweVisitorImportLoaded) return;
    window.pweVisitorImportLoaded = true;

    const MAX_FILE_BYTES = 5 * 1024 * 1024;
    const MAX_JSON_BYTES = 8 * 1024 * 1024;
    const TIMEOUT_MS = 30000;
    let activeModal = null;
    const messages = {
        pl: {
            reading: 'Odczytywanie i sprawdzanie pliku…', ready: 'Plik poprawny. Sprawdź podgląd i kliknij „Wyślij dane”.',
            sending: 'Wysyłanie danych…', sent: 'Zaproszenia wysłane prawidłowo',
            invalid: 'Plik zawiera błędy. Popraw go i wybierz ponownie. Dane nie zostały wysłane.',
            errors: 'Liczba błędów: {n}.', moreErrors: 'Pokazano pierwsze {n} błędów.', row: 'Wiersz {n}', file: 'Plik',
            full_name: 'Imię i nazwisko', inviting_company: 'Firma zapraszająca', email: 'Adres e-mail',
            preview: 'Do wysłania: {n} osób. Podgląd: pierwsze {shown} wierszy.',
            encoding: 'Kodowanie CSV: {encoding}.', sheet: 'Arkusz: {sheet}.',
            empty: 'Wybrany plik jest pusty.', extension: 'Wybierz plik z rozszerzeniem .csv lub .xlsx.',
            size: 'Plik przekracza limit 5 MB.', fileName: 'Nazwa pliku jest zbyt długa lub zawiera niedozwolone znaki. Zmień nazwę i spróbuj ponownie.',
            unsupported: 'Ta przeglądarka nie obsługuje importu plików. Użyj aktualnej wersji Chrome, Edge, Firefox lub Safari.',
            readFailed: 'Nie udało się odczytać pliku. Zapisz go ponownie jako CSV lub XLSX i spróbuj jeszcze raz.',
            workerFailed: 'Nie udało się uruchomić odczytu pliku. Odśwież stronę; jeśli problem wróci, skontaktuj się z administratorem.',
            timeoutRead: 'Odczyt pliku trwał zbyt długo. Podziel listę na mniejsze pliki i spróbuj ponownie.',
            jsonSize: 'Dane po zamianie na JSON przekraczają limit 8 MB. Podziel listę na mniejsze pliki.',
            endpoint: 'Nieprawidłowa konfiguracja endpointu. Adres musi prowadzić do tej samej witryny.',
            rejected: 'Serwer odrzucił dane (HTTP {status}).',
            noReceipt: 'Nie udało się potwierdzić odbioru danych. Przed ponowną wysyłką sprawdź wynik po stronie serwera.',
            unexpected: 'Serwer nie zwrócił poprawnego potwierdzenia JSON. Przed ponowną wysyłką sprawdź wynik po stronie serwera.',
            cleared: 'Wybierz plik CSV lub XLSX.', noGuests: 'Plik nie zawiera żadnych osób do wysłania.'
        },
        en: {
            reading: 'Reading and validating the file…', ready: 'The file is valid. Review the preview and click “Send data”.',
            sending: 'Sending data…', sent: 'Invitations sent successfully',
            invalid: 'The file contains errors. Correct it and select it again. No data has been sent.',
            errors: 'Number of errors: {n}.', moreErrors: 'Showing the first {n} errors.', row: 'Row {n}', file: 'File',
            full_name: 'Full name', inviting_company: 'Inviting company', email: 'Email',
            preview: 'Guests to send: {n}. Preview: first {shown} rows.',
            encoding: 'CSV encoding: {encoding}.', sheet: 'Worksheet: {sheet}.',
            empty: 'The selected file is empty.', extension: 'Select a .csv or .xlsx file.',
            size: 'The file exceeds the 5 MB limit.', fileName: 'The file name is too long or contains unsupported characters. Rename it and try again.',
            unsupported: 'This browser does not support file import. Use an up-to-date Chrome, Edge, Firefox or Safari.',
            readFailed: 'The file could not be read. Save it again as CSV or XLSX and try again.',
            workerFailed: 'The file reader could not start. Refresh the page; if the problem persists, contact the administrator.',
            timeoutRead: 'Reading the file took too long. Split the list into smaller files and try again.',
            jsonSize: 'The JSON data exceeds the 8 MB limit. Split the list into smaller files.',
            endpoint: 'Invalid endpoint configuration. The URL must point to this same website.',
            rejected: 'The server rejected the data (HTTP {status}).',
            noReceipt: 'Receipt could not be confirmed. Check the result on the server before sending the data again.',
            unexpected: 'The server did not return a valid JSON confirmation. Check the result on the server before sending again.',
            cleared: 'Select a CSV or XLSX file.', noGuests: 'The file does not contain any guests to send.'
        }
    };

    function init(root) {
        if (root.dataset.importInitialized) return;
        const panel = root.querySelector('[data-import-panel]');
        const toggle = root.querySelector('[data-import-toggle]');
        const backdrop = root.querySelector('[data-import-backdrop]');
        if (!panel || !toggle || !backdrop) return;
        root.dataset.importInitialized = '1';
        const lang = root.dataset.language === 'en' ? 'en' : 'pl';
        const guestLang = root.dataset.guestLanguage === 'en' ? 'en' : 'pl';   // język gościa w GF: wersja strony (pl albo en, wszystko poza pl = en)
        const t = (key, args = {}) => (messages[lang][key] || key).replace(/\{(\w+)\}/g, (_, name) => String(args[name] ?? ''));
        const el = name => panel.querySelector('[data-import-' + name + ']') || root.querySelector('[data-import-' + name + ']');
        const input = el('file'), send = el('send'), clear = el('clear');
        const close = el('close'), title = el('title');
        const status = el('status'), errors = el('errors'), preview = el('preview'), summary = el('summary'), rows = el('rows');
        let payload = null, body = '', readingWorker = null, workerTimer = null, generation = 0, sending = false;
        let modalOpen = false, nativeModal = false, backdropPress = false, scrollStyles = [], backgroundState = [];

        // Keep the original DOM nodes, including the file input and its selected file.
        // Moving the dialog out of the generator avoids theme overflow/transform clipping.
        document.body.appendChild(backdrop);
        document.body.appendChild(panel);

        function focus(element) {
            if (element && element.isConnected) element.focus({preventScroll: true});
        }
        function lockBackground() {
            scrollStyles = [document.documentElement, document.body].map(element => ({
                element, value: element.style.getPropertyValue('overflow'), priority: element.style.getPropertyPriority('overflow')
            }));
            scrollStyles.forEach(({element}) => element.style.setProperty('overflow', 'hidden', 'important'));
            backgroundState = Array.from(document.body.children)
                .filter(element => element !== panel && element !== backdrop && !['SCRIPT', 'STYLE', 'LINK'].includes(element.tagName))
                .map(element => ({element, inert: element.getAttribute('inert'), ariaHidden: element.getAttribute('aria-hidden')}));
            backgroundState.forEach(({element}) => {
                element.setAttribute('inert', '');
                element.setAttribute('aria-hidden', 'true');
            });
        }
        function unlockBackground() {
            backgroundState.forEach(({element, inert, ariaHidden}) => {
                if (inert === null) element.removeAttribute('inert');
                else element.setAttribute('inert', inert);
                if (ariaHidden === null) element.removeAttribute('aria-hidden');
                else element.setAttribute('aria-hidden', ariaHidden);
            });
            scrollStyles.forEach(({element, value, priority}) => {
                if (value) element.style.setProperty('overflow', value, priority);
                else element.style.removeProperty('overflow');
            });
            backgroundState = [];
            scrollStyles = [];
        }
        function closeModal(restoreFocus = true) {
            if (!modalOpen) return;
            modalOpen = false;
            if (nativeModal && panel.open) panel.close();
            panel.removeAttribute('open');
            panel.removeAttribute('data-fallback-open');
            panel.hidden = true;
            backdrop.hidden = true;
            backdropPress = false;
            toggle.setAttribute('aria-expanded', 'false');
            unlockBackground();
            if (activeModal && activeModal.panel === panel) activeModal = null;
            document.removeEventListener('keydown', modalKeydown, true);
            document.removeEventListener('focusin', containFocus, true);
            if (restoreFocus) focus(toggle);
            // Closing only hides the dialog: selected data and any in-flight request stay intact.
        }
        function openModal() {
            if (modalOpen) return;
            if (activeModal) activeModal.close(false);
            panel.hidden = false;
            nativeModal = false;
            if (typeof panel.showModal === 'function' && typeof panel.close === 'function') {
                try { panel.showModal(); nativeModal = true; } catch (_) { /* Use the same modal as a fallback. */ }
            }
            if (!nativeModal) {
                panel.setAttribute('open', '');
                panel.setAttribute('data-fallback-open', '');
                backdrop.hidden = false;
            }
            modalOpen = true;
            activeModal = {panel, close: closeModal};
            toggle.setAttribute('aria-expanded', 'true');
            focus(title || close || panel);
            lockBackground();
            document.addEventListener('keydown', modalKeydown, true);
            document.addEventListener('focusin', containFocus, true);
        }
        function focusableElements() {
            return Array.from(panel.querySelectorAll('a[href], button, input, select, textarea, [tabindex]')).filter(element => {
                const style = window.getComputedStyle(element);
                return element.tabIndex >= 0 && !element.matches(':disabled') && !element.closest('[hidden], [inert]') && style.display !== 'none' && style.visibility !== 'hidden';
            });
        }
        function modalKeydown(event) {
            if (!modalOpen) return;
            if (event.key === 'Escape') {
                event.preventDefault();
                event.stopPropagation();
                closeModal();
                return;
            }
            if (event.key !== 'Tab') return;
            const items = focusableElements(), first = items[0], last = items[items.length - 1];
            const current = document.activeElement;
            if (!items.length) { event.preventDefault(); focus(title || panel); }
            else if (!items.includes(current)) { event.preventDefault(); focus(event.shiftKey ? last : first); }
            else if (event.shiftKey && current === first) { event.preventDefault(); focus(last); }
            else if (!event.shiftKey && current === last) { event.preventDefault(); focus(first); }
        }
        function containFocus(event) {
            if (modalOpen && !panel.contains(event.target)) focus(title || close || panel);
        }
        function isBackdropClick(event) {
            if (event.target !== panel) return false;
            const rect = panel.getBoundingClientRect();
            return event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom;
        }

        function setStatus(message, kind = '') {
            status.textContent = message;
            status.dataset.kind = kind;
        }
        function stopReader() {
            if (readingWorker) readingWorker.terminate();
            readingWorker = null;
            window.clearTimeout(workerTimer);
            workerTimer = null;
        }
        function reset() {
            generation++;
            stopReader();
            payload = null;
            body = '';
            send.disabled = true;
            preview.hidden = true;
            rows.replaceChildren();
            summary.textContent = '';
            errors.replaceChildren();
            errors.hidden = true;
            panel.removeAttribute('aria-busy');
        }
        function showErrors(items, count) {
            errors.replaceChildren();
            const heading = document.createElement('p');
            heading.textContent = t('errors', {n: count});
            errors.appendChild(heading);
            const list = document.createElement('ol');
            items.slice(0, 50).forEach(item => {
                const li = document.createElement('li');
                const label = item.row > 0 ? t('row', {n: item.row}) : t('file');
                const field = ['full_name', 'inviting_company', 'email'].includes(item.field) ? ' — ' + t(item.field) : '';
                li.textContent = label + field + ': ' + String(item.message || '');
                list.appendChild(li);
            });
            errors.appendChild(list);
            if (count > Math.min(items.length, 50)) {
                const note = document.createElement('p');
                note.textContent = t('moreErrors', {n: Math.min(items.length, 50)});
                errors.appendChild(note);
            }
            errors.hidden = false;
        }
        function showPreview(result, file) {
            if (result.error_count || (result.errors && result.errors.length)) {
                showErrors(result.errors || [], result.error_count || result.errors.length);
                setStatus(t('invalid'), 'error');
                return;
            }
            if (!Array.isArray(result.guests) || !result.guests.length) throw new Error(t('noGuests'));
            const candidate = {schema_version: 1, source: 'exhibitor-visitor-generator', file_name: file.name, language: guestLang, guests: result.guests};
            const encoded = JSON.stringify(candidate);
            if (new Blob([encoded]).size > MAX_JSON_BYTES) throw new Error(t('jsonSize'));
            payload = candidate;
            body = encoded;
            const fragment = document.createDocumentFragment();
            result.guests.slice(0, 10).forEach(guest => {
                const tr = document.createElement('tr');
                ['row', 'full_name', 'inviting_company', 'email'].forEach(key => {
                    const td = document.createElement('td');
                    td.textContent = String(guest[key]);
                    tr.appendChild(td);
                });
                fragment.appendChild(tr);
            });
            rows.replaceChildren(fragment);
            summary.textContent = t('preview', {n: result.guests.length, shown: Math.min(result.guests.length, 10)});
            if (result.encoding && /\.csv$/i.test(file.name)) summary.textContent += ' ' + t('encoding', {encoding: result.encoding});
            if (result.sheet_name) summary.textContent += ' ' + t('sheet', {sheet: result.sheet_name});
            preview.hidden = false;
            send.disabled = false;
            setStatus(t('ready'), 'success');
        }

        toggle.addEventListener('click', openModal);
        close.addEventListener('click', () => closeModal());
        backdrop.addEventListener('click', () => closeModal());
        panel.addEventListener('cancel', event => { event.preventDefault(); closeModal(); });
        panel.addEventListener('close', () => { if (!panel.open) closeModal(); });
        panel.addEventListener('pointerdown', event => { backdropPress = isBackdropClick(event); });
        panel.addEventListener('click', event => {
            if (backdropPress && isBackdropClick(event)) closeModal();
            backdropPress = false;
        });
        clear.addEventListener('click', () => {
            if (sending) return;
            reset();
            input.value = '';
            clear.hidden = true;
            setStatus(t('cleared'));
            input.focus();
        });
        input.addEventListener('change', async () => {
            if (sending) return;
            reset();
            const current = generation, file = input.files && input.files[0];
            clear.hidden = !file;
            if (!file) { setStatus(t('cleared')); return; }
            try {
                const extension = (file.name.match(/\.([^.]+)$/) || [])[1]?.toLowerCase();
                if (!['csv', 'xlsx'].includes(extension)) throw new Error(t('extension'));
                if (!file.size) throw new Error(t('empty'));
                if (file.size > MAX_FILE_BYTES) throw new Error(t('size'));
                // WY\u0141\u0104CZONE (nazw\u0119 pliku czy\u015bci serwer): if (Array.from(file.name).length > 255 || /[\x00-\x1f\x7f-\x9f\u2028\u2029<>\\/]/.test(file.name)) throw new Error(t('fileName'));
                if (!window.Worker || !file.arrayBuffer || !window.fetch || !window.AbortController) throw new Error(t('unsupported'));
                setStatus(t('reading'));
                panel.setAttribute('aria-busy', 'true');
                const buffer = await file.arrayBuffer();
                if (current !== generation) return;
                const workerUrl = new URL(root.dataset.workerUrl, window.location.href);
                if (workerUrl.origin !== window.location.origin) throw new Error(t('workerFailed'));
                readingWorker = new Worker(workerUrl.href);
                workerTimer = window.setTimeout(() => {
                    if (current !== generation) return;
                    stopReader();
                    panel.removeAttribute('aria-busy');
                    setStatus(t('timeoutRead'), 'error');
                }, TIMEOUT_MS);
                readingWorker.onmessage = event => {
                    if (current !== generation) return;
                    stopReader();
                    panel.removeAttribute('aria-busy');
                    try {
                        if (!event.data || !event.data.ok) throw new Error(event.data?.message || t('readFailed'));
                        showPreview(event.data.result, file);
                    } catch (error) { setStatus(error.message || t('readFailed'), 'error'); }
                };
                readingWorker.onerror = event => {
                    event.preventDefault();
                    if (current !== generation) return;
                    stopReader();
                    panel.removeAttribute('aria-busy');
                    setStatus(t('workerFailed'), 'error');
                };
                readingWorker.postMessage({buffer, extension, language: lang}, [buffer]);
            } catch (error) {
                if (current !== generation) return;
                stopReader();
                panel.removeAttribute('aria-busy');
                setStatus(error.message || t('readFailed'), 'error');
            }
        });
        send.addEventListener('click', async () => {
            if (!payload || !body || send.disabled || sending) return;
            let endpoint;
            try {
                if (!root.dataset.endpoint || !root.dataset.endpoint.trim()) throw new Error();
                endpoint = new URL(root.dataset.endpoint, window.location.href);
                if (!/^https?:$/.test(endpoint.protocol) || endpoint.origin !== window.location.origin || endpoint.username || endpoint.password || endpoint.hash) throw new Error();
            } catch (_) { setStatus(t('endpoint'), 'error'); return; }
            sending = true;
            send.disabled = true;
            input.disabled = true;
            clear.disabled = true;
            errors.hidden = true;
            panel.setAttribute('aria-busy', 'true');
            setStatus(t('sending'));
            const controller = new AbortController();
            const timeout = window.setTimeout(() => controller.abort(), TIMEOUT_MS);
            try {
                const response = await fetch(endpoint.href, {
                    method: 'POST', credentials: 'same-origin', mode: 'same-origin', redirect: 'error', cache: 'no-store',
                    headers: {'Content-Type': 'application/json; charset=UTF-8', 'Accept': 'application/json', 'X-PWE-Import': '1'},
                    body, signal: controller.signal
                });
                let result = null;
                try { result = await response.json(); } catch (_) { /* A failed/HTML response is not a success. */ }
                if (!response.ok || result?.success !== true) {
                    if (Array.isArray(result?.errors) && result.errors.length) showErrors(result.errors, result.error_count || result.errors.length);
                    setStatus(result?.message ? String(result.message) : response.ok ? t('unexpected') : t('rejected', {status: response.status}), 'error');
                    return;
                }
                if (!Number.isInteger(result.received) || result.received !== payload.guests.length) {
                    setStatus(t('unexpected'), 'error');
                    return;
                }
                reset();
                input.value = '';
                clear.hidden = true;
                setStatus(t('sent'), 'success');
            } catch (_) {
                // Do not retry automatically: a timeout does not prove the server received nothing.
                setStatus(t('noReceipt'), 'error');
            } finally {
                window.clearTimeout(timeout);
                sending = false;
                input.disabled = false;
                clear.disabled = false;
                panel.removeAttribute('aria-busy');
                // Re-selecting a file is required after a send, preventing accidental double clicks.
            }
        });
    }
    function initAll() { document.querySelectorAll('[data-pwe-visitor-import]').forEach(init); }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initAll, {once: true});
    else initAll();
    document.addEventListener('gform/postRender', initAll);
})();
