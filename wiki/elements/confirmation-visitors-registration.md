# `Confirmation_Visitors_Registration`

**Typ:** element AutoSwitch  
**Plik:** `elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php`  
**Klasa:** `Confirmation_Visitors_Registration` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-confirmation-visitors-registration]`

## Typy stron

- `confirmation-visitors-registration`

## Presety

- `byli` → `elements/confirmation-visitors-registration/confirmation-visitors-registration/presets/byli/preset.php`
- `platyna` → `elements/confirmation-visitors-registration/confirmation-visitors-registration/presets/platyna/preset.php`
- `premium` → `elements/confirmation-visitors-registration/confirmation-visitors-registration/presets/premium/preset.php`
- `standard` → `elements/confirmation-visitors-registration/confirmation-visitors-registration/presets/standard/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Confirmation_Visitors_Registration` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Confirmation_Visitors_Registration::init()`
- `Confirmation_Visitors_Registration::get_data()`
- `Confirmation_Visitors_Registration::render()`
- `Confirmation_Visitors_Registration::register_gravity_forms_filters()`
- `Confirmation_Visitors_Registration::prepare_registration_form()`
- `Confirmation_Visitors_Registration::register_ajax_handlers()`
- `Confirmation_Visitors_Registration::update_registration_address()`
- `Confirmation_Visitors_Registration::add_apartment_field()`

## Shortcody używane wewnętrznie

- `[gravityform]`
- `[trade_fair_date_custom_format]`
- `[trade_fair_datetotimer]`
- `[trade_fair_edition]`
- `[trade_fair_enddata]`
- `[trade_fair_group]`

## Główne zależności

- `GFAPI::get_entry()`
- `GFAPI::get_form()`
- `GFAPI::update_entry()`
- `GFAPI::update_form()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::is_pwe_session_page()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php.md)
