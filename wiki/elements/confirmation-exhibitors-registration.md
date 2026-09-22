# `Confirmation_Exhibitors_Registration`

**Typ:** element AutoSwitch  
**Plik:** `elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php`  
**Klasa:** `Confirmation_Exhibitors_Registration` (linia 6)  
**Shortcode:** `[pwe-elements-auto-switch-confirmation-exhibitors-registration]`

## Typy stron

- `confirmation-exhibitors-registration`

## Presety

- `all` → `elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Confirmation_Exhibitors_Registration` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Confirmation_Exhibitors_Registration::init()`
- `Confirmation_Exhibitors_Registration::get_data()`
- `Confirmation_Exhibitors_Registration::render()`
- `Confirmation_Exhibitors_Registration::register_gravity_filters()`
- `Confirmation_Exhibitors_Registration::get_session_info()`
- `Confirmation_Exhibitors_Registration::get_session_data()`
- `Confirmation_Exhibitors_Registration::prepare_form()`
- `Confirmation_Exhibitors_Registration::fix_validation_and_inject()`
- `Confirmation_Exhibitors_Registration::inject_session_data()`
- `Confirmation_Exhibitors_Registration::override_saved_field_value()`
- `Confirmation_Exhibitors_Registration::register_ajax_handlers()`
- `Confirmation_Exhibitors_Registration::force_clear_pwe_session()`
- `Confirmation_Exhibitors_Registration::ajax_clear_session()`
- `Confirmation_Exhibitors_Registration::update_exhibitor_data()`
- `Confirmation_Exhibitors_Registration::clear_session_after_submission()`
- `Confirmation_Exhibitors_Registration::clear_session_on_confirmation()`

## Shortcody używane wewnętrznie

- `[gravityform]`

## Główne zależności

- `GFAPI::get_entry()`
- `GFAPI::get_form()`
- `GFAPI::send_notifications()`
- `GFAPI::update_entry()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::is_pwe_session_page()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php.md)
