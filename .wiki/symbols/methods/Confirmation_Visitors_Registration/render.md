# `Confirmation_Visitors_Registration::render()`

**Źródło:** `elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php:25`  
**Sygnatura:** `public static render($group = '', $params = [], $atts = [])`

## Krótki opis

Renderuje dane zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `DateTime::createFromFormat()`
- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::get_gf_form_id()`
- `PWE_Functions::is_pwe_session_page()`
- `PWE_Functions::lang()`
- `PWE_Functions::set_translation_context()`
- `self::add_apartment_field()`
- `self::get_data()`
- `self::register_gravity_forms_filters()`

### Shortcody wywoływane
- `[gravityform]`
- `[trade_fair_date_custom_format]`
- `[trade_fair_datetotimer]`
- `[trade_fair_edition]`
- `[trade_fair_enddata]`
- `[trade_fair_group]`

### API WordPress
- `wp_safe_redirect()`

## Kontekst

- Klasa: [Confirmation_Visitors_Registration](../../classes/Confirmation_Visitors_Registration.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
