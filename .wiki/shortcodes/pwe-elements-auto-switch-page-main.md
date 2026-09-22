# `[pwe-elements-auto-switch-page-main]`

Renderuje pełną kompozycję strony typu **`main`** przez `PWE_Elements::render_elements()`.

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
| 1 | `Header` | — | gr1:1, gr2:1, b2c:1, b2c-new:1, week:1 |
| 2 | `Countdown` | — | gr1:2, gr2:2, b2c:2, b2c-new:2, week:2 |
| 3 | `Combined_Events` | — | gr1:0, gr2:0, b2c:0, b2c-new:0, week:2 |
| 4 | `About` | — | gr1:3, gr2:3, b2c:3, b2c-new:2, week:3.3 |
| 5 | `Tickets` | — | gr1:0, gr2:0, b2c:0, b2c-new:3, week:5 |
| 6 | `Attractions` | — | gr1:0, gr2:0, b2c:0, b2c-new:4, week:5 |
| 7 | `Sectors` | — | gr1:3, gr2:3, b2c:3, b2c-new:8, week:3.1 |
| 8 | `Conference` | — | gr1:4, gr2:5, b2c:4, b2c-new:0, week:2 |
| 9 | `Speakers` | — | gr1:4, gr2:5, b2c:4, b2c-new:0, week:2 |
| 10 | `Guests` | — | gr1:4, gr2:5, b2c:4, b2c-new:6, week:0 |
| 11 | `Premieres` | — | gr1:0, gr2:5, b2c:0, b2c-new:8, week:0 |
| 12 | `Opinions` | — | gr1:13, gr2:12, b2c:5, b2c-new:0, week:0 |
| 13 | `Exhibitors` | — | gr1:7, gr2:4, b2c:6, b2c-new:5, week:3 |
| 14 | `Logotypes` | slug=patrons-partners-international | gr1:8, gr2:7, b2c:0, b2c-new:0, week:4 |
| 15 | `Logotypes` | slug=patrons-partners | gr1:9, gr2:8, b2c:8, b2c-new:6, week:4 |
| 16 | `Statistics` | — | gr1:5, gr2:6, b2c:10, b2c-new:7, week:1 |
| 17 | `Logotypes` | slug=patrons-partners-pwe | gr1:6, gr2:0, b2c:0, b2c-new:0, week:0 |
| 18 | `Halls` | — | gr1:11, gr2:10, b2c:11, b2c-new:0, week:2.2 |
| 19 | `Other_Events` | — | gr1:12, gr2:11, b2c:12, b2c-new:0, week:0 |
| 20 | `Profiles` | — | gr1:10, gr2:9, b2c:13, b2c-new:0, week:0 |
| 21 | `Posts` | — | gr1:14, gr2:13, b2c:14, b2c-new:9, week:0 |
| 22 | `Logotypes` | slug=europe-event | gr1:13, gr2:0, b2c:0, b2c-new:0, week:0 |
| 23 | `Medals` | — | gr1:15, gr2:15, b2c:15, b2c-new:0, week:0 |
| 24 | `Summary` | — | gr1:16, gr2:16, b2c:16, b2c-new:0, week:0 |
| 25 | `Countdown` | — | gr1:0, gr2:17, b2c:0, b2c-new:0, week:0 |
| 26 | `Footer` | — | gr1:999, gr2:999, b2c:999, b2c-new:999, week:999 |

## Źródła

- `includes/class-elements-data.php` — definicja kompozycji.
- `includes/class-elements.php` — rejestracja shortcode i renderowanie.
