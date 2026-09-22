# `[sc_pwe_text_store]`

**Kategoria:** SEO / teksty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `sc_pwe_text_store`  
**Źródło:** `includes/class-shortcodes.php:3552`

## Krótki opis

Generuje tekst SEO/UI zależny od nazwy targów, roku i języka.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `sc_pwe_text_store`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[trade_fair_catalog_year]`, `[trade_fair_name]`, `[trade_fair_name_eng]`.

## Zależności wykryte statycznie

**Inne shortcody:**
- `[trade_fair_catalog_year]`
- `[trade_fair_name]`
- `[trade_fair_name_eng]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
