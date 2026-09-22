---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Przegląd bezpieczeństwa — obserwacje z kodu 1.8.8

Ten dokument nie jest audytem penetracyjnym. To przegląd statyczny miejsc istotnych bezpieczeństwowo, wykrytych podczas dokumentowania kodu.

## 1. `pwe_clear_transients` — sekret w źródle i query string

`includes/class-clear-transients.php` definiuje sekret bezpośrednio w kodzie i porównuje go z `$_GET['pwe_clear_transients']`. Dokumentacja **celowo nie reprodukuje wartości sekretu**.

Ryzyka:

- sekret znajduje się w repozytorium/paczce,
- query string może pojawić się w access logach, historii i telemetryce,
- handler wykonuje operacje kosztowne/modyfikujące cache i wpisy.

Rekomendacja: sekret środowiskowy + autoryzacja WordPress/nonce albo dedykowany endpoint chroniony nagłówkiem.

## 2. AJAX aktualizacji odwiedzającego

`update_registration_address` działa także jako `wp_ajax_nopriv`. Kod zawiera komentarz, że weryfikacja CSRF jest tymczasowo wyłączona ze względu na cache. Wymagany `entry_id` pochodzi z sesji PHP, co ogranicza zakres, ale nie zastępuje ochrony CSRF.

Rekomendacja: przywrócić token/nonce zaprojektowany tak, aby działał z używanym cache, ewentualnie token sesyjny specyficzny dla operacji.

## 3. AJAX aktualizacji wystawcy i czyszczenia sesji

`update_exhibitor_data` i `clear_pwe_session` są dostępne dla `nopriv`. Pierwszy wymaga powiązanego entry w sesji; drugi czyści sesję. Warto utrzymać ścisłą walidację inputu oraz rozważyć ochronę CSRF dla operacji modyfikującej entry.

## 4. News Sync API — klucz w query string

`api/news/index.php` akceptuje `PWE_API_KEY_2` zarówno przez nagłówek, jak i `?key=`. Warto wycofać query-string credential i przyjmować wyłącznie nagłówek.

## 5. CAP Graphics API

POST do `api/cap/doc.php` korzysta z nagłówka `X-PWE-API-Key`, `hash_equals()` oraz whitelisty docelowych assetów. To korzystniejszy wzorzec. GET statusu jest publiczny, a CORS ustawiony na `*`; należy potwierdzić, że zwracane statusy nie zawierają informacji, które powinny być prywatne.

## 6. Log rejestracji

`uploads/logs/registration-log.csv` może zawierać dane formularzy oraz IP. Downloader wymaga nonce oraz określonej reguły dostępu, ale sam plik znajduje się pod uploadami. Należy sprawdzić ochronę bezpośredniego URL katalogu `/uploads/logs/` na produkcji.

Rekomendacje:

- blokada HTTP katalogu logów,
- minimalizacja logowanych pól,
- retencja/rotacja,
- ścisłe uprawnienia do strony `/logs` i CSV.

## 7. Dane dostępowe CAP

Credentiale baz CAP są pobierane ze stałych `PWE_DB_*`, co pozwala utrzymywać je poza kodem. Należy zachować ten model i nie kopiować sekretów do `.wiki` ani logów.

## 8. GitHub updater

Token updatera jest pobierany z tabeli WordPress (`custom_klavio_setup`). To nadal sekret aplikacyjny przechowywany w bazie; powinien mieć minimalne uprawnienia potrzebne tylko do odczytu release/repozytorium.

## 9. Sygnalizacja dla Developer Wiki

Przy indeksowaniu kodu rekomendowane jest oznaczanie następujących konstrukcji jako security-sensitive:

- handlerów `nopriv`,
- użyć `$_GET`, `$_POST`, `$_FILES`,
- kluczy/sekretów i porównań autoryzacyjnych,
- `wp_remote_post()` do lokalnych/zewnętrznych endpointów,
- zapisów do filesystemu,
- bezpośrednich zapytań SQL,
- operacji usuwania entries/postów,
- generowania/streamowania plików zawierających dane użytkowników.
