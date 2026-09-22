# `Step2`

**Typ:** element AutoSwitch  
**Plik:** `elements/step2/step2/step2.php`  
**Klasa:** `Step2` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-step2]`

## Typy stron

- `step2`

## Presety

- `all` → `elements/step2/step2/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Step2` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Step2::get_data()`
- `Step2::render()`

## Shortcody używane wewnętrznie

- `[pwe_group]`
- `[trade_fair_domainadress]`
- `[trade_fair_enddata]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/step2/step2/step2.php.md)
