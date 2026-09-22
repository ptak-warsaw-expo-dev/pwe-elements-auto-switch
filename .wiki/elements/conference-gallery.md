# `Conference_Gallery`

**Typ:** element AutoSwitch  
**Plik:** `elements/conferences/conference-gallery/conference-gallery.php`  
**Klasa:** `Conference_Gallery` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-conference-gallery]`

## Typy stron

- `conferences`

## Presety

- `all` → `elements/conferences/conference-gallery/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Conference_Gallery` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Conference_Gallery::get_data()`
- `Conference_Gallery::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/conferences/conference-gallery/conference-gallery.php.md)
