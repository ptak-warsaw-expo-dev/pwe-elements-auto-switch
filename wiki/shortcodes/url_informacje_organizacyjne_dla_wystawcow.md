# `[url_informacje_organizacyjne_dla_wystawcow]`

**Typ:** dynamiczny shortcode URL  
**Callback:** `PWE_Shortcodes::show_multilang_url()`  
**Źródło danych:** `assets/website-translation.json` (nadpisywane przez `pwe-multilang/website-translation.json`, jeżeli istnieje)

## Krótki opis

Zwraca URL strony opisanej kluczem `informacje_organizacyjne_dla_wystawcow` w języku bieżącym lub podanym przez atrybut `lang`. Może zwrócić adres absolutny po ustawieniu `absolute=true`.

## Przepływ

1. `register_url_shortcodes()` pobiera klucze z połączonych danych URL i rejestruje shortcode `url_<klucz>`.
2. `show_multilang_url()` usuwa prefiks `url_` i odnajduje wpis w danych JSON.
3. Język jest ustalany z atrybutu `lang` lub automatycznie.
4. Dla względnych URL-i dodawany jest prefiks językowy dla języków innych niż polski, z ochroną przed duplikacją prefiksu.
5. Atrybut `absolute=true` przepuszcza ścieżkę przez `home_url()`.

## Wybrane wartości z pliku zapasowego

- `pl` → `/informacje-organizacyjne-dla-wystawcow/` (Informacje organizacyjne dla Wystawców)
- `en` → `/organizational-information-for-exhibitors/` (Organizational Information for Exhibitors)
- `de` → `/organisatorische-informationen-fur-aussteller/` (Organisatorische Informationen für Aussteller)
- `uk` → `/organizacijna-informacija-dlya-eksponentiv/` (Організаційна інформація для експонентів)

## Obsługiwane języki

`cs`, `de`, `en`, `es`, `et`, `fr`, `hu`, `it`, `lt`, `lv`, `pl`, `ro`, `sk`, `uk`
