<?php

namespace WooMetaWizard\Includes;

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woo_MetaWizard_Admin {

    public static function init() {
        // Admin-specific hooks.
        add_action( 'admin_menu', [ __CLASS__, 'add_admin_menu' ] );
        add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
        add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
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

        register_setting( 'woo_metawizard_settings', 'woo_metawizard_model', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);
    
        register_setting( 'woo_metawizard_settings', 'woo_metawizard_temperature', [
            'sanitize_callback' => 'floatval'
        ]);
    
        register_setting( 'woo_metawizard_settings', 'woo_metawizard_max_tokens', [
            'sanitize_callback' => 'intval'
        ]);

        register_setting( 'woo_metawizard_settings', 'woo_metawizard_delete_data_on_deactivation', [
            'type'              => 'string',
            'sanitize_callback' =>  [ 'Woo_MetaWizard_Utils', 'sanitize_checkbox' ],
            'default'           => 'no'
        ]);

        // OpenAI settings section.
        add_settings_section(
            'woo_metawizard_section',
            __( 'OpenAI Settings', 'woo-metawizard' ),
            null,
            'woo-metawizard'
        );

        // OpenAI API key field.
        add_settings_field(
            'woo_metawizard_openai_api_key',
            __( 'API Key', 'woo-metawizard' ),
            [ __CLASS__, 'render_api_key_field' ],
            'woo-metawizard',
            'woo_metawizard_section'
        );

        // Model field.
        add_settings_field(
            'woo_metawizard_model',
            __( 'Model', 'woo-metawizard' ),
            [ __CLASS__, 'render_model_field' ],
            'woo-metawizard',
            'woo_metawizard_section'
        );

        // Temperature field.
        add_settings_field(
            'woo_metawizard_temperature',
            __( 'Temperature', 'woo-metawizard' ),
            [ __CLASS__, 'render_temperature_field' ],
            'woo-metawizard',
            'woo_metawizard_section'
        );

        // Max tokens field.
        add_settings_field(
            'woo_metawizard_max_tokens',
            __( 'Max Tokens', 'woo-metawizard' ),
            [ __CLASS__, 'render_max_tokens_field' ],
            'woo-metawizard',
            'woo_metawizard_section'
        );

        // Plugin settings section.
        add_settings_section(
            'woo_metawizard_plugin_section',
            __( 'Plugin Settings', 'woo-metawizard' ),
            null,
            'woo-metawizard'
        );

        // Delete plugin data field.
        add_settings_field(
            'woo_metawizard_delete_data_on_deactivation',
            __( 'Plugin Deactivation', 'woo-metawizard' ),
            [ __CLASS__, 'render_delete_data_on_deactivation_field' ],
            'woo-metawizard',
            'woo_metawizard_plugin_section'
        );
    }

    public static function render_api_key_field() {
        // Get the value of the API key from the database.
        $api_key = get_option( 'woo_metawizard_openai_api_key' );
        ?>
            <div class="woo-metawizard__field-wrap">
                <div class="woo-metawizard__tips">
                    <input type="text" name="woo_metawizard_openai_api_key" value="<?php echo esc_attr( $api_key ); ?>" class="regular-text" />
                    <span class="woo-metawizard__tips--tooltip" aria-label="<?php esc_attr_e( 'The API Key is required to authenticate requests to OpenAI. Obtain it from your OpenAI account dashboard. Keep it secure and do not share it.', 'woo-metawizard' ); ?>">?</span>
                </div>
                <p class="description">
                    <?php esc_html_e( 'Obtain your API Key from your OpenAI account dashboard. If you don’t have an account, sign up at', 'woo-metawizard' ); ?> 
                    <a href="https://platform.openai.com/signup" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'OpenAI', 'woo-metawizard' ); ?>
                    </a>.
                </p>
            </div>
        <?php
    }

    public static function render_model_field() {
        $model = get_option( 'woo_metawizard_model', 'gpt-4' );
        ?>
            <div class="woo-metawizard__field-wrap">
                <div class="woo-metawizard__tips">
                    <select name="woo_metawizard_model" id="woo_metawizard_model">
                        <option value="gpt-3.5-turbo" <?php selected( $model, 'gpt-3.5-turbo' ); ?>>GPT-3.5 Turbo</option>
                        <option value="gpt-4" <?php selected( $model, 'gpt-4' ); ?>>GPT-4</option>
                    </select>
                    <span class="woo-metawizard__tips--tooltip" aria-label="<?php esc_attr_e( 'Select the model you want to use. GPT-4 offers more advanced capabilities, while GPT-3.5 Turbo is faster and more cost-effective.', 'woo-metawizard' ); ?>">?</span>
                </div>
            </div>
        <?php
    }

    public static function render_temperature_field() {
        $temperature = get_option( 'woo_metawizard_temperature', 0.7 );
        ?>
            <div class="woo-metawizard__field-wrap">
                <div class="woo-metawizard__tips">
                    <input type="number" step="0.1" min="0" max="1" name="woo_metawizard_temperature" value="<?php echo esc_attr( $temperature ); ?>" />
                    <span class="woo-metawizard__tips--tooltip" aria-label="<?php esc_attr_e( 'Controls the creativity of the AI output. Lower values (e.g., 0.1) make the output more focused and deterministic, while higher values (e.g., 0.9) make it more random and creative.', 'woo-metawizard' ); ?>">?</span>
                </div>
            </div>
        <?php
    }

    public static function render_max_tokens_field() {
        $max_tokens = get_option( 'woo_metawizard_max_tokens', 260 );
        ?>
            <div class="woo-metawizard__field-wrap">
                <div class="woo-metawizard__tips">
                    <input type="number" min="100" max="500" name="woo_metawizard_max_tokens" value="<?php echo esc_attr( $max_tokens ); ?>" />
                    <span class="woo-metawizard__tips--tooltip" aria-label="<?php esc_attr_e( 'Defines the maximum length of the AI response in tokens. A token roughly corresponds to a word or punctuation mark.', 'woo-metawizard' ); ?>">?</span>
                </div>
            </div>
        <?php
    }

    public static function render_delete_data_on_deactivation_field() {
        $value = get_option( 'woo_metawizard_delete_data_on_deactivation', 'no' );
        ?>
        <input type="checkbox" name="woo_metawizard_delete_data_on_deactivation" value="yes" <?php checked( 'yes', $value ); ?>>
        <?php _e( 'Check this if you want to delete all plugin data when deactivating the plugin.', 'woo-metawizard' ); ?>
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

    public static function enqueue_scripts( $hook_suffix ) {
        // Enqueue styles and scripts on the Woo Meta Helper settings page.
    if ( $hook_suffix == 'woocommerce_page_woo-metawizard' ) {
        wp_enqueue_style(
            'woo-metawizard-settings-styles',
            WMH_PLUGIN_URL . 'assets/css/admin/admin-styles.css',
            [],
            WMH_VERSION
        );
    }

        // Only enqueue on the product edit screen.
        if ( $hook_suffix == 'post.php' && get_post_type() == 'product' ) {
            wp_enqueue_style(
                'woo-metawizard-styles',
                WMH_PLUGIN_URL . 'assets/css/styles.css',
                [],
                WMH_VERSION
            );

            wp_enqueue_script(
                'woo-metawizard-js',
                WMH_PLUGIN_URL . 'assets/js/woo-metawizard.js',
                [ 'jquery' ],
                WMH_VERSION,
                true
            );

            wp_localize_script( 'woo-metawizard-js', 'woo_metawizard', [
                'post_id'      => get_the_ID(),
                'nonce'        => wp_create_nonce( 'woo_metawizard_generate_seo' ),
                'nonce_save'   => wp_create_nonce( 'woo_metawizard_save_suggestion' ),
                'nonce_delete' => wp_create_nonce( 'woo_metawizard_delete_suggestion' )
            ]);
        }
    }
}
