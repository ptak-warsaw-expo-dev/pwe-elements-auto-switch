# Klasa `PWE_GF_Email_Entry_Cleanup`

**Źródło:** `includes/class-hooks.php:134`  
**Metody:** 15

## Rola

Klasa `PWE_GF_Email_Entry_Cleanup` jest zdefiniowana w pliku `includes/class-hooks.php`. Jej dokładna rola wynika z metod i zależności poniżej; dla klas należących do elementów/komponentów dodatkowy opis domenowy znajduje się w `.wiki/elements/` lub `.wiki/components/`.

## Metody

- [`public static init()`](../methods/PWE_GF_Email_Entry_Cleanup/init.md) — linia 174
- [`public static maybe_run_automatic_cleanup()`](../methods/PWE_GF_Email_Entry_Cleanup/maybe_run_automatic_cleanup.md) — linia 178
- [`private static normalize_emails($emails)`](../methods/PWE_GF_Email_Entry_Cleanup/normalize_emails.md) — linia 206
- [`private static get_emails()`](../methods/PWE_GF_Email_Entry_Cleanup/get_emails.md) — linia 224
- [`private static get_email_prefixes()`](../methods/PWE_GF_Email_Entry_Cleanup/get_email_prefixes.md) — linia 228
- [`private static get_cleanup_rules_hash($emails, $prefixes)`](../methods/PWE_GF_Email_Entry_Cleanup/get_cleanup_rules_hash.md) — linia 242
- [`private static normalize_domain($domain)`](../methods/PWE_GF_Email_Entry_Cleanup/normalize_domain.md) — linia 246
- [`private static get_current_domain()`](../methods/PWE_GF_Email_Entry_Cleanup/get_current_domain.md) — linia 260
- [`private static is_current_domain_excluded()`](../methods/PWE_GF_Email_Entry_Cleanup/is_current_domain_excluded.md) — linia 270
- [`private static entry_matches_prefix($entry, $email_field_ids, $prefixes)`](../methods/PWE_GF_Email_Entry_Cleanup/entry_matches_prefix.md) — linia 286
- [`private static get_email_field_ids($form)`](../methods/PWE_GF_Email_Entry_Cleanup/get_email_field_ids.md) — linia 304
- [`private static acquire_lock()`](../methods/PWE_GF_Email_Entry_Cleanup/acquire_lock.md) — linia 324
- [`private static release_lock()`](../methods/PWE_GF_Email_Entry_Cleanup/release_lock.md) — linia 333
- [`public static run_cleanup()`](../methods/PWE_GF_Email_Entry_Cleanup/run_cleanup.md) — linia 337
- [`private static save_error_result($message)`](../methods/PWE_GF_Email_Entry_Cleanup/save_error_result.md) — linia 539

## Dokument pliku

- [Otwórz dokumentację `includes/class-hooks.php`](../../files/includes/class-hooks.php.md)
