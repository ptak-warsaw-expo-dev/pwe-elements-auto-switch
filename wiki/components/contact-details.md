# `Contact_Details`

**Typ:** komponent  
**Plik:** `components/contact-details/contact-details.php`  
**Klasa:** `Contact_Details` (linia 4)  
**Shortcode:** `[pwe-elements-component-contact-details]`

## Typy stron

- `contact-details`

## Presety

- `all` → `components/contact-details/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Contact_Details` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Contact_Details::get_data()`
- `Contact_Details::pwe_clean_value()`
- `Contact_Details::pwe_option_value()`
- `Contact_Details::pwe_first_not_empty()`
- `Contact_Details::pwe_split_emails()`
- `Contact_Details::pwe_phone_href()`
- `Contact_Details::pwe_data_value()`
- `Contact_Details::pwe_render_email_links()`
- `Contact_Details::render()`

## Shortcody używane wewnętrznie

- `[pwe_edition]`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/components/contact-details/contact-details.php.md)
