---
plugin: PWE Elements AutoSwitch
version: 1.8.8
source: uploaded archive
source_commit: null
language: pl
---

# Proces: odczyt danych CAP i cache

## Normalny request

Zgodnie z implementacją `PWE_Functions` dane CAP są odczytywane z priorytetem:

```text
cache statyczny requestu
  -> transient WordPress
  -> plik JSON w uploads/pwe_cache/PWE_Functions
  -> baza CAP
```

Jeżeli request dochodzi do bazy, `connect_database()` próbuje wykorzystać lokalny serwer Cyber_Folks i cache'uje obiekt `wpdb` na czas requestu.

Po skutecznym pobraniu dane mogą zostać zapisane do warstw cache, w tym do trwałego JSON.

## Dlaczego JSON

JSON jest fallbackiem odporniejszym na niedostępność zewnętrznej/centralnej bazy niż sam transient. Dokument cache pamięta typ źródła, argumenty i dane, więc późniejszy proces refresh może odtworzyć konkretny wariant zapytania.

## Ochrona danych cache

- katalog cache otrzymuje `.htaccess` blokujący dostęp,
- zapis odbywa się atomowo przez plik tymczasowy i `rename`,
- struktura zachowuje różnicę obiekt/tablica,
- automatyczne odtwarzanie getterów korzysta z whitelisty metod.

## Wpływ na elementy

Elementy nie muszą znać lokalizacji bazy ani szczegółów cache. Wywołują konkretne gettery `PWE_Functions`, np. dane targów, logotypy, sektory, prelegentów czy pliki planu.
