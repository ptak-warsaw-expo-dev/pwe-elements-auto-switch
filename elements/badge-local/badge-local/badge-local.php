<?php
if (!defined('ABSPATH')) exit;

class Badge_Local {

    public static function get_data() {
        return [
            'types' => ['badge-local'],
            'presets' => [
                'all' => plugin_dir_path(__FILE__) . 'presets/all/preset.php',
            ],
        ];
    }

    /**
     * Static method to generate mass bages.
     * 
     * @param number @badge_form_id form id
     *
     * The method processes the following steps:
     * 1. Checks if the form was submitted and necessary inputs are present.
     * 2. Updates the URL of the confirmation message.
     * 3. Collects data from the form inputs and prepares it for badge generation.
     * 4. Generates multiple badges adding to forms and opens each badge URL in a new window for download.w.
     */
    public static function massGenerator($badge_form_id) {

        // 1. Checks if the form was submitted and necessary inputs are present.
        if (
            isset($_POST["gform_submit"]) &&
            $_POST["gform_submit"] == $badge_form_id &&
            !empty($_POST['input_5']) &&
            isset($_POST['input_3'])
        ) {

            // 2. Updates the URL of the confirmation message.
            echo '<script>
            jQuery(function ($) {
                const gfMessage = $(".gform_confirmation_message a");
                if (gfMessage.length) {
                    const urlMessage = gfMessage.eq(0).attr("href") + "?parametr=masowy";
                    gfMessage.eq(0).attr("href", urlMessage);
                    gfMessage.eq(1).hide();
                    window.open(gfMessage.eq(1).attr("href"));
                }
            });
            </script>';

            // base entry data
            $multi_badge = [
                'form_id' => $badge_form_id
            ];

            // 3. Collects data from the form inputs and prepares it for badge generation.
            foreach ($_POST as $key => $value) {
                if (strpos(strtolower($key), 'input') !== false) {
                    preg_match_all('/\d+/', $key, $id);
                    $field_id = $id[0][0];
                    $multi_badge[$field_id] = $value;
                }
            }

            $count = (int) ($_POST['multi_send'] ?? 1);

            // 4. Generates multiple badges adding to forms and opens each badge URL in a new window for download.
            for ($i = 1; $i < $count; $i++) {

                // create entry for each badge
                $entry_id = GFAPI::add_entry($multi_badge);

                if (is_wp_error($entry_id)) {
                    continue;
                }

                // force processing
                $entry = GFAPI::get_entry($entry_id);

                if (!is_wp_error($entry)) {
                    do_action(
                        'gform_after_submission',
                        $entry,
                        ['id' => $badge_form_id]
                    );
                }

                $qr_code_url = '';
                $tries = 0;

                while ($tries < 10) {

                    // priority 1: PWE QR
                    $pwe_url = gform_get_meta($entry_id, 'pwe_qr_code_url_encoded');

                    if (!empty($pwe_url)) {
                        $qr_code_url = $pwe_url;
                        break;
                    }

                    // priority 2: QR CODE FEEDS
                    $qr_feeds = GFAPI::get_feeds(null, $badge_form_id, 'qr-code');

                    if (!is_wp_error($qr_feeds)) {
                        foreach ($qr_feeds as $feed) {

                            $feed_id = $feed['id'] ?? null;
                            if (!$feed_id) {
                                continue;
                            }

                            $url = gform_get_meta(
                                $entry_id,
                                'qr-code_feed_' . $feed_id . '_url'
                            );

                            if (!empty($url)) {
                                $qr_code_url = $url;
                                break 2;
                            }
                        }
                    }

                    usleep(200000);
                    $tries++;
                }

                if (empty($qr_code_url)) {
                    error_log("MassGenerator: missing QR for entry " . $entry_id);
                    continue;
                }

                // generate badge URL with query parameters
                $badge_url =
                    'https://warsawexpo.eu/assets/badge/local/loading.html'
                    . '?category=' . $multi_badge[5]
                    . '&getname=' . $multi_badge[1]
                    . '&firma=' . $multi_badge[2]
                    . '&qrcode=' . $qr_code_url;

                echo '<script>window.open("' . $badge_url . '");</script>';
            }
        }
    }

