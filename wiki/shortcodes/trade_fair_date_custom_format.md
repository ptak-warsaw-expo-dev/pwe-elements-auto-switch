# `[trade_fair_date_custom_format]`

**Kategoria:** Daty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_date_custom_format`  
**Źródło:** `includes/class-shortcodes.php:2400`

## Krótki opis

Zwraca zakres dat targów sformatowany przez `PWE_Functions::transform_dates()`.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_date_custom_format`.
3. Odczytuje ustawienia WordPress: `trade_fair_date_custom_format`.
5. Korzysta z helperów `PWE_Functions`: `transform_dates()`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `trade_fair_date_custom_format`

**PWE_Functions:**
- `PWE_Functions::transform_dates()`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
