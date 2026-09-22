# `[trade_fair_badge]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_badge`  
**Źródło:** `includes/class-shortcodes.php:2824`

## Krótki opis

Zwraca wartość „badge” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_badge`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_badge]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_badge`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_badge`

**Inne shortcody:**
- `[pwe_badge]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
