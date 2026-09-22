# `elements/main/header/header.php`

Element strony AutoSwitch implementowany przez `Header`; zwykle deklaruje `get_data()` i `render()`.

## Metadane

- **Kategoria:** `element`
- **Rozmiar:** 2593 B
- **Liczba linii:** 65
- **Źródło:** `elements/main/header/header.php`

## Klasy i metody

### `Header` — linia 4

- `public static get_data()` — linia 6
- `public static render($group = '', $params = [], $atts = [])` — linia 18

## Shortcody wywoływane przez plik

- `[pwe_desc_]`
- `[pwe_name_]`
- `[trade_fair_date_custom_format]`
- `[trade_fair_date_multilang]`
- `[trade_fair_edition]`

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_element()`
- `PWE_Functions::assets_per_group()`
- `PWE_Functions::id_rnd()`
- `PWE_Functions::lang()`
- `PWE_Functions::multi_translation()`
- `PWE_Functions::set_translation_context()`
- `PWE_Groups::is_b2c()`

## Dołączane pliki / wyrażenia include

- `$preset_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
