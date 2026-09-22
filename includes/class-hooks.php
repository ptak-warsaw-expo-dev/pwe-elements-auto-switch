<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Add filter to override menu output
add_action('plugins_loaded', function() {
    add_filter('pwe_override_menu_output', function($html) {
        ob_start();
        Menu::render('all');
        return ob_get_clean();
    });
});

// Loading registration classes (for AJAX and specific pages)
add_action('init', function() {

    // 1. Jeśli to AJAX – zawsze ładujemy pliki i inicjalizujemy klasy
    if (wp_doing_ajax()) {
        $file_visitors  = PWE_PLUGIN_PATH . 'elements/confirmation-visitors-registration/confirmation-visitors-registration/confirmation-visitors-registration.php';
        $file_exhibitors = PWE_PLUGIN_PATH . 'elements/confirmation-exhibitors-registration/confirmation-exhibitors-registration/confirmation-exhibitors-registration.php';

        if (file_exists($file_visitors)) {
            require_once $file_visitors;
            if (class_exists('Confirmation_Visitors_Registration')) {
                Confirmation_Visitors_Registration::init();
            }
        }

        if (file_exists($file_exhibitors)) {
            require_once $file_exhibitors;
            if (class_exists('Confirmation_Exhibitors_Registration')) {
                Confirmation_Exhibitors_Registration::init();
            }
        }
        return;
    }
});

// Redirects for trade fair plan and post-show reports
add_action('template_redirect', function () {

    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

    if (!class_exists('PWE_Functions')) {
        return;
    }

    $cache_key = $_SERVER['HTTP_HOST'] ?? 'default';
    $transient_key = 'pwe_fairs_redirects_' . md5($cache_key);

    $redirects = get_transient($transient_key);

    if ($redirects === false) {

        $files = PWE_Functions::get_database_fairs_data_files();

        if (empty($files) || !is_array($files)) {
            return;
        }

        $redirects = [];
        $fair_plan_years = [];

        foreach ($files as $file) {
            if (($file->category_slug ?? '') === 'trade-fair-plan' && !empty($file->year)) {
                $fair_plan_years[] = (int) $file->year;
            }
        }

        if (!empty($fair_plan_years)) {
            $latest_year = max($fair_plan_years);

            $redirects['plan-targow-' . $latest_year] = home_url('/plan-targow/');
            $redirects['en/fair-plan-' . $latest_year] = home_url('/en/fair-plan/');
        }

        foreach ($files as $file) {

            if (($file->category_slug ?? '') !== 'post-show-report') {
                continue;
            }

            if (empty($file->year) || empty($file->language) || empty($file->file_path)) {
                continue;
            }

            $year = (string) $file->year;
            $lang = (string) $file->language;

            if ($lang === 'pl') {
                $redirects['post-show-' . $year] = 'https://cap.warsawexpo.eu' . $file->file_path;
            }

            if ($lang === 'en') {
                $redirects['en/post-show-' . $year] = 'https://cap.warsawexpo.eu' . $file->file_path;
            }
        }

        set_transient($transient_key, $redirects, 600);
    }

    if (!empty($redirects[$path])) {
        wp_redirect($redirects[$path], 301);
        exit;
    }
});

// Remove white space in phone input Gravity Forms
add_filter( 'gform_save_field_value', function ( $value, $entry, $field, $form, $input_id ) {

    if ( $field && $field->get_input_type() === 'phone' && is_string( $value ) ) {
        $value = preg_replace( '/\s+/u', '', $value );
    }

    return $value;

}, 10, 5 );

add_action(
    'gform_after_email',
    [ 'PWE_Functions', 'gravity_forms_smtp_monitor' ],
    10,
    12
);

/**
 * Gravity Forms entries cleanup by hardcoded e-mail address list.
 *
 * No WP-Cron and no admin panel are used. The plugin performs only a cheap
 * timestamp check during a normal request. Gravity Forms is scanned at most
 * once every 7 days per site.
 */
