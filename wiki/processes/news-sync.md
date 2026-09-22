---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: synchronizacja newsów PL/EN

Punkt wejścia: `api/news/index.php`.

To bezpośredni endpoint PHP (nie WordPress REST API), który ładuje środowisko WordPress i przyjmuje JSON.

## Autoryzacja

Wymagany jest `PWE_API_KEY_2`. Kod wersji 1.8.8 akceptuje klucz zarówno w nagłówku `X-API-KEY`, jak i query string `?key=`.

## `action=upsert`

1. Walidacja JSON i `slug`.
2. Przygotowanie treści PL/EN przez helper, który pakuje HTML w WPBakery `vc_raw_html`.
3. Wyszukanie istniejącego postu po slug, a następnie `_pwe_sync_slug`.
4. Utworzenie/aktualizacja polskiego wpisu i kategorii.
5. Zapis `_pwe_sync_slug`.
6. Ustawienie danych językowych WPML.
7. Pobranie obrazka przez WordPress media sideload i zapis `_pwe_source_image_url`.
8. Wyszukanie lub utworzenie wersji EN.
9. Połączenie tłumaczenia EN z tym samym TRID WPML.
10. Odpowiedź JSON z rezultatami.

## `action=delete`

1. Wyszukanie postu po slug/meta.
2. Ustalenie tłumaczenia EN przez WPML.
3. Usunięcie EN, a następnie PL.

## Ryzyko

Obsługa klucza w URL może prowadzić do zapisu sekretu w logach serwera/proxy/historii. Dla nowych klientów powinien być preferowany nagłówek. Dokument endpointu: [../endpoints/news-sync.md](../endpoints/news-sync.md).
