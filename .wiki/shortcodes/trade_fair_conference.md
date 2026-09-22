# `[trade_fair_conference]`

**Kategoria:** Konferencja  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_conference`  
**Źródło:** `includes/class-shortcodes.php:2663`

## Krótki opis

Zwraca dane konferencji przypisanej do targów.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_conference`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_conference_name]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_conference`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_conference`

**Inne shortcody:**
- `[pwe_conference_name]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
