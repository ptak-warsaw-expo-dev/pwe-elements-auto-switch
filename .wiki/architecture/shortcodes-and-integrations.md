---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Shortcody i integracje tekstowe

## PWE_Shortcodes

`includes/class-shortcodes.php` tworzy instancję `PWE_Shortcodes` i na `init` (priorytet 20) rejestruje główną mapę shortcode'ów danych targowych.

Wersja 1.8.8 zawiera dużą statyczną mapę callbacków, obejmującą m.in. nazwę/opisy targów, daty, edycję, dane kontaktowe, social media, katalogi, teksty CTA, dane generatorów i informacje mailingowe.

Każdy shortcode ma osobny dokument w [../shortcodes/index.md](../shortcodes/index.md).

## Źródła wartości

W callbackach występują trzy główne strategie:

1. delegowanie do shortcode'a dostawcy danych (`[pwe_*]`), jeśli jest dostępny,
2. pobieranie wartości z opcji WordPress,
3. pobieranie danych przez `PWE_Functions` z CAP/cache.

Część callbacków generuje wynik złożony (np. sformatowane daty, teksty SEO, ikony, adresy URL), zamiast zwracać pojedynczą wartość.

## Gravity Forms

Klasa mapuje część tych samych wartości również do merge tags Gravity Forms i modyfikuje treści/powiadomienia przez filtry `gform_*`. Dzięki temu dane targowe używane w shortcode'ach mogą być dostępne także w formularzach i mailach.

## Yoast SEO

Wtyczka posiada integrację z filtrami Yoast i może zastępować własne znaczniki/shortcody w treściach SEO.

## Dynamiczne URL-e multilingual

Wtyczka ładuje mapę URL z `assets/website-translation.json`. Na jej podstawie tworzone są shortcody `url_<klucz>`. Jeżeli dostępna jest osobna wtyczka multilingual, mapa może zostać nadpisana jej danymi. Callback uwzględnia język/ścieżkę oraz parametr `absolute=true`.

## Shortcody AutoSwitch

Niezależnie od `PWE_Shortcodes`, `PWE_Elements::init()` dynamicznie rejestruje trzy rodziny shortcode'ów:

- `pwe-elements-auto-switch-page-*` — kompletne strony,
- `pwe-elements-auto-switch-*` — pojedyncze elementy,
- `pwe-elements-component-*` — komponenty współdzielone.

Dlatego lista w `.wiki/shortcodes/` łączy zarówno dane tekstowe, jak i dynamiczną warstwę renderującą.
