---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: rejestracja wystawcy

## 1. Formularz początkowy

`Registration_Exhibitors` obsługuje stronę `registration-exhibitors` i używa wariantu `all`.

`render()`:

1. rejestruje obsługę sesji,
2. ustawia tłumaczenia i assety,
3. odnajduje Gravity Form `Zostań wystawcą`,
4. pobiera grupę targową i teksty tłumaczone,
5. generuje `[gravityform ... ajax="false"]`,
6. renderuje preset.

## 2. Zapamiętanie rejestracji

Na `gform_after_submission` metoda `Registration_Exhibitors::entry_to_session()` weryfikuje ID formularza i zapisuje:

```text
$_SESSION['pwe_exhibitor_entry']
  entry_id
  email (jeżeli wykryto pole e-mail)
  phone (jeżeli wykryto pole phone)
```

## 3. Strona potwierdzenia / krok 2

`Confirmation_Exhibitors_Registration::init()` rejestruje filtry Gravity Forms oraz dwa AJAX-y. Render wybiera formularz pomiędzy `Zostań wystawcą` i `Zostań wystawcą (krok2)` w zależności od tego, czy sesja pochodzi od wystawcy, czy odwiedzającego.

Dane e-mail/telefon z sesji są wstrzykiwane w kilku miejscach lifecycle GF:

- `gform_pre_render`,
- `gform_pre_validation`,
- `gform_validation`,
- `gform_save_field_value`,
- `gform_pre_submission`.

Pola są ukrywane i nie są ponownie wymagane, gdy wartości są dostępne w sesji.

## 4. AJAX `update_exhibitor_data`

Handler:

1. wymaga `pwe_exhibitor_entry.entry_id`,
2. odnajduje formularz `Zostań wystawcą`,
3. pobiera formularz i entry przez `GFAPI`,
4. aktualizuje whitelistę pól rozpoznawanych po `adminLabel` (`name`, `nip`, `company`, `area`, pola adresu),
5. zapisuje entry,
6. aktywuje tylko powiadomienie `Admin Notification Potwierdzenie - <LANG>` i wywołuje wysyłkę GF,
7. wysyła nieblokujący POST do `custom-element/action_handler.php` z `element=gform_after_submission`,
8. czyści sesję,
9. zwraca JSON success.

## 5. Czyszczenie sesji

Sesja jest również czyszczona po submission/confirmation. Istnieje osobny AJAX `clear_pwe_session` dostępny także dla niezalogowanych użytkowników.
