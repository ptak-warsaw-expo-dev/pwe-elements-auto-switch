---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Tłumaczenia i assety

## Język

Bootstrap definiuje `PWE_LANG` jako pierwsze dwa znaki `determine_locale()`. W wielu miejscach używany jest również `PWE_Functions::lang()`.

Elementy przed renderowaniem często wywołują:

- `PWE_Functions::set_translation_context(element_slug, group, element_type)`,
- `PWE_Functions::multi_translation(key)`.

Warstwa tłumaczeń łączy kontekst elementu/presetu z globalnymi tłumaczeniami i danymi z plików/JSON. Gdy nie można ustalić oczekiwanego języka, część helperów posiada fallback do języka angielskiego.

## Assety globalne

`PWE_Elements::adding_styles()` ładuje:

- `assets/style.css`,
- opcjonalny `assets/style-<group>.css`.

`PWE_Elements::adding_scripts()` ładuje `assets/script.js` z zależnością od jQuery. Wersjonowanie wykorzystuje `filemtime()`, dzięki czemu zmiana pliku zmienia wersję assetu.

## Assety per element / per grupa

Elementy wywołują helpery `PWE_Functions::assets_per_element()` i `assets_per_group()`. Pozwala to ładować zasoby specyficzne dla elementu oraz jego wariantu/presetu.

## Presety

Wiele klas elementów zwraca w `get_data()` mapę `presets`, a `render()` wybiera odpowiedni `preset.php`. Przykładowo `Registration_Visitors` ma warianty `standard`, `premium`, `byli` i `platyna` wybierane na podstawie `utm_source`.
