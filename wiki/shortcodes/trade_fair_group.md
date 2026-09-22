# `[trade_fair_group]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_group`  
**Źródło:** `includes/class-shortcodes.php:3009`

## Krótki opis

Zwraca grupę targową domeny na podstawie danych CAP.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_group`.
5. Korzysta z helperów `PWE_Functions`: `get_database_groups_data()`.

## Zależności wykryte statycznie

**PWE_Functions:**
- `PWE_Functions::get_database_groups_data()`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
