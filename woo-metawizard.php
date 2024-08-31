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

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define constants.
define( 'WMH_VERSION', '1.0.0' );
define( 'WMH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WMH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include the core plugin class.
require_once WMH_PLUGIN_DIR . 'includes/class-woo-metawizard.php';

// Activation and deactivation hooks.
register_activation_hook( __FILE__, [ 'Woo_MetaWizard', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'Woo_MetaWizard', 'deactivate' ] );

// Initialize the plugin.
add_action( 'plugins_loaded', [ 'Woo_MetaWizard', 'init' ] );
