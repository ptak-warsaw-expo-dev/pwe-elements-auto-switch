# `Speakers_Page`

**Typ:** element AutoSwitch  
**Plik:** `elements/speakers/speakers/speakers.php`  
**Klasa:** `Speakers_Page` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-speakers-page]`

## Typy stron

- `speakers`

## Presety

- `gr1` → `elements/speakers/speakers/presets/gr1/preset.php`
- `gr2` → `elements/speakers/speakers/presets/gr2/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Speakers_Page` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Speakers_Page::get_data()`
- `Speakers_Page::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_speakers()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/speakers/speakers/speakers.php.md)
