---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# PWE Elements AutoSwitch — dokumentacja techniczna

Dokumentacja została przygotowana na podstawie **rzeczywistego kodu wersji 1.8.8** z przesłanego archiwum. Katalog `.wiki/` jest oddzielną warstwą wiedzy: nie zmienia działania wtyczki i może zostać commitowany razem z repozytorium, aby prywatna aplikacja Wiki mogła go indeksować.

## Co robi wtyczka

`PWE Elements AutoSwitch` buduje strony wydarzeń WordPress z dynamicznych elementów zależnych od bieżącej domeny/grupy targowej. Wtyczka:

- wykrywa grupę domeny (`gr1`, `gr2`, `gr3`, `b2c`, `b2c-new`, `week` oraz ewentualne grupy zwracane z bazy),
- rejestruje strony, elementy i komponenty jako shortcody,
- integruje te shortcody z WPBakery,
- dobiera kolejność i aktywność elementów per grupa,
- ładuje wspólne i grupowe assety,
- pobiera dane biznesowe z baz CAP i posiada wielowarstwowy cache,
- dostarcza własny system shortcode'ów danych targowych,
- obsługuje procesy rejestracji odwiedzających i wystawców oparte o Gravity Forms,
- udostępnia kilka handlerów HTTP/AJAX do synchronizacji, aktualizacji formularzy i narzędzi administracyjnych,
- aktualizuje się z GitHub przy pomocy Plugin Update Checker.

## Najważniejsze punkty wejścia

| Obszar | Dokument |
|---|---|
| Architektura | [architecture/overview.md](architecture/overview.md) |
| Bootstrap i lifecycle | [architecture/bootstrap-lifecycle.md](architecture/bootstrap-lifecycle.md) |
| AutoSwitch / renderowanie | [architecture/rendering-autoswitch.md](architecture/rendering-autoswitch.md) |
| Dane CAP i cache | [architecture/data-and-cache.md](architecture/data-and-cache.md) |
| Shortcody | [shortcodes/index.md](shortcodes/index.md) |
| Endpointy i AJAX | [endpoints/index.md](endpoints/index.md) |
| Hooki WordPress | [hooks/index.md](hooks/index.md) |
| Elementy | [elements/index.md](elements/index.md) |
| Komponenty | [components/index.md](components/index.md) |
| Procesy biznesowe | [processes/index.md](processes/index.md) |
| Pliki PHP | [files/index.md](files/index.md) |
| Przegląd bezpieczeństwa | [security/review.md](security/review.md) |
| Zależności zewnętrzne | [reference/third-party.md](reference/third-party.md) |
| Tabele CAP | [reference/database-tables.md](reference/database-tables.md) |
| Grupy i strony | [reference/groups-and-pages.md](reference/groups-and-pages.md) |

## Inwentaryzacja wersji 1.8.8

- wszystkie pliki archiwum: **600**,
- pliki PHP first-party objęte dokumentacją: **194**,
- wykryte klasy first-party: **66**,
- wykryte metody klas: **529**,
- wykryte funkcje globalne: **63**,
- wpisy shortcode udokumentowane w `.wiki/shortcodes/`: **175**,
- elementy AutoSwitch: **41**,
- komponenty: **7**,
- udokumentowane endpointy/handlery HTTP: **7**,
- brak użycia `register_rest_route()` w kodzie wersji 1.8.8.

Maszynowe indeksy znajdują się w `.wiki/inventory/` i są przeznaczone do późniejszego importu do Developer Wiki.

## Jak czytać dokumentację

Dokumenty dzielą informacje na dwa rodzaje:

1. **Fakty strukturalne** — rejestracje hooków/shortcode'ów, nazwy klas/metod, pliki, zależności i wywołania możliwe do wskazania bezpośrednio w kodzie.
2. **Opis przepływu** — uporządkowane wyjaśnienie zachowania wynikające z analizy kodu.

Dokumentacja nie zastępuje kodu jako źródła prawdy. Jeżeli wtyczka zostanie zmieniona, Wiki powinna porównać wersję/commit dokumentacji z bieżącym kodem i oznaczyć opis jako wymagający odświeżenia.
