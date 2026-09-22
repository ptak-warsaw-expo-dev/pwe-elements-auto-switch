# `Fair_Plan::pwe_create_fair_plan_news()`

**Źródło:** `elements/fair-plan/fair-plan/fair-plan.php:169`  
**Sygnatura:** `public static pwe_create_fair_plan_news($year)`

## Krótki opis

Realizuje logikę techniczną związaną z `pwe_create_fair_plan_news`.

## Wykryte zależności statyczne

### Wywołania statyczne
- `self::find_fair_plan_post()`
- `self::set_featured_image_by_url()`

### API WordPress
- `do_action()`
- `apply_filters()`
- `wp_insert_post()`
- `wp_update_post()`

## Kontekst

- Klasa: [Fair_Plan](../../classes/Fair_Plan.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/fair-plan/fair-plan/fair-plan.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
