# `Opinions`

**Typ:** element AutoSwitch  
**Plik:** `elements/main/opinions/opinions.php`  
**Klasa:** `Opinions` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-opinions]`

## Typy stron

- `main`

## Presety

- `gr1` → `elements/main/opinions/presets/gr1/preset.php`
- `gr2` → `elements/main/opinions/presets/gr2/preset.php`
- `week` → `elements/main/opinions/presets/week/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Opinions` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Opinions::get_data()`
- `Opinions::render()`

## Shortcody używane wewnętrznie

- `[trade_fair_edition]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_opinions()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/main/opinions/opinions.php.md)
