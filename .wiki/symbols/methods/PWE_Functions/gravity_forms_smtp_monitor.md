# `PWE_Functions::gravity_forms_smtp_monitor()`

**Źródło:** `includes/class-functions.php:4868`  
**Sygnatura:** `public static gravity_forms_smtp_monitor($is_success = null, $to = '', $subject = '', $message = '', $headers = [], $attachments = [], $message_format = '', $from = '', $from_name = '', $bcc = '', $reply_to = '', $entry = false)`

## Krótki opis

Realizuje logikę techniczną związaną z `gravity_forms_smtp_monitor`.

## Wykryte zależności statyczne

### Wywołania statyczne
- `GFAPI::get_form()`

### API WordPress
- `add_action()`
- `get_transient()`
- `set_transient()`
- `delete_transient()`

## Kontekst

- Klasa: [PWE_Functions](../../classes/PWE_Functions.md)
- Plik: [Otwórz dokument pliku](../../../files/includes/class-functions.php.md)

## Uwagi

- Opis zależności jest deterministyczny dla wywołań literalnie widocznych w kodzie. Wywołania dynamiczne/refleksyjne mogą wymagać analizy całego procesu.
