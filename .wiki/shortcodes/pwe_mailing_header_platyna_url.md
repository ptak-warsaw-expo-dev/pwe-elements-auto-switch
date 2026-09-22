# `[pwe_mailing_header_platyna_url]`

**Kategoria:** Mailing  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_pwe_mailing_header_platyna_url`  
**Źródło:** `includes/class-shortcodes.php:4258`

## Krótki opis

Zwraca absolutny URL nagłówka mailingowego dla wariantu Platyna z fallbackami.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_pwe_mailing_header_platyna_url`.
5. Korzysta z helperów `PWE_Functions`: `lang()`.

## Zależności wykryte statycznie

**PWE_Functions:**
- `PWE_Functions::lang()`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
