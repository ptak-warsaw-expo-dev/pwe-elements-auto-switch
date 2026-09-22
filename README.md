# 🧩 PWE Elements AutoSwitch

Dynamiczne elementy WordPress, które automatycznie dostosowują się do grup domen.
Ta wtyczka pozwala na inteligentne renderowanie komponentów (elementów) w zależności od przypisanej grupy domen, ułatwiając zarządzanie wieloma wersjami stron w ramach jednego motywu.

🚀 Funkcjonalność

PWE Elements AutoSwitch automatycznie przełącza zestawy elementów i ich konfiguracje w zależności od aktualnej domeny lub grupy, do której dana domena należy.

Główne możliwości:

🔁 Dynamiczne przełączanie elementów w zależności od domeny (grupy).

🧱 Integracja z WPBakery Page Builder – automatyczna rejestracja shortcode’ów i bloków.

🪶 Automatyczne ładowanie stylów i skryptów (zarówno wspólnych, jak i specyficznych dla grupy).

⚙️ Łatwe dodawanie nowych elementów poprzez definicję w PWE_Elements_Data.

🧰 Obsługa wielu układów stron (main, catalog i inne).

🔒 Opcjonalna integracja z autoupdaterem GitHub (z prywatnym tokenem API).

📁 Struktura wtyczki
<pre>
pwe-elements-auto-switch/
│
├── pwe-elements-auto-switch.php         # Główny plik wtyczki
├── includes/
│   ├── class-groups.php                 # Definicja grup domen
│   ├── class-elements.php               # Obsługa shortcode’ów, stylów, skryptów
│   ├── class-elements-data.php          # Lista elementów i ich kolejność
│   ├── class-functions.php              # Pomocnicze funkcje
│
├── assets/
│   ├── style.css                        # Główne style
│   ├── style-gr1.css                    # Style specyficzne dla grupy
│   ├── script.js                        # Skrypty JS
│
└── elements/
    ├── main/                            # Elementy strony głównej
    ├── catalog/                         # Elementy katalogu wystawców
</pre>

🧠 Jak to działa

Wtyczka wykrywa domenę i przypisuje ją do jednej z grup zdefiniowanych w PWE_Groups.

Na podstawie grupy wybierane są odpowiednie style, skrypty i zestaw elementów.

Elementy są renderowane według kolejności i typu określonego w PWE_Elements_Data.

Shortcode’y (np. [pwe-elements-auto-switch-page-main]) generują kompletną strukturę strony.

⚙️ Dostępne shortcode’y
Shortcode	Opis	Typ strony
[pwe-elements-auto-switch-page-main]	Renderuje główną stronę wydarzenia z dynamicznymi komponentami	main
[pwe-elements-auto-switch-page-catalog]	Renderuje stronę katalogu wystawców	catalog
...

🧩 Dodawanie nowego elementu

Aby dodać nowy element:

Utwórz plik PHP z klasą np. Hero_Section w katalogu elements/main/hero-section/hero-section.php.

Zdefiniuj w nim metody get_data() i render().

Dodaj wpis w tablicy self::$elements_files w class-elements-data.php, np.:

['class' => 'Hero_Section', 'file' => 'elements/main/hero-section/hero-section.php', 'order' => ['gr1' => 2, 'gr2' => 3, 'b2c' => 2]],

🧑‍💻 Wymagania

WordPress 6.0+

PHP 7.4 lub wyższy

(Opcjonalnie) WPBakery Page Builder

🛠 Instalacja

Pobierz najnowsze wydanie z Releases
.

Wgraj folder pwe-elements-auto-switch do katalogu /wp-content/plugins/.

Aktywuj wtyczkę w panelu WordPress → Wtyczki.

Dodaj shortcode na wybranej stronie.

👨‍💻 Autorzy

PWE Web Developers
https://github.com/ptak-warsaw-expo-dev

## 📜 Licence

Ten projekt jest objęty licencją GPL v2 lub nowszą.
Szczegóły: [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)
