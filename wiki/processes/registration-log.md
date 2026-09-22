---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: log rejestracji Gravity Forms

`PWE_Registration_Log` utrzymuje CSV w `wp-content/uploads/logs/registration-log.csv` i dostarcza shortcode `[registration_log]`.

## Cel

Logowanie prób walidacji/rejestracji w celu diagnostyki formularzy. W logu występują m.in. data, czas, status, ID i nazwa formularza, IP, dane oraz błędy walidacji.

## Zapis

Kod używa blokady pliku (`flock`) przy zapisie. Dane trafiają do katalogu uploadów WordPress.

## Strona logu

Klasa może utworzyć stronę `/logs` zawierającą shortcode `[registration_log]`. Shortcode renderuje interfejs podglądu zgodnie z regułami dostępu klasy.

## Pobieranie CSV

`handle_csv_download()` reaguje na `registration_log_download=1`:

1. sprawdza uprawnienie (zalogowany użytkownik lub poprawny klucz dostępu),
2. weryfikuje nonce downloadu,
3. lokalizuje CSV,
4. czyści bufory i ustawia nagłówki,
5. wysyła BOM UTF-8 i strumieniuje plik.

## Dane wrażliwe

CSV może zawierać dane formularzy oraz adres IP. Powinien być traktowany jako zasób wrażliwy, z ograniczonym dostępem i polityką retencji. Szczegóły bezpieczeństwa: [../security/review.md](../security/review.md).
