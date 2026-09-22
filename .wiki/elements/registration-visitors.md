# `Registration_Visitors`

**Typ:** element AutoSwitch  
**Plik:** `elements/registration-visitors/registration-visitors/registration-visitors.php`  
**Klasa:** `Registration_Visitors` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-registration-visitors]`

## Typy stron

- `registration-visitors`

## Presety

- `byli` → `elements/registration-visitors/registration-visitors/presets/byli/preset.php`
- `platyna` → `elements/registration-visitors/registration-visitors/presets/platyna/preset.php`
- `premium` → `elements/registration-visitors/registration-visitors/presets/premium/preset.php`
- `standard` → `elements/registration-visitors/registration-visitors/presets/standard/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Registration_Visitors` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Registration_Visitors::get_data()`
- `Registration_Visitors::render()`
- `Registration_Visitors::get_existing_document()`
- `Registration_Visitors::get_vip_badge_mockup()`
- `Registration_Visitors::register_gravity_forms_filters()`
- `Registration_Visitors::register_session_handler()`
- `Registration_Visitors::entry_to_session()`
- `Registration_Visitors::add_utm_to_confirmation_redirect()`
- `Registration_Visitors::hide_registration_fields()`

## Shortcody używane wewnętrznie

- `[gravityform]`
- `[pwe_industry]`
- `[trade_fair_group]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::exhibitor_logos()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::is_pwe_session_page()`
- `PWE_Functions::lang()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/registration-visitors/registration-visitors/registration-visitors.php.md)
