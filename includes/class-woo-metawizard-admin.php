<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woo_MetaWizard_Admin {

    public static function init() {
        // Admin-specific hooks.
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
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

    public static function register_settings() {
        // Register the setting for the OpenAI API key.
        register_setting( 'woo_metawizard_settings', 'woo_metawizard_openai_api_key', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);

        // Add OpenAI settings section.
        add_settings_section(
            'woo_metawizard_section',
            __( 'OpenAI Settings', 'woo-metawizard' ),
            null,
            'woo-metawizard'
        );

        // Add the OpenAI API key field.
        add_settings_field(
            'woo_metawizard_openai_api_key',
            __( 'API Key', 'woo-metawizard' ),
            [ __CLASS__, 'render_api_key_field' ],
            'woo-metawizard',
            'woo_metawizard_section'
        );
    }

    public static function render_api_key_field() {
        // Get the value of the API key from the database.
        $api_key = get_option( 'woo_metawizard_openai_api_key' );
        ?>
            <input type="text" name="woo_metawizard_openai_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
            <p class="description"><?php esc_html_e( 'Enter your OpenAI API key.', 'woo-metawizard' ); ?></p>
        <?php
    }

    public static function render_admin_page() {
        ?>
            <div class="wrap">
                <h1><?php esc_html_e( 'Woo Meta Helper Settings', 'woo-metawizard' ); ?></h1>
                <form action="options.php" method="post">
                    <?php
                        settings_fields( 'woo_metawizard_settings' );
                        do_settings_sections( 'woo-metawizard' );
                        submit_button( __( 'Save Settings', 'woo-metawizard' ) );
                    ?>
                </form>
            </div>
        <?php
    }
}
