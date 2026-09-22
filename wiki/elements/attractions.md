# `Attractions`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/attractions/attractions.php`  
**Klasa:** `Attractions` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-attractions]`

## Typy stron

- `main`

## Presety

- `b2c-new` → `elements/main/attractions/presets/b2c-new/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Attractions` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Attractions::get_data()`
- `Attractions::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_attractions()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/attractions/attractions.php.md)