    /**
     * Static method to generate mass bages.
     * 
     * @param number @badge_form_id form id
     *
     * The method processes the following steps:
     * 1. Checks if the form was submitted and necessary inputs are present.
     * 2. Updates the URL of the confirmation message.
     * 3. Collects data from the form inputs and prepares it for badge generation.
     * 4. Generates multiple badges adding to forms and opens each badge URL in a new window for download.w.
     */
    public static function qrOnlyDownload($badge_form_id) {

        if (
            !isset($_POST['gform_submit']) ||
            (int) $_POST['gform_submit'] !== (int) $badge_form_id ||
            empty($_POST['input_5']) ||
            !isset($_POST['input_3'])
        ) {
            return;
        }

        if (!class_exists('ZipArchive')) {
            error_log('qrOnlyDownload: rozszerzenie PHP ZipArchive nie jest dostępne.');
            return;
        }

        echo '
        <script>
            jQuery(function ($) {
                const gfMessage = $(".gform_confirmation_message a");

                if (gfMessage.length) {
                    const currentUrl = gfMessage.eq(0).attr("href");

                    if (currentUrl) {
                        const separator = currentUrl.includes("?") ? "&" : "?";
                        gfMessage.eq(0).attr(
                            "href",
                            currentUrl + separator + "parametr=masowy&qrcode=only"
                        );
                    }

                    gfMessage.eq(1).hide();
                }
            });
        </script>';

        $multi_badge = [
            'form_id' => $badge_form_id,
        ];

        foreach ($_POST as $key => $value) {
            if (strpos(strtolower($key), 'input') === false) {
                continue;
            }

            preg_match('/\d+/', $key, $matches);

            if (!empty($matches[0])) {
                $multi_badge[$matches[0]] = $value;
            }
        }

        $upload_dir = wp_upload_dir();

        if (!empty($upload_dir['error'])) {
            error_log('qrOnlyDownload: wp_upload_dir error: ' . $upload_dir['error']);
            return;
        }

        $badge_name = sanitize_file_name(do_shortcode('[trade_fair_badge]'));

        if (empty($badge_name)) {
            $badge_name = 'badges';
        }

        $zip_filename = $badge_name . '_qr_only.zip';
        $zip_path = trailingslashit($upload_dir['basedir']) . $zip_filename;
        $zip_url = trailingslashit($upload_dir['baseurl']) . $zip_filename;

        $zip = new ZipArchive();

        $open_result = $zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($open_result !== true) {
            error_log('qrOnlyDownload: nie udało się otworzyć ZIP. Kod: ' . print_r($open_result, true) . ', ścieżka: ' . $zip_path);
            return;
        }

        $temp_files = [];
        $added_files = 0;
        $count = max(1, (int) ($_POST['multi_send'] ?? 1));

        for ($i = 0; $i < $count; $i++) {

            $entry_id = GFAPI::add_entry($multi_badge);

            if (is_wp_error($entry_id)) {
                error_log('qrOnlyDownload: add_entry error: ' . $entry_id->get_error_message());
                continue;
            }

            $entry = GFAPI::get_entry($entry_id);

            if (is_wp_error($entry)) {
                error_log('qrOnlyDownload: get_entry error dla ID ' . $entry_id . ': ' . $entry->get_error_message());
                continue;
            }

            do_action('gform_after_submission', $entry, ['id' => $badge_form_id]);

            $file_path = '';
            $tries = 0;

            /*
            * QR code generation can occur during the hook or shortly after.
            * We wait a maximum of about 3 seconds.
            */
            while ($tries < 15 && empty($file_path)) {

                $pwe_url = gform_get_meta($entry_id, 'pwe_qr_code_url');

                if (!empty($pwe_url)) {
                    $tmp_file = self::pwe_download_temp_qr($pwe_url);

                    if (!empty($tmp_file) && file_exists($tmp_file)) {
                        $file_path = $tmp_file;
                        $temp_files[] = $tmp_file;
                        break;
                    }
                }

                $qr_feeds = GFAPI::get_feeds(null, $badge_form_id, 'qr-code');

                if (!is_wp_error($qr_feeds)) {
                    foreach ($qr_feeds as $feed) {
                        $feed_id = $feed['id'] ?? null;

                        if (!$feed_id) {
                            continue;
                        }

                        $path = gform_get_meta($entry_id, 'qr-code_feed_' . $feed_id . '_file');

                        if (!empty($path) && file_exists($path)) {
                            $file_path = $path;
                            break;
                        }
                    }
                }

                if (empty($file_path)) {
                    usleep(200000);
                    $tries++;
                }
            }

            if (empty($file_path) || !file_exists($file_path)) {
                error_log('qrOnlyDownload: brak pliku QR dla entry ID ' . $entry_id);
                continue;
            }

            /*
            * Each file must have a unique name within the ZIP file.
            * A basename alone can cause files to be overwritten.
            */
            $extension = pathinfo($file_path, PATHINFO_EXTENSION);

            if (empty($extension)) {
                $extension = 'png';
            }

            $name_in_zip = sprintf('qr_%d_%d.%s', $i + 1, $entry_id, sanitize_file_name($extension));

            $add_result = $zip->addFile($file_path, $name_in_zip);

            if ($add_result) {
                $added_files++;
            } else {
                error_log('qrOnlyDownload: addFile nie powiódł się dla ' . $file_path);
            }
        }

        /*
        * First we close the ZIP, only then we delete the source files.
        */
        $close_result = $zip->close();

        foreach ($temp_files as $temp_file) {
            if (is_string($temp_file) && file_exists($temp_file)) {
                @unlink($temp_file);
            }
        }

        if (!$close_result) {
            error_log('qrOnlyDownload: ZipArchive::close() zwróciło false dla '. $zip_path);
            return;
        }

        if ($added_files === 0) {
            error_log('qrOnlyDownload: nie dodano żadnego pliku do ZIP-a.');
            if (file_exists($zip_path)) {
                @unlink($zip_path);
            }
            return;
        }

        if (!file_exists($zip_path) || filesize($zip_path) === 0) {
            error_log('qrOnlyDownload: ZIP nie istnieje albo jest pusty: ' . $zip_path);
            return;
        }

        echo '
        <script>
            window.open(' . wp_json_encode($zip_url . '?ts=' . time()) . ', "_blank");
        </script>';
    }

