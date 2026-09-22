# `Exhibitor_Catalog`

**Typ:** element AutoSwitch  
**Plik:** `elements/catalog/exhibitor_catalog_vue/exhibitor_catalog_vue.php`  
**Klasa:** `Exhibitor_Catalog` (linia 7)  
**Shortcode:** `[pwe-elements-auto-switch-exhibitor-catalog]`

## Typy stron

- `catalog`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Exhibitor_Catalog` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Exhibitor_Catalog::get_data()`
- `Exhibitor_Catalog::get_info()`
- `Exhibitor_Catalog::render()`
- `Exhibitor_Catalog::enqueue_assets()`
- `Exhibitor_Catalog::enqueue_feedback_assets()`
- `Exhibitor_Catalog::get_plugin_version()`
- `Exhibitor_Catalog::get_catalog_type()`
- `Exhibitor_Catalog::sync_archive_catalog_entry()`

## Shortcody używane wewnętrznie

- `[pwe_katalog]`

## Główne zależności

- `PWE_Functions::add_log()`

## Powiązane źródła

- [Dokument pliku](../files/elements/catalog/exhibitor_catalog_vue/exhibitor_catalog_vue.php.md)
