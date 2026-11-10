<?php
if (!defined('ABSPATH')) exit;

/** Public, stateless import preview. This endpoint never saves guests or sends invitations. */
final class PWE_Exhibitor_Visitor_Import {
    public const MAX_ROWS = 3000;
    public const MAX_BODY_BYTES = 8388608;
    public const MAX_ERRORS = 50;
    private static $initialized = false;

    public static function init() {
        if (self::$initialized) return;
        self::$initialized = true;
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes() {
        register_rest_route('pwe/v1', '/exhibitor-visitors/test', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'handle'],
            // Public validation only. A production endpoint with side effects needs its own authorization.
            'permission_callback' => '__return_true',
        ]);
    }

    public static function endpoint_url() {
        return rest_url('pwe/v1/exhibitor-visitors/test');
    }

    public static function handle($request) {
        if ($request->get_method() !== 'POST') {
            return self::failure('Dozwolona jest tylko metoda POST.', 405);
        }
        // A non-simple header and Origin check prevent ordinary cross-site browser submissions.
        // They are deliberately NOT authentication; this endpoint has no persistent side effects.
        if (trim((string) $request->get_header('x-pwe-import')) !== '1') {
            return self::failure('Brak wymaganego nagłówka X-PWE-Import.', 403);
        }
        $origin = trim((string) $request->get_header('origin'));
        if ($origin !== '' && !self::same_origin($origin, home_url('/'))) {
            return self::failure('Import można wysłać wyłącznie z tej samej witryny.', 403);
        }
        $content_type = strtolower(trim(explode(';', (string) $request->get_header('content-type'), 2)[0]));
        if ($content_type !== 'application/json') {
            return self::failure('Wymagany Content-Type: application/json.', 415);
        }
        $body = (string) $request->get_body();
        if (strlen($body) > self::MAX_BODY_BYTES) {
            return self::failure('Dane JSON przekraczają limit 8 MiB.', 413);
        }
        $payload = json_decode($body, false, 32);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return self::failure('Nieprawidłowy JSON lub kodowanie UTF-8.', 400);
        }
        if (!is_object($payload)) {
            return self::failure('Dane importu muszą być obiektem JSON.', 422);
        }

        $errors = [];
        $error_count = 0;
        if (array_diff(array_keys(get_object_vars($payload)), ['schema_version', 'source', 'file_name', 'guests'])) {
            self::add_error($errors, $error_count, 0, 'schema', 'JSON zawiera nieobsługiwane pola.');
        }
        if (($payload->schema_version ?? null) !== 1) {
            self::add_error($errors, $error_count, 0, 'schema_version', 'Nieobsługiwana wersja struktury JSON.');
        }
        if (($payload->source ?? null) !== 'exhibitor-visitor-generator') {
            self::add_error($errors, $error_count, 0, 'source', 'Nieprawidłowe źródło importu.');
        }
        $file_name = $payload->file_name ?? null;
        if (!is_string($file_name) || self::normalize_spaces($file_name) === '' ||
            self::text_length($file_name) > 255 || preg_match('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{2028}\x{2029}]/u', $file_name)) {
            self::add_error($errors, $error_count, 0, 'file_name', 'Brak poprawnej nazwy pliku (maksymalnie 255 znaków).');
        }
        if (!isset($payload->guests) || !is_array($payload->guests) || count($payload->guests) < 1 || count($payload->guests) > self::MAX_ROWS) {
            self::add_error($errors, $error_count, 0, 'guests', 'Import musi zawierać od 1 do 3000 osób.');
            return self::validation_failure($errors, $error_count);
        }

        $emails = [];
        foreach ($payload->guests as $index => $guest) {
            $row = $index + 2;
            if (!is_object($guest)) {
                self::add_error($errors, $error_count, $row, 'guest', 'Dane osoby muszą być obiektem JSON.');
                continue;
            }
            if (isset($guest->row) && is_int($guest->row) && $guest->row >= 2) {
                $row = $guest->row;
            } else {
                self::add_error($errors, $error_count, $row, 'row', 'Brak poprawnego numeru wiersza pliku.');
            }
            if (array_diff(array_keys(get_object_vars($guest)), ['row', 'full_name', 'inviting_company', 'email'])) {
                self::add_error($errors, $error_count, $row, 'guest', 'Dane osoby zawierają nieobsługiwane pola.');
            }
            foreach (['full_name', 'inviting_company', 'email'] as $field) {
                $value = $guest->$field ?? null;
                $message = self::field_error($field, $value);
                if ($message !== '') {
                    self::add_error($errors, $error_count, $row, $field, $message);
                    continue;
                }
                if ($field === 'email') {
                    $key = strtolower(self::normalize_spaces($value));
                    if (isset($emails[$key])) {
                        self::add_error($errors, $error_count, $row, 'email', 'Adres e-mail powtarza się w wierszu ' . $emails[$key] . '.');
                    } else {
                        $emails[$key] = $row;
                    }
                }
            }
        }
        if ($error_count) return self::validation_failure($errors, $error_count);

        // Intentionally no writes, logging of personal data, Gravity Forms calls or mail.
        return self::response([
            'success' => true,
            'test' => true,
            'received' => count($payload->guests),
            'message' => 'Dane odebrane i poprawne.',
        ]);
    }

    private static function field_error($field, $value) {
        $labels = ['full_name' => 'Imię i nazwisko', 'inviting_company' => 'Firma zapraszająca', 'email' => 'Adres e-mail'];
        $label = $labels[$field];
        if (!is_string($value)) return $label . ': wymagany jest tekst.';
        if (preg_match('/[\x{0000}-\x{001F}\x{007F}-\x{009F}\x{2028}\x{2029}]/u', $value)) return $label . ': usuń znaki sterujące, tabulatory lub podziały wiersza.';
        $value = self::normalize_spaces($value);
        if ($value === '') return $label . ': pole jest wymagane.';
        $limit = $field === 'email' ? 254 : 200;
        if (self::text_length($value) > $limit) return $label . ': maksymalnie ' . $limit . ' znaków.';
        if (strpbrk($value, '<>') !== false) return $label . ': znaki < oraz > są niedozwolone.';

        if ($field === 'full_name') {
            if (!preg_match('/^[\p{L}\p{M} .\'‘’\x{2010}-\x{2015}-]+$/u', $value)) {
                return 'Imię i nazwisko: dozwolone są litery, spacje, apostrofy, łączniki i kropki.';
            }
            $parts = explode(' ', $value);
            if (count($parts) < 2) return 'Podaj imię i nazwisko (co najmniej dwa człony).';
            foreach ($parts as $part) {
                if (!preg_match('/\p{L}/u', $part)) return 'Każdy człon imienia i nazwiska musi zawierać literę.';
            }
        } elseif ($field === 'inviting_company') {
            if (preg_match('/^[=+@]/u', $value)) return 'Firma zapraszająca: wpisz tekst zamiast formuły.';
        } elseif (!self::valid_email($value)) {
            return 'Adres e-mail ma nieprawidłowy format.';
        }
        return '';
    }

    private static function valid_email($email) {
        if (strlen($email) > 254 || substr_count($email, '@') !== 1) return false;
        [$local, $domain] = explode('@', $email, 2);
        if (strlen($local) < 1 || strlen($local) > 64 || $local[0] === '.' || substr($local, -1) === '.' || strpos($local, '..') !== false) return false;
        if (!preg_match('/^[a-zA-Z0-9!#$%&\'*+\/=?^_`{|}~.\-]+$/D', $local)) return false;
        $labels = explode('.', $domain);
        if (count($labels) < 2) return false;
        foreach ($labels as $label) {
            if (strlen($label) < 1 || strlen($label) > 63 || !preg_match('/^[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/D', $label)) return false;
        }
        return (bool) is_email($email);
    }

    private static function normalize_spaces($value) {
        return trim(preg_replace('/[\s\p{Z}\x{FEFF}]+/u', ' ', $value));
    }

    private static function text_length($value) {
        return preg_match_all('/./us', $value);
    }

    private static function same_origin($origin, $site_url) {
        $from = wp_parse_url($origin);
        $site = wp_parse_url($site_url);
        if (!is_array($from) || !is_array($site) || empty($from['scheme']) || empty($from['host']) || empty($site['scheme']) || empty($site['host'])) return false;
        if (isset($from['user']) || isset($from['pass']) || isset($from['query']) || isset($from['fragment']) || !empty($from['path'])) return false;
        $from_scheme = strtolower($from['scheme']);
        $site_scheme = strtolower($site['scheme']);
        if (!in_array($from_scheme, ['http', 'https'], true)) return false;
        $from_port = $from['port'] ?? ($from_scheme === 'https' ? 443 : 80);
        $site_port = $site['port'] ?? ($site_scheme === 'https' ? 443 : 80);
        return $from_scheme === $site_scheme && strtolower($from['host']) === strtolower($site['host']) && $from_port === $site_port;
    }

    private static function add_error(&$errors, &$count, $row, $field, $message) {
        ++$count;
        if (count($errors) < self::MAX_ERRORS) $errors[] = ['row' => $row, 'field' => $field, 'message' => $message];
    }

    private static function validation_failure($errors, $count) {
        return self::response([
            'success' => false,
            'message' => 'Import zawiera błędy. Popraw wskazane wiersze i wczytaj plik ponownie.',
            'errors' => $errors,
            'error_count' => $count,
        ], 422);
    }

    private static function failure($message, $status) {
        return self::response(['success' => false, 'message' => $message, 'errors' => [], 'error_count' => 0], $status);
    }

    private static function response($data, $status = 200) {
        return new WP_REST_Response($data, $status, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
