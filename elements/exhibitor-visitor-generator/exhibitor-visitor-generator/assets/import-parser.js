/* PWE visitor import: local parsing and validation, no network requests. */
(function (root, factory) {
    'use strict';
    var api = factory();
    if (typeof module === 'object' && module.exports) module.exports = api;
    else root.PWEVisitorImportParser = api;
}(typeof self !== 'undefined' ? self : globalThis, function () {
    'use strict';
    const MAX_ROWS = 20000, MAX_FILE_BYTES = 5 * 1024 * 1024, MAX_ERRORS = 50;
    const MESSAGES = {
        pl: {
            empty: 'Plik jest pusty.', extension: 'Wybierz plik CSV lub XLSX.', size: 'Plik może mieć maksymalnie 5 MB.',
            header: 'Pierwszy niepusty wiersz musi zawierać dokładnie 3 różne nagłówki: Imię i nazwisko, Firma zapraszająca, Adres e-mail.',
            columns: 'Wiersz zawiera dane poza trzema wymaganymi kolumnami.', required: 'Pole jest wymagane.', text: 'Wpisz wartość tekstową, bez dat, liczb i formuł.',
            controls: 'Usuń znaki sterujące, podziały wierszy oraz znaki < i >.', long: 'Pole może mieć maksymalnie {0} znaków.',
            name: 'Podaj imię i nazwisko: co najmniej dwa człony zawierające litery. Dozwolone są też spacje, kropki, apostrofy i łączniki.',
            company: 'Nazwa firmy nie może zaczynać się od =, + ani @.', email: 'Podaj poprawny adres e-mail, np. jan.kowalski@example.com.',
            duplicate: 'Adres e-mail powtarza się w pliku. Pierwsze wystąpienie: wiersz {0}.',
            rows: 'Plik może zawierać maksymalnie 20 000 gości. Podziel go na mniejsze pliki.', noGuests: 'Plik zawiera nagłówki, ale nie zawiera żadnego gościa.',
            quotes: 'Nieprawidłowe cudzysłowy w CSV, wiersz {0}. Zapisz plik ponownie jako CSV.', encoding: 'Nie można odczytać kodowania CSV. Zapisz plik jako CSV UTF-8.',
            delimiter: 'Nie można rozpoznać separatora CSV. Użyj średnika, przecinka lub tabulatora oraz nagłówków z wzoru.',
            hugeField: 'CSV zawiera zbyt długie pole w wierszu {0}.', tooManyColumns: 'Plik zawiera zbyt wiele kolumn. Pozostaw trzy kolumny z wzoru.',
            binaryCSV: 'Ten plik nie jest tekstowym plikiem CSV. Wybierz oryginalny plik XLSX albo zapisz plik jako CSV UTF-8.',
            zip: 'Plik nie jest poprawnym XLSX. Zapisz arkusz ponownie w formacie XLSX.', zipLimit: 'Arkusz jest zbyt duży po rozpakowaniu lub zawiera zbyt wiele elementów. Skopiuj dane do nowego pliku z wzoru.',
            encrypted: 'Pliki szyfrowane i chronione hasłem nie są obsługiwane. Zapisz kopię XLSX bez hasła.', macros: 'Pliki z makrami nie są obsługiwane. Zapisz zwykły plik XLSX.',
            zip64: 'Ten wariant archiwum XLSX nie jest obsługiwany. Zapisz mniejszy, zwykły plik XLSX.',
            library: 'Nie udało się załadować obsługi XLSX. Odśwież stronę lub użyj CSV.', workbook: 'Nie można odczytać XLSX. Sprawdź, czy plik jest poprawny i nie jest chroniony hasłem.',
            sheets: 'XLSX musi zawierać dokładnie jeden arkusz z listą gości. Usuń dodatkowe arkusze lub zapisz każdy osobno.',
            grid: 'Arkusz zawiera komórki poza dozwolonym zakresem XLSX.',
            merged: 'Arkusz zawiera scalone komórki w kolumnach importu. Rozdziel je przed importem.',
            formula: 'Formuły nie są obsługiwane. Wklej wynik jako zwykły tekst.', cellError: 'Komórka zawiera błąd arkusza. Popraw jej wartość.'
        },
        en: {
            empty: 'The file is empty.', extension: 'Choose a CSV or XLSX file.', size: 'The maximum file size is 5 MB.',
            header: 'The first nonempty row must contain exactly 3 distinct headings: Full name, Inviting company, Email address (the Polish template headings also work).',
            columns: 'This row contains data outside the three required columns.', required: 'This field is required.', text: 'Enter text, without dates, numbers or formulas.',
            controls: 'Remove control characters, line breaks and the < and > characters.', long: 'This field can contain at most {0} characters.',
            name: 'Enter a first and last name: at least two parts containing letters. Spaces, periods, apostrophes and hyphens are also allowed.',
            company: 'The company name cannot begin with =, + or @.', email: 'Enter a valid email address, for example jan.kowalski@example.com.',
            duplicate: 'This email address occurs more than once. First occurrence: row {0}.',
            rows: 'A file can contain at most 20,000 guests. Split it into smaller files.', noGuests: 'The file contains headings but no guests.',
            quotes: 'Malformed CSV quotes at row {0}. Save the file again as CSV.', encoding: 'The CSV encoding cannot be read. Save the file as CSV UTF-8.',
            delimiter: 'The CSV delimiter could not be detected. Use a semicolon, comma or tab and the template headings.',
            hugeField: 'The CSV contains an excessively long field at row {0}.', tooManyColumns: 'The file contains too many columns. Keep the three template columns.',
            binaryCSV: 'This is not a text CSV file. Choose the original XLSX file or save it as CSV UTF-8.',
            zip: 'This is not a valid XLSX file. Save the workbook again in XLSX format.', zipLimit: 'The unpacked workbook is too large or contains too many parts. Copy the data into a new template file.',
            encrypted: 'Encrypted and password-protected files are not supported. Save an XLSX copy without a password.', macros: 'Files with macros are not supported. Save a standard XLSX file.',
            zip64: 'This XLSX archive variant is not supported. Save a smaller, standard XLSX file.',
            library: 'The XLSX reader could not be loaded. Refresh the page or use CSV.', workbook: 'The XLSX file could not be read. Check that it is valid and not password-protected.',
            sheets: 'The XLSX file must contain exactly one worksheet with the guest list. Remove additional worksheets or save each separately.',
            grid: 'The worksheet contains cells outside the XLSX grid.',
            merged: 'The import columns contain merged cells. Unmerge them before importing.',
            formula: 'Formulas are not supported. Paste the result as plain text.', cellError: 'This cell contains a spreadsheet error. Correct its value.'
        }
    };
    function translator(language) {
        const messages = MESSAGES[language === 'en' ? 'en' : 'pl'];
        return function (key) {
            const args = Array.prototype.slice.call(arguments, 1);
            return messages[key].replace(/\{(\d+)\}/g, (_, n) => String(args[Number(n)]));
        };
    }
    const blank = value => value == null || (typeof value === 'string' && value.trim() === '');
    const normalize = value => value.trim().replace(/[\s\u00a0]+/gu, ' ');
    const unsafe = value => /[\u0000-\u001f\u007f-\u009f\u2028\u2029<>]/u.test(value);
    function headerKey(value) {
        const key = value.normalize('NFD').replace(/\p{M}/gu, '').toLowerCase().replace(/ł/g, 'l').replace(/[^a-z0-9]/g, '');
        const aliases = {
            imieinazwisko: 'full_name', imienazwisko: 'full_name', fullname: 'full_name', nameandsurname: 'full_name', guestfullname: 'full_name',
            firmazapraszajaca: 'inviting_company', invitingcompany: 'inviting_company', company: 'inviting_company', firma: 'inviting_company',
            adresemail: 'email', email: 'email', emailaddress: 'email', guestemail: 'email', emailosobyzapraszanej: 'email'
        };
        return aliases[key] || '';
    }
    function validEmail(value) {
        if (value.length < 6 || value.length > 254 || !/^[\x21-\x7e]+$/.test(value)) return false;
        const parts = value.split('@');
        if (parts.length !== 2 || parts[0].length > 64 || !/^[a-zA-Z0-9!#$%&'*+\/=?^_`{|}~.-]+$/.test(parts[0])) return false;
        if (parts[0][0] === '.' || parts[0].endsWith('.') || parts[0].includes('..')) return false;
        const labels = parts[1].split('.');
        return labels.length >= 2 && labels.every(label => /^[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?$/.test(label));
    }
    function validateRows(rows, metadata, t) {
        rows = rows.filter(row => row.values.some(value => !blank(value)) || Object.keys(row.issues || {}).length);
        if (!rows.length) throw new Error(t('empty'));
        const heading = rows.shift(), headers = Array.from(heading.values);
        while (headers.length && blank(headers[headers.length - 1])) headers.pop();
        // WYŁĄCZONE (nagłówki, znaki i formuły sprawdza serwer w PHP):
        // if (headers.length !== 3 || Object.keys(heading.issues || {}).length || headers.some(value => typeof value !== 'string' || unsafe(value))) throw new Error(t('header'));
        if (headers.length > 3) throw new Error(t('tooManyColumns'));   // ZOSTAJE: maksymalnie 3 kolumny
        const keys = headers.map(headerKey);
        // WYŁĄCZONE: if (keys.some(key => !key) || new Set(keys).size !== 3) throw new Error(t('header'));
        // Gdy nagłówki są rozpoznane, kolumny są przypisane wg nich; w przeciwnym razie wg kolejności: imię, firma, e-mail.
        const recognized = keys.length > 0 && keys.every(key => key) && new Set(keys).size === keys.length;
        const fields = recognized ? keys : ['full_name', 'inviting_company', 'email'];
        if (!rows.length) throw new Error(t('noGuests'));
        if (rows.length > MAX_ROWS) throw new Error(t('rows'));
        const errors = [], guests = [], emails = new Map();
        let errorCount = 0;
        function error(row, field, message) {
            errorCount++;
            if (errors.length < MAX_ERRORS) errors.push({ row, field, message });
        }
        rows.forEach(row => {
            const guest = { row: row.row, full_name: '', inviting_company: '', email: '' };
            if (row.values.slice(3).some(value => !blank(value)) || Object.keys(row.issues || {}).some(column => Number(column) >= 3)) error(row.row, 'columns', t('columns'));
            fields.forEach((field, column) => {
                const raw = row.values[column];
                // Wartość idzie do serwera bez zmian (liczby i daty jako tekst, formuły jako tekst zaczynający się od "=").
                // Wszystkie sprawdzenia danych robi serwer w PHP (mass_vip_add.php).
                guest[field] = raw == null ? '' : String(raw);
                /* WYŁĄCZONE (sprawdza serwer): wymagane pole, tylko tekst, znaki sterujące i < >, długość,
                   imię i nazwisko (dwa człony), firma (nie od = + @), format e-maila, duplikaty e-maili.
                const issue = (row.issues || {})[column];
                if (issue) { error(row.row, field, issue); return; }
                if (blank(raw)) { error(row.row, field, t('required')); return; }
                if (typeof raw !== 'string') { error(row.row, field, t('text')); return; }
                if (unsafe(raw)) { error(row.row, field, t('controls')); return; }
                const value = normalize(raw), limit = field === 'email' ? 254 : 200;
                guest[field] = value;
                if (Array.from(value).length > limit) { error(row.row, field, t('long', limit)); return; }
                if (field === 'full_name' && (!/^[\p{L}\p{M} .'‘’\u2010-\u2015-]+$/u.test(value) || value.split(' ').length < 2 || !value.split(' ').every(part => /\p{L}/u.test(part)))) error(row.row, field, t('name'));
                if (field === 'inviting_company' && /^[=+@]/.test(value)) error(row.row, field, t('company'));
                if (field === 'email') {
                    if (!validEmail(value)) error(row.row, field, t('email'));
                    else if (emails.has(value.toLowerCase())) error(row.row, field, t('duplicate', emails.get(value.toLowerCase())));
                    else emails.set(value.toLowerCase(), row.row);
                }
                */
            });
            guests.push(guest);
        });
        return Object.assign({ guests: errorCount ? [] : guests, errors, error_count: errorCount, total: rows.length }, metadata);
    }
    function decodeCSV(bytes, t) {
        let encoding = 'UTF-8', decoder = 'utf-8';
        if (bytes[0] === 0xff && bytes[1] === 0xfe) { encoding = 'UTF-16 LE'; decoder = 'utf-16le'; }
        else if (bytes[0] === 0xfe && bytes[1] === 0xff) { encoding = 'UTF-16 BE'; decoder = 'utf-16be'; }
        else if ((bytes[0] === 0x50 && bytes[1] === 0x4b) || bytes.slice(0, 256).includes(0)) throw new Error(t('binaryCSV'));
        let value;
        try { value = new TextDecoder(decoder, { fatal: true }).decode(bytes); }
        catch (_) {
            if (decoder !== 'utf-8' || (bytes[0] === 0xef && bytes[1] === 0xbb && bytes[2] === 0xbf)) throw new Error(t('encoding'));
            try { value = new TextDecoder('windows-1250', { fatal: true }).decode(bytes); encoding = 'Windows-1250'; }
            catch (_) { throw new Error(t('encoding')); }
        }
        return { value: value.replace(/^\ufeff/, ''), encoding };
    }
    function detectDelimiter(text, t) {
        const counts = { ';': 0, ',': 0, '\t': 0 };
        let quote = false, hasContent = false;
        for (let i = 0; i < text.length; i++) {
            const c = text[i];
            if (c === '"') {
                if (quote && text[i + 1] === '"') { i++; continue; }
                quote = !quote;
            } else if (!quote && (c === '\r' || c === '\n')) {
                if (hasContent) break;
                Object.keys(counts).forEach(key => { counts[key] = 0; });
            } else if (!quote && Object.prototype.hasOwnProperty.call(counts, c)) counts[c]++;
            if (!/\s/.test(c)) hasContent = true;
        }
        const choices = Object.keys(counts).sort((a, b) => counts[b] - counts[a]);
        if (!counts[choices[0]] || counts[choices[0]] === counts[choices[1]]) throw new Error(t('delimiter'));
        return choices[0];
    }
    function csvRows(text, delimiter, startLine, t) {
        const rows = [];
        let values = [], field = '', quoted = false, closed = false, line = startLine, rowLine = startLine;
        function fieldEnd() {
            values.push(field); field = ''; closed = false;
            if (values.length > 32) throw new Error(t('tooManyColumns'));
        }
        function rowEnd() {
            fieldEnd();
            if (values.some(value => !blank(value))) {
                rows.push({ row: rowLine, values });
                if (rows.length > MAX_ROWS + 1) throw new Error(t('rows'));
            }
            values = [];
        }
        for (let i = 0; i < text.length; i++) {
            const c = text[i], newline = c === '\r' || c === '\n';
            if (quoted) {
                if (c === '"') {
                    if (text[i + 1] === '"') { field += '"'; i++; }
                    else { quoted = false; closed = true; }
                } else if (newline) {
                    if (c === '\r' && text[i + 1] === '\n') i++;
                    field += '\n'; line++;
                } else field += c;
            } else if (c === delimiter) fieldEnd();
            else if (newline) {
                rowEnd();
                if (c === '\r' && text[i + 1] === '\n') i++;
                line++; rowLine = line;
            } else if (closed) {
                if (c !== ' ' && c !== '\t') throw new Error(t('quotes', rowLine));
            } else if (c === '"') {
                if (field !== '') throw new Error(t('quotes', rowLine));
                quoted = true;
            } else field += c;
            if (field.length > 8192) throw new Error(t('hugeField', rowLine));
        }
        if (quoted) throw new Error(t('quotes', rowLine));
        rowEnd();
        return rows;
    }
    function parseCSV(bytes, t) {
        const decoded = decodeCSV(bytes, t);
        let text = decoded.value, startLine = 1, delimiter;
        const separator = /^sep=([;,\t])(?:\r\n|\n|\r|$)/i.exec(text);
        if (separator) { delimiter = separator[1]; text = text.slice(separator[0].length); startLine = 2; }
        else delimiter = detectDelimiter(text, t);
        return validateRows(csvRows(text, delimiter, startLine, t), { encoding: decoded.encoding, sheet_name: '' }, t);
    }
    function checkZip(bytes, t) {
        const view = new DataView(bytes.buffer, bytes.byteOffset, bytes.byteLength), length = bytes.length;
        const u16 = n => view.getUint16(n, true), u32 = n => view.getUint32(n, true);
        if (length < 22 || u32(0) !== 0x04034b50) throw new Error(t('zip'));
        let end = -1;
        for (let i = length - 22; i >= Math.max(0, length - 65557); i--) {
            if (u32(i) === 0x06054b50 && i + 22 + u16(i + 20) === length) { end = i; break; }
        }
        if (end < 0) throw new Error(t('zip'));
        const count = u16(end + 10), directorySize = u32(end + 12), directory = u32(end + 16);
        if (count === 0xffff || directorySize === 0xffffffff || directory === 0xffffffff || (end >= 20 && u32(end - 20) === 0x07064b50)) throw new Error(t('zip64'));
        if (u16(end + 4) || u16(end + 6) || u16(end + 8) !== count || directory + directorySize !== end) throw new Error(t('zip'));
        if (!count || count > 300) throw new Error(t('zipLimit'));
        const names = new Set(), spans = [];
        let cursor = directory, unpacked = 0;
        for (let i = 0; i < count; i++) {
            if (cursor + 46 > end || u32(cursor) !== 0x02014b50) throw new Error(t('zip'));
            const flags = u16(cursor + 8), method = u16(cursor + 10), packed = u32(cursor + 20), size = u32(cursor + 24);
            const nameLength = u16(cursor + 28), extraLength = u16(cursor + 30), commentLength = u16(cursor + 32), local = u32(cursor + 42);
            const next = cursor + 46 + nameLength + extraLength + commentLength;
            if (next > end || local + 30 > directory) throw new Error(t('zip'));
            if (flags & 0x41) throw new Error(t('encrypted'));
            if (packed === 0xffffffff || size === 0xffffffff || local === 0xffffffff) throw new Error(t('zip64'));
            if (method !== 0 && method !== 8) throw new Error(t('zip'));
            unpacked += size;
            if (unpacked > 25 * 1024 * 1024) throw new Error(t('zipLimit'));
            for (let e = cursor + 46 + nameLength; e < cursor + 46 + nameLength + extraLength;) {
                if (e + 4 > cursor + 46 + nameLength + extraLength) throw new Error(t('zip'));
                if (u16(e) === 1) throw new Error(t('zip64'));
                e += 4 + u16(e + 2);
                if (e > cursor + 46 + nameLength + extraLength) throw new Error(t('zip'));
            }
            const nameBytes = bytes.subarray(cursor + 46, cursor + 46 + nameLength);
            let name;
            try { name = new TextDecoder('utf-8', { fatal: true }).decode(nameBytes); }
            catch (_) { throw new Error(t('zip')); }
            if (!name || names.has(name) || /[\u0000\\]/.test(name) || name.split('/').includes('..')) throw new Error(t('zip'));
            names.add(name);
            if (/vbaProject\.bin$|^xl\/(macrosheets|dialogsheets)\//i.test(name)) throw new Error(t('macros'));
            if (u32(local) !== 0x04034b50 || u16(local + 6) !== flags || u16(local + 8) !== method || u16(local + 26) !== nameLength) throw new Error(t('zip'));
            const data = local + 30 + nameLength + u16(local + 28);
            if (data + packed > directory) throw new Error(t('zip'));
            for (let n = 0; n < nameLength; n++) if (bytes[local + 30 + n] !== nameBytes[n]) throw new Error(t('zip'));
            if (!(flags & 8) && (u32(local + 18) !== packed || u32(local + 22) !== size)) throw new Error(t('zip'));
            if (method === 0 && packed !== size) throw new Error(t('zip'));
            spans.push([local, data + packed]);
            cursor = next;
        }
        if (cursor !== end || !names.has('[Content_Types].xml') || !names.has('xl/workbook.xml') || !names.has('_rels/.rels')) throw new Error(t('zip'));
        spans.sort((a, b) => a[0] - b[0]);
        if (spans.some((span, i) => i && spans[i - 1][1] > span[0])) throw new Error(t('zip'));
    }
    function parseXLSX(bytes, XLSX, t) {
        checkZip(bytes, t);
        // Worker ładuje bibliotekę XLSX tylko dla plików .xlsx. Gdy XLSX ma nazwę .csv, doładowujemy ją tutaj (ta sama ścieżka co w workerze).
        if ((!XLSX || typeof XLSX.read !== 'function') && typeof importScripts === 'function') {
            try { importScripts('vendor/xlsx.mini.min.js?ver=0.20.3'); XLSX = self.XLSX; } catch (_) { /* poniżej zgłosimy błąd biblioteki */ }
        }
        if (!XLSX || typeof XLSX.read !== 'function') throw new Error(t('library'));
        let workbook;
        try {
            // A sheetRows cutoff silently loses cells when an exporter omits or lies about dimensions.
            // Parse the bounded archive completely in the worker; derive the range from actual cells.
            workbook = XLSX.read(bytes, { type: 'array', dense: true, nodim: true, sheets: 0, cellFormula: true, cellHTML: false, cellText: false, cellStyles: false, cellDates: false, bookVBA: false, bookDeps: false, bookFiles: false, WTF: true });
        } catch (_) { throw new Error(t('workbook')); }
        // WYŁĄCZONE: if (workbook.SheetNames.length !== 1) throw new Error(t('sheets'));   // czytamy pierwszy arkusz
        const name = workbook.SheetNames[0], sheet = workbook.Sheets[name];
        if (!sheet || !sheet['!ref']) throw new Error(t('empty'));
        const range = XLSX.utils.decode_range(sheet['!fullref'] || sheet['!ref']);
        if (range.e.r > 1048575 || range.e.c > 16383) throw new Error(t('grid'));
        // WYŁĄCZONE: if ((sheet['!merges'] || []).some(merge => merge.s.c <= 2 && merge.s.r <= range.e.r && merge.e.r >= range.s.r)) throw new Error(t('merged'));
        const rows = [], data = sheet['!data'] || [];
        if (!sheet['!data']) {
            Object.keys(sheet).filter(key => /^[A-Z]+[1-9][0-9]*$/.test(key)).forEach(key => {
                const cell = XLSX.utils.decode_cell(key);
                if (!data[cell.r]) data[cell.r] = [];
                data[cell.r][cell.c] = sheet[key];
            });
        }
        const rowNumbers = Object.keys(data).map(Number).filter(Number.isInteger).sort((a, b) => a - b);
        for (const r of rowNumbers) {
            const values = [], issues = {};
            for (const key of Object.keys(data[r] || {})) {
                const c = Number(key);
                if (!Number.isInteger(c) || c < 0) continue;
                if (r < 0 || r > 1048575 || c > 16383) throw new Error(t('grid'));
                const cell = data[r][c];
                values[c] = cell && cell.v != null ? cell.v : '';
                // Formuła albo błąd komórki: zamiast blokować plik, przekazujemy tekst zaczynający się od "=",
                // a serwer pomija taki wiersz i zapisuje go w logu jako błąd (kod 7).
                // WYŁĄCZONE: if (cell && (cell.f != null || cell.F != null)) issues[c] = t('formula');
                // WYŁĄCZONE: else if (cell && cell.t === 'e') issues[c] = t('cellError');
                if (cell && (cell.f != null || cell.F != null)) values[c] = '=' + String(cell.f != null ? cell.f : cell.F);
                else if (cell && cell.t === 'e') values[c] = '=#BŁĄD';
            }
            if (values.some(value => !blank(value)) || Object.keys(issues).length) {
                rows.push({ row: r + 1, values, issues });
                if (rows.length > MAX_ROWS + 1) throw new Error(t('rows'));
            }
        }
        return validateRows(rows, { encoding: '', sheet_name: name }, t);
    }
    function parseBuffer(buffer, extension, XLSX, language) {
        const t = translator(language), bytes = buffer instanceof Uint8Array ? buffer : new Uint8Array(buffer);
        if (!bytes.length) throw new Error(t('empty'));
        if (bytes.length > MAX_FILE_BYTES) throw new Error(t('size'));
        extension = String(extension || '').toLowerCase().replace(/^\./, '');
        // ZOSTAJE: rozszerzenie musi być .csv albo .xlsx.
        if (extension !== 'csv' && extension !== 'xlsx') throw new Error(t('extension'));
        // Format wybieramy po ZAWARTOŚCI, a nie po rozszerzeniu: plik ZIP (początek PK 03 04) to XLSX, każdy inny to CSV.
        // Dzięki temu XLSX z nazwą .csv i CSV z nazwą .xlsx są po prostu odczytane.
        const isZip = bytes.length > 3 && bytes[0] === 0x50 && bytes[1] === 0x4b && bytes[2] === 0x03 && bytes[3] === 0x04;
        return isZip ? parseXLSX(bytes, XLSX, t) : parseCSV(bytes, t);
        // WYŁĄCZONE (format wg rozszerzenia):
        // if (extension === 'csv') return parseCSV(bytes, t);
        // if (extension === 'xlsx') return parseXLSX(bytes, XLSX, t);
        // throw new Error(t('extension'));
    }
    return { parseBuffer, MAX_ROWS, MAX_FILE_BYTES, MAX_ERRORS };
}));
