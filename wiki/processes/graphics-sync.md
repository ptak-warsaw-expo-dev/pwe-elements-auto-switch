---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: synchronizacja grafik CAP

Punkt wejścia: `api/cap/doc.php`.

Endpoint operuje na jawnej whiteliście plików graficznych wymaganych w katalogu `/doc` instalacji WordPress.

## GET

GET zwraca status znanych assetów i nie wymaga klucza API.

## POST

1. Ustalenie oczekiwanego `PWE_API_KEY_2` ze stałej lub środowiska.
2. Sprawdzenie nagłówka `X-PWE-API-Key` przez `hash_equals()`.
3. Odczyt `files[]` i odpowiadających im `paths[]`.
4. Sprawdzenie, czy każda ścieżka znajduje się na whiteliście.
5. Walidacja rozszerzenia/MIME, rozmiaru, szerokości i wysokości zgodnie z definicją assetu.
6. Zapis zaakceptowanych plików.
7. Zwrócenie bieżącego statusu assetów i ewentualne powiadomienie mailowe.

Endpoint obsługuje `OPTIONS` i ustawia CORS `*`; autoryzacja zapisu nadal opiera się na nagłówku API.

Pełny opis: [../endpoints/cap-graphics.md](../endpoints/cap-graphics.md).
