# `Flip_Book`

**Typ:** element AutoSwitch  
**Plik:** `elements/flip-book/flip-book.php`  
**Klasa:** `Flip_Book` (linia 11)  
**Shortcode:** `[pwe-elements-auto-switch-flip-book]`

## Typy stron

- `flip-book`

## Mechanizm renderowania

1. `PWE_Elements` rejestruje shortcode dynamicznie na podstawie klasy.
2. Przy wywołaniu ładowana jest klasa `Flip_Book` i uruchamiana jest jej metoda `render()`.
3. Klasa ustawia kontekst tłumaczeń oraz ładuje assety wspólne i/lub presetowe, jeśli robi to w swoim `render()`.
4. Jeżeli element używa presetów, `render()` wybiera plik `preset.php` odpowiadający grupie/wariantowi i dołącza go.
5. Wynik jest zwracany w wrapperze `pwe-element-auto-switch` generowanym przez `PWE_Elements`.

## Metody

- `Flip_Book::init()`
- `Flip_Book::__construct()`
- `Flip_Book::get_data()`

## Główne zależności

- `PWE_Functions::assets_per_element()`

## Powiązane źródła

- [Dokument pliku](../files/elements/flip-book/flip-book.php.md)
