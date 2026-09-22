# `includes/class-clear-transients.php`

Plik warstwy rdzeniowej definiujący klasę `PWE_Clear_Transients` i jej logikę pomocniczą/integracyjną.

## Metadane

- **Kategoria:** `core`
- **Rozmiar:** 3622 B
- **Liczba linii:** 113
- **Źródło:** `includes/class-clear-transients.php`

## Klasy i metody

### `PWE_Clear_Transients` — linia 6

- `public static init()` — linia 8
- `public static handle_request()` — linia 12
- `public static clear_all_transients()` — linia 72
- `public static create_plans_news()` — linia 88

## Rejestracje WordPress wykryte w pliku

- **action:** `template_redirect` — linia 9

## Wybrane zależności wywołań

- `Fair_Plan::create_missing_news_for_files()`
- `Fair_Plan::create_or_update_fair_plan_pages()`
- `PWE_Clear_Transients::init()`
- `PWE_Functions::get_database_fairs_data_files()`
- `PWE_Functions::refresh_database_json_cache()`

## Tabele SQL widoczne statycznie

- `database`

## Dołączane pliki / wyrażenia include

- `_once plugin_dir_path(dirname(__FILE__)) . 'elements/fair-plan/fair-plan/fair-plan.php'`
- `_once plugin_dir_path(dirname(__FILE__)) . 'elements/fair-plan/fair-plan/fair-plan.php'`

## Powiązana dokumentacja

- [Symbole tego pliku](../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
