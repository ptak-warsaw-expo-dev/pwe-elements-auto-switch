/* Parsing stays off the page's main thread. All scripts are served locally. */
'use strict';
importScripts('import-parser.js' + self.location.search);
self.onmessage = function (event) {
    const request = event.data || {}, language = request.lang || request.language || 'pl';
    try {
        const extension = String(request.extension || '').toLowerCase().replace(/^\./, '');
        if (extension === 'xlsx' && !self.XLSX) {
            try { importScripts('vendor/xlsx.mini.min.js?ver=0.20.3'); }
            catch (_) { throw new Error(language === 'en' ? 'The XLSX reader could not be loaded. Refresh the page or use CSV.' : 'Nie udało się załadować obsługi XLSX. Odśwież stronę lub użyj CSV.'); }
        }
        const result = self.PWEVisitorImportParser.parseBuffer(request.buffer, extension, self.XLSX, language);
        self.postMessage({ ok: true, result });
    } catch (error) {
        self.postMessage({ ok: false, message: error && error.message ? error.message : (language === 'en' ? 'The file could not be read.' : 'Nie można odczytać pliku.') });
    }
};
