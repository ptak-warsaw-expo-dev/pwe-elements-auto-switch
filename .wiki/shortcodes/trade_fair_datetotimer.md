# `[trade_fair_datetotimer]`

**Kategoria:** Daty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_datetotimer`  
**Źródło:** `includes/class-shortcodes.php:2362`

## Krótki opis

Zwraca datę/czas startu targów w formacie używanym przez liczniki.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_datetotimer`.
3. Odczytuje ustawienia WordPress: `trade_fair_datetotimer`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `trade_fair_datetotimer`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
