<?php
if (!defined('ABSPATH')) exit;

define('PWE_FORMS_DOWNLOAD_ACTION', 'pwe_forms_download');
define('PWE_FORMS_NONCE_ACTION', 'pwe_forms_tools');

class Forms {

    public static function get_data() {
        return [
            'types' => ['forms'],
            'presets' => self::get_presets(),
        ];
    }

    public static function get_presets(): array {
        $presets = [];
        $preset_dir = plugin_dir_path(__FILE__) . 'presets/';
        foreach (glob($preset_dir . '*/preset.php') ?: [] as $preset_file) {
            $preset_slug = basename(dirname($preset_file));
            if ($preset_slug !== '') {
                $presets[$preset_slug] = $preset_file;
            }
        }

        return $presets;
    }

    private static function resolve_group(string $group = ''): string {
        $data = self::get_data();
        $group = sanitize_key($group);

        if (isset($_GET['token'])) {
            $group = sanitize_key(wp_unslash($_GET['token']));
        }

        if ($group === '' || !isset($data['presets'][$group])) {
            return 'forms';
        }

        return $group;
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'forms';

        $group = self::resolve_group((string) $group);

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $lang = PWE_Functions::lang();
            $forms = class_exists('GFAPI') ? GFAPI::get_forms(null, false) : [];
            $post_id = get_queried_object_id();
            $url = '';
            $access = $post_id ? pwe_forms_hidden_access_fields($post_id) : '';
            $languages = $forms ? pwe_forms_language_map($forms) : [];
            $mode = in_array($group, ['json', 'csv'], true) ? 'stats' : $group;
            $preselected_form_ids = preg_match('/^\d+(?:,\d+)*$/', $group) ? array_map('absint', explode(',', $group)) : [];

            if (!$post_id || post_password_required($post_id)) {
                return '';
            }

            /* <-------------> General code end <-------------> */

            $export_snippet = pwe_forms_handle_export($post_id);

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($export_snippet . $output);
            }
        }

        return '';
    }
}

if (!class_exists('FormsDownload')) {
    class_alias('Forms', 'FormsDownload');
}

function pwe_forms_nav_items(): array {
    return [
        'forms'    => 'Formularze',
        'forms-v2' => 'Formularze v2',
        'entries'  => 'Skaner QR',
        'bulk'     => 'Eksport JSON',
        'qrcode'   => 'Kody QR',
        'stats'    => 'Statystyki',
    ];
}

function pwe_forms_render_nav(string $active): string {
    if (!current_user_can('administrator')) {
        return '';
    }

    $base_url = remove_query_arg('token');
    $output = '<nav class="pwe-gf-tool__tabs">';
    foreach (pwe_forms_nav_items() as $slug => $label) {
        $link = $slug === 'forms' ? $base_url : add_query_arg('token', $slug, $base_url);
        $class = $slug === $active ? ' class="active"' : '';
        $output .= '<a href="' . esc_url($link) . '"' . $class . '>' . esc_html($label) . '</a>';
    }
    return $output . '</nav>';
}

function pwe_forms_hidden_access_fields(int $post_id): string {
    return sprintf(
        '<input type="hidden" name="source_post_id" value="%d"><input type="hidden" name="_pwe_nonce" value="%s">',
        $post_id,
        esc_attr(wp_create_nonce(PWE_FORMS_NONCE_ACTION . ':' . $post_id))
    );
}

function pwe_forms_export_dir(): string {
    $upload_dir = wp_upload_dir();
    $dir = trailingslashit($upload_dir['basedir']) . 'pwe-forms-exports';

    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
    }

    // Blocks directory listing of previously generated export files.
    $index = trailingslashit($dir) . 'index.php';
    if (!file_exists($index)) {
        file_put_contents($index, '<?php // Silence is golden.');
    }

    return $dir;
}

function pwe_forms_export_url(string $filename): string {
    $upload_dir = wp_upload_dir();
    return trailingslashit($upload_dir['baseurl']) . 'pwe-forms-exports/' . rawurlencode($filename);
}

