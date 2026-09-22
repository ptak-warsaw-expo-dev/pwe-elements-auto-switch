# `Badge_Local`

**Typ:** element AutoSwitch  
**Plik:** `elements/badge-local/badge-local/badge-local.php`  
**Klasa:** `Badge_Local` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-badge-local]`

## Typy stron

- `badge-local`

## Presety

- `all` → `elements/badge-local/badge-local/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Badge_Local` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Badge_Local::get_data()`
- `Badge_Local::massGenerator()`
- `Badge_Local::qrOnlyDownload()`
- `Badge_Local::pwe_download_temp_qr()`
- `Badge_Local::badge_name_changer()`
- `Badge_Local::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_badge]`

## Główne zależności

- `GFAPI::add_entry()`
- `GFAPI::get_entry()`
- `GFAPI::get_feeds()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/badge-local/badge-local/badge-local.php.md)
