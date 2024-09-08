<?php

namespace WooMetaWizard\Includes;

use WooMetaWizard\Includes\Woo_MetaWizard_Admin;
use WooMetaWizard\Includes\Woo_MetaWizard_Metabox;
use WooMetaWizard\Includes\Woo_MetaWizard_Frontend;

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woo_MetaWizard {

    public static function init() {
        // Load dependencies.
        self::load_dependencies();

        // Initialize classes based on the environment.
        if ( is_admin() ) {
            Woo_MetaWizard_Admin::init();
            Woo_MetaWizard_Metabox::init();
        }

        // Frontend initialization.
        if ( ! is_admin() ) {
            Woo_MetaWizard_Frontend::init();
        }
    }

    public static function load_dependencies() {
        // Load dependencies via PSR-4 autoload.
    }

    public static function activate() {
        self::set_default_api_settings();
    }

    public static function deactivate() {
        $delete_data = get_option( 'woo_metawizard_delete_data_on_deactivation', 'no' );

        if ( 'yes' === $delete_data ) {
            self::delete_plugin_data();
        }
    }

    private static function set_default_api_settings() {
        if ( false === get_option( 'woo_metawizard_openai_api_key' ) ) {
            add_option( 'woo_metawizard_openai_api_key', '' );
        }

        if ( false === get_option( 'woo_metawizard_model' ) ) {
            add_option( 'woo_metawizard_model', 'gpt-4' );
        }

        if ( false === get_option( 'woo_metawizard_temperature' ) ) {
            add_option( 'woo_metawizard_temperature', '0.7' );
        }

        if ( false === get_option( 'woo_metawizard_max_tokens' ) ) {
            add_option( 'woo_metawizard_max_tokens', '260' );
        }
    }

    private static function delete_plugin_data() {
        // Delete settings.
        delete_option( 'woo_metawizard_openai_api_key' );
        delete_option( 'woo_metawizard_model' );
        delete_option( 'woo_metawizard_temperature' );
        delete_option( 'woo_metawizard_max_tokens' );
        delete_option( 'woo_metawizard_delete_data_on_deactivation' );

        // Delete custom database tables or post meta.
        global $wpdb;

        // Delete a custom table named _woo_metawizard_suggestions.
        $table_name = $wpdb->prefix . 'woo_metawizard_suggestions';
        $wpdb->query( "DROP TABLE IF EXISTS $table_name" );

        // Optionally, delete custom post meta or other data.
        $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE '_woo_metawizard_%'" );
    }
}
