# `Summary`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/summary/summary.php`  
**Klasa:** `Summary` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-summary]`

## Typy stron

- `main`

## Presety

- `gr1` → `elements/main/summary/presets/gr1/preset.php`
- `gr2` → `elements/main/summary/presets/gr2/preset.php`
- `week` → `elements/main/summary/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Summary` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Summary::get_data()`
- `Summary::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/summary/summary.php.md)
