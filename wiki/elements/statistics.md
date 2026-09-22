# `Statistics`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/statistics/statistics.php`  
**Klasa:** `Statistics` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-statistics]`

## Typy stron

- `main`

## Presety

- `b2c-new` → `elements/main/statistics/presets/b2c-new/preset.php`
- `gr1` → `elements/main/statistics/presets/gr1/preset.php`
- `gr2` → `elements/main/statistics/presets/gr2/preset.php`
- `week` → `elements/main/statistics/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Statistics` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Statistics::get_data()`
- `Statistics::render()`
- `Statistics::ordinal_suffix()`
- `Statistics::adapting_word()`
- `Statistics::sc_int()`
- `Statistics::compare_values()`

## Shortcody używane wewnętrznie

- `[$shortcode]`
- `[pwe_edition]`
- `[pwe_visitors]`
- `[pwe_visitors_foreign]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_associates_data()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/statistics/statistics.php.md)
