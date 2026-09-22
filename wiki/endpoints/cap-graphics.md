# CAP Graphics API

**Plik:** `api/cap/doc.php`  
**Adres:** `/wp-content/plugins/pwe-elements-auto-switch/api/cap/doc.php`

## Cel

Synchronizacja i walidacja wymaganych grafik w katalogu `/doc` instalacji WordPress. Ten endpoint nie jest WordPress REST API — sam ładuje `wp-load.php`.

## GET

Zwraca status grafiki z jednej, jawnej whitelisty `$assets`. Dla każdego zasobu kod zna oczekiwaną szerokość, wysokość oraz opcjonalny limit KB. GET nie wymaga klucza API.

## POST

1. Odczytuje `PWE_API_KEY_2` ze stałej lub zmiennej środowiskowej.
2. Wymaga nagłówka `X-PWE-API-Key` i porównuje go przez `hash_equals()`.
3. Przyjmuje `files[]` oraz odpowiadające im `paths[]`.
4. Odrzuca ścieżki spoza whitelisty `$assets`.
5. Weryfikuje rozszerzenie, rozmiar, rzeczywiste wymiary obrazu oraz MIME.
6. Przygotowuje pliki do zapisu i zwraca świeży status zasobów.
7. Po udanym zapisie może wysłać powiadomienie mailowe do listy zdefiniowanej w pliku.

## CORS

Kod ustawia `Access-Control-Allow-Origin: *` oraz obsługuje preflight `OPTIONS`. Zapis nadal wymaga klucza API.

## Bezpieczeństwo

Najważniejszą ochroną ścieżki jest whitelistowanie dokładnych nazw docelowych. Klucz API nie powinien być logowany ani przesyłany w URL; tutaj jest używany jako nagłówek.
