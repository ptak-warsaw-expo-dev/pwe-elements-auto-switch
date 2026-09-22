---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: okresowe usuwanie wpisów Gravity Forms po e-mailach

W `includes/class-hooks.php` znajduje się `PWE_GF_Email_Entry_Cleanup`.

## Mechanizm

Klasa jest inicjalizowana przez hooki WordPress i okresowo sprawdza, czy należy wykonać cleanup. Harmonogram jest realizowany stanem zapisywanym w opcjach i sprawdzaniem podczas zwykłego requestu WordPress, a nie przez rejestrację osobnego eventu WP-Cron w analizowanej wersji.

Proces:

1. ustala, czy od ostatniego przebiegu minął wymagany okres,
2. zabezpiecza wykonanie lockiem,
3. iteruje po formularzach/entries Gravity Forms w rozważanych statusach,
4. porównuje adresy e-mail z regułami/prefiksami i wyjątkami domenowymi zdefiniowanymi w klasie,
5. usuwa pasujące entries trwale,
6. zapisuje wynik oraz termin następnego przebiegu w opcjach WordPress.

## Uwaga operacyjna

Reguły adresów/prefiksów są zakodowane w klasie, dlatego ich zmiana jest zmianą kodu, nie konfiguracji panelowej.
