---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Architektura wtyczki

## Główne warstwy

```text
pwe-elements-auto-switch.php
        |
        +--> PWE_Groups ---------------------> domena -> grupa
        |
        +--> PWE_Elements_Data --------------> rejestr klas + kompozycja stron
        |
        +--> PWE_Elements -------------------> shortcody stron/elementów/komponentów
        |                                      + WPBakery + assety + render
        |
        +--> PWE_Functions ------------------> dane CAP, cache, tłumaczenia, helpery
        |
        +--> PWE_Shortcodes -----------------> shortcody danych targowych
        |
        +--> PWE_Hooks / moduły -------------> integracje WordPress/Gravity Forms
        |
        +--> elementy -----------------------> konkretne sekcje i procesy stron
        |
        +--> endpointy/AJAX -----------------> synchronizacja i operacje formularzy
```

## Bootstrap

Główny plik `pwe-elements-auto-switch.php` definiuje `PWE_PLUGIN_FILE`, `PWE_PLUGIN_PATH`, `PWE_LANG`, ustawia strefę `Europe/Warsaw` i ładuje klasy rdzenia. Następnie singleton `PWE_Elements_AutoSwitch`:

1. dodaje na `init` utworzenie `PWE_Updater`,
2. natychmiast wywołuje `PWE_Elements::init()`.

Dodatkowo bootstrap ładuje walidatory telefonu/e-mail, log rejestracji, komponent menu oraz `Flip_Book` jeśli klasa jeszcze nie istnieje.

## AutoSwitch

Centralna idea wtyczki to **renderowanie tej samej strony inaczej dla różnych grup domen**. `PWE_Groups` pobiera mapę domen z bazy CAP przez `PWE_Functions::get_database_groups_data()`. `PWE_Elements_Data` definiuje skład stron i kolejność elementów per grupa. `PWE_Elements` zamienia te definicje na shortcody i HTML.

## Rejestr elementów

`PWE_Elements_Data` posiada listy nazw klas elementów i komponentów, ale ich ścieżki wykrywa automatycznie: skanuje katalogi `elements/` i `components/`, tokenizuje PHP przez `token_get_all()` i mapuje zadeklarowane klasy na pliki. Dzięki temu definicja strony operuje głównie na nazwach klas.

## Warstwa danych

`PWE_Functions` jest dużą klasą usługową. Odpowiada m.in. za:

- połączenia z bazami CAP,
- pobieranie danych targów, grup, konferencji, logotypów, biletów, prelegentów itd.,
- cache transient + trwały cache JSON,
- tłumaczenia,
- assety elementów/presetów,
- helpery Gravity Forms,
- pliki/grafiki i dane kontaktowe,
- narzędzia diagnostyczne.

Szczegóły: [data-and-cache.md](data-and-cache.md).

## Warstwa prezentacji

Elementy są klasami zlokalizowanymi głównie pod `elements/`. Typowy element posiada `get_data()` i `render()`, a część również `init()` i własne presety. Komponenty z `components/` są rejestrowane podobnie, ale służą jako współdzielone fragmenty.

## Integracje

Najważniejsze wykryte integracje:

- WordPress core,
- WPBakery (`vc_map`, `vc_raw_html`),
- Gravity Forms (`GFAPI`, hooki `gform_*`),
- WPML w synchronizacji newsów,
- WP Rocket przy czyszczeniu cache,
- Uncode (meta/layout i surowy blok WPBakery),
- `custom-element` w przepływach rejestracji,
- Plugin Update Checker / GitHub.

Pełna lista zewnętrznego kodu: [../reference/third-party.md](../reference/third-party.md).
