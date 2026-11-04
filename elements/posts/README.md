# Elementy postów

Odtworzone 20.10.2026 z najnowszych dostępnych kopii VS Code i historii edycji.

- `posts-page/` — pusty folder na listę wpisów.
- `post-template/post-template.php` — loader i wybór presetu.
- Presety: `summary`, `summary_v2` (domyślny), `preview`, `medal-ceremony`.
- Każdy preset ma `preset.php`, `assets/style.css` i `assets/{typ}.json`.
- `summary_v2` ma dodatkowo `assets/script.js`.

Rejestracja: `Post_Template` w `$elements` i kompozycja `posts` w `$pages`
w `includes/class-elements-data.php`.

```text
[pwe-elements-auto-switch-post-template template="summary_v2"]
```

Parametr `template` ma pierwszeństwo przed metadaną `_pwe_post_template`.
JSON-y są obecnie wspólne dla wszystkich wpisów danego rodzaju; API nie jest podłączone.

`summary_v2` obsługuje tytuły sekcji z JSON-a, `additional_title`,
`additional_description`, `video_title`, `medal_ceremony_description`.
Tytuł pobrany z YouTube jest osobnym podpisem (`youtube_title`).
PHP dopuszcza `<br>` w tytułach przekazywanych przez `$section_title`.
Starsze pole `medal_ceremony_text` jest zapasowym opisem medali.

## Brakujące pliki źródłowe

Nie odnaleziono oryginalnego `summary_v2/assets/pwe-medal.webp`.
Należy ponownie wgrać tę grafikę pod wskazaną nazwą. Zachowano odwołanie w presecie.
Nie odzyskano pierwotnego referencyjnego HTML-a; działający preset go nie potrzebuje.
