# `[trade_fair_branzowy_eng]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_branzowy_eng`  
**Źródło:** `includes/class-shortcodes.php:2815`

## Krótki opis

Zwraca wartość „branzowy eng” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_branzowy_eng`.
3. Odczytuje ustawienia WordPress: `trade_fair_date_eng`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `trade_fair_date_eng`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
