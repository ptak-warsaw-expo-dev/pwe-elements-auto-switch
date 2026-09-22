# `Forms`

**Typ:** element AutoSwitch  
**Plik:** `elements/forms/forms/forms.php`  
**Klasa:** `Forms` (linia 7)  
**Shortcode:** `[pwe-elements-auto-switch-forms]`

## Typy stron

- `forms`

## Presety

- `bulk` → `elements/forms/forms/presets/bulk/preset.php`
- `csv` → `elements/forms/forms/presets/csv/preset.php`
- `entries` → `elements/forms/forms/presets/entries/preset.php`
- `forms` → `elements/forms/forms/presets/forms/preset.php`
- `forms-v2` → `elements/forms/forms/presets/forms-v2/preset.php`
- `json` → `elements/forms/forms/presets/json/preset.php`
- `qrcode` → `elements/forms/forms/presets/qrcode/preset.php`
- `stats` → `elements/forms/forms/presets/stats/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Forms` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Forms::get_data()`
- `Forms::get_presets()`
- `Forms::resolve_group()`
- `Forms::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_domainadress]`
- `[trade_fair_name]`

## Główne zależności

- `GFAPI::count_entries()`
- `GFAPI::get_entries()`
- `GFAPI::get_entry()`
- `GFAPI::get_feeds()`
- `GFAPI::get_form()`
- `GFAPI::get_forms()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/forms/forms/forms.php.md)
