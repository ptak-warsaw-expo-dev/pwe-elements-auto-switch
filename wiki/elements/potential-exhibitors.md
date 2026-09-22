# `Potential_Exhibitors`

**Typ:** element AutoSwitch  
**Plik:** `elements/potential-exhibitors/potential-exhibitors/potential-exhibitors.php`  
**Klasa:** `Potential_Exhibitors` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-potential-exhibitors]`

## Typy stron

- `potential-exhibitors`

## Presety

- `all` → `elements/potential-exhibitors/potential-exhibitors/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Potential_Exhibitors` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Potential_Exhibitors::get_data()`
- `Potential_Exhibitors::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_date_custom_format]`
- `[trade_fair_edition]`

## Główne zależności

- `GFAPI::get_forms()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/potential-exhibitors/potential-exhibitors/potential-exhibitors.php.md)
