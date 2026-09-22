# `api/cap/doc.php`

Bezpośredni endpoint HTTP ładowany poza WordPress REST API; plik sam ładuje WordPress i obsługuje żądanie.

## Metadane

- **Kategoria:** `direct-http-endpoint`
- **Rozmiar:** 16310 B
- **Liczba linii:** 655
- **Źródło:** `api/cap/doc.php`

## Funkcje globalne

- `pwe_images_status(string $root, array $assets)` — linia 441
- `pwe_images_send_notification(array $recipients, array $uploadedDetails, string $changedByName = '', string $changedByEmail = '')` — linia 511
- `pwe_images_normalize_files(array $files)` — linia 608
- `pwe_images_cleanup_staged(array $staged)` — linia 634
- `pwe_images_response(array $data, int $code = 200)` — linia 643

## Dołączane pliki / wyrażenia include

- `_once $wpLoad`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
