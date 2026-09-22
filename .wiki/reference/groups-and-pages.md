---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Grupy domen i strony AutoSwitch

## Grupy

`PWE_Groups` startuje z kluczami `gr1`, `gr2`, `gr3`, `b2c`, `b2c-new`, `week` i uzupełnia je danymi `fair_group/fair_domain` z CAP.

W bieżącym kodzie `get_current_group()` posiada regułę tymczasową, która zwraca `gr2` dla wszystkich dopasowanych grup innych niż `gr1`, `b2c-new` i `week`. Dlatego sama obecność np. `gr3` lub `b2c` w konfiguracji nie oznacza, że ta wartość dotrze do rendererów bez normalizacji.

## Typy stron z `PWE_Elements_Data::$pages`

- `main`
- `catalog`
- `flip-book`
- `speakers`
- `registration-visitors`
- `registration-exhibitors`
- `contact`
- `potential-exhibitors`
- `medal-ceremony`
- `fair-plan`
- `exhibitor-visitor-generator`
- `exhibitor-worker-generator`
- `confirmation-visitors-registration`
- `confirmation-exhibitors-registration`
- `badge-local`
- `call-center`
- `step2`
- `forms`
- `conferences`
- `layout`

Każdy klucz automatycznie otrzymuje shortcode `pwe-elements-auto-switch-page-<klucz>`.

## Strona `main`

`main` jest szczególną kompozycją wieloelementową. Definiuje indywidualny `order` dla grup i może zawierać kilka wystąpień tej samej klasy z różnymi `params` (np. `Logotypes` z różnymi `slug`). Wartość `0` wyłącza wpis dla danej grupy; `Footer` ma wysoki `order` i jest renderowany na końcu.

Dokładna konfiguracja maszynowa znajduje się w [../inventory/pages.json](../inventory/pages.json), a poszczególne klasy w [../elements/index.md](../elements/index.md).
