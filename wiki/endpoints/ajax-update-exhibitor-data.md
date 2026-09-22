# AJAX `update_exhibitor_data`

**Źródło:** `elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php`

Aktualizuje wpis formularza „Zostań wystawcą” powiązany z bieżącą sesją wystawcy.

## Przepływ

1. Wymaga `$_SESSION['pwe_exhibitor_entry']['entry_id']`.
2. Odnajduje formularz „Zostań wystawcą” i entry przez `GFAPI`.
3. Aktualizuje dozwolone pola według `adminLabel`: m.in. `name`, `nip`, `company`, `area`, pola adresowe.
4. Zapisuje entry.
5. Aktywuje tylko powiadomienie Gravity Forms nazwane `Admin Notification Potwierdzenie - <LANG>` i wywołuje `GFAPI::send_notifications()`.
6. Wysyła nieblokujący POST do `/wp-content/plugins/custom-element/action_handler.php` z `element=gform_after_submission`.
7. Czyści sesję PWE i zwraca JSON success.

Handler jest dostępny również dla niezalogowanych użytkowników (`wp_ajax_nopriv_...`); warunkiem biznesowym jest ważna sesja wystawcy.
