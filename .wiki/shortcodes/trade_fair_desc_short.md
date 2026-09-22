# `[trade_fair_desc_short]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_desc_short`  
**Źródło:** `includes/class-shortcodes.php:2341`

## Krótki opis

Zwraca skrócony polski opis targów z fallbackiem do pełnego opisu.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_desc_short`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_short_desc_pl]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_desc`, `trade_fair_desc_short`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_desc`
- `trade_fair_desc_short`

**Inne shortcody:**
- `[pwe_short_desc_pl]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
