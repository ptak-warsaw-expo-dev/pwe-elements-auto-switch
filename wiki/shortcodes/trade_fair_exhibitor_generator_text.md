# `[trade_fair_exhibitor_generator_text]`

**Kategoria:** Generator wystawców  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_exhibitor_generator_text`  
**Źródło:** `includes/class-shortcodes.php:3294`

## Krótki opis

Zwraca wartość „exhibitor generator text” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_exhibitor_generator_text`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[trade_fair_group]`.

## Zależności wykryte statycznie

**Inne shortcody:**
- `[trade_fair_group]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
