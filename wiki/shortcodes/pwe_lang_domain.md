# `[pwe_lang_domain]`

**Kategoria:** Dane targów  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `get_lang_domain`  
**Źródło:** `includes/class-shortcodes.php:2888`

## Krótki opis

Zwraca bieżącą domenę/URL uwzględniający język.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `get_lang_domain`.
5. Korzysta z helperów `PWE_Functions`: `lang()`.

## Zależności wykryte statycznie

**PWE_Functions:**
- `PWE_Functions::lang()`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