    /**
     * Static method to download QR code from URL and save it as a temporary file.
     * 
     * @param string $url The URL of the QR code to download.
     * 
     * @return string The path to the downloaded temporary file, or an empty string on failure.
     */
    public static function pwe_download_temp_qr($url) {

        if (empty($url)) {
            return '';
        }

        $upload_dir = wp_upload_dir();
        $tmp_dir = $upload_dir['basedir'] . '/tmp_qr/';

        if (!file_exists($tmp_dir)) {
            mkdir($tmp_dir, 0777, true);
        }

        $filename = 'qr_' . md5($url . microtime(true)) . '.png';
        $path = $tmp_dir . $filename;

        $response = wp_remote_get($url, [
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0'
            ]
        ]);

        if (is_wp_error($response)) {
            return '';
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            return '';
        }

        $body = wp_remote_retrieve_body($response);

        if (empty($body) || strpos($body, "\x89PNG") !== 0) {
            return '';
        }

        file_put_contents($path, $body);

        return $path;
    }

    /**
     * Static method to changing form field content.
     * 
     * @param number @badge_form_id form id
     *
     * The method processes the following steps:
     * 1. Finding radio field with badge names.
     * 2. Adjusting badge names in the finded fields.
     * 
     * @return object @content
     */
    public static function badge_name_changer($content, $field, $value, $lead_id, $form_id) {
        // 1. Finding radio field with badge names.
        foreach($field as $f_id => $f_label) {
            if (!is_array($f_label) && strpos(strtolower($f_label), 'wybierz') !== false) {
                // 2. Adjusting badge names in the finded fields.
                $badge = do_shortcode('[trade_fair_badge]');
                
                $content = preg_replace('/(_[a-zA-Z0-9_]+_a6)/', $badge . '$1', $content);
                $content = preg_replace('/[a-zA-Z0-9_]+_empty_wystawca_a6/', 'empty_wystawca_a6', $content);
                $content = preg_replace('/[a-zA-Z0-9_]+_empty_zlot_a6/', 'empty_zlot_a6', $content);
            }
        }
        return $content;
    }

    public static function render($group = '', $params = [], $atts = []) {

        $data = self::get_data();
        $element_type = $data['types'][0];
        $element_slug = 'badge-local';

        $group = 'all';

        // Add context to translations function
        PWE_Functions::set_translation_context($element_slug, $group, $element_type);
        // Global assets
        PWE_Functions::assets_per_element($element_slug, $element_type);
        // Assets per group
        PWE_Functions::assets_per_group($element_slug, $group, $element_type);

        $preset_file = self::get_data()['presets'][$group] ?? null;
        if ($preset_file && file_exists($preset_file)) {

            /* <-------------> General code start <-------------> */

            $form_id = PWE_Functions::get_gf_form_id('Badge generator(local)');

            if (!$form_id) {
                return;
            }

            // Filtering badge names
            add_filter( 'gform_field_content', [ 'Badge_Local', 'badge_name_changer' ], 10, 5 );

            /* <-------------> General code end <-------------> */

            $output = include $preset_file;

            if ($output) {
                echo do_shortcode($output);
            }
        }
    }
}
