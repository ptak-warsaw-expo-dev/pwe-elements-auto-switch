# Handler `pwe_clear_transients`

**Źródło:** `includes/class-clear-transients.php`

Handler `template_redirect` uruchamiany, gdy query parameter `pwe_clear_transients` odpowiada stałej `PWE_CLEAR_TOKEN`.

## Przepływ

1. Czyści cache WP Rocket, jeśli funkcja `rocket_clean_domain()` jest dostępna.
2. Wymusza odświeżenie JSON cache z bazy CAP przez `PWE_Functions::refresh_database_json_cache()` (oraz analogiczny helper z innej wtyczki, jeśli istnieje).
3. Usuwa z `wp_options` wszystkie transients o prefiksie `pwe_`.
4. Aktualizuje/zakłada strony i wpisy planów targów przez `Fair_Plan`.
5. Renderuje prostą stronę potwierdzenia i kończy request.

## Ryzyko

Token jest zdefiniowany bezpośrednio w kodzie źródłowym i przesyłany w URL. W dokumentacji nie reprodukujemy jego wartości. Zalecane jest przeniesienie sekretu do konfiguracji środowiska i/lub zastosowanie uwierzytelnienia WordPress oraz nonce.