// Random prefix so export filenames can't be guessed/enumerated by other visitors.
function pwe_forms_unique_filename(string $base): string {
    return wp_generate_password(10, false, false) . '_' . sanitize_file_name($base);
}

// Returns [stored filename (random prefix, used on disk/URL), friendly filename (shown to the visitor)].
function pwe_forms_prepare_filename(string $base): array {
    return [pwe_forms_unique_filename($base), sanitize_file_name($base)];
}

function pwe_forms_download_snippet(string $file_path, string $filename, string $display_name): string {
    if (!file_exists($file_path)) {
        return '';
    }

    return '<a class="pwe-gf-tool__download-link" href="' . esc_url(pwe_forms_export_url($filename)) . '" download="' . esc_attr($display_name) . '" style="display:none">Pobierz</a>'
        . '<script>(function(){var l=document.currentScript.previousElementSibling;if(l){l.click();l.remove();}})();</script>';
}

function pwe_forms_fair_name(): string {
    $name = sanitize_title(wp_strip_all_tags(do_shortcode('[trade_fair_name]')));
    return $name ?: 'gravity-forms';
}

function pwe_forms_domain_name(): string {
    $name = sanitize_title(wp_strip_all_tags(do_shortcode('[trade_fair_domainadress]')));
    return $name ?: pwe_forms_fair_name();
}

function pwe_forms_csv_value($value): string {
    if (is_array($value)) {
        $value = implode(', ', array_map('strval', $value));
    }

    $value = str_replace(["\r\n", "\r", "\n"], ' ', (string) $value);
    if (preg_match('/^[=+\-@]/', ltrim($value))) {
        $value = "'" . $value;
    }

    return $value;
}

function pwe_forms_csv_file_start(string $filename): array {
    [$stored, $friendly] = pwe_forms_prepare_filename($filename);
    $path = trailingslashit(pwe_forms_export_dir()) . $stored;
    $handle = fopen($path, 'wb');
    fwrite($handle, "\xEF\xBB\xBF");
    return [$handle, $path, $stored, $friendly];
}

function pwe_forms_get_views(int $form_id): int {
    global $wpdb;
    $table = $wpdb->prefix . 'gf_form_view';
    $value = $wpdb->get_var($wpdb->prepare("SELECT SUM(count) FROM {$table} WHERE form_id = %d", $form_id));
    return (int) $value;
}

function pwe_forms_form_stats(?array $forms = null): array {
    $result = [];
    $forms = $forms ?? GFAPI::get_forms(null, null);
    foreach ($forms as $form) {
        $count = GFAPI::count_entries((int) $form['id']);
        $result[] = [
            'form id'      => (int) $form['id'],
            'nazwa'        => (string) $form['title'],
            'wpisy'        => is_wp_error($count) ? 0 : (int) $count,
            'wyswietlenia' => pwe_forms_get_views((int) $form['id']),
        ];
    }
    return $result;
}

function pwe_forms_find_language_field(array $form): ?string {
    foreach (($form['fields'] ?? []) as $field) {
        $admin_label = strtolower((string) ($field->adminLabel ?? ''));
        $label = strtolower((string) ($field->label ?? ''));
        if (in_array($admin_label, ['pwe_lang', 'lang'], true) || in_array($label, ['lang', 'language'], true)) {
            return (string) $field->id;
        }
    }
    return null;
}

function pwe_forms_form_columns(array $form): array {
    $columns = [];
    foreach (($form['fields'] ?? []) as $field) {
        $columns[(string) $field->id] = (string) $field->label;
    }
    $columns['source_url'] = 'source_url';
    $columns['date_updated'] = 'date_updated';
    return $columns;
}

