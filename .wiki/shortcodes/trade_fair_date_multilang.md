# `[trade_fair_date_multilang]`

**Kategoria:** Daty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_date_multilang`  
**Źródło:** `includes/class-shortcodes.php:2442`

## Krótki opis

Zwraca datę targów w języku wskazanym parametrem `lang` lub wykrytym automatycznie.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_date_multilang`.
3. Odczytuje ustawienia WordPress: `trade_fair_date_`.
5. Korzysta z helperów `PWE_Functions`: `lang()`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `trade_fair_date_`

**PWE_Functions:**
- `PWE_Functions::lang()`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
