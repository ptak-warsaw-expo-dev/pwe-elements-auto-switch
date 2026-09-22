# News Sync API

**Plik:** `api/news/index.php`  
**Adres:** `/wp-content/plugins/pwe-elements-auto-switch/api/news/index.php`

## Cel

Synchronizuje wpisy aktualności z zewnętrznego źródła do WordPressa w wersjach PL/EN i spina je przez WPML.

## Autoryzacja

Wymaga `PWE_API_KEY_2`. Kod akceptuje klucz z `?key=` albo nagłówka `X-API-KEY` i porównuje go przez `hash_equals()`.

## Payload

JSON zawiera `action` (`upsert` albo `delete`). Dla `upsert` oczekiwany jest obiekt `post` z `slug` i danymi treści; dla `delete` wymagany jest `slug`.

## Upsert — przepływ

1. Waliduje JSON i slug.
2. Przygotowuje zawartość PL/EN przez `pwe_prepare_uncode_raw_html()`; HTML jest pakowany do shortcode WPBakery `vc_raw_html`.
3. Szuka istniejącego wpisu po slug, a następnie po meta `_pwe_sync_slug`.
4. Tworzy lub aktualizuje polski post, kategorię news i meta `_pwe_sync_slug`.
5. Ustawia informacje językowe WPML dla PL.
6. Pobiera obraz wyróżniający z URL przez `media_sideload_image()` i zapamiętuje jego źródło w `_pwe_source_image_url`.
7. Odnajduje/tworzy wersję EN, zapisuje ją i wiąże z tym samym TRID WPML.
8. Zwraca identyfikatory, URL-e i informacje o obrazach.

## Delete — przepływ

Odnajduje post po slug/meta, ustala tłumaczenie EN przez `wpml_object_id`, usuwa wersję EN, a następnie PL.

## Uwagi bezpieczeństwa

Obsługa klucza przez query string istnieje w kodzie i może powodować ujawnienie klucza w logach/proxy/history. Dla nowych integracji preferowany powinien być wyłącznie nagłówek.
