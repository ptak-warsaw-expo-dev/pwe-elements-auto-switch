<?php

if (!defined('ABSPATH')) {
    exit;
}

final class PWE_Updater
{
    public function __construct()
    {
        $this->setup_updater();
    }

    /**
     * Setting the auto-update mechanism
     */
    private function setup_updater(): void
    {
        $checker_file = PWE_PLUGIN_PATH . 'plugin-update-checker/plugin-update-checker.php';

        if (!file_exists($checker_file)) {
            return;
        }

        require_once $checker_file;

        $update_checker = Puc_v4_Factory::buildUpdateChecker(
            'https://github.com/ptak-warsaw-expo-dev/pwe-elements-auto-switch',
            PWE_PLUGIN_FILE,
            'pwe-elements-auto-switch'
        );

        $github_key = PWE_Functions::get_database_meta_data('github_personal_access_token');
        
        if (is_string($github_key) && trim($github_key) !== '') {
            $update_checker->setAuthentication(trim($github_key));
        }

        $update_checker->getVcsApi()->enableReleaseAssets();
    }
}