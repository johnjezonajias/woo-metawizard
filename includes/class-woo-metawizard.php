<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woo_MetaWizard {

    public static function init() {
        // Load dependencies.
        self::load_dependencies();

        // Initialize the admin and frontend classes.
        Woo_MetaWizard_Admin::init();
        Woo_MetaWizard_Frontend::init();
        Woo_MetaWizard_Metabox::init();
    }

    public static function load_dependencies() {
        require_once WMH_PLUGIN_DIR . 'includes/class-woo-metawizard-admin.php';
        require_once WMH_PLUGIN_DIR . 'includes/class-woo-metawizard-frontend.php';
        require_once WMH_PLUGIN_DIR . 'includes/class-woo-metawizard-metabox.php';

        // Include utility functions.
        require_once WMH_PLUGIN_DIR . 'utilities/class-woo-metawizard-utils.php';
        require_once WMH_PLUGIN_DIR . 'utilities/class-woo-metawizard-api.php';
    }

    public static function activate() {
        
    }

    public static function deactivate() {
        
    }
}
