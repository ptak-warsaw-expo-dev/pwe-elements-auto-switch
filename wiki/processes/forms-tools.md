---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces/obszar: narzędzia Forms

Element `Forms` (`elements/forms/forms/forms.php`) jest kontenerem wielu presetów/narzędzi związanych z Gravity Forms. `get_presets()` wykrywa dostępne warianty, a `resolve_group()` wybiera aktywny preset. Render ustawia kontekst tłumaczeń/assetów i dołącza odpowiedni plik.

W drzewie `elements/forms/forms/presets/` znajdują się narzędzia m.in. do:

- list formularzy i wariantów widoku,
- analizy/obsługi entries,
- generowania i pobierania danych JSON,
- kodów QR,
- statystyk i eksportów.

Wtyczka rejestruje także shortcode `[gf_download_autoswitch]`, powiązany z funkcjami download/export.

Eksporty tworzone przez część narzędzi są zapisywane pod uploadami WordPress, w katalogach dedykowanych PWE Forms, z losowymi fragmentami nazw plików i kontrolami dostępu/nonce zależnymi od konkretnego presetu.

Ze względu na wielkość tego modułu szczegóły poszczególnych plików znajdują się w [../files/index.md](../files/index.md), a shortcode w [../shortcodes/gf_download_autoswitch.md](../shortcodes/gf_download_autoswitch.md).
