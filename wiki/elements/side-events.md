# `Side_Events`

**Typ:** element AutoSwitch  
**Plik:** `elements/conferences/side-events/side-events.php`  
**Klasa:** `Side_Events` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-side-events]`

## Typy stron

- `conferences`

## Presety

- `all` → `elements/conferences/side-events/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Side_Events` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Side_Events::get_data()`
- `Side_Events::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_group]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/conferences/side-events/side-events.php.md)
