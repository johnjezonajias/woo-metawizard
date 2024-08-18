<?php

class WooMetaWizard_API {

    /**
     * Sends a request to the OpenAI API to generate SEO suggestions.
     *
     * This function prepares and sends an API request to OpenAI using the provided primer data.
     * It includes the necessary headers, body content, and handles the response. The function 
     * also includes error handling to ensure robustness in case of network issues or invalid responses.
     *
     * @param array $primer The primer data to be sent to OpenAI. This data includes details about the store, product, and other relevant information.
     * 
     * @return array An array containing the status of the request. 
     *               - 'error' (boolean): Indicates if an error occurred.
     *               - 'message' (string|array): Error message if an error occurred, otherwise the response from OpenAI.
     */
    public static function call_openai_api( $primer ) {
        $url = 'https://api.openai.com/v1/chat/completions';
        $api_key = get_option( 'woo_metawizard_openai_api_key' );
        $headers = array(
            "Authorization: Bearer {$api_key}",
            "Content-Type: application/json"
        );

        $data = array(
            "model"       => "gpt-4",
            "messages"    => array(
                array(
                    "role"    => "system",
                    "content" => $primer
                )
            ),
            "temperature" => 0.7,
            "max_tokens"  => 260
        );

        $curl = curl_init( $url );
        curl_setopt( $curl, CURLOPT_POST, 1 );
        curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $data ) );
        curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

        $result = curl_exec( $curl );

        if ( curl_errno( $curl ) ) {
            return ['error' => 'Curl error: ' . curl_error( $curl )];
        }

        // Check HTTP status code.
        $httpCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
        if ( $httpCode !== 200 ) {
            return ['error' => 'HTTP Error Code: ' . $httpCode];
        }

        curl_close( $curl );

        // Parse the response.
        $response = json_decode( $result, true );

        if ( ! empty( $response['choices'] ) ) {
            return $response['choices'][0]['message']['content'];
        } else {
            return ['error' => 'No response from API'];
        }
    }
}