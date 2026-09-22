# `[trade_fair_1stdismantlday]`

**Kategoria:** Daty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_1stdismantlday`  
**Źródło:** `includes/class-shortcodes.php:2709`

## Krótki opis

Zwraca wartość „1stdismantlday” dla bieżących targów, z fallbackami z konfiguracji/shortcodów danych.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_1stdismantlday`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_date_end]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_1nddismantlday`, `trade_fair_enddata`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_1nddismantlday`
- `trade_fair_enddata`

**Inne shortcody:**
- `[pwe_date_end]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
