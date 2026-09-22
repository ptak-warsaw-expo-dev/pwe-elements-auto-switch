# `includes/class-elements-data.php`

Plik warstwy rdzeniowej definiujący klasę `PWE_Elements_Data` i jej logikę pomocniczą/integracyjną.

## Metadane

- **Kategoria:** `core`
- **Rozmiar:** 13591 B
- **Liczba linii:** 329
- **Źródło:** `includes/class-elements-data.php`

## Klasy i metody

### `PWE_Elements_Data` — linia 17

- `public static get_all_elements($current_group = null)` — linia 192
- `public static get_all_element_files()` — linia 197
- `public static get_all_components()` — linia 202
- `public static get_page($page)` — linia 207
- `private static get_registry($classes)` — linia 212
- `public static get_file_for_class($class)` — linia 224
- `private static build_class_file_map()` — linia 231
- `private static get_classes_from_file($path)` — linia 255
- `private static previous_significant_token($tokens, $index)` — linia 282
- `private static class_to_slug($class)` — linia 291
- `public static require_class($class)` — linia 296
- `public static require_elements($type)` — linia 316
- `public static get_order_for($class, $group, $type)` — linia 321

## Tabele SQL widoczne statycznie

- `a`

## Dołączane pliki / wyrażenia include

- `_class($class) { if (class_exists($class, false)) return true`
- `_once $path`
- `_elements($type) { foreach (self::get_page($type) as $element) self::require_class($element['class'])`

## Powiązana dokumentacja

- [Symbole tego pliku](../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