if ( ! class_exists( 'PWE_GF_Email_Entry_Cleanup' ) ) {

    class PWE_GF_Email_Entry_Cleanup {

        /**
         * Central list used on every site with this plugin version.
         * Add one e-mail address per line.
         */
        private const EMAILS = [
            'anton.melnychuk@warsawexpo.eu',
            'piotr.krupniewski@warsawexpo.eu',
            'jakub.chola@warsawexpo.eu',
            'antonmelnychuk1@gmail.com',
            'jakub.goral@warsawexpo.eu',
            'nataliasobolptakexpo@gmail.com',
            'oliwia.ptasinska@warsawexpo.eu'
        ];

        /**
         * E-mail prefixes to remove, regardless of the domain.
         * Example: 'test@' matches test@example.com and test@company.pl.
         */
        private const EMAIL_PREFIXES = [
            'test@',
            'asd@',
        ];

        /**
         * Domains where Gravity Forms cleanup must never run.
         * Add one hostname per line, without protocol or path.
         */
        private const EXCLUDED_DOMAINS = [
            'mr.glasstec.pl',
        ];

        const OPTION_LAST_RUN    = 'pwe_gf_cleanup_last_run';
        const OPTION_NEXT_RUN    = 'pwe_gf_cleanup_next_run';
        const OPTION_LAST_RESULT = 'pwe_gf_cleanup_last_result';
        const OPTION_EMAILS_HASH = 'pwe_gf_cleanup_emails_hash';
        const LOCK_KEY           = 'pwe_gf_cleanup_lock';
        const INTERVAL           = WEEK_IN_SECONDS;

        public static function init() {
            add_action( 'wp_loaded', [ __CLASS__, 'maybe_run_automatic_cleanup' ], 100 );
        }

        public static function maybe_run_automatic_cleanup() {
            if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
                return;
            }

            if ( self::is_current_domain_excluded() ) {
                return;
            }

            $emails = self::get_emails();
            $prefixes = self::get_email_prefixes();

            if ( empty( $emails ) && empty( $prefixes ) ) {
                return;
            }

            $next_run = (int) get_option( self::OPTION_NEXT_RUN, 0 );
            $emails_hash = self::get_cleanup_rules_hash( $emails, $prefixes );
            $saved_hash = (string) get_option( self::OPTION_EMAILS_HASH, '' );

            // Run immediately after the hardcoded list changes. Otherwise, at most once per week.
            if ( $saved_hash === $emails_hash && $next_run > 0 && time() < $next_run ) {
                return;
            }

            self::run_cleanup();
        }

        private static function normalize_emails( $emails ) {
            if ( ! is_array( $emails ) ) {
                $emails = preg_split( '/[\s,;]+/u', (string) $emails, -1, PREG_SPLIT_NO_EMPTY );
            }

            $normalized = [];

            foreach ( $emails as $email ) {
                $email = strtolower( trim( sanitize_email( $email ) ) );

                if ( $email && is_email( $email ) ) {
                    $normalized[ $email ] = $email;
                }
            }

            return array_values( $normalized );
        }

        private static function get_emails() {
            return self::normalize_emails( self::EMAILS );
        }

        private static function get_email_prefixes() {
            $normalized = [];

            foreach ( self::EMAIL_PREFIXES as $prefix ) {
                $prefix = strtolower( trim( (string) $prefix ) );

                if ( $prefix !== '' && strpos( $prefix, '@' ) !== false ) {
                    $normalized[ $prefix ] = $prefix;
                }
            }

            return array_values( $normalized );
        }

        private static function get_cleanup_rules_hash( $emails, $prefixes ) {
            return md5( implode( '|', $emails ) . '||' . implode( '|', $prefixes ) );
        }

        private static function normalize_domain( $domain ) {
            $domain = strtolower( trim( (string) $domain ) );
            $domain = preg_replace( '#^https?://#i', '', $domain );
            $domain = preg_replace( '#/.*$#', '', $domain );
            $domain = preg_replace( '/:\d+$/', '', $domain );
            $domain = rtrim( $domain, '.' );

            if ( strpos( $domain, 'www.' ) === 0 ) {
                $domain = substr( $domain, 4 );
            }

            return $domain;
        }

        private static function get_current_domain() {
            $host = wp_parse_url( home_url(), PHP_URL_HOST );

            if ( ! $host && ! empty( $_SERVER['HTTP_HOST'] ) ) {
                $host = (string) $_SERVER['HTTP_HOST'];
            }

            return self::normalize_domain( $host );
        }

        private static function is_current_domain_excluded() {
            $current_domain = self::get_current_domain();

            if ( $current_domain === '' ) {
                return false;
            }

            foreach ( self::EXCLUDED_DOMAINS as $excluded_domain ) {
                if ( $current_domain === self::normalize_domain( $excluded_domain ) ) {
                    return true;
                }
            }

            return false;
        }

        private static function entry_matches_prefix( $entry, $email_field_ids, $prefixes ) {
            foreach ( $email_field_ids as $field_id ) {
                $value = strtolower( trim( (string) rgar( $entry, $field_id ) ) );

                if ( $value === '' ) {
                    continue;
                }

                foreach ( $prefixes as $prefix ) {
                    if ( strpos( $value, $prefix ) === 0 ) {
                        return true;
                    }
                }
            }

            return false;
        }

        private static function get_email_field_ids( $form ) {
            $field_ids = [];

            if ( empty( $form['fields'] ) || ! is_array( $form['fields'] ) ) {
                return $field_ids;
            }

            foreach ( $form['fields'] as $field ) {
                if ( ! is_object( $field ) || ! method_exists( $field, 'get_input_type' ) ) {
                    continue;
                }

                if ( $field->get_input_type() === 'email' ) {
                    $field_ids[] = (string) $field->id;
                }
            }

            return array_values( array_unique( $field_ids ) );
        }

        private static function acquire_lock() {
            if ( get_transient( self::LOCK_KEY ) ) {
                return false;
            }

            set_transient( self::LOCK_KEY, 1, 10 * MINUTE_IN_SECONDS );
            return true;
        }

        private static function release_lock() {
            delete_transient( self::LOCK_KEY );
        }

        public static function run_cleanup() {
            if ( self::is_current_domain_excluded() ) {
                return;
            }

            if ( ! class_exists( 'GFAPI' ) ) {
                self::save_error_result( 'Gravity Forms nie jest aktywne lub GFAPI nie jest dostępne.' );
                return;
            }

            $emails = self::get_emails();
            $prefixes = self::get_email_prefixes();

            if ( ( empty( $emails ) && empty( $prefixes ) ) || ! self::acquire_lock() ) {
                return;
            }

            $started_at = time();
            $forms_checked = 0;
            $forms_with_email = 0;
            $entry_ids = [];
            $errors = [];

            try {
                // Include active, inactive and trashed forms so matching entries are
                // permanently removed even when the form itself is no longer active.
                $forms = GFAPI::get_forms( null, null );

                if ( ! is_array( $forms ) ) {
                    $forms = [];
                }

                foreach ( $forms as $form ) {
                    $forms_checked++;

                    $email_field_ids = self::get_email_field_ids( $form );

                    if ( empty( $email_field_ids ) ) {
                        continue;
                    }

                    $forms_with_email++;
                    $form_id = (int) $form['id'];

                    foreach ( array_chunk( $emails, 25 ) as $email_batch ) {
                        foreach ( [ 'active', 'spam', 'trash' ] as $status ) {
                            $field_filters = [ 'mode' => 'any' ];

                            foreach ( $email_field_ids as $field_id ) {
                                $field_filters[] = [
                                    'key'      => $field_id,
                                    'value'    => $email_batch,
                                    'operator' => 'IN',
                                ];
                            }

                            $search_criteria = [
                                'status'        => $status,
                                'field_filters' => $field_filters,
                            ];

                            $offset = 0;
                            $page_size = 200;

                            do {
                                $total_count = 0;
                                $paging = [
                                    'offset'    => $offset,
                                    'page_size' => $page_size,
                                ];

                                $entries = GFAPI::get_entry_ids( $form_id, $search_criteria, null, $paging, $total_count );

                                if ( is_wp_error( $entries ) ) {
                                    $errors[] = sprintf( 'Formularz %d: %s', $form_id, $entries->get_error_message() );
                                    break;
                                }

                                if ( ! is_array( $entries ) || empty( $entries ) ) {
                                    break;
                                }

                                foreach ( $entries as $entry_id ) {
                                    $entry_id = (int) $entry_id;

                                    if ( $entry_id > 0 ) {
                                        $entry_ids[ $entry_id ] = $entry_id;
                                    }
                                }

                                $offset += count( $entries );
                            } while ( $offset < (int) $total_count );
                        }
                    }


                    // Gravity Forms has no native "starts with" operator.
                    // Search broadly with CONTAINS, then verify the prefix exactly before deleting.
                    if ( ! empty( $prefixes ) ) {
                        foreach ( [ 'active', 'spam', 'trash' ] as $status ) {
                            $field_filters = [ 'mode' => 'any' ];

                            foreach ( $email_field_ids as $field_id ) {
                                foreach ( $prefixes as $prefix ) {
                                    $field_filters[] = [
                                        'key'      => $field_id,
                                        'value'    => $prefix,
                                        'operator' => 'CONTAINS',
                                    ];
                                }
                            }

                            $search_criteria = [
                                'status'        => $status,
                                'field_filters' => $field_filters,
                            ];

                            $offset = 0;
                            $page_size = 200;

                            do {
                                $total_count = 0;
                                $paging = [
                                    'offset'    => $offset,
                                    'page_size' => $page_size,
                                ];

                                $candidate_ids = GFAPI::get_entry_ids( $form_id, $search_criteria, null, $paging, $total_count );

                                if ( is_wp_error( $candidate_ids ) ) {
                                    $errors[] = sprintf( 'Formularz %d: %s', $form_id, $candidate_ids->get_error_message() );
                                    break;
                                }

                                if ( ! is_array( $candidate_ids ) || empty( $candidate_ids ) ) {
                                    break;
                                }

                                foreach ( $candidate_ids as $candidate_id ) {
                                    $candidate_id = (int) $candidate_id;

                                    if ( $candidate_id <= 0 || isset( $entry_ids[ $candidate_id ] ) ) {
                                        continue;
                                    }

                                    $entry = GFAPI::get_entry( $candidate_id );

                                    if ( is_wp_error( $entry ) ) {
                                        $errors[] = sprintf( 'Wpis %d: %s', $candidate_id, $entry->get_error_message() );
                                        continue;
                                    }

                                    if ( self::entry_matches_prefix( $entry, $email_field_ids, $prefixes ) ) {
                                        $entry_ids[ $candidate_id ] = $candidate_id;
                                    }
                                }

                                $offset += count( $candidate_ids );
                            } while ( $offset < (int) $total_count );
                        }
                    }
                }

                $deleted = 0;

                foreach ( $entry_ids as $entry_id ) {
                    $delete_result = GFAPI::delete_entry( $entry_id );

                    if ( is_wp_error( $delete_result ) ) {
                        $errors[] = sprintf( 'Wpis %d: %s', $entry_id, $delete_result->get_error_message() );
                        continue;
                    }

                    if ( $delete_result === false ) {
                        $errors[] = sprintf( 'Wpis %d: nie udało się usunąć.', $entry_id );
                        continue;
                    }

                    $deleted++;
                }

                $finished_at = time();

                update_option( self::OPTION_LAST_RUN, $finished_at, false );
                update_option( self::OPTION_NEXT_RUN, $finished_at + self::INTERVAL, false );
                update_option( self::OPTION_EMAILS_HASH, self::get_cleanup_rules_hash( $emails, $prefixes ), false );
                update_option( self::OPTION_LAST_RESULT, [
                    'started_at'       => $started_at,
                    'finished_at'      => $finished_at,
                    'emails_count'     => count( $emails ),
                    'prefixes_count'   => count( $prefixes ),
                    'forms_checked'    => $forms_checked,
                    'forms_with_email' => $forms_with_email,
                    'matched'          => count( $entry_ids ),
                    'deleted'          => $deleted,
                    'errors'           => array_slice( $errors, 0, 20 ),
                ], false );
            } finally {
                self::release_lock();
            }
        }

        private static function save_error_result( $message ) {
            $timestamp = time();

            update_option( self::OPTION_LAST_RUN, $timestamp, false );
            update_option( self::OPTION_NEXT_RUN, $timestamp + self::INTERVAL, false );
            update_option( self::OPTION_LAST_RESULT, [
                'started_at'  => $timestamp,
                'finished_at' => $timestamp,
                'error'       => (string) $message,
            ], false );
        }
    }

    PWE_GF_Email_Entry_Cleanup::init();
}

