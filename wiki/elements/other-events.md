# `Other_Events`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/other-events/other-events.php`  
**Klasa:** `Other_Events` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-other-events]`

## Typy stron

- `main`

## Presety

- `gr1` → `elements/main/other-events/presets/gr1/preset.php`
- `gr2` → `elements/main/other-events/presets/gr2/preset.php`
- `week` → `elements/main/other-events/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Other_Events` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Other_Events::get_data()`
- `Other_Events::render()`

## Shortcody używane wewnętrznie

- `[pwe_desc_]`
- `[pwe_desc_en]`
- `[trade_fair_datetotimer]`
- `[trade_fair_domainadress]`
- `[trade_fair_enddata]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::json_fairs()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/other-events/other-events.php.md)
