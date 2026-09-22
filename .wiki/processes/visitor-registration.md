---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: rejestracja odwiedzającego

Proces jest rozdzielony pomiędzy `Registration_Visitors`, Gravity Forms oraz `Confirmation_Visitors_Registration`.

## 1. Wejście na stronę rejestracji

Element `Registration_Visitors` obsługuje typ strony `registration-visitors`.

Na podstawie `utm_source` wybierany jest wariant:

- brak/inna wartość -> `standard`,
- `premium` -> `premium`,
- `byli` -> `byli`,
- `platyna` -> `platyna`.

Dla rozpoznanych wariantów `utm_source` wartość jest zapisywana w `$_SESSION['pwe_registration_utm_source']`.

## 2. Przygotowanie formularza

`Registration_Visitors::render()`:

1. ustawia kontekst tłumaczeń,
2. ładuje assety elementu/presetu,
3. odnajduje formularz Gravity Forms o nazwie `Rejestracja` przez `PWE_Functions::get_gf_form_id()`,
4. generuje shortcode `[gravityform ... ajax="false"]`,
5. pobiera pomocnicze dane (m.in. grupa targowa, logotypy, industry),
6. dołącza odpowiedni preset.

## 3. Zapamiętanie entry po wysłaniu

Klasa rejestruje `gform_after_submission`. Dla formularza `Rejestracja` zapisuje do `$_SESSION['pwe_reg_entry']` co najmniej `entry_id` oraz odnalezione wartości e-mail/telefon; wariant UTM jest również dostępny w sesji.

## 4. Strona potwierdzenia

`Confirmation_Visitors_Registration` uruchamia własne filtry GF i handler AJAX. Jeżeli konfiguracja `reg_form_update_entries` wymaga aktualizacji, a sesja nie zawiera rejestracji, użytkownik niebędący administratorem jest kierowany z powrotem do `/rejestracja`.

Klasa:

- ukrywa pola e-mail/telefon, gdy dane są już w sesji,
- może dodać ukryte pole numeru lokalu do konfiguracji formularza,
- pobiera edycję i daty targów przez shortcody,
- zawiera regułę blokującą/redirectującą stronę w okresie trzech tygodni przed datą końcową zgodnie z aktualnym kodem.

## 5. Aktualizacja danych adresowych przez AJAX

`update_registration_address`:

1. wymaga `$_SESSION['pwe_reg_entry']['entry_id']`,
2. pobiera formularz i entry przez `GFAPI`,
3. mapuje pola według `adminLabel`: `name`, `street`, `house`, `apartment/local`, `post`, `city`,
4. sanitizuje wartości POST,
5. zapisuje entry przez `GFAPI::update_entry()`,
6. opcjonalnie uruchamia integrację `GF_Integration` z pluginu `custom-element`,
7. przy określonej relacji daty może uruchomić `Activation_DB`,
8. usuwa dane rejestracji/UTM z sesji,
9. zwraca JSON success.

## Bezpieczeństwo

AJAX jest dostępny również przez `wp_ajax_nopriv_update_registration_address`. W źródle znajduje się komentarz, że weryfikacja CSRF jest tymczasowo wyłączona dla zgodności z cache. Operacja opiera się na posiadaniu właściwej sesji. Zobacz [../security/review.md](../security/review.md).
