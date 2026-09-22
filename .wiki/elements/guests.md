# `Guests`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/guests/guests.php`  
**Klasa:** `Guests` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-guests]`

## Typy stron

- `main`

## Presety

- `b2c-new` → `elements/main/guests/presets/b2c-new/preset.php`
- `gr1` → `elements/main/guests/presets/gr1/preset.php`
- `gr2` → `elements/main/guests/presets/gr2/preset.php`
- `week` → `elements/main/guests/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Guests` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Guests::get_data()`
- `Guests::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_guests()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/guests/guests.php.md)
