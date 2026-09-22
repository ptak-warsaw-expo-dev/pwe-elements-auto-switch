# `Call_Center`

**Typ:** element AutoSwitch  
**Plik:** `elements/call-center/call-center/call-center.php`  
**Klasa:** `Call_Center` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-call-center]`

## Typy stron

- `call-center`

## Presety

- `all` → `elements/call-center/call-center/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Call_Center` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Call_Center::get_data()`
- `Call_Center::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/call-center/call-center/call-center.php.md)
