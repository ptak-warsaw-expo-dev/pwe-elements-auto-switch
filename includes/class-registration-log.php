<?php

error_log('PWE Registration Log: FILE LOADED');

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('PWE_Registration_Log')) {

    class PWE_Registration_Log
    {
        /**
         * Gravity Forms form ID.
         *
         * Set to 0 to log all forms.
         *
         * @var int
         */
        private $form_id = 0;

        /**
         * CSV file name.
         *
         * @var string
         */
        private $filename = 'registration-log.csv';

        /**
         * CSV delimiter.
         *
         * @var string
         */
        private $delimiter = ';';

        /**
         * Shortcode name.
         *
         * @var string
         */
        private $shortcode = 'registration_log';

        /**
         * Public access key.
         *
         * Change this value to your own long random key.
         *
         * Example URL:
         * ?key=YOUR_KEY
         *
         * @var string
         */
        private $access_key = PWE_API_KEY_5;

        /**
         * Prevent duplicate logging during the same request.
         *
         * @var bool
         */
        private $request_logged = false;


        /**
         * Initialize hooks.
         */
        public function __construct()
        {

        error_log('PWE Registration Log: CONSTRUCTOR RUNNING');
            /**
             * Log every validation attempt.
             */
            add_filter(
                'gform_validation',
                array($this, 'log_submission_attempt'),
                PHP_INT_MAX
            );

            /**
             * Register frontend shortcode.
             */
            add_shortcode(
                $this->shortcode,
                array($this, 'render_shortcode')
            );

            /**
             * Handle CSV download requests.
             */
            add_action(
                'template_redirect',
                array($this, 'handle_csv_download')
            );

            /**
             * Create the Logs page after WordPress
             * has completed its initialization.
             */
            add_action(
                'wp_loaded',
                array($this, 'ensure_logs_page_exists'),
                20
            );

        }


        /**
         * Returns the storage directory.
         *
         * Files are stored in:
         * wp-content/uploads/logs/
         *
         * @return string
         */
        private function get_storage_directory()
        {
            $upload_dir = wp_upload_dir();

            $directory = trailingslashit($upload_dir['basedir'])
                . 'logs';

            if (!file_exists($directory)) {
                wp_mkdir_p($directory);
            }

            return $directory;
        }


        /**
         * Returns the full CSV file path.
         *
         * @return string
         */
        private function get_csv_path()
        {
            return trailingslashit(
                $this->get_storage_directory()
            ) . $this->filename;
        }


        /**
         * Returns CSV column headers.
         *
         * @return array
         */
        private function get_csv_headers()
        {
            return array(
                'Date',
                'Time',
                'Status',
                'Form ID',
                'Form Name',
                'IP',
                'Data',
                'Validation Errors',
            );
        }


        /**
         * Logs every Gravity Forms submission attempt.
         *
         * The attempt is logged regardless of whether
         * Gravity Forms accepts or rejects the submission.
         *
         * @param array $validation_result Gravity Forms validation result.
         *
         * @return array
         */
        public function log_submission_attempt($validation_result)
        {
            /**
             * Prevent duplicate writes during the same request.
             */
            if ($this->request_logged) {
                return $validation_result;
            }

            if (empty($validation_result['form'])) {
                return $validation_result;
            }

            $form = $validation_result['form'];

            /**
             * Ignore forms other than the configured form.
             */
            if (
                $this->form_id > 0
                && (int) $form['id'] !== (int) $this->form_id
            ) {
                return $validation_result;
            }

            $this->request_logged = true;

            $csv_path = $this->get_csv_path();

            $file_exists = (
                file_exists($csv_path)
                && filesize($csv_path) > 0
            );

            $handle = fopen(
                $csv_path,
                'a'
            );

            if (!$handle) {

                error_log(
                    'Registration Log: Unable to open CSV file: '
                    . $csv_path
                );

                return $validation_result;
            }

            /**
             * Prevent simultaneous writes to the CSV file.
             */
            if (!flock($handle, LOCK_EX)) {

                fclose($handle);

                return $validation_result;
            }

            /**
             * Write CSV headers when creating a new file.
             */
            if (!$file_exists) {

                fputcsv(
                    $handle,
                    $this->get_csv_headers(),
                    $this->delimiter
                );
            }

            /**
             * Extract submitted form values.
             */
            $fields_data = $this->get_form_values($form);

            $formatted_data = array();

            foreach ($fields_data as $label => $value) {

                $formatted_data[] =
                    $label . ': ' . $value;
            }

            /**
             * Determine submission status.
             */
            $is_valid = !empty(
                $validation_result['is_valid']
            );

            $status = $is_valid
                ? 'VALID'
                : 'INVALID';

            /**
             * Collect validation errors.
             */
            $validation_errors =
                $this->get_validation_errors($form);

            /**
             * Prepare CSV row.
             */
            $row = array(
                current_time('Y-m-d'),
                current_time('H:i:s'),
                $status,
                isset($form['id'])
                    ? (int) $form['id']
                    : '',
                sanitize_text_field(
                    isset($form['title'])
                        ? $form['title']
                        : ''
                ),
                $this->get_user_ip(),
                implode(
                    ' | ',
                    $formatted_data
                ),
                implode(
                    ' | ',
                    $validation_errors
                ),
            );

            /**
             * Write row to CSV.
             */
            fputcsv(
                $handle,
                $row,
                $this->delimiter
            );

            fflush($handle);

            flock(
                $handle,
                LOCK_UN
            );

            fclose($handle);

            /**
             * Return original validation result.
             *
             * Logging must not modify Gravity Forms behavior.
             */
            return $validation_result;
        }


        /**
         * Extracts submitted Gravity Forms values.
         *
         * @param array $form Gravity Forms form configuration.
         *
         * @return array
         */
        private function get_form_values($form)
        {
            $fields_data = array();

            if (empty($form['fields'])) {
                return $fields_data;
            }

            foreach ($form['fields'] as $field) {

                /**
                 * Skip fields without user input.
                 */
                if (
                    in_array(
                        $field->type,
                        array(
                            'html',
                            'section',
                            'page',
                            'captcha',
                        ),
                        true
                    )
                ) {
                    continue;
                }

                /**
                 * Prefer admin label when available.
                 */
                $label = !empty($field->adminLabel)
                    ? $field->adminLabel
                    : $field->label;

                if (empty($label)) {
                    $label = 'Field ' . $field->id;
                }

                $value = $this->get_field_value(
                    $field
                );

                /**
                 * Keep only submitted values.
                 */
                if (
                    $value === ''
                    || $value === null
                ) {
                    continue;
                }

                $fields_data[$label] = $value;
            }

            return $fields_data;
        }


        /**
         * Returns submitted value for a Gravity Forms field.
         *
         * Supports both regular and compound fields.
         *
         * @param object $field Gravity Forms field object.
         *
         * @return string
         */
        private function get_field_value($field)
        {
            $field_id = (string) $field->id;

            /**
             * Handle compound fields such as:
             * name, address and similar fields.
             */
            if (!empty($field->inputs)) {

                $parts = array();

                foreach ($field->inputs as $input) {

                    if (empty($input['id'])) {
                        continue;
                    }

                    $input_name =
                        'input_'
                        . str_replace(
                            '.',
                            '_',
                            $input['id']
                        );

                    $value = rgpost(
                        $input_name
                    );

                    if (
                        $value === ''
                        || $value === null
                    ) {
                        continue;
                    }

                    if (is_array($value)) {

                        $value = implode(
                            ', ',
                            $value
                        );
                    }

                    $value = sanitize_text_field(
                        wp_unslash(
                            (string) $value
                        )
                    );

                    if ($value !== '') {
                        $parts[] = $value;
                    }
                }

                return implode(
                    ' ',
                    $parts
                );
            }

            /**
             * Handle standard Gravity Forms fields.
             */
            $value = rgpost(
                'input_' . $field_id
            );

            /**
             * Handle checkbox and other array values.
             */
            if (is_array($value)) {

                $clean_values = array();

                foreach ($value as $item) {

                    $item = sanitize_text_field(
                        wp_unslash(
                            (string) $item
                        )
                    );

                    if ($item !== '') {
                        $clean_values[] = $item;
                    }
                }

                return implode(
                    ', ',
                    $clean_values
                );
            }

            return sanitize_text_field(
                wp_unslash(
                    (string) $value
                )
            );
        }


        /**
         * Returns validation errors for failed fields.
         *
         * @param array $form Gravity Forms form configuration.
         *
         * @return array
         */
        private function get_validation_errors($form)
        {
            $errors = array();

            if (empty($form['fields'])) {
                return $errors;
            }

            foreach ($form['fields'] as $field) {

                if (
                    empty(
                        $field->failed_validation
                    )
                ) {
                    continue;
                }

                $label = !empty(
                    $field->adminLabel
                )
                    ? $field->adminLabel
                    : $field->label;

                if (empty($label)) {
                    $label =
                        'Field ' . $field->id;
                }

                $message = !empty(
                    $field->validation_message
                )
                    ? $field->validation_message
                    : 'Validation failed';

                $errors[] =
                    sanitize_text_field($label)
                    . ': '
                    . sanitize_text_field(
                        wp_strip_all_tags(
                            $message
                        )
                    );
            }

            return $errors;
        }


        /**
         * Returns the visitor IP address.
         *
         * @return string
         */
        private function get_user_ip()
        {
            if (
                empty(
                    $_SERVER['REMOTE_ADDR']
                )
            ) {
                return '';
            }

            return sanitize_text_field(
                wp_unslash(
                    $_SERVER['REMOTE_ADDR']
                )
            );
        }


        /**
         * Checks whether the current visitor
         * can access registration logs.
         *
         * Access is allowed for:
         * - logged-in WordPress users,
         * - visitors with a valid access key.
         *
         * @return bool
         */
        private function can_access_logs()
        {
            if (is_user_logged_in()) {
                return true;
            }

            if (
                empty(
                    $_GET[
                        'key'
                    ]
                )
            ) {
                return false;
            }

            $provided_key =
                sanitize_text_field(
                    wp_unslash(
                        $_GET[
                            'key'
                        ]
                    )
                );

            return hash_equals(
                $this->access_key,
                $provided_key
            );
        }


        /**
         * Reads all CSV rows.
         *
         * @return array
         */
        private function read_csv()
        {
            $csv_path =
                $this->get_csv_path();

            if (
                !file_exists($csv_path)
                || filesize($csv_path) === 0
            ) {
                return array();
            }

            $handle = fopen(
                $csv_path,
                'r'
            );

            if (!$handle) {
                return array();
            }

            $rows = array();

            /**
             * Use shared file lock while reading.
             */
            if (flock($handle, LOCK_SH)) {

                while (
                    (
                        $data = fgetcsv(
                            $handle,
                            0,
                            $this->delimiter
                        )
                    ) !== false
                ) {
                    $rows[] = $data;
                }

                flock(
                    $handle,
                    LOCK_UN
                );
            }

            fclose($handle);

            return $rows;
        }

        /**
         * Calculates registration statistics from CSV rows.
         *
         * Returns:
         * - total number of registrations,
         * - valid registrations,
         * - invalid registrations,
         * - statistics grouped by Gravity Forms form.
         *
         * @param array $rows CSV rows including the header row.
         *
         * @return array
         */
        private function get_statistics($rows)
        {
            $statistics = array(
                'total' => 0,
                'valid' => 0,
                'invalid' => 0,
                'forms' => array(),
            );

            if (empty($rows)) {
                return $statistics;
            }

            /**
             * Remove the CSV header row.
             */
            $headers = array_shift($rows);

            /**
             * Find column indexes dynamically.
             *
             * This prevents statistics from breaking
             * if the CSV column order changes later.
             */
            $status_index = array_search(
                'Status',
                $headers,
                true
            );

            $form_id_index = array_search(
                'Form ID',
                $headers,
                true
            );

            $form_name_index = array_search(
                'Form Name',
                $headers,
                true
            );

            if (
                $status_index === false
                || $form_id_index === false
                || $form_name_index === false
            ) {
                return $statistics;
            }

            foreach ($rows as $row) {

                $status = isset($row[$status_index])
                    ? strtoupper(trim($row[$status_index]))
                    : '';

                $form_id = isset($row[$form_id_index])
                    ? trim($row[$form_id_index])
                    : '';

                $form_name = isset($row[$form_name_index])
                    ? trim($row[$form_name_index])
                    : '';

                $statistics['total']++;

                if ($status === 'VALID') {
                    $statistics['valid']++;
                }

                if ($status === 'INVALID') {
                    $statistics['invalid']++;
                }

                /**
                 * Use form ID as the unique group key.
                 */
                $form_key = $form_id !== ''
                    ? $form_id
                    : 'unknown';

                if (!isset($statistics['forms'][$form_key])) {

                    $statistics['forms'][$form_key] = array(
                        'id' => $form_id,
                        'name' => $form_name,
                        'total' => 0,
                        'valid' => 0,
                        'invalid' => 0,
                    );
                }

                $statistics['forms'][$form_key]['total']++;

                if ($status === 'VALID') {
                    $statistics['forms'][$form_key]['valid']++;
                }

                if ($status === 'INVALID') {
                    $statistics['forms'][$form_key]['invalid']++;
                }
            }

            /**
             * Sort forms by registration count,
             * highest first.
             */
            uasort(
                $statistics['forms'],
                function ($a, $b) {
                    return $b['total'] <=> $a['total'];
                }
            );

            return $statistics;
        }

        /**
         * Generates the CSV download URL.
         *
         * The access key is preserved for
         * non-authenticated visitors.
         *
         * @return string
         */
        private function get_download_url()
        {
            $args = array(
                'registration_log_download'
                    => '1',
            );

            /**
             * Preserve public access key
             * when visitor is not logged in.
             */
            if (
                !is_user_logged_in()
                && !empty(
                    $_GET[
                        'key'
                    ]
                )
            ) {

                $args[
                    'key'
                ] = sanitize_text_field(
                    wp_unslash(
                        $_GET[
                            'key'
                        ]
                    )
                );
            }

            $url = add_query_arg(
                $args,
                home_url('/')
            );

            return wp_nonce_url(
                $url,
                'registration_log_download',
                'registration_log_nonce'
            );
        }


        /**
         * Handles CSV download requests.
         */
        public function handle_csv_download()
        {
            if (
                empty(
                    $_GET[
                        'registration_log_download'
                    ]
                )
                || $_GET[
                    'registration_log_download'
                ] !== '1'
            ) {
                return;
            }

            /**
             * Verify access permissions.
             */
            if (!$this->can_access_logs()) {

                wp_die(
                    'Access denied.',
                    'Access denied',
                    array(
                        'response' => 403,
                    )
                );
            }

            /**
             * Validate security nonce.
             */
            if (
                empty(
                    $_GET[
                        'registration_log_nonce'
                    ]
                )
                || !wp_verify_nonce(
                    sanitize_text_field(
                        wp_unslash(
                            $_GET[
                                'registration_log_nonce'
                            ]
                        )
                    ),
                    'registration_log_download'
                )
            ) {

                wp_die(
                    'Invalid security token.',
                    'Security error',
                    array(
                        'response' => 403,
                    )
                );
            }

            $csv_path =
                $this->get_csv_path();

            if (!file_exists($csv_path)) {

                wp_die(
                    'CSV file does not exist.',
                    'File not found',
                    array(
                        'response' => 404,
                    )
                );
            }

            /**
             * Remove any buffered output
             * before sending the CSV file.
             */
            while (ob_get_level()) {
                ob_end_clean();
            }

            nocache_headers();

            header(
                'Content-Type: text/csv; charset=UTF-8'
            );

            header(
                'Content-Disposition: attachment; filename="'
                . basename($this->filename)
                . '"'
            );

            /**
             * UTF-8 BOM improves Excel compatibility.
             */
            echo "\xEF\xBB\xBF";

            readfile($csv_path);

            exit;
        }


        /**
         * Renders registration log shortcode.
         *
         * Usage:
         *
         * [registration_log]
         *
         * @return string
         */
        public function render_shortcode()
        {
            if (!$this->can_access_logs()) {
                return '<p>Access denied.</p>';
            }

            $rows = $this->read_csv();

            /**
             * Calculate statistics before modifying
             * the CSV rows used by the table.
             */
            $statistics = $this->get_statistics($rows);

            ob_start();

            $this->render_styles();

            ?>

            <div
                class="registration-log"
                data-per-page="30"
            >

                <div class="registration-log__header">

                    <div class="registration-log__summary">

                        <div class="registration-log__stats">

                            <button
                                type="button"
                                class="registration-log__stat registration-log__stat--total is-active"
                                data-filter-type="all"
                                data-filter-value=""
                            >
                                <span class="registration-log__stat-label">
                                    Registrations
                                </span>

                                <strong class="registration-log__stat-value">
                                    <?php echo esc_html($statistics['total']); ?>
                                </strong>
                            </button>


                            <button
                                type="button"
                                class="registration-log__stat registration-log__stat--valid"
                                data-filter-type="status"
                                data-filter-value="VALID"
                            >
                                <span class="registration-log__stat-label">
                                    Valid
                                </span>

                                <strong class="registration-log__stat-value">
                                    <?php echo esc_html($statistics['valid']); ?>
                                </strong>
                            </button>


                            <button
                                type="button"
                                class="registration-log__stat registration-log__stat--invalid"
                                data-filter-type="status"
                                data-filter-value="INVALID"
                            >
                                <span class="registration-log__stat-label">
                                    Invalid
                                </span>

                                <strong class="registration-log__stat-value">
                                    <?php echo esc_html($statistics['invalid']); ?>
                                </strong>
                            </button>


                            <?php foreach ($statistics['forms'] as $form_statistics) : ?>

                                <button
                                    type="button"
                                    class="registration-log__stat registration-log__stat--form"
                                    data-filter-type="form"
                                    data-filter-value="<?php echo esc_attr($form_statistics['id']); ?>"
                                >

                                    <span class="registration-log__stat-label">

                                        <?php
                                        echo esc_html(
                                            $form_statistics['name']
                                        );
                                        ?>

                                        <?php if ($form_statistics['id'] !== '') : ?>

                                            <small>
                                                #<?php echo esc_html($form_statistics['id']); ?>
                                            </small>

                                        <?php endif; ?>

                                    </span>

                                    <strong class="registration-log__stat-value">
                                        <?php echo esc_html($form_statistics['total']); ?>
                                    </strong>

                                    <span class="registration-log__stat-details">

                                        V:
                                        <?php echo esc_html($form_statistics['valid']); ?>

                                        /

                                        I:
                                        <?php echo esc_html($form_statistics['invalid']); ?>

                                    </span>

                                </button>

                            <?php endforeach; ?>

                        </div>

                    </div>


                    <?php if (!empty($rows)) : ?>

                        <a
                            href="<?php echo esc_url($this->get_download_url()); ?>"
                            class="registration-log__download"
                        >
                            Download CSV
                        </a>

                    <?php endif; ?>

                </div>


                <?php if (empty($rows)) : ?>

                    <div class="registration-log__empty">
                        No registrations found.
                    </div>

                <?php else : ?>

                    <?php

                    /**
                     * First CSV row contains column headers.
                     */
                    $headers = array_shift($rows);

                    /**
                     * Find important column indexes dynamically.
                     */
                    $status_index = array_search(
                        'Status',
                        $headers,
                        true
                    );

                    $form_id_index = array_search(
                        'Form ID',
                        $headers,
                        true
                    );

                    /**
                     * Display newest submissions first.
                     */
                    $rows = array_reverse($rows);

                    ?>

                    <div class="registration-log__results-info">

                        Showing

                        <strong class="registration-log__visible-count">
                            0
                        </strong>

                        of

                        <strong class="registration-log__filtered-count">
                            0
                        </strong>

                        registrations

                    </div>


                    <div class="registration-log__table-wrapper">

                        <table class="registration-log__table">

                            <thead>

                                <tr>

                                    <?php foreach ($headers as $header) : ?>

                                        <th>
                                            <?php echo esc_html($header); ?>
                                        </th>

                                    <?php endforeach; ?>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($rows as $row) : ?>

                                    <?php

                                    $row_status = (
                                        $status_index !== false
                                        && isset($row[$status_index])
                                    )
                                        ? strtoupper(
                                            trim($row[$status_index])
                                        )
                                        : '';

                                    $row_form_id = (
                                        $form_id_index !== false
                                        && isset($row[$form_id_index])
                                    )
                                        ? trim($row[$form_id_index])
                                        : '';

                                    ?>

                                    <tr
                                        class="registration-log__row"
                                        data-status="<?php echo esc_attr($row_status); ?>"
                                        data-form-id="<?php echo esc_attr($row_form_id); ?>"
                                    >

                                        <?php foreach ($headers as $index => $header) : ?>

                                            <td>

                                                <?php
                                                echo esc_html(
                                                    isset($row[$index])
                                                        ? $row[$index]
                                                        : ''
                                                );
                                                ?>

                                            </td>

                                        <?php endforeach; ?>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>


                    <div class="registration-log__no-results">
                        No registrations match this filter.
                    </div>


                    <div class="registration-log__pagination">

                        <button
                            type="button"
                            class="registration-log__pagination-button registration-log__pagination-prev"
                        >
                            Previous
                        </button>

                        <div class="registration-log__pagination-pages"></div>

                        <button
                            type="button"
                            class="registration-log__pagination-button registration-log__pagination-next"
                        >
                            Next
                        </button>

                    </div>

                <?php endif; ?>

            </div>

            <?php

            $this->render_script();

            return ob_get_clean();
        }


        /**
         * Renders styles used by the shortcode.
         */
        private function render_styles()
        {
            ?>

            <style>
                .row.limit-width:has(.registration-log) {
                    max-width: 100% !important;
                }

                .row-container:has(.registration-log) .post-title-wrapper {
                    display: none;
                }

                .registration-log {
                    width: 100%;
                    margin: 20px 0;
                    box-sizing: border-box;
                }

                .registration-log *,
                .registration-log *::before,
                .registration-log *::after {
                    box-sizing: border-box;
                }

                /* Header */

                .registration-log__header {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 20px;
                    margin-bottom: 20px;
                }

                .registration-log__summary {
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 10px;
                }

                .registration-log__heading {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .registration-log__title {
                    margin: 0;
                    padding: 0;
                    font-size: 22px;
                    line-height: 1.3;
                }

                /* Filters / statistics */

                .registration-log__stats {
                    display: flex;
                    align-items: stretch;
                    flex-wrap: wrap;
                    gap: 8px;
                }

                .registration-log__stat {
                    display: inline-flex;
                    align-items: center;
                    gap: 7px;

                    min-height: 40px;

                    margin: 0;
                    padding: 7px 12px;

                    border: 1px solid #d8d8d8;
                    border-radius: 5px;

                    background: #fff;
                    color: #333;

                    font-family: inherit;
                    font-size: 13px;
                    line-height: 1.2;

                    cursor: pointer;

                    transition:
                        border-color 0.15s ease,
                        background-color 0.15s ease,
                        color 0.15s ease,
                        box-shadow 0.15s ease;
                }

                .registration-log__stat:hover {
                    border-color: #999;
                    background: #f7f7f7;
                }

                .registration-log__stat:focus {
                    outline: none;
                }

                .registration-log__stat:focus-visible {
                    outline: 2px solid #222;
                    outline-offset: 2px;
                }

                .registration-log__stat.is-active {
                    border-color: #222;
                    background: #222;
                    color: #fff;
                    box-shadow: 0 0 0 1px #222;
                }

                .registration-log__stat-label {
                    color: inherit;
                }

                .registration-log__stat-label small {
                    margin-left: 3px;
                    color: inherit;
                    font-size: 10px;
                    opacity: 0.65;
                }

                .registration-log__stat-value {
                    color: inherit;
                    font-size: 16px;
                    font-weight: 700;
                }

                .registration-log__stat-details {
                    padding-left: 7px;
                    border-left: 1px solid rgba(0, 0, 0, 0.15);

                    color: inherit;
                    font-size: 11px;
                    white-space: nowrap;

                    opacity: 0.7;
                }

                .registration-log__stat.is-active .registration-log__stat-details {
                    border-color: rgba(255, 255, 255, 0.25);
                }

                /* Total */

                .registration-log__stat--total:not(.is-active) {
                    background: #f3f3f3;
                }

                /* Valid */

                .registration-log__stat--valid:not(.is-active) {
                    border-color: #b7dfc1;
                    background: #f4fbf6;
                }

                .registration-log__stat--valid:not(.is-active)
                .registration-log__stat-value {
                    color: #218838;
                }

                /* Invalid */

                .registration-log__stat--invalid:not(.is-active) {
                    border-color: #efc2c2;
                    background: #fff6f6;
                }

                .registration-log__stat--invalid:not(.is-active)
                .registration-log__stat-value {
                    color: #c62828;
                }

                /* Form buttons */

                .registration-log__stat--form:not(.is-active) {
                    background: #f7f7f7;
                }

                /* Download button */

                .registration-log__download {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;

                    min-height: 40px;

                    padding: 10px 18px;

                    border: 0;
                    border-radius: 5px;

                    background: #222;
                    color: #fff !important;

                    font-size: 14px;
                    font-weight: 600;
                    line-height: 1;

                    text-decoration: none !important;

                    cursor: pointer;
                    white-space: nowrap;

                    transition:
                        background-color 0.15s ease,
                        opacity 0.15s ease;
                }

                .registration-log__download:hover {
                    background: #444;
                    color: #fff !important;
                }

                .registration-log__download:focus-visible {
                    outline: 2px solid #222;
                    outline-offset: 2px;
                }

                /* Results info */

                .registration-log__results-info {
                    margin: 0 0 10px;

                    color: #777;

                    font-size: 12px;
                }

                .registration-log__results-info strong {
                    color: #333;
                    font-weight: 700;
                }

                /* Table */

                .registration-log__table-wrapper {
                    width: 100%;
                    overflow-x: auto;

                    border: 1px solid #ddd;
                    border-radius: 6px;

                    background: #fff;
                }

                .registration-log__table {
                    width: 100%;
                    min-width: 1200px;

                    margin: 0;

                    border: 0;
                    border-collapse: collapse;

                    background: #fff;

                    font-size: 14px;
                }

                .registration-log__table thead {
                    background: #f5f5f5;
                }

                .registration-log__table th {
                    padding: 12px 14px;

                    border: 0;
                    border-bottom: 1px solid #ddd;

                    background: #f5f5f5;
                    color: #222;

                    text-align: left;
                    font-weight: 600;

                    white-space: nowrap;
                }

                .registration-log__table td {
                    min-width: 110px;

                    padding: 11px 14px;

                    border: 0;
                    border-bottom: 1px solid #eee;

                    color: #333;

                    vertical-align: top;

                    white-space: normal;
                    word-break: break-word;
                }

                .registration-log__table tbody tr:last-child td {
                    border-bottom: 0;
                }

                .registration-log__table tbody tr:hover td {
                    background: #fafafa;
                }

                /* Date */

                .registration-log__table th:nth-child(1),
                .registration-log__table td:nth-child(1) {
                    min-width: 110px;
                    white-space: nowrap;
                }

                /* Time */

                .registration-log__table th:nth-child(2),
                .registration-log__table td:nth-child(2) {
                    min-width: 90px;
                    white-space: nowrap;
                }

                /* Status */

                .registration-log__table th:nth-child(3),
                .registration-log__table td:nth-child(3) {
                    min-width: 90px;
                    white-space: nowrap;
                    font-weight: 600;
                }

                /* Form ID */

                .registration-log__table th:nth-child(4),
                .registration-log__table td:nth-child(4) {
                    min-width: 80px;
                    white-space: nowrap;
                }

                /* Form Name */

                .registration-log__table th:nth-child(5),
                .registration-log__table td:nth-child(5) {
                    min-width: 180px;
                }

                /* IP */

                .registration-log__table th:nth-child(6),
                .registration-log__table td:nth-child(6) {
                    min-width: 130px;
                    white-space: nowrap;
                }

                /* Data */

                .registration-log__table th:nth-child(7),
                .registration-log__table td:nth-child(7) {
                    min-width: 350px;
                }

                /* Validation Errors */

                .registration-log__table th:nth-child(8),
                .registration-log__table td:nth-child(8) {
                    min-width: 250px;
                }

                /* Empty state */

                .registration-log__empty {
                    padding: 20px;

                    border: 1px solid #ddd;
                    border-radius: 6px;

                    background: #fff;
                    color: #666;
                }

                /* No filtered results */

                .registration-log__no-results {
                    display: none;

                    padding: 25px;

                    border: 1px solid #ddd;
                    border-radius: 5px;

                    background: #fff;

                    color: #777;

                    text-align: center;
                }

                /* Pagination */

                .registration-log__pagination {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    flex-wrap: wrap;

                    gap: 6px;

                    margin-top: 18px;
                }

                .registration-log__pagination-pages {
                    display: flex;
                    align-items: center;
                    flex-wrap: wrap;
                    gap: 6px;
                }

                .registration-log__pagination-button {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;

                    min-width: 38px;
                    height: 38px;

                    padding: 0 12px;

                    border: 1px solid #ddd;
                    border-radius: 4px;

                    background: #fff;
                    color: #333;

                    font-family: inherit;
                    font-size: 13px;
                    line-height: 1;

                    cursor: pointer;

                    transition:
                        border-color 0.15s ease,
                        background-color 0.15s ease,
                        color 0.15s ease;
                }

                .registration-log__pagination-button:hover:not(:disabled) {
                    border-color: #999;
                    background: #f5f5f5;
                }

                .registration-log__pagination-button.is-active {
                    border-color: #222;
                    background: #222;
                    color: #fff;
                }

                .registration-log__pagination-button:disabled {
                    cursor: default;
                    opacity: 0.35;
                }

                .registration-log__pagination-button:focus {
                    outline: none;
                }

                .registration-log__pagination-button:focus-visible {
                    outline: 2px solid #222;
                    outline-offset: 2px;
                }

                /* Mobile */

                @media (max-width: 767px) {

                    .registration-log {
                        margin: 15px 0;
                    }

                    .registration-log__header {
                        align-items: stretch;
                        flex-direction: column;
                        gap: 12px;
                    }

                    .registration-log__summary {
                        width: 100%;
                        align-items: stretch;
                        flex-direction: column;
                    }

                    .registration-log__stats {
                        width: 100%;
                    }

                    .registration-log__stat {
                        flex-grow: 1;
                        justify-content: center;
                    }

                    .registration-log__download {
                        width: 100%;
                    }

                    .registration-log__pagination {
                        width: 100%;
                    }

                    .registration-log__pagination-prev,
                    .registration-log__pagination-next {
                        flex-grow: 1;
                    }

                    .registration-log__pagination-pages {
                        justify-content: center;
                        width: 100%;
                        order: -1;
                    }
                }

            </style>

            <?php

            error_log('test');
        }

        /**
         * Renders JavaScript used for filtering
         * and pagination.
         */
        private function render_script()
        {
            ?>

            <script>
                document.addEventListener('DOMContentLoaded', function () {

                    document
                        .querySelectorAll('.registration-log')
                        .forEach(function (container) {

                            const rows = Array.from(
                                container.querySelectorAll(
                                    '.registration-log__row'
                                )
                            );

                            if (!rows.length) {
                                return;
                            }

                            const filterButtons = Array.from(
                                container.querySelectorAll(
                                    '.registration-log__stat'
                                )
                            );

                            const pagination = container.querySelector(
                                '.registration-log__pagination'
                            );

                            const paginationPages = container.querySelector(
                                '.registration-log__pagination-pages'
                            );

                            const prevButton = container.querySelector(
                                '.registration-log__pagination-prev'
                            );

                            const nextButton = container.querySelector(
                                '.registration-log__pagination-next'
                            );

                            const visibleCount = container.querySelector(
                                '.registration-log__visible-count'
                            );

                            const filteredCount = container.querySelector(
                                '.registration-log__filtered-count'
                            );

                            const noResults = container.querySelector(
                                '.registration-log__no-results'
                            );

                            const perPage = parseInt(
                                container.dataset.perPage || '30',
                                10
                            );

                            let currentPage = 1;

                            let activeFilterType = 'all';
                            let activeFilterValue = '';


                            /**
                             * Returns rows matching the currently
                             * selected filter.
                             */
                            function getFilteredRows() {

                                return rows.filter(function (row) {

                                    if (activeFilterType === 'all') {
                                        return true;
                                    }

                                    if (activeFilterType === 'status') {

                                        return row.dataset.status ===
                                            activeFilterValue;
                                    }

                                    if (activeFilterType === 'form') {

                                        return row.dataset.formId ===
                                            activeFilterValue;
                                    }

                                    return true;
                                });
                            }


                            /**
                             * Renders pagination page buttons.
                             */
                            function renderPagination(
                                totalPages
                            ) {

                                paginationPages.innerHTML = '';

                                if (totalPages <= 1) {

                                    pagination.style.display = 'none';

                                    return;
                                }

                                pagination.style.display = 'flex';


                                /**
                                 * Limit the number of visible
                                 * pagination buttons.
                                 */
                                let startPage = Math.max(
                                    1,
                                    currentPage - 2
                                );

                                let endPage = Math.min(
                                    totalPages,
                                    currentPage + 2
                                );


                                /**
                                 * Keep up to five page buttons visible.
                                 */
                                if (
                                    currentPage <= 3
                                ) {
                                    endPage = Math.min(
                                        totalPages,
                                        5
                                    );
                                }

                                if (
                                    currentPage >= totalPages - 2
                                ) {
                                    startPage = Math.max(
                                        1,
                                        totalPages - 4
                                    );
                                }


                                for (
                                    let page = startPage;
                                    page <= endPage;
                                    page++
                                ) {

                                    const button =
                                        document.createElement(
                                            'button'
                                        );

                                    button.type = 'button';

                                    button.className =
                                        'registration-log__pagination-button registration-log__pagination-page';

                                    button.textContent = page;

                                    if (page === currentPage) {

                                        button.classList.add(
                                            'is-active'
                                        );
                                    }

                                    button.addEventListener(
                                        'click',
                                        function () {

                                            currentPage = page;

                                            renderRows();

                                            scrollToTable();
                                        }
                                    );

                                    paginationPages.appendChild(
                                        button
                                    );
                                }


                                prevButton.disabled =
                                    currentPage <= 1;

                                nextButton.disabled =
                                    currentPage >= totalPages;
                            }


                            /**
                             * Displays rows for the active filter
                             * and current pagination page.
                             */
                            function renderRows() {

                                const filteredRows =
                                    getFilteredRows();

                                const totalFiltered =
                                    filteredRows.length;

                                const totalPages = Math.max(
                                    1,
                                    Math.ceil(
                                        totalFiltered / perPage
                                    )
                                );


                                /**
                                 * Prevent current page from being
                                 * outside the available page range.
                                 */
                                if (currentPage > totalPages) {
                                    currentPage = totalPages;
                                }


                                /**
                                 * Hide every row first.
                                 */
                                rows.forEach(function (row) {
                                    row.style.display = 'none';
                                });


                                const start =
                                    (currentPage - 1) * perPage;

                                const end =
                                    start + perPage;

                                const visibleRows =
                                    filteredRows.slice(
                                        start,
                                        end
                                    );


                                visibleRows.forEach(
                                    function (row) {

                                        row.style.display =
                                            'table-row';
                                    }
                                );


                                if (visibleCount) {

                                    visibleCount.textContent =
                                        visibleRows.length;
                                }

                                if (filteredCount) {

                                    filteredCount.textContent =
                                        totalFiltered;
                                }


                                if (noResults) {

                                    noResults.style.display =
                                        totalFiltered === 0
                                            ? 'block'
                                            : 'none';
                                }


                                renderPagination(
                                    totalPages
                                );
                            }


                            /**
                             * Scrolls back to the table after
                             * changing pagination page.
                             */
                            function scrollToTable() {

                                const tableWrapper =
                                    container.querySelector(
                                        '.registration-log__table-wrapper'
                                    );

                                if (!tableWrapper) {
                                    return;
                                }

                                const top =
                                    tableWrapper
                                        .getBoundingClientRect()
                                        .top
                                    + window.pageYOffset
                                    - 30;

                                window.scrollTo({
                                    top: top,
                                    behavior: 'smooth'
                                });
                            }


                            /**
                             * Handle filter buttons.
                             */
                            filterButtons.forEach(
                                function (button) {

                                    button.addEventListener(
                                        'click',
                                        function () {

                                            filterButtons.forEach(
                                                function (item) {

                                                    item.classList.remove(
                                                        'is-active'
                                                    );
                                                }
                                            );

                                            button.classList.add(
                                                'is-active'
                                            );

                                            activeFilterType =
                                                button.dataset.filterType
                                                || 'all';

                                            activeFilterValue =
                                                button.dataset.filterValue
                                                || '';

                                            currentPage = 1;

                                            renderRows();
                                        }
                                    );
                                }
                            );


                            /**
                             * Previous page.
                             */
                            prevButton.addEventListener(
                                'click',
                                function () {

                                    if (currentPage <= 1) {
                                        return;
                                    }

                                    currentPage--;

                                    renderRows();

                                    scrollToTable();
                                }
                            );


                            /**
                             * Next page.
                             */
                            nextButton.addEventListener(
                                'click',
                                function () {

                                    const filteredRows =
                                        getFilteredRows();

                                    const totalPages =
                                        Math.ceil(
                                            filteredRows.length
                                            / perPage
                                        );

                                    if (
                                        currentPage >= totalPages
                                    ) {
                                        return;
                                    }

                                    currentPage++;

                                    renderRows();

                                    scrollToTable();
                                }
                            );


                            /**
                             * Initial table rendering.
                             */
                            renderRows();
                        });
                });
            </script>

            <?php
        }

        /**
         * Creates the Logs page if it does not exist.
         */
        public function ensure_logs_page_exists()
        {
            $existing_page = get_page_by_path(
                'logs',
                OBJECT,
                'page'
            );

            if (
                $existing_page
                && $existing_page->post_status !== 'trash'
            ) {
                return;
            }

            $page_id = wp_insert_post(
                array(
                    'post_title'   => 'Logs',
                    'post_name'    => 'logs',
                    'post_content' => '[registration_log]',
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                ),
                true
            );

            if (is_wp_error($page_id)) {
                error_log(
                    'PWE Registration Log: Unable to create Logs page: '
                    . $page_id->get_error_message()
                );
            }
        }
    }

    /**
     * Initialize registration log.
     */
    new PWE_Registration_Log();
}