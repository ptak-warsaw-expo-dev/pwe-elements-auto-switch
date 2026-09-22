---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Hooki, AJAX i prace okresowe

Pełna lista rejestracji wykrytych statycznie znajduje się w [../hooks/index.md](../hooks/index.md) oraz `inventory/hooks.json`.

## Najważniejsze rodziny hooków

- `init` — updater i rejestracja shortcode'ów,
- `wp_enqueue_scripts` — globalne CSS/JS,
- `vc_before_init` — integracja WPBakery,
- `template_redirect` — operacje query-based, m.in. cache/log download,
- `gform_*` — lifecycle Gravity Forms, sesje, walidacja, merge tags, powiadomienia,
- `wp_ajax_*` / `wp_ajax_nopriv_*` — aktualizacja danych rejestracji i sesji,
- filtry SEO/menu/integracji zależne od modułu.

## Cron

W analizowanym kodzie nie znaleziono rejestracji `wp_schedule_event()` jako głównego mechanizmu okresowego dla modułów first-party. Część prac okresowych, np. cleanup entries po e-mailach, jest uruchamiana warunkowo podczas zwykłego requestu i sama zapisuje czas ostatniego/następnego wykonania.