function pwe_forms_write_form_csv($handle, int $form_id, string $language = ''): bool {
    $form = GFAPI::get_form($form_id);
    if (is_wp_error($form) || empty($form['fields'])) {
        return false;
    }

    $criteria = ['status' => 'active'];
    if ($language !== '') {
        $lang_field = pwe_forms_find_language_field($form);
        if ($lang_field) {
            $criteria['field_filters'] = [['key' => $lang_field, 'value' => $language]];
        }
    }

    $columns = pwe_forms_form_columns($form);
    fputcsv($handle, array_values($columns));

    $offset = 0;
    $size = 200;
    do {
        $entries = GFAPI::get_entries($form_id, $criteria, null, ['offset' => $offset, 'page_size' => $size]);
        if (is_wp_error($entries)) {
            return false;
        }

        foreach ($entries as $entry) {
            $row = [];
            foreach ($columns as $field_id => $label) {
                $row[] = pwe_forms_csv_value($entry[$field_id] ?? '');
            }
            fputcsv($handle, $row);
        }

        $offset += $size;
    } while (count($entries) === $size);

    return true;
}

function pwe_forms_get_qr_url(array $entry, int $form_id): string {
    $url = (string) gform_get_meta($entry['id'], 'pwe_qr_code_url');
    if ($url !== '') {
        return $url;
    }

    $feeds = GFAPI::get_feeds(null, $form_id, 'pwe_qr');
    if (is_wp_error($feeds) || !$feeds) {
        $feeds = GFAPI::get_feeds(null, $form_id, 'qr-code');
    }
    if (is_wp_error($feeds)) {
        return '';
    }

    foreach ($feeds as $feed) {
        $url = (string) gform_get_meta($entry['id'], 'qr-code_feed_' . $feed['id'] . '_url');
        if ($url !== '') {
            return $url;
        }
    }

    return '';
}

function pwe_forms_entry_for_json(array $entry, array $form): array {
    $row = [
        'form_id'  => (int) $form['id'],
        'entry_id' => (int) $entry['id'],
        'qr_code'  => pwe_forms_get_qr_url($entry, (int) $form['id']),
    ];
    foreach (($form['fields'] ?? []) as $field) {
        $row[(string) $field->label] = $entry[(string) $field->id] ?? '';
    }
    return $row;
}

