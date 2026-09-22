# `[trade_fair_feed_prefix]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_feed_prefix`  
**Źródło:** `includes/class-shortcodes.php:2831`

## Krótki opis

Zwraca wartość „feed prefix” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_feed_prefix`.
3. Odczytuje ustawienia WordPress: `trade_fair_badge`, `trade_fair_feed_prefix`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `trade_fair_badge`
- `trade_fair_feed_prefix`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
