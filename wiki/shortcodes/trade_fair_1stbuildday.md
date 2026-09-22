# `[trade_fair_1stbuildday]`

**Kategoria:** Daty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_1stbuildday`  
**Źródło:** `includes/class-shortcodes.php:2693`

## Krótki opis

Zwraca wartość „1stbuildday” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_1stbuildday`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_date_start]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_1stbuildday`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_1stbuildday`

**Inne shortcody:**
- `[pwe_date_start]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
