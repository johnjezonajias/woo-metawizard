<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woo_MetaWizard_Admin {

    public static function init() {
        // Admin-specific hooks.
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
    }

    public static function add_admin_menu() {
        // Add a new submenu under WooCommerce.
        add_submenu_page(
            'woocommerce',
            __( 'Woo Meta Helper', 'woo-metawizard' ),
            __( 'Woo Meta Helper', 'woo-metawizard' ),
            'manage_options',
            'woo-metawizard',
            [ __CLASS__, 'render_admin_page' ]
        );
    }

    public static function render_admin_page() {
        echo '<div class="wrap">';
            echo '<h1>' . esc_html__( 'Woo Meta Helper Settings', 'woo-metawizard' ) . '</h1>';
            // Admin page content goes here.
        echo '</div>';
    }
}
