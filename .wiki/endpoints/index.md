# Endpointy i handlery HTTP

Projekt nie używa `register_rest_route()`. Integracje HTTP są realizowane przez bezpośrednie pliki PHP, WordPress AJAX i handlery `template_redirect`.

| Endpoint/handler | Typ | Metody | Autoryzacja | Dokument |
|---|---|---|---|---|
| `/wp-content/plugins/pwe-elements-auto-switch/api/cap/doc.php` | direct-http | GET, POST, OPTIONS | GET bez klucza; POST wymaga nagłówka X-PWE-API-Key zgodnego z PWE_API_KEY_2 | [cap-graphics](cap-graphics.md) |
| `/wp-content/plugins/pwe-elements-auto-switch/api/news/index.php` | direct-http | POST | PWE_API_KEY_2 przez ?key= lub X-API-KEY | [news-sync](news-sync.md) |
| `admin-ajax.php?action=update_registration_address` | wp-ajax | POST | dostępne również nopriv; kod opiera autoryzację logiczną na sesji pwe_reg_entry; komentarz mówi o tymczasowo wyłączonej weryfikacji CSRF | [ajax-update-registration-address](ajax-update-registration-address.md) |
| `admin-ajax.php?action=update_exhibitor_data` | wp-ajax | POST | dostępne również nopriv; wymagany entry_id w sesji pwe_exhibitor_entry | [ajax-update-exhibitor-data](ajax-update-exhibitor-data.md) |
| `admin-ajax.php?action=clear_pwe_session` | wp-ajax | POST | dostępne również nopriv; czyści sesję PWE | [ajax-clear-pwe-session](ajax-clear-pwe-session.md) |
| `dowolny frontend URL?pwe_clear_transients=<token>` | query-handler | GET | porównanie z hardcoded stałą PWE_CLEAR_TOKEN w źródle | [clear-transients](clear-transients.md) |
| `/?registration_log_download=1&registration_log_nonce=...` | query-handler | GET | zalogowany użytkownik lub klucz PWE_API_KEY_5 + wymagany nonce | [registration-log-download](registration-log-download.md) |
