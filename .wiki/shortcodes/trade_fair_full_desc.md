# `[trade_fair_full_desc]`

**Kategoria:** SEO / teksty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `sc_pwe_trade_fair_full_desc`  
**Źródło:** `includes/class-shortcodes.php:3396`

## Krótki opis

Zwraca rozbudowany opis targów używany również w integracji SEO.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `sc_pwe_trade_fair_full_desc`.
3. Odczytuje ustawienia WordPress: `pwe_general_options`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
