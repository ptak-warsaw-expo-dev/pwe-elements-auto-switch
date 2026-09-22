# `Fair_Plan`

**Typ:** element AutoSwitch  
**Plik:** `elements/fair-plan/fair-plan/fair-plan.php`  
**Klasa:** `Fair_Plan` (linia 4)  
**Shortcode:** `[pwe-elements-auto-switch-fair-plan]`

## Typy stron

- `fair-plan`

## Presety

- `all` → `elements/fair-plan/fair-plan/presets/all/preset.php`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Fair_Plan` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Fair_Plan::get_data()`
- `Fair_Plan::set_featured_image_by_url()`
- `Fair_Plan::find_fair_plan_post()`
- `Fair_Plan::remove_news_without_active_plan()`
- `Fair_Plan::pwe_create_fair_plan_news()`
- `Fair_Plan::create_or_update_fair_plan_pages()`
- `Fair_Plan::set_uncode_header_none()`
- `Fair_Plan::set_uncode_show_title_off()`
- `Fair_Plan::create_missing_news_for_files()`
- `Fair_Plan::render()`

## Główne zależności

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_database_fairs_data_files()`
- `PWE_Functions::set_translation_context()`

## Powiązane źródła

- [Dokument pliku](../files/elements/fair-plan/fair-plan/fair-plan.php.md)
