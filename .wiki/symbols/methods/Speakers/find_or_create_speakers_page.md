# `Speakers::find_or_create_speakers_page()`

**Źródło:** `elements/main/speakers/speakers.php:202`  
**Sygnatura:** `private static find_or_create_speakers_page(string $title, string $slug, string $content)`

## Krótki opis

Wyszukuje or create speakers page zgodnie z logiką implementacji.

## Wykryte zależności statyczne

### Wywołania statyczne
- `self::find_page_by_slug()`

### API WordPress
- `wp_insert_post()`
- `wp_update_post()`

## Kontekst

- Klasa: [Speakers](../../classes/Speakers.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/main/speakers/speakers.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
