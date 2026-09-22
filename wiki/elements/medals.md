# `Medals`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/medals/medals.php`  
**Klasa:** `Medals` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-medals]`

## Typy stron

- `main`

## Presety

- `gr1` → `elements/main/medals/presets/gr1/preset.php`
- `gr2` → `elements/main/medals/presets/gr2/preset.php`
- `week` → `elements/main/medals/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Medals` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Medals::get_data()`
- `Medals::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/medals/medals.php.md)
