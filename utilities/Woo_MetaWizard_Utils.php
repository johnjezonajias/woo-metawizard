<?php

namespace WooMetaWizard\Utilities;

// Prevent direct access to the file.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Woo_MetaWizard_Utils {

    /**
     * Get WooCommerce store name.
     *
     * @return string
     */
    public static function get_store_name() {
        return get_option( 'blogname' );
    }

    /**
     * Get WooCommerce store address.
     *
     * @return array
     */
    public static function get_store_address() {
        return array(
            'address_1' => get_option( 'woocommerce_store_address' ),
            'address_2' => get_option( 'woocommerce_store_address_2' ),
            'city'      => get_option( 'woocommerce_store_city' ),
            'postcode'  => get_option( 'woocommerce_store_postcode' ),
            'country'   => WC()->countries->get_base_country(),
            'state'     => WC()->countries->get_base_state(),
        );
    }

    /**
     * Get WooCommerce store contact information.
     *
     * @return array
     */
    public static function get_store_contact_info() {
        return array(
            'email' => get_option( 'woocommerce_email_from_address' ),
            'phone' => get_option( 'woocommerce_store_phone' ),
        );
    }

    /**
     * Get the store's full address as a string.
     *
     * @return string
     */
    public static function get_full_store_address() {
        $address = self::get_store_address();
        $address_parts = array_filter( array(
            $address['address_1'],
            $address['address_2'],
            $address['city'],
            $address['state'],
            $address['postcode'],
            $address['country'],
        ) );

        return implode( ', ', $address_parts );
    }

    /**
     * Get the store's general information.
     *
     * @return array
     */
    public static function get_store_info() {
        return array(
            'name'         => self::get_store_name(),
            'full_address' => self::get_full_store_address(),
            'contact_info' => self::get_store_contact_info(),
        );
    }

    /**
     * Get product categories as a comma-separated string.
     *
     * @param int $product_id
     * @return string
     */
    public static function get_product_categories( $product_id ) {
        $terms = get_the_terms( $product_id, 'product_cat' );

        if ( ! $terms || is_wp_error( $terms ) ) {
            return '';
        }

        $categories = wp_list_pluck( $terms, 'name' );

        return implode( ', ', $categories );
    }

    /**
     * Get the product URL.
     *
     * @param int $product_id
     * @return string
     */
    public static function get_product_url( $product_id ) {
        return get_permalink( $product_id );
    }

    /**
     * Get the product's main image URL.
     *
     * @param int $product_id
     * @return string
     */
    public static function get_product_image_url( $product_id ) {
        $image_id = get_post_thumbnail_id( $product_id );

        if ( ! $image_id ) {
            return '';
        }

        $image_url = wp_get_attachment_url( $image_id );

        return $image_url ? $image_url : '';
    }

    /**
     * Get all variations of a product.
     *
     * @param int $product_id
     * @return array
     */
    public static function get_product_variations( $product_id ) {
        // Ensure WooCommerce is available.
        if ( ! class_exists( 'WooCommerce' ) ) {
            return array();
        }

        $product = wc_get_product( $product_id );

        // Ensure it's a variable product.
        if ( ! $product || ! $product->is_type( 'variable' ) ) {
            return array();
        }

        $variations = $product->get_children(); // Get variation IDs.
        $variation_data = array();

        foreach ( $variations as $variation_id ) {
            // Ensure WC_Product_Variation class exists.
            if ( class_exists( 'WC_Product_Variation' ) ) {
                $variation = new \WC_Product_Variation( $variation_id );

                // Gather variation data.
                $variation_data[] = array(
                    'variation_id'  => $variation_id,
                    'attributes'    => $variation->get_attributes(),
                    'regular_price' => $variation->get_regular_price(),
                    'sale_price'    => $variation->get_sale_price(),
                    'sku'           => $variation->get_sku(),
                    'description'   => $variation->get_description(),
                );
            }
        }

        return $variation_data;
    }

    /**
     * Get a formatted string of product variation details with currency symbols.
     *
     * @param int $product_id
     * @return string
     */
    public static function get_product_variations_string( $product_id ) {
        $variations = self::get_product_variations( $product_id );
    
        if ( empty( $variations ) ) {
            return '';
        }
    
        // Get the WooCommerce currency symbol (e.g., ₱).
        $currency_symbol = html_entity_decode( get_woocommerce_currency_symbol() );
        $variation_strings = array();
    
        foreach ( $variations as $variation ) {
            // Create a string for the attributes (e.g., Pa_size: large).
            $attributes_string = implode( ', ', array_map( function( $value, $key ) {
                return ucfirst( $key ) . ": $value";
            }, $variation['attributes'], array_keys( $variation['attributes'] ) ) );
    
            // Handle price: Sale price if available, otherwise regular price.
            if ( ! empty( $variation['sale_price'] ) ) {
                $price_string = "Sale Price: {$currency_symbol}{$variation['sale_price']}";
            } else {
                $price_string = "Regular Price: {$currency_symbol}{$variation['regular_price']}";
            }
    
            // Combine attributes and price into one string.
            $variation_strings[] = "[{$attributes_string}, {$price_string}]";
        }
    
        // Return the variations as a single string, separated by space.
        return implode( " ", $variation_strings );
    }    

    /**
     * Sanitize a checkbox input.
     *
     * This method ensures that only 'yes' or 'no' values are accepted. 
     * If the input is 'yes', it returns 'yes'; otherwise, it returns 'no'.
     *
     * @param string $input The checkbox input value.
     * @return string 'yes' if the input is 'yes', otherwise 'no'.
     */
    public static function sanitize_checkbox( $input ) {
        return $input === 'yes' ? 'yes' : 'no';
    }
}
