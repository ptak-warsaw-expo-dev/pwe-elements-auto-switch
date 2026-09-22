# `Combined_Events`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/combined-events/combined-events.php`  
**Klasa:** `Combined_Events` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-combined-events]`

## Typy stron

- `main`

## Presety

- `gr1` → `elements/main/combined-events/presets/gr1/preset.php`
- `week` → `elements/main/combined-events/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Combined_Events` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Combined_Events::get_data()`
- `Combined_Events::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_domainadress]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_associates_data()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/combined-events/combined-events.php.md)
