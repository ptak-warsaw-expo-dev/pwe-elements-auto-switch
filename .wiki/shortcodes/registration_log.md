# `[registration_log]`

**Kategoria:** Diagnostyka  
**Callback:** `PWE_Registration_Log::render_shortcode`  
**Źródło:** `includes/class-registration-log.php:79`

## Krótki opis

Wyświetla interfejs raportu prób rejestracji Gravity Forms z danych CSV.

## Przepływ

1. `PWE_Registration_Log` zapisuje każdą próbę walidacji Gravity Forms do CSV w `wp-content/uploads/logs/registration-log.csv`.
2. Shortcode sprawdza dostęp: zalogowany użytkownik WordPress lub poprawny klucz dostępu.
3. Odczytuje CSV, wylicza statystyki i renderuje tabelę/filtry.
4. Pobranie CSV jest obsługiwane przez `template_redirect` i wymaga uprawnień oraz nonce.
