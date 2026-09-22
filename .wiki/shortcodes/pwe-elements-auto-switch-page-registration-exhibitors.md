# `[pwe-elements-auto-switch-page-registration-exhibitors]`

Renderuje pełną kompozycję strony typu **`registration-exhibitors`** przez `PWE_Elements::render_elements()`.

## Przepływ

1. `PWE_Elements::init()` buduje shortcode automatycznie z klucza strony w `PWE_Elements_Data::$pages`.
2. `render_elements()` ustala bieżącą grupę przez `PWE_Groups::get_current_group()`.
3. Ładuje klasy przypisane do typu strony przez `PWE_Elements_Data::require_elements()`.
4. Odrzuca elementy z kolejnością `<= 0`, inicjalizuje klasy posiadające `init()` i sprawdza `get_data()[types]`.
5. Sortuje aktywne elementy wg pola `order` dla bieżącej grupy.
6. Wywołuje statyczne `render($group, $params, $atts)` każdej klasy i opakowuje wynik w wrapper AutoSwitch.

## Skład strony

| # | Klasa | Parametry | Aktywność wg grupy |
|---:|---|---|---|
| 1 | `Registration_Exhibitors` | — | domyślnie 1 |
| 2 | `Footer` | — | domyślnie 1 |

## Źródła

- `includes/class-elements-data.php` — definicja kompozycji.
- `includes/class-elements.php` — rejestracja shortcode i renderowanie.
