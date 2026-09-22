# `Conference`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/conference/conference.php`  
**Klasa:** `Conference` (linia 5)  
**Shortcode:** `[pwe-elements-auto-switch-conference]`

## Typy stron

- `main`

## Presety

- `gr1` → `elements/main/conference/presets/gr1/preset.php`
- `gr1-shedule` → `elements/main/conference/presets/gr1-shedule/preset.php`
- `gr2` → `elements/main/conference/presets/gr2/preset.php`
- `gr2-home` → `elements/main/conference/presets/gr2-home/preset.php`
- `gr2-shedule` → `elements/main/conference/presets/gr2-shedule/preset.php`
- `week-shedule` → `elements/main/conference/presets/week-shedule/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Conference` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Conference::get_data()`
- `Conference::get_conferences_brief()`
- `Conference::conference_overlaps_fair()`
- `Conference::getConferenceOrganizer()`
- `Conference::getConferenceOrganizersAll()`
- `Conference::render()`

## Shortcody używane wewnętrznie

- `[pwe_conference_desc_]`
- `[pwe_conference_title_]`
- `[trade_fair_datetotimer]`
- `[trade_fair_enddata]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_conference_adds_data()`
- `PWE_Functions::get_database_conferences_data()`
- `PWE_Functions::get_database_fairs_data_adds()`
- `PWE_Functions::get_database_logotypes_data()`
- `PWE_Functions::lang()`
- `PWE_Functions::languageChecker()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/conference/conference.php.md)
