# `Confirmation_Exhibitors_Registration::update_exhibitor_data()`

**Źródło:** `elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php:258`  
**Sygnatura:** `public static update_exhibitor_data()`

## Krótki opis

Aktualizuje exhibitor data zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `GFAPI::get_entry()`
- `GFAPI::get_form()`
- `GFAPI::send_notifications()`
- `GFAPI::update_entry()`
- `PWE_Functions::get_gf_form_id()`
- `self::force_clear_pwe_session()`

### API WordPress
- `wp_remote_post()`
- `wp_send_json_success()`
- `wp_send_json_error()`

## Kontekst

- Klasa: [Confirmation_Exhibitors_Registration](../../classes/Confirmation_Exhibitors_Registration.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
