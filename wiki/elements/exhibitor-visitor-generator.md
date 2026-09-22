# `Exhibitor_Visitor_Generator`

**Typ:** element AutoSwitch  
**Plik:** `elements/exhibitor-visitor-generator/exhibitor-visitor-generator/exhibitor-visitor-generator.php`  
**Klasa:** `Exhibitor_Visitor_Generator` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-exhibitor-visitor-generator]`

## Typy stron

- `exhibitor-visitor-generator`

## Presety

- `all` → `elements/exhibitor-visitor-generator/exhibitor-visitor-generator/presets/all/preset.php`
- `single` → `elements/exhibitor-visitor-generator/exhibitor-visitor-generator/presets/single/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Exhibitor_Visitor_Generator` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Exhibitor_Visitor_Generator::get_data()`
- `Exhibitor_Visitor_Generator::render()`

## Shortcody używane wewnętrznie

- `[gravityform]`
- `[trade_fair_contact]`
- `[trade_fair_exhibitor_generator_badge_url]`
- `[trade_fair_group]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/exhibitor-visitor-generator/exhibitor-visitor-generator/exhibitor-visitor-generator.php.md)
