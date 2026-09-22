# `Header`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/header/header.php`  
**Klasa:** `Header` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-header]`

## Typy stron

- `main`

## Presety

- `b2c-new` → `elements/main/header/presets/b2c-new/preset.php`
- `gr1` → `elements/main/header/presets/gr1/preset.php`
- `gr2` → `elements/main/header/presets/gr2/preset.php`
- `week` → `elements/main/header/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Header` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Header::get_data()`
- `Header::render()`

## Shortcody używane wewnętrznie

- `[pwe_desc_]`
- `[pwe_name_]`
- `[trade_fair_date_custom_format]`
- `[trade_fair_date_multilang]`
- `[trade_fair_edition]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::id_rnd()`
- `PWE_Functions::lang()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/header/header.php.md)
