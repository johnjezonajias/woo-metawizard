<?php

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woo_MetaWizard_Metabox {

    public static function init() {
        add_action( 'add_meta_boxes', [ __CLASS__, 'add_meta_box' ] );
        add_action( 'save_post', [ __CLASS__, 'save_meta_box_data' ] );
        add_action( 'wp_ajax_woo_metawizard_generate_seo', [ __CLASS__, 'generate_seo_suggestions' ] );
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

        $entry_number = 1;

        // Retrieve product's actual title and description.
        $product_title       = get_the_title( $post->ID );
        $product_excerpt     = get_the_excerpt( $post->ID );
        $product_description = get_post_field( 'post_content', $post->ID );
        $product_description = !empty( $product_description ) ? $product_description : $product_excerpt;
        $product_keywords    = '';

        // Retrieve previous suggestions.
        $previous_suggestions = get_post_meta( $post->ID, '_woo_metawizard_suggestions', true );

        // Sort previous suggestions by timestamp in descending order (latest first).
        if ( ! empty( $previous_suggestions ) ) {
            usort( $previous_suggestions, function( $a, $b ) {
                return $b['timestamp'] - $a['timestamp'];
            });
        }

        ?>
        <div class="woo-metawizard-metabox">
            <div id="woo_metawizard_suggestions"></div>

            <div class="woo-metawizard-current">
                <h3>Product Meta:</h3>
                <p>
                    <label for="woo_metawizard_meta_title"><?php esc_html_e( 'Meta Title', 'woo-metawizard' ); ?></label>
                    <input type="text" id="woo_metawizard_meta_title" name="woo_metawizard_meta_title" value="<?php echo esc_attr( $product_title ); ?>" class="widefat" readonly />
                </p>
                <p>
                    <label for="woo_metawizard_meta_description"><?php esc_html_e( 'Meta Description', 'woo-metawizard' ); ?></label>
                    <textarea id="woo_metawizard_meta_description" name="woo_metawizard_meta_description" class="widefat" readonly><?php echo esc_textarea( $product_description ); ?></textarea>
                </p>
                <p>
                    <label for="woo_metawizard_meta_keywords"><?php esc_html_e( 'Meta Keywords', 'woo-metawizard' ); ?></label>
                    <input type="text" id="woo_metawizard_meta_keywords" name="woo_metawizard_meta_keywords" value="<?php echo esc_attr( $product_keywords ); ?>" class="widefat" readonly />
                </p>
                <p>
                    <button type="button" class="button button-primary" id="woo_metawizard_generate_seo"><?php esc_html_e( 'Generate SEO Suggestions', 'woo-metawizard' ); ?></button>
                </p>
            </div>

            <?php if ( ! empty( $previous_suggestions ) ) : ?>
                <h3><?php esc_html_e( 'Previously Suggested Metadata', 'woo-metawizard' ); ?></h3>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'No.', 'woo-metawizard' ); ?></th>
                            <th><?php esc_html_e( 'Meta Title', 'woo-metawizard' ); ?></th>
                            <th><?php esc_html_e( 'Meta Description', 'woo-metawizard' ); ?></th>
                            <th><?php esc_html_e( 'Meta Keywords', 'woo-metawizard' ); ?></th>
                            <th><?php esc_html_e( 'Date Created', 'woo-metawizard' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $previous_suggestions as $suggestion ) : ?>
                            <tr>
                                <td><?php echo esc_html( $entry_number ); ?></td>
                                <td><?php echo esc_html( $suggestion['meta_title'] ); ?></td>
                                <td><?php echo esc_html( $suggestion['meta_description'] ); ?></td>
                                <td><?php echo esc_html( $suggestion['meta_keywords'] ); ?></td>
                                <td><?php echo esc_html( date( 'Y-m-d H:i:s', $suggestion['timestamp'] ) ); ?></td>
                            </tr>
                            <?php $entry_number++; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br>
            <?php endif; ?>
        </div>
        <?php
    }

    public static function save_meta_box_data( $post_id ) {
        // Check nonce.
        if ( ! isset( $_POST['woo_metawizard_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['woo_metawizard_meta_box_nonce'], 'woo_metawizard_meta_box' ) ) {
            return;
        }
    
        // Check if the current user has permission to edit the post.
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    
        // Retrieve current product title and description.
        $current_title       = get_the_title( $post_id );
        $current_description = get_post_field( 'post_content', $post_id );
    
        // Retrieve submitted meta data.
        $meta_title       = isset( $_POST['woo_metawizard_meta_title'] ) ? sanitize_text_field( $_POST['woo_metawizard_meta_title'] ) : '';
        $meta_description = isset( $_POST['woo_metawizard_meta_description'] ) ? sanitize_textarea_field( $_POST['woo_metawizard_meta_description'] ) : '';
        $meta_keywords    = isset( $_POST['woo_metawizard_meta_keywords'] ) ? sanitize_text_field( $_POST['woo_metawizard_meta_keywords'] ) : '';
    
        // Check if the submitted meta data is different from the current product title and description.
        if ( $meta_title === $current_title && $meta_description === $current_description ) {
            // Do not save if the values are the same as the current product title and description.
            return;
        }
    
        // Save or update the meta data.
        update_post_meta( $post_id, '_woo_metawizard_meta_title', $meta_title );
        update_post_meta( $post_id, '_woo_metawizard_meta_description', $meta_description );
        update_post_meta( $post_id, '_woo_metawizard_meta_keywords', $meta_keywords );
    
        // Store the suggestions with a timestamp.
        $previous_suggestions = get_post_meta( $post_id, '_woo_metawizard_suggestions', true ) ?: [];
        $previous_suggestions[] = [
            'meta_title'       => $meta_title,
            'meta_description' => $meta_description,
            'meta_keywords'    => $meta_keywords,
            'timestamp'        => time(),
        ];
        update_post_meta( $post_id, '_woo_metawizard_suggestions', $previous_suggestions );
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
            - Highlight the store location " . $store_info['full_address'] . " to improve local SEO and differentiate from competitors.
            - Use relevant, high-impact keywords to boost search visibility.
            - Maintain a consistent and professional tone across all meta titles.

            Guidelines for Meta Descriptions:
            - Create concise, informative descriptions that clearly present product features and benefits.
            - Highlight unique selling points, including any variation and customization options.
            - Reinforce brand identity by mentioning the store name " . $store_info['name'] . " in the description.
            - Emphasize the store’s location " . $store_info['full_address'] . " to attract local customers and stand out in the local market.
            - Incorporate the product image from " . $product_image_url . " to enhance visual appeal and search relevance.
            - Strategically integrate relevant keywords to improve search rankings without keyword stuffing.
            - Ensure a consistent tone that matches the meta title and overall brand voice.

            Guidelines for Meta Keywords:
            - Choose keywords that precisely describe the product’s key features and variations.
            - Include industry-relevant, high-impact keywords that align with the product category " . $product_categories . ".
            - Avoid excessive keywords; focus on quality, relevance, and alignment with meta content.
            - Ensure keyword consistency with the content in the meta title and description for better SEO performance.

            Please format the response as a JSON object with the following structure:
            {
                \"meta_title\": \"Title\",
                \"meta_description\": \"Description\",
                \"meta_keywords\": \"Keywords\",
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
}
