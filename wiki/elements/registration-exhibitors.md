# `Registration_Exhibitors`

**Typ:** element AutoSwitch  
**Plik:** `elements/registration-exhibitors/registration-exhibitors/registration-exhibitors.php`  
**Klasa:** `Registration_Exhibitors` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-registration-exhibitors]`

## Typy stron

- `registration-exhibitors`

## Presety

- `all` → `elements/registration-exhibitors/registration-exhibitors/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Registration_Exhibitors` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Registration_Exhibitors::get_data()`
- `Registration_Exhibitors::render()`
- `Registration_Exhibitors::register_session_handler()`
- `Registration_Exhibitors::entry_to_session()`

## Shortcody używane wewnętrznie

- `[gravityform]`
- `[trade_fair_group]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::exhibitor_logos()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/registration-exhibitors/registration-exhibitors/registration-exhibitors.php.md)
