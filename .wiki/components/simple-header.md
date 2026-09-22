# `Simple_Header`

**Typ:** komponent  
**Plik:** `components/simple-header/simple-header.php`  
**Klasa:** `Simple_Header` (linia 4)  
**Shortcode:** `[pwe-elements-component-simple-header]`

## Typy stron

- `speakers`

## Presety

- `all` → `components/simple-header/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Simple_Header` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Simple_Header::get_data()`
- `Simple_Header::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_date_multilang]`
- `[trade_fair_name]`
- `[trade_fair_name_eng]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::lang_pl()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/components/simple-header/simple-header.php.md)
