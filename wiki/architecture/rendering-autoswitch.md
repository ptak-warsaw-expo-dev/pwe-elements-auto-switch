---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# AutoSwitch: grupy, strony i renderowanie

## Ustalenie grupy

`PWE_Groups::groups()` pobiera dane przez `PWE_Functions::get_database_groups_data()` i buduje mapę `fair_group -> fair_domain[]`.

W kodzie startowo znane są grupy:

- `gr1`,
- `gr2`,
- `gr3`,
- `b2c`,
- `b2c-new`,
- `week`.

Jeżeli baza zwróci inny `fair_group`, jest dynamicznie dodawany do mapy.

### Ważna reguła tymczasowa

`PWE_Groups::get_current_group()` zawiera oznaczoną komentarzem `Temporary` regułę: grupa inna niż `gr1`, `b2c-new` i `week` jest zwracana jako `gr2`. Oznacza to, że część wartości z bazy w bieżącej implementacji jest celowo normalizowana do `gr2`.

`PWE_Groups::is_b2c()` traktuje `b2c` i `b2c-new` jako B2C.

## Kompozycja stron

`PWE_Elements_Data::$pages` jest źródłem kompozycji stron. Każdy wpis może zawierać:

- `class` — klasę elementu/komponentu,
- `order` — pozycję per grupa,
- `params` — dodatkowe parametry, np. `slug` dla kilku instancji `Logotypes`.

`order <= 0` oznacza wyłączenie danego elementu w grupie.

Przykładowa strona `main` składa się m.in. z `Header`, `Countdown`, `About`, `Sectors`, `Conference`, `Speakers`, `Exhibitors`, kilku wariantów `Logotypes`, `Statistics`, `Halls`, `Posts`, `Medals`, `Summary` i `Footer`; realny zestaw zależy od grupy i wartości `order`.

Pełna lista stron i elementów: [../reference/groups-and-pages.md](../reference/groups-and-pages.md).

## Renderowanie strony

`PWE_Elements::render_elements()`:

```text
shortcode strony
  -> grupa domeny
  -> definicja PWE_Elements_Data
  -> require klas dla typu strony
  -> init() klas, jeśli istnieje
  -> get_data()['types']
  -> odfiltruj order <= 0
  -> sortuj po order
  -> Class::render(group, params, atts)
  -> HTML #pweElementsAutoSwitch
```

Dla grupy `week` może zostać aktywowany `pwe-theme-white`, ale tylko gdy shortcode strony `main` otrzyma `change_theme_color="true"`. W przeciwnym razie używany jest ciemny theme class.

## Renderowanie pojedynczego elementu

`render_single_element()` wyszukuje wskazaną klasę w rejestrze strony, ładuje odpowiednie pliki, scala domyślne `params` z atrybutami shortcode, wywołuje `init()` jeśli istnieje i następnie `render()`.

## Swiper

Obie ścieżki renderowania próbują dołączyć `assets/<group>/swiper.php`, jeśli plik istnieje i klasa `PWE_Swiper` nie jest jeszcze załadowana.

## WPBakery

Dla stron i pojedynczych elementów `PWE_Elements::init()` rejestruje `vc_map()` na `vc_before_init`. Niektóre strony mają dedykowane parametry edytora, np. `page-main` (B2C/theme), `catalog` (archive year/ids/exhibitor changer) i `flip-book` (PDF URL).
