# `includes/class-updater.php`

Plik warstwy rdzeniowej definiujący klasę `PWE_Updater` i jej logikę pomocniczą/integracyjną.

## Metadane

- **Kategoria:** `core`
- **Rozmiar:** 1775 B
- **Liczba linii:** 64
- **Źródło:** `includes/class-updater.php`

## Klasy i metody

### `PWE_Updater` — linia 7

- `public __construct()` — linia 9
- `private get_github_key()` — linia 18
- `private setup_updater()` — linia 42

## Wybrane zależności wywołań

- `Puc_v4_Factory::buildUpdateChecker()`

## Tabele SQL widoczne statycznie

- `mechanism`
- `the`

## Dołączane pliki / wyrażenia include

- `_once $checker_file`

## Powiązana dokumentacja

- [Symbole tego pliku](../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
