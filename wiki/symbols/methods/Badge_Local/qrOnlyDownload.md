# `Badge_Local::qrOnlyDownload()`

**Źródło:** `elements/badge-local/badge-local/badge-local.php:155`  
**Sygnatura:** `public static qrOnlyDownload($badge_form_id)`

## Krótki opis

Realizuje logikę techniczną związaną z `qrOnlyDownload`.

## Wykryte zależności statyczne

### Wywołania statyczne
- `GFAPI::add_entry()`
- `GFAPI::get_entry()`
- `GFAPI::get_feeds()`
- `ZipArchive::close()`
- `self::pwe_download_temp_qr()`

### Shortcody wywoływane
- `[trade_fair_badge]`

### API WordPress
- `do_action()`

## Kontekst

- Klasa: [Badge_Local](../../classes/Badge_Local.md)
- Plik: [Otwórz dokument pliku](../../../files/elements/badge-local/badge-local/badge-local.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
