---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: renderowanie strony AutoSwitch

## Cel

Zamiana jednego shortcode'a strony, np. `[pwe-elements-auto-switch-page-main]`, na zestaw sekcji właściwy dla konkretnej domeny/grupy.

## Przepływ krok po kroku

1. `PWE_Elements::init()` rejestruje shortcode na podstawie klucza strony z `PWE_Elements_Data::$pages`.
2. WordPress wywołuje callback shortcode'a.
3. Callback przechodzi do `PWE_Elements::render_elements($type, $atts)`.
4. `PWE_Groups::get_current_group()` ustala grupę na podstawie bieżącego `HTTP_HOST` i danych z CAP.
5. `PWE_Elements_Data::get_all_elements()` zwraca konfigurację strony.
6. `PWE_Elements_Data::require_elements($type, $group)` ładuje klasy potrzebne do strony.
7. Dla każdego wpisu pobierany jest `order` dla aktualnej grupy; `order <= 0` wyłącza element.
8. Jeśli klasa posiada `init()`, metoda jest wywoływana przed renderem.
9. `Class::get_data()` potwierdza, czy element wspiera bieżący typ strony.
10. Aktywne elementy są sortowane rosnąco po `order`.
11. Każdy element otrzymuje `group`, własne `params` i atrybuty shortcode'a (`atts`).
12. Wyniki są opakowane w `#pweElementsAutoSwitch` oraz kontenery klas CSS `pwe-element-auto-switch`.

## Zależności

- `includes/class-groups.php`
- `includes/class-elements-data.php`
- `includes/class-elements.php`
- klasy z `elements/` i `components/`
- `PWE_Functions` do danych, assetów i tłumaczeń

## Warianty

- `page-main` ma opcje B2C i theme `week`.
- `catalog` przyjmuje parametry archiwum oraz `exhibitor_changer`.
- `flip-book` przyjmuje `pdf_url`.
- pojedyncze elementy mogą być renderowane niezależnie przez shortcode `pwe-elements-auto-switch-<slug>`.
