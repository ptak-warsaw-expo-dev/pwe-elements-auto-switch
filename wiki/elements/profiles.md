# `Profiles`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/profiles/profiles.php`  
**Klasa:** `Profiles` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-profiles]`

## Typy stron

- `main`

## Presety

- `b2c-new` → `elements/main/profiles/presets/b2c-new/preset.php`
- `gr1` → `elements/main/profiles/presets/gr1/preset.php`
- `gr2` → `elements/main/profiles/presets/gr2/preset.php`
- `week` → `elements/main/profiles/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Profiles` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Profiles::get_data()`
- `Profiles::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_profiles()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/profiles/profiles.php.md)
