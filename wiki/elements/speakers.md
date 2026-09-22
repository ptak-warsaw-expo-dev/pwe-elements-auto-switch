# `Speakers`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/speakers/speakers.php`  
**Klasa:** `Speakers` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-speakers]`

## Typy stron

- `main`

## Presety

- `gr1` → `elements/main/speakers/presets/gr1/preset.php`
- `gr2` → `elements/main/speakers/presets/gr2/preset.php`
- `week` → `elements/main/speakers/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Speakers` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Speakers::get_data()`
- `Speakers::render()`
- `Speakers::create_speakers_pages()`
- `Speakers::find_or_create_speakers_page()`
- `Speakers::find_page_by_slug()`
- `Speakers::assign_wpml_language()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_speakers()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/speakers/speakers.php.md)
