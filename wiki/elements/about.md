# `About`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/about/about.php`  
**Klasa:** `About` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-about]`

## Typy stron

- `main`

## Presety

- `b2c-new` → `elements/main/about/presets/b2c-new/preset.php`
- `gr1` → `elements/main/about/presets/gr1/preset.php`
- `gr2` → `elements/main/about/presets/gr2/preset.php`
- `week` → `elements/main/about/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `About` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `About::get_data()`
- `About::render()`

## Shortcody używane wewnętrznie

- `[pwe_about_desc_]`
- `[pwe_about_title_]`
- `[trade_fair_name]`
- `[trade_fair_name_eng]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::exhibitor_logos()`
- `PWE_Functions::lang()`
- `PWE_Functions::languageChecker()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/about/about.php.md)
