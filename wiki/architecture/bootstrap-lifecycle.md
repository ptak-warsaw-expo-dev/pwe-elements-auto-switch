---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Bootstrap i lifecycle

## 1. Ładowanie pliku głównego

`pwe-elements-auto-switch.php` jest głównym entrypointem WordPress. Po sprawdzeniu `ABSPATH` definiuje stałe ścieżek/języka i ładuje rdzeń wtyczki.

Ładowane są m.in.:

- `includes/class-groups.php`,
- `includes/class-elements-data.php`,
- `includes/class-elements.php`,
- `includes/class-functions.php`,
- `includes/class-hooks.php`,
- `includes/class-shortcodes.php`,
- `includes/class-clear-transients.php`,
- `includes/class-updater.php`,
- `components/menu/menu.php`,
- walidatory telefonu i e-mail,
- `includes/class-registration-log.php`.

## 2. Start singletonu

`PWE_Elements_AutoSwitch::get_instance()` tworzy singleton. Konstruktor:

1. rejestruje anonimowy callback na `init`, który tworzy `PWE_Updater`,
2. wywołuje `PWE_Elements::init()`.

## 3. Rejestracja AutoSwitch

`PWE_Elements::init()`:

1. pobiera aktualną grupę domeny,
2. buduje shortcody stron na podstawie kluczy `PWE_Elements_Data::get_all_elements()`,
3. rejestruje CSS i JS na `wp_enqueue_scripts`,
4. rejestruje shortcode dla każdej strony (`pwe-elements-auto-switch-page-*`),
5. rejestruje odpowiadające bloki WPBakery na `vc_before_init`,
6. iteruje po wszystkich elementach i rejestruje shortcode pojedynczego elementu (`pwe-elements-auto-switch-*`),
7. rejestruje komponenty (`pwe-elements-component-*`) i ich mapowania WPBakery.

## 4. Render requestu

Przy użyciu shortcode strony WordPress wywołuje `PWE_Elements::render_elements($type, $atts)`:

1. ustala grupę domeny,
2. pobiera kompozycję strony,
3. ładuje pliki klas wymaganych dla danego typu strony,
4. odrzuca elementy z `order <= 0`,
5. opcjonalnie wywołuje statyczne `init()` klasy,
6. sprawdza `get_data()['types']`,
7. sortuje elementy po `order`,
8. wywołuje `Class::render($group, $params, $atts)`,
9. opakowuje wynik w kontenery CSS AutoSwitch.

Shortcode pojedynczego elementu przechodzi przez `render_single_element()` i wykonuje podobny proces, ale tylko dla wskazanej klasy.

## 5. Assety

Globalne `assets/style.css` i `assets/script.js` są rejestrowane przez `PWE_Elements`. Jeżeli istnieje `assets/style-<group>.css`, jest dodawany również styl grupowy. Poszczególne elementy mogą dodatkowo ładować assety przez `PWE_Functions::assets_per_element()` i `assets_per_group()`.

## 6. Updater

Na `init` tworzony jest `PWE_Updater`. Jeżeli biblioteka Plugin Update Checker istnieje, updater konfiguruje repozytorium GitHub i może pobrać token z tabeli WordPress `custom_klavio_setup` (`github_secret_2`). Włączone jest pobieranie release assets.
