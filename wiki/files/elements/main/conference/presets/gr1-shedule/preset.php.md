# `elements/main/conference/presets/gr1-shedule/preset.php`

Preset/szablon elementu; zawiera HTML/PHP specyficzny dla grupy/układu i jest dołączany przez metodę `render()` klasy elementu.

## Metadane

- **Kategoria:** `element-preset`
- **Rozmiar:** 15518 B
- **Liczba linii:** 497
- **Źródło:** `elements/main/conference/presets/gr1-shedule/preset.php`

## Funkcje globalne

- `getFairDays()` — linia 7
- `parseDateRange(?string $range)` — linia 38
- `admin_log($message, $type = 'log')` — linia 79
- `output_conference_logs()` — linia 101

## Shortcody wywoływane przez plik

- `[trade_fair_datetotimer]`
- `[trade_fair_enddata]`

## Wybrane zależności wywołań

- `PWE_Functions::assets_per_group()`
- `PWE_Functions::languageChecker()`
- `PWE_Functions::multi_translation()`
- `PWE_Swiper::swiperScripts()`

## Dołączane pliki / wyrażenia include

- `_once plugin_dir_path(__DIR__) . 'gr1/preset.php'`

## Powiązana dokumentacja

- [Symbole tego pliku](../../../../../../symbols/index.md) — indeks klas, metod i funkcji.

## Uwagi do interpretacji

- Lista symboli jest wynikiem tokenizacji PHP i rozróżnia metody klas od funkcji globalnych.
- Wywołania budowane dynamicznie mogą nie być widoczne w zależnościach statycznych.
- Opisy kluczowych przepływów znajdują się w `.wiki/processes/` oraz `.wiki/architecture/`.
