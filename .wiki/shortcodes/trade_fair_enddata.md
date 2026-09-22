# `[trade_fair_enddata]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_enddata`  
**Źródło:** `includes/class-shortcodes.php:2381`

## Krótki opis

Zwraca datę/czas zakończenia targów.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_enddata`.
3. Odczytuje ustawienia WordPress: `trade_fair_enddata`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `trade_fair_enddata`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