function pwe_forms_handle_export(int $post_id): string {
    $action = isset($_POST['action']) ? sanitize_key(wp_unslash($_POST['action'])) : '';
    if ('POST' !== ($_SERVER['REQUEST_METHOD'] ?? '') || PWE_FORMS_DOWNLOAD_ACTION !== $action) {
        return '';
    }

    $nonce = isset($_POST['_pwe_nonce']) ? sanitize_text_field(wp_unslash($_POST['_pwe_nonce'])) : '';
    if (!$post_id || !wp_verify_nonce($nonce, PWE_FORMS_NONCE_ACTION . ':' . $post_id)) {
        return '';
    }

    if (!class_exists('GFAPI')) {
        return '';
    }

    $type = isset($_POST['export_type']) ? sanitize_key(wp_unslash($_POST['export_type'])) : '';

    if ('stats_json' === $type) {
        $base = 'stats_' . pwe_forms_domain_name() . '_' . wp_date('Y-m-d') . '.json';
        [$filename, $friendly] = pwe_forms_prepare_filename($base);
        $path = trailingslashit(pwe_forms_export_dir()) . $filename;
        file_put_contents($path, wp_json_encode(pwe_forms_form_stats(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return pwe_forms_download_snippet($path, $filename, $friendly);
    }

    if ('stats_csv' === $type) {
        [$handle, $path, $filename, $friendly] = pwe_forms_csv_file_start('stats_' . pwe_forms_domain_name() . '_' . wp_date('Y-m-d') . '.csv');
        fputcsv($handle, ['form id', 'nazwa', 'ilość wpisów', 'ilość wyświetleń']);
        foreach (pwe_forms_form_stats() as $row) {
            fputcsv($handle, array_map('pwe_forms_csv_value', array_values($row)));
        }
        fclose($handle);
        return pwe_forms_download_snippet($path, $filename, $friendly);
    }

    if ('form_csv' === $type) {
        $form_id = isset($_POST['form_id']) ? absint($_POST['form_id']) : 0;
        $form = GFAPI::get_form($form_id);
        if (!$form_id || is_wp_error($form)) {
            return '';
        }

        $lang = isset($_POST['export_lang']) ? sanitize_key(wp_unslash($_POST['export_lang'])) : '';
        $name = pwe_forms_domain_name() . '_' . sanitize_title($form['title']);
        $name .= $lang ? '_' . $lang : '';
        [$handle, $path, $filename, $friendly] = pwe_forms_csv_file_start($name . '_' . wp_date('Y-m-d') . '.csv');
        pwe_forms_write_form_csv($handle, $form_id, $lang);
        fclose($handle);
        return pwe_forms_download_snippet($path, $filename, $friendly);
    }

    if ('scanner_csv' === $type) {
        $codes = isset($_POST['qr_search']) ? preg_split('/\R/', wp_unslash($_POST['qr_search'])) : [];
        [$handle, $path, $filename, $friendly] = pwe_forms_csv_file_start('dane_skaner_' . pwe_forms_domain_name() . '.csv');
        fputcsv($handle, ['id', 'Kod QR', 'Imię i nazwisko', 'Email', 'Telefon']);
        $number = 1;
        foreach ($codes as $code) {
            $code = trim($code);
            if (!preg_match('/^([A-Za-z]+)(\d{3})(\d+)([A-Za-z]+)(\d+)$/', $code, $matches)) {
                continue;
            }
            $entry = GFAPI::get_entry((int) $matches[3]);
            if (is_wp_error($entry)) {
                continue;
            }
            $form = GFAPI::get_form((int) $entry['form_id']);
            if (is_wp_error($form)) {
                continue;
            }
            $data = ['name' => '', 'email' => '', 'phone' => ''];
            foreach ($form['fields'] as $field) {
                $label = strtolower(remove_accents((string) $field->label));
                $value = pwe_forms_csv_value($entry[(string) $field->id] ?? '');
                if (strpos($label, 'mail') !== false) {
                    $data['email'] = $value;
                } elseif (strpos($label, 'telefon') !== false || strpos($label, 'phone') !== false) {
                    $data['phone'] = $value;
                } elseif (preg_match('/imie|name|nazwisko|osoba/', $label)) {
                    $data['name'] = trim($data['name'] . ' ' . $value);
                }
            }
            fputcsv($handle, [$number++, pwe_forms_csv_value($code), $data['name'], $data['email'], $data['phone']]);
        }
        fclose($handle);
        return pwe_forms_download_snippet($path, $filename, $friendly);
    }

    if ('forms_json' === $type) {
        $ids = isset($_POST['form_ids']) ? array_filter(array_map('absint', (array) $_POST['form_ids'])) : [];
        $groups = ['vip_gold' => [], 'klaviyo' => [], 'gosc' => []];
        foreach ($ids as $form_id) {
            $form = GFAPI::get_form($form_id);
            if (is_wp_error($form)) {
                continue;
            }
            $offset = 0;
            do {
                $entries = GFAPI::get_entries($form_id, ['status' => 'active'], null, ['offset' => $offset, 'page_size' => 200]);
                if (is_wp_error($entries)) {
                    break;
                }
                foreach ($entries as $entry) {
                    $row = pwe_forms_entry_for_json($entry, $form);
                    $utm = strtolower((string) ($row['UTM'] ?? ''));
                    $group = strpos($utm, 'utm_source=byli') !== false ? 'vip_gold' : (strpos($utm, 'utm_source=klaviyo') !== false ? 'klaviyo' : 'gosc');
                    $groups[$group][] = $row;
                }
                $offset += 200;
            } while (count($entries) === 200);
        }

        if (!array_filter($groups)) {
            return '';
        }

        $export_dir = pwe_forms_export_dir();

        if (class_exists('ZipArchive')) {
            [$filename, $friendly] = pwe_forms_prepare_filename(pwe_forms_domain_name() . '_wpisy.zip');
            $path = trailingslashit($export_dir) . $filename;
            $zip = new ZipArchive();
            if (true === $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
                foreach ($groups as $group => $rows) {
                    if ($rows) {
                        $zip->addFromString(pwe_forms_domain_name() . '_' . $group . '.json', wp_json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                    }
                }
                $zip->close();
                if (file_exists($path) && filesize($path) > 0) {
                    return pwe_forms_download_snippet($path, $filename, $friendly);
                }
            }
        }

        [$filename, $friendly] = pwe_forms_prepare_filename(pwe_forms_domain_name() . '_wpisy.json');
        $path = trailingslashit($export_dir) . $filename;
        file_put_contents($path, wp_json_encode($groups, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        return pwe_forms_download_snippet($path, $filename, $friendly);
    }

    if ('form_name_json' === $type) {
        $wanted = isset($_POST['form_name']) ? sanitize_text_field(wp_unslash($_POST['form_name'])) : '';
        foreach (GFAPI::get_forms(null, null) as $form) {
            if (0 === strcasecmp((string) $form['title'], $wanted)) {
                $entries = GFAPI::get_entries((int) $form['id'], null, null, ['offset' => 0, 'page_size' => 0]);
                $entries = is_wp_error($entries) ? [] : $entries;
                if (!$entries) {
                    return '';
                }
                [$filename, $friendly] = pwe_forms_prepare_filename(pwe_forms_domain_name() . '_' . sanitize_title($form['title']) . '_' . wp_date('Y-m-d') . '.json');
                $path = trailingslashit(pwe_forms_export_dir()) . $filename;
                file_put_contents($path, wp_json_encode($entries, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                return pwe_forms_download_snippet($path, $filename, $friendly);
            }
        }
    }

    return '';
}

function pwe_forms_language_map(array $forms): array {
    $wpml = apply_filters('wpml_active_languages', null, ['skip_missing' => 0]);
    $map = [];
    foreach ($forms as $form) {
        $field = pwe_forms_find_language_field($form);
        if (!$field) {
            continue;
        }
        $codes = is_array($wpml) ? array_keys($wpml) : [];
        if (!$codes) {
            $offset = 0;
            do {
                $entries = GFAPI::get_entries((int) $form['id'], ['status' => 'active'], null, ['offset' => $offset, 'page_size' => 200]);
                if (is_wp_error($entries)) {
                    $entries = [];
                    break;
                }
                foreach ($entries as $entry) {
                    $code = sanitize_key((string) ($entry[$field] ?? ''));
                    if ($code !== '') {
                        $codes[] = $code;
                    }
                }
                $offset += 200;
            } while (count($entries) === 200);
            $codes = array_values(array_unique($codes));
        }
        foreach ($codes as $code) {
            $count = GFAPI::count_entries((int) $form['id'], [
                'status' => 'active',
                'field_filters' => [['key' => $field, 'value' => $code]],
            ]);
            $count = is_wp_error($count) ? 0 : (int) $count;
            if ($count > 0) {
                $flag = '';
                if (is_array($wpml) && isset($wpml[$code]) && is_array($wpml[$code])) {
                    $flag = esc_url_raw((string) ($wpml[$code]['country_flag_url'] ?? ''));
                }
                $map[(int) $form['id']][sanitize_key($code)] = [
                    'count' => $count,
                    'flag'  => $flag,
                ];
            }
        }
        if (empty($map[(int) $form['id']])) {
            unset($map[(int) $form['id']]);
        }
    }
    return $map;
}

function pwe_forms_qr_view(array $forms): string {
    $html = '<div class="pwe-gf-tool__accordion">';
    foreach ($forms as $form) {
        $entries = GFAPI::get_entries((int) $form['id'], null, ['key' => 'id', 'direction' => 'DESC'], ['offset' => 0, 'page_size' => 8]);
        if (is_wp_error($entries)) {
            continue;
        }
        $images = '';
        $number = 1;
        foreach ($entries as $entry) {
            $url = pwe_forms_get_qr_url($entry, (int) $form['id']);
            if ($url) {
                $images .= '<figure><img src="' . esc_url($url) . '" alt="Kod QR ' . $number . '"><figcaption>QR #' . $number++ . '</figcaption></figure>';
            }
        }
        if ($images) {
            $html .= '<details><summary>' . esc_html($form['title']) . '</summary><div class="pwe-gf-tool__qr-grid">' . $images . '</div></details>';
        }
    }
    return $html . '</div>';
}

function pwe_forms_tools_shortcode(): string {
    return Forms::render();
}

add_shortcode('gf_download_autoswitch', 'pwe_forms_tools_shortcode');
