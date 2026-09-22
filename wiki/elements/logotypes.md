# `Logotypes`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/logotypes/logotypes.php`  
**Klasa:** `Logotypes` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-logotypes]`

## Typy stron

- `main`

## Presety

- `b2c-new` → `elements/main/logotypes/presets/b2c-new/preset.php`
- `gr1` → `elements/main/logotypes/presets/gr1/preset.php`
- `gr2` → `elements/main/logotypes/presets/gr2/preset.php`
- `week` → `elements/main/logotypes/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Logotypes` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Logotypes::get_data()`
- `Logotypes::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_group]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_logotypes_data()`
- `PWE_Functions::get_database_meta_data()`
- `PWE_Functions::languageChecker()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/logotypes/logotypes.php.md)
