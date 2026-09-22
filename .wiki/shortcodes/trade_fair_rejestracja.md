# `[trade_fair_rejestracja]`

**Kategoria:** Kontakt / rejestracja  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_rejestracja`  
**Źródło:** `includes/class-shortcodes.php:2939`

## Krótki opis

Zwraca wartość „rejestracja” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_rejestracja`.
3. Odczytuje ustawienia WordPress: `trade_fair_rejestracja`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `trade_fair_rejestracja`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
