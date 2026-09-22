# `[trade_fair_date]`

**Kategoria:** Daty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_date`  
**Źródło:** `includes/class-shortcodes.php:2414`

## Krótki opis

Zwraca polską reprezentację dat targów.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_date`.
3. Odczytuje ustawienia WordPress: `trade_fair_date`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `trade_fair_date`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
