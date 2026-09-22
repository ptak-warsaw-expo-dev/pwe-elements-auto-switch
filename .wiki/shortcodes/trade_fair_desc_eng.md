# `[trade_fair_desc_eng]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_desc_eng`  
**Źródło:** `includes/class-shortcodes.php:2334`

## Krótki opis

Zwraca angielski opis targów.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_desc_eng`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_desc_en]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_desc_eng`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_desc_eng`

**Inne shortcody:**
- `[pwe_desc_en]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
