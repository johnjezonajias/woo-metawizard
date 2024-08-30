<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woo_MetaWizard_Metabox {

    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_box' ] );
        add_action( 'wp_ajax_woo_metawizard_generate_seo', [ __CLASS__, 'generate_seo_suggestions' ] );
        add_action( 'wp_ajax_woo_metawizard_save_suggestion', [ __CLASS__, 'save_suggestion' ] );
        add_action( 'wp_ajax_woo_metawizard_delete_suggestion', [ __CLASS__, 'delete_suggestion' ] );
        add_action( 'wp_ajax_woo_metawizard_refresh_suggestions_table', [ __CLASS__, 'refresh_suggestions_table' ] );
    }

    public static function add_meta_box() {
        add_meta_box(
            'woo_metawizard_metabox',
            __( 'Woo Meta Helper', 'woo-metawizard' ),
            [ __CLASS__, 'render_meta_box' ],
            'product',
            'normal',
            'high'
        );
    }

    public static function render_meta_box( $post ) {
        // Add nonce for security and authentication.
        wp_nonce_field( 'woo_metawizard_meta_box', 'woo_metawizard_meta_box_nonce' );

        // Retrieve product's actual title and description.
        $product_title       = get_the_title( $post->ID );
        $product_excerpt     = get_the_excerpt( $post->ID );
        $product_description = get_post_field( 'post_content', $post->ID );
        $product_description = !empty( $product_description ) ? $product_description : $product_excerpt;

        // Retrieve previous suggestions.
        $previous_suggestions = get_post_meta( $post->ID, '_woo_metawizard_suggestions', true );
        ?>

        <div class="woo-metawizard-metabox">
            <div id="woo_metawizard_suggestions"></div>

            <div class="woo-metawizard-current">
                <h4><?php esc_html_e( 'Current product data', 'woo-metawizard' ); ?></h4>
                <p>
                    <strong><?php esc_html_e( 'Title', 'woo-metawizard' ); ?></strong><br />
                    <?php echo esc_html( $product_title ); ?>
                </p>
                <p>
                    <strong><?php esc_html_e( 'Description', 'woo-metawizard' ); ?></strong><br />
                    <?php echo esc_textarea( $product_description ); ?>
                </p>
                <p>
                    <button type="button" class="button button-primary" id="woo_metawizard_generate_seo">
                        <?php esc_html_e( 'Generate SEO Suggestion', 'woo-metawizard' ); ?>
                    </button>
                </p>
            </div>
            
            <div class="woo-metawizard-placeholder">
                <div class="woo-metawizard-wrap">
                    <h4><?php esc_html_e( 'Optimized SEO suggestion result', 'woo-metawizard' ); ?></h4>
                    <p>
                        <label for="woo_metawizard_meta_title"><?php esc_html_e( 'Meta Title', 'woo-metawizard' ); ?></label>
                        <input type="text" id="woo_metawizard_meta_title" name="woo_metawizard_meta_title" value="" class="widefat" />
                    </p>
                    <p>
                        <label for="woo_metawizard_meta_description"><?php esc_html_e( 'Meta Description', 'woo-metawizard' ); ?></label>
                        <textarea id="woo_metawizard_meta_description" name="woo_metawizard_meta_description" class="widefat"></textarea>
                    </p>
                    <p>
                        <button type="button" class="button button-primary" id="woo_metawizard_use_for_yoast" disabled>
                            <?php esc_html_e( 'Use it for Yoast', 'woo-metawizard' ); ?>
                        </button>
                        <button type="button" class="button button-primary" id="woo_metawizard_save_suggestion" disabled>
                            <?php esc_html_e( 'Save for Reference', 'woo-metawizard' ); ?>
                        </button>
                    </p>
                </div>
                <span id="form-spinner" class="spinner" style="display:none;"></span>
            </div>

            <?php if ( ! empty( $previous_suggestions ) ) : ?>
                <div class="woo-metawizard-table">
                    <h4><?php esc_html_e( 'Saved suggested data', 'woo-metawizard' ); ?></h4>
                    <table id="woo-metawizard-table" class="widefat">
                        <thead>
                            <tr>
                                <th><?php esc_html_e( 'No.', 'woo-metawizard' ); ?></th>
                                <th><?php esc_html_e( 'Meta Title', 'woo-metawizard' ); ?></th>
                                <th><?php esc_html_e( 'Meta Description', 'woo-metawizard' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $previous_suggestions as $index => $suggestion ) : ?>
                                <tr>
                                    <td>
                                        <?php echo esc_html( $index + 1 ); ?>
                                    </td>
                                    <td>
                                        <?php echo esc_html( $suggestion['meta_title'] ); ?><br />
                                        <div class="row-actions">
                                            <a href="#" class="woo-metawizard-use-yoast" data-index="<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Use it for Yoast', 'woo-metawizard' ); ?></a> |
                                            <a href="#" class="woo-metawizard-delete" data-index="<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Delete', 'woo-metawizard' ); ?></a> | 
                                            <?php echo esc_html( date( 'Y-m-d H:i:s', $suggestion['timestamp'] ) ); ?>
                                        </div>
                                    </td>
                                    <td><?php echo esc_html( $suggestion['meta_description'] ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <span id="table-spinner" class="spinner" style="display:none;"></span>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }

    public static function generate_seo_suggestions() {
        // Verify the nonce for security.
        check_ajax_referer( 'woo_metawizard_generate_seo', 'nonce' );

        $post_id = intval( $_POST['post_id'] );
    
        // Check if the user has permission.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            wp_send_json_error( [ 'message' => __( 'You do not have permission to perform this action.', 'woo-metawizard' ) ] );
        }

        // Retrieve current product title and description.
        $product_title       = get_the_title( $post_id );
        $product_description = get_post_field( 'post_content', $post_id );

        // Retrieve WooCommerce store information.
        $store_info = WooMetaWizard_Utils::get_store_info();

        // Retrieve current product information.
        $product_categories = WooMetaWizard_Utils::get_product_categories( $post_id );
        $product_url        = WooMetaWizard_Utils::get_product_url( $post_id);
        $product_image_url  = WooMetaWizard_Utils::get_product_image_url( $post_id );
        $variations_string  = WooMetaWizard_Utils::get_product_variations_string( $post_id );

        // Main PRIMER for OpenAI.
        $primer = "
            Current Product Information:
            - Product Title: $product_title
            - Description: $product_description
            - Product Categories: $product_categories
            - Product URL: $product_url
            - Product Image URL: $product_image_url
            - Variations: $variations_string

            Guidelines for Meta Titles:
            - Include the product name and key features for relevance and consistency.
            - Mention variation options (size, color) and customization to highlight choices.
            - Emphasize unique selling points and benefits that distinguish the product.
            - Incorporate the store name " . $store_info['name'] . " to enhance brand recognition.
            - Highlight the store location to improve local SEO and differentiate from competitors.
            - Use relevant, high-impact keywords to boost search visibility.
            - Maintain a consistent and professional tone across all meta titles.
            - Make sure the SEO title strictly does not exceed 60 characters or approximately 600 pixels in width to avoid truncation in search engine results.

            Guidelines for Meta Descriptions:
            - Create concise, informative descriptions that clearly present product features and benefits.
            - Highlight unique selling points, including any variation and customization options.
            - Reinforce brand identity by mentioning the store name " . $store_info['name'] . " in the description.
            - Emphasize the store’s location to attract local customers and stand out in the local market.
            - Strategically integrate relevant keywords to improve search rankings without keyword stuffing.
            - Ensure a consistent tone that matches the meta title and overall brand voice.
            - Strictly limit the meta description to 155-160 characters to comply with Yoast SEO guidelines and prevent truncation in search results.

            Please format the response as a JSON object with the following structure:
            {
                \"meta_title\": \"Title\",
                \"meta_description\": \"Description\"
            }

            Also, the best meta title length is 50-60 characters and meta description length is between 50-160 characters. Please ensure that you limit its length appropriately after generating the content.
        ";

        // Call OpenAI API with product data (you will need to implement the API call).
        $response = WooMetaWizard_API::call_openai_api( $primer );

        if ( is_array( $response ) && isset( $response['error'] ) ) {
            wp_send_json_error( [ 'message' => $response['error'] ] );
        } else {
            wp_send_json_success( $response );
        }
    }

    public static function save_suggestion() {
        check_ajax_referer( 'woo_metawizard_save_suggestion', 'nonce' );
    
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        $meta_title = isset( $_POST['meta_title'] ) ? sanitize_text_field( $_POST['meta_title'] ) : '';
        $meta_description = isset( $_POST['meta_description'] ) ? sanitize_textarea_field( $_POST['meta_description'] ) : '';
    
        if ( $post_id && $meta_title && $meta_description ) {
            // Save the suggestion data independently of the post save action.
            $previous_suggestions = get_post_meta( $post_id, '_woo_metawizard_suggestions', true ) ?: [];
            $previous_suggestions[] = [
                'meta_title' => $meta_title,
                'meta_description' => $meta_description,
                'timestamp' => time(),
            ];
            update_post_meta( $post_id, '_woo_metawizard_suggestions', $previous_suggestions );
    
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    public static function delete_suggestion() {
        // Check nonce for security.
        check_ajax_referer( 'woo_metawizard_delete_suggestion', 'nonce' );
    
        if ( isset( $_POST['post_id'], $_POST['index'] ) ) {
            $post_id = intval( $_POST['post_id'] );
            $index = intval( $_POST['index'] );
    
            $suggestions = get_post_meta( $post_id, '_woo_metawizard_suggestions', true );
    
            if ( is_array( $suggestions ) && isset( $suggestions[ $index ] ) ) {
                // Remove the suggestion.
                unset( $suggestions[ $index ] );
    
                // Reindex the array.
                $suggestions = array_values( $suggestions );
    
                // Update the meta without triggering a post update.
                update_post_meta( $post_id, '_woo_metawizard_suggestions', $suggestions );
    
                wp_send_json_success();
            }
        }
    
        wp_send_json_error();
    }

    public static function refresh_suggestions_table() {
        if ( ! isset( $_POST['post_id'] ) || ! current_user_can( 'edit_post', intval( $_POST['post_id'] ) ) ) {
            wp_send_json_error();
            return;
        }

        $post_id = intval( $_POST['post_id'] );
        $previous_suggestions = get_post_meta( $post_id, '_woo_metawizard_suggestions', true );

        if ( empty( $previous_suggestions ) ) {
            wp_send_json_error( 'No suggestions found.' );
            return;
        }

        ob_start();
        foreach ( $previous_suggestions as $index => $suggestion ) {
            ?>
            <tr>
                <td>
                    <?php echo esc_html( $index + 1 ); ?>
                    <span class="spinner" style="display:none;"></span>
                </td>
                <td>
                    <?php echo esc_html( $suggestion['meta_title'] ); ?><br />
                    <div class="row-actions">
                        <a href="#" class="woo-metawizard-delete" data-index="<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Delete', 'woo-metawizard' ); ?></a> | 
                        <?php echo esc_html( date( 'Y-m-d H:i:s', $suggestion['timestamp'] ) ); ?>
                    </div>
                </td>
                <td><?php echo esc_html( $suggestion['meta_description'] ); ?></td>
            </tr>
            <?php
        }
        $table_content = ob_get_clean();

        wp_send_json_success( $table_content );
    }
}
