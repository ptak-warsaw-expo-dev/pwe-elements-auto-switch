# `[trade_fair_exhibitor_generator_header_url]`

**Kategoria:** Generator wystawców  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_exhibitor_generator_header_url`  
**Źródło:** `includes/class-shortcodes.php:3332`

## Krótki opis

Zwraca wartość „exhibitor generator header url” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_exhibitor_generator_header_url`.
5. Korzysta z helperów `PWE_Functions`: `get_database_fairs_data_files()`, `lang()`.

## Zależności wykryte statycznie

**PWE_Functions:**
- `PWE_Functions::get_database_fairs_data_files()`
- `PWE_Functions::lang()`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
