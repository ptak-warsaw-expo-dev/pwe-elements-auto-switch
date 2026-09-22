# `[sc_pwe_trade_fair_conference_title]`

**Kategoria:** SEO / teksty  
**Rejestracja:** `PWE_Shortcodes::register_shortcodes()`  
**Callback:** `show_trade_fair_conference_title (pl) / show_trade_fair_conference_title_eng (inne)`  
**Źródło:** `includes/class-shortcodes.php:2673`

## Krótki opis

Zwraca tytuł konferencji dopasowany do języka strony.

## Jak działa

1. Shortcode jest rejestrowany podczas hooka `init` (priorytet 20) przez `PWE_Shortcodes::register_shortcodes()`.
2. WordPress wywołuje callback `show_trade_fair_conference_title (pl) / show_trade_fair_conference_title_eng (inne)`.
3. Callback korzysta z innych shortcode’ów jako źródła/fallbacku: `[pwe_conference_title_pl]`.
4. Odczytuje ustawienia WordPress: `pwe_general_options`, `trade_fair_conference_title`.

## Zależności wykryte statycznie

**Opcje WordPress:**
- `pwe_general_options`
- `trade_fair_conference_title`

**Inne shortcody:**
- `[pwe_conference_title_pl]`

## Uwagi

- Dokument opisuje implementację w wersji 1.8.8 z przesłanego archiwum.
- Szczegółowy kod callbacku można otworzyć w Code Explorerze po ścieżce wskazanej powyżej.
