# `Medal_Ceremony`

**Typ:** element AutoSwitch  
**Plik:** `elements/medal-ceremony/medal-ceremony/medal-ceremony.php`  
**Klasa:** `Medal_Ceremony` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-medal-ceremony]`

## Typy stron

- `medal-ceremony`

## Presety

- `all` → `elements/medal-ceremony/medal-ceremony/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Medal_Ceremony` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Medal_Ceremony::get_data()`
- `Medal_Ceremony::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_adds()`
- `PWE_Functions::get_database_fairs_data_files()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::lang_pl()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/medal-ceremony/medal-ceremony/medal-ceremony.php.md)
