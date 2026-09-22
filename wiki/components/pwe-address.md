# `PWE_Address`

**Typ:** komponent  
**Plik:** `components/pwe-address/pwe-address.php`  
**Klasa:** `PWE_Address` (linia 4)  
**Shortcode:** `[pwe-elements-component-pwe-address]`

## Typy stron

- `pwe-address`

## Presety

- `all` → `components/pwe-address/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `PWE_Address` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `PWE_Address::get_data()`
- `PWE_Address::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/components/pwe-address/pwe-address.php.md)
