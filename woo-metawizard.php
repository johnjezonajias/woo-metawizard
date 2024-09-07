<?php
/**
 * Woo Meta Helper
 *
 * @package           WooMetaWizard
 * @developer         John Jezon Ajias
 * @license           CC BY-NC 4.0
 *
 * @wordpress-plugin
 * Plugin Name:       Woo Meta Helper
 * Plugin URI:        https://webdevjohnajias.one/woo-meta-helper
 * Description:       A plugin to enhance WooCommerce meta handling, providing additional meta data management and customization options.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      8.1
 * Author:            John Jezon Ajias
 * Author URI:        https://webdevjohnajias.one
 * Text Domain:       woo-metawizard
 * License:           CC BY-NC 4.0
 * License URI:       https://creativecommons.org/licenses/by-nc/4.0/
 * Update URI:        https://webdevjohnajias.one/woo-meta-helper/update
 * Requires Plugins:  woocommerce, wordpress-seo
 */

namespace WooMetaWizard;

use WooMetaWizard\Includes\Woo_MetaWizard;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WMH_VERSION', '1.0.0' );
define( 'WMH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WMH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Check if Composer's autoload file exists and include it.
if ( file_exists( WMH_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
    require_once WMH_PLUGIN_DIR . 'vendor/autoload.php';
} else {
    // Handle the case when autoload.php is missing.
    error_log( 'Composer autoload file not found. Please install the dependencies via Composer. [ composer install --optimize-autoloader --no-dev ]' );
}

// Register hooks.
register_activation_hook( __FILE__, [ Woo_MetaWizard::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ Woo_MetaWizard::class, 'deactivate' ] );

// Initialize the plugin.
add_action( 'plugins_loaded', [ Woo_MetaWizard::class, 'init' ] );
