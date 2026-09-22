---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: aktualizacja wtyczki z GitHub

`PWE_Updater` jest tworzony na hooku WordPress `init` z konstruktora singletonu wtyczki.

## Przepływ

1. `PWE_Updater::__construct()` wywołuje `setup_updater()`.
2. Kod sprawdza obecność `plugin-update-checker/plugin-update-checker.php` i ładuje bibliotekę.
3. `Puc_v4_Factory::buildUpdateChecker()` jest konfigurowany dla repozytorium `ptak-warsaw-expo-dev/pwe-elements-auto-switch`.
4. `get_github_key()` sprawdza tabelę `<prefix>custom_klavio_setup`.
5. Szuka rekordu o `klavio_list_name = github_secret_2` i wykorzystuje `klavio_list_id` jako token.
6. Jeżeli token istnieje, przekazuje go do `setAuthentication()`.
7. `enableReleaseAssets()` włącza pobieranie paczek z GitHub Releases.

## Zależność

Biblioteka `plugin-update-checker/` jest zależnością third-party i nie została opisana plik po pliku w dokumentacji first-party. Zobacz [../reference/third-party.md](../reference/third-party.md).
