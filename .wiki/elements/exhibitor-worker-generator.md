# `Exhibitor_Worker_Generator`

**Typ:** element AutoSwitch  
**Plik:** `elements/exhibitor-worker-generator/exhibitor-worker-generator/exhibitor-worker-generator.php`  
**Klasa:** `Exhibitor_Worker_Generator` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-exhibitor-worker-generator]`

## Typy stron

- `exhibitor-worker-generator`

## Presety

- `all` → `elements/exhibitor-worker-generator/exhibitor-worker-generator/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Exhibitor_Worker_Generator` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Exhibitor_Worker_Generator::get_data()`
- `Exhibitor_Worker_Generator::render()`

## Shortcody używane wewnętrznie

- `[gravityform]`
- `[trade_fair_group]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/exhibitor-worker-generator/exhibitor-worker-generator/exhibitor-worker-generator.php.md)
