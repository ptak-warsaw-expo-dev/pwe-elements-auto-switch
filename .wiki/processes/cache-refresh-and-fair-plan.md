---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: odświeżenie cache i planów targów

## Punkt startowy

`PWE_Clear_Transients` rejestruje `template_redirect` i reaguje na parametr query `pwe_clear_transients`, jeżeli wartość odpowiada sekretowi skonfigurowanemu w kodzie.

## Przepływ

1. Jeżeli dostępny jest WP Rocket, wykonywane jest `rocket_clean_domain()`.
2. `PWE_Functions::refresh_database_json_cache()` wymusza przebudowę cache danych CAP.
3. Jeżeli istnieje `PWECommonFunctions`, wywoływana jest analogiczna metoda z tej klasy.
4. `clear_all_transients()` usuwa z `wp_options` transients i timeouty o prefiksie `pwe_`.
5. `create_plans_news()` ładuje klasę `Fair_Plan`.
6. `Fair_Plan::create_or_update_fair_plan_pages()` aktualizuje główne strony planu targów.
7. `PWE_Functions::get_database_fairs_data_files()` pobiera pliki/plan z CAP.
8. `Fair_Plan::create_missing_news_for_files()` tworzy brakujące wpisy roczne.
9. Handler generuje prostą stronę potwierdzenia i kończy request.

## Powiązane pliki

- `includes/class-clear-transients.php`
- `includes/class-functions.php`
- `elements/fair-plan/fair-plan/fair-plan.php`

## Bezpieczeństwo

Wersja 1.8.8 ma sekret handlera zapisany bezpośrednio w źródle i przesyła go w query string. `.wiki` celowo nie reprodukuje wartości. Szczegóły: [../security/review.md](../security/review.md).
