# `[trade_fair_exhibitor_generator_badge_url]`

**Kategoria:** Generator wystawców  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_exhibitor_generator_badge_url`  
**Źródło:** `includes/class-shortcodes.php:3371`

## Krótki opis

Zwraca wartość „exhibitor generator badge url” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_exhibitor_generator_badge_url`.
5. Korzysta z helperów `PWE_Functions`: `get_database_fairs_data_files()`.

## Zależności wykryte statycznie

**PWE_Functions:**
- `PWE_Functions::get_database_fairs_data_files()`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
