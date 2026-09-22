# `[trade_fair_facebook]`

**Kategoria:** Social media  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_facebook`  
**Źródło:** `includes/class-shortcodes.php:2840`

## Krótki opis

Zwraca wartość „facebook” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_facebook`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_facebook]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_facebook`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_facebook`

**Inne shortcody:**
- `[pwe_facebook]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
