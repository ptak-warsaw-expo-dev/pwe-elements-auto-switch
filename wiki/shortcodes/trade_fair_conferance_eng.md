# `[trade_fair_conferance_eng]`

**Kategoria:** Konferencja  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_conference_title_eng`  
**Źródło:** `includes/class-shortcodes.php:2683`

## Krótki opis

Zwraca dane konferencji przypisanej do targów.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_conference_title_eng`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_conference_title_en]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_conference_title_eng`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_conference_title_eng`

**Inne shortcody:**
- `[pwe_conference_title_en]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
