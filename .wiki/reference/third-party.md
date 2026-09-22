# Zależności zewnętrzne i integracje

## Bundled Plugin Update Checker

Katalog `plugin-update-checker/` zawiera bibliotekę zewnętrzną służącą do aktualizacji wtyczki z GitHuba. Nie traktujemy jej klas/funkcji jako domenowego API PWE i nie generujemy osobnego pliku dokumentacji dla każdego pliku biblioteki. Warstwa PWE korzystająca z tej biblioteki znajduje się w `includes/class-updater.php`.

## WordPress / WPBakery

- WordPress: hooki, shortcody, options/transients, media/post APIs, enqueue assetów.
- WPBakery: `vc_map()` i shortcody `vc_row`, `vc_column`, `vc_raw_html`.

## Gravity Forms

Wtyczka intensywnie korzysta z `GFAPI`, filtrów `gform_*`, sesji PHP oraz powiadomień Gravity Forms. Procesy są opisane osobno w `processes/`.

## WPML

Kod używa filtrów/akcji WPML m.in. do wykrywania języka i wiązania wpisów PL/EN.

## WP Rocket

Handler czyszczenia cache wywołuje `rocket_clean_domain()`, jeżeli funkcja istnieje.
