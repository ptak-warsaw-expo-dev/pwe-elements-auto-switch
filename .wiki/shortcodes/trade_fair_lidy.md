# `[trade_fair_lidy]`

**Kategoria:** Kontakt / rejestracja  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_lidy`  
**Źródło:** `includes/class-shortcodes.php:2993`

## Krótki opis

Zwraca wartość „lidy” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_lidy`.
3. Wynik jest budowany bezpośrednio przez kod callbacku na podstawie bieżącego kontekstu WordPress/PWE.

## Zależności wykryte statycznie

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
