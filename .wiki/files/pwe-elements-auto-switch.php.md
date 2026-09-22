# `pwe-elements-auto-switch.php`

Główny plik startowy wtyczki: definiuje stałe, ładuje klasy/addony i uruchamia singleton `PWE_Elements_AutoSwitch`.

## Metadane

- **Kategoria:** `bootstrap`
- **Rozmiar:** 2855 B
- **Liczba linii:** 84
- **Źródło:** `pwe-elements-auto-switch.php`

## Klasy i metody

### `PWE_Elements_AutoSwitch` — linia 49

- `public static get_instance()` — linia 61
- `private __construct()` — linia 71

## Rejestracje WordPress wykryte w pliku

- **action:** `init` — linia 73

## Wybrane zależności wywołań

- `PWE_Elements::init()`
- `PWE_Elements_AutoSwitch::get_instance()`

## Tabele SQL widoczne statycznie

- `URI`

## Dołączane pliki / wyrażenia include

- `_once PWE_PLUGIN_PATH . 'includes/class-groups.php'`
- `_once PWE_PLUGIN_PATH . 'includes/class-elements-data.php'`
- `_once PWE_PLUGIN_PATH . 'includes/class-elements.php'`
- `_once PWE_PLUGIN_PATH . 'includes/class-functions.php'`
- `_once PWE_PLUGIN_PATH . 'includes/class-hooks.php'`
- `_once PWE_PLUGIN_PATH . 'includes/class-shortcodes.php'`
- `_once PWE_PLUGIN_PATH . 'includes/class-clear-transients.php'`
- `_once PWE_PLUGIN_PATH . 'includes/class-updater.php'`
- `_once PWE_PLUGIN_PATH . 'components/menu/menu.php'`
- `_once PWE_PLUGIN_PATH . 'addons/phone-validator/phone-validator.php'`
- `_once PWE_PLUGIN_PATH . 'addons/email-validator/email-validator.php'`
- `_once PWE_PLUGIN_PATH . 'includes/class-registration-log.php'`
- `_once PWE_PLUGIN_PATH . 'elements/flip-book/flip-book.php'`

## Powiązana dokumentacja

- [Symbole tego pliku](../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
