# Pobieranie CSV logu rejestracji

**Źródło:** `includes/class-registration-log.php`

`PWE_Registration_Log::handle_csv_download()` reaguje na `registration_log_download=1` podczas `template_redirect`.

1. Sprawdza dostęp do logów: dowolny zalogowany użytkownik WordPress albo poprawny klucz publicznego dostępu.
2. Weryfikuje `registration_log_nonce`.
3. Odnajduje plik `wp-content/uploads/logs/registration-log.csv`.
4. Czyści bufory, ustawia nagłówki CSV, dodaje BOM UTF-8 i streamuje plik.

Log zawiera dane formularzy i adres IP, dlatego powinien być traktowany jako zasób wrażliwy.
