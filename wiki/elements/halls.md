# `Halls`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/halls/halls.php`  
**Klasa:** `Halls` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-halls]`

## Typy stron

- `main`

## Presety

- `b2c-new` → `elements/main/halls/presets/b2c-new/preset.php`
- `gr1` → `elements/main/halls/presets/gr1/preset.php`
- `gr2` → `elements/main/halls/presets/gr2/preset.php`
- `week` → `elements/main/halls/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Halls` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Halls::get_data()`
- `Halls::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_datetotimer]`
- `[trade_fair_domainadress]`
- `[trade_fair_enddata]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::json_fairs()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/halls/halls.php.md)
