# `[trade_fair_main2]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_main2`  
**Źródło:** `includes/class-shortcodes.php:2772`

## Krótki opis

Zwraca wartość „main2” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_main2`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_color_main2]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_main2`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_main2`

**Inne shortcody:**
- `[pwe_color_main2]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
