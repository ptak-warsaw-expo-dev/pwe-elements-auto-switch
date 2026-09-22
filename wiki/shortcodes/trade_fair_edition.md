# `[trade_fair_edition]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_edition`  
**Źródło:** `includes/class-shortcodes.php:2747`

## Krótki opis

Zwraca wartość „edition” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_edition`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_edition]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_edition`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_edition`

**Inne shortcody:**
- `[pwe_edition]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
