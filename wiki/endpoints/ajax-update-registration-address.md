# AJAX `update_registration_address`

**Źródło:** `elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php`

Aktualizuje dane adresowe istniejącego wpisu Gravity Forms zapamiętanego w sesji `$_SESSION['pwe_reg_entry']['entry_id']`.

## Przepływ

1. Startuje sesję i wymaga `entry_id`.
2. Odnajduje formularz „Rejestracja” przez `PWE_Functions::get_gf_form_id()`.
3. Ładuje formularz i entry przez `GFAPI`.
4. Mapuje pola po `adminLabel` (`name`, `street`, `house`, `apartment/local`, `post`, `city`) i sanitizuje dane POST.
5. Zapisuje entry przez `GFAPI::update_entry()`.
6. Zwraca JSON WordPress.

## Bezpieczeństwo

Handler jest zarejestrowany także jako `wp_ajax_nopriv_...`. W kodzie znajduje się komentarz, że weryfikacja CSRF została tymczasowo wyłączona ze względu na cache; bezpieczeństwo operacji zależy więc przede wszystkim od posiadania prawidłowej sesji PWE.
