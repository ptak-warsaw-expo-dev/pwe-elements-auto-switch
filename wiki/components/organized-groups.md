# `Organized_Groups`

**Typ:** komponent  
**Plik:** `components/organized-groups/organized-groups.php`  
**Klasa:** `Organized_Groups` (linia 4)  
**Shortcode:** `[pwe-elements-component-organized-groups]`

## Typy stron

- `organized-groups`

## Presety

- `all` → `components/organized-groups/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Organized_Groups` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Organized_Groups::get_data()`
- `Organized_Groups::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/components/organized-groups/organized-groups.php.md)
