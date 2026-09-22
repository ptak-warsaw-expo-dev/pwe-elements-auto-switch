# `Exhibitors_Top12`

**Typ:** komponent  
**Plik:** `components/exhibitors-top12/exhibitors-top12.php`  
**Klasa:** `Exhibitors_Top12` (linia 4)  
**Shortcode:** `[pwe-elements-component-exhibitors-top12]`

## Typy stron

- `exhibitors-top12`

## Presety

- `byli-premium-visitors` → `components/exhibitors-top12/presets/byli-premium-visitors/preset.php`
- `standard-exhibitors` → `components/exhibitors-top12/presets/standard-exhibitors/preset.php`
- `standard-visitors` → `components/exhibitors-top12/presets/standard-visitors/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Exhibitors_Top12` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Exhibitors_Top12::get_data()`
- `Exhibitors_Top12::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::exhibitor_logos()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/components/exhibitors-top12/exhibitors-top12.php.md)
