# `[trade_fair_contact_media]`

**Kategoria:** Kontakt / rejestracja  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_contact_media`  
**Źródło:** `includes/class-shortcodes.php:2989`

## Krótki opis

Zwraca pole kontaktowe targów; kod stosuje wartość konfiguracyjną i/lub domyślną wartość z danych grupy CAP.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_contact_media`.
3. Wynik jest budowany bezpośrednio przez kod callbacku na podstawie bieżącego kontekstu WordPress/PWE.

## Zależności wykryte statycznie

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
