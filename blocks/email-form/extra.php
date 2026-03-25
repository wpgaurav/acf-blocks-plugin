<?php
/**
 * Email Form block helpers.
 *
 * Registers the REST API proxy endpoint used by webhook-based form
 * submissions. The proxy is necessary because browsers block cross-origin
 * POST requests to arbitrary webhook URLs due to CORS restrictions.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'acf_email_form_register_rest_route' ) ) {
	/**
	 * Register the email-form-proxy REST endpoint.
	 */
	function acf_email_form_register_rest_route() {
		register_rest_route( 'email-form-proxy/v1', '/submit', array(
			'methods'             => 'POST',
			'callback'            => 'acf_email_form_proxy_handler',
			'permission_callback' => '__return_true',
		) );
	}
	add_action( 'rest_api_init', 'acf_email_form_register_rest_route' );
}

if ( ! function_exists( 'acf_email_form_proxy_handler' ) ) {
	/**
	 * Handle proxied webhook submissions.
	 *
	 * Receives form data and forwards it to the configured webhook URL,
	 * adding any authentication headers the site owner has configured.
	 *
	 * @param WP_REST_Request $request The REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	function acf_email_form_proxy_handler( $request ) {
		$params      = $request->get_json_params();
		$webhook_url = isset( $params['webhookUrl'] ) ? esc_url_raw( $params['webhookUrl'] ) : '';
		$auth_header = isset( $params['webhookAuthHeaders'] ) ? sanitize_text_field( $params['webhookAuthHeaders'] ) : '';
		$data        = isset( $params['data'] ) && is_array( $params['data'] ) ? $params['data'] : array();

		if ( empty( $webhook_url ) ) {
			return new WP_Error( 'missing_webhook', __( 'Webhook URL is required.', 'acf-blocks' ), array( 'status' => 400 ) );
		}

		// Only allow https webhooks in production to prevent credential leaks.
		$scheme = wp_parse_url( $webhook_url, PHP_URL_SCHEME );
		if ( 'https' !== $scheme && ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
			return new WP_Error( 'insecure_webhook', __( 'Webhook URL must use HTTPS.', 'acf-blocks' ), array( 'status' => 400 ) );
		}

		// Block requests to private/internal IP ranges.
		$host = wp_parse_url( $webhook_url, PHP_URL_HOST );
		if ( $host ) {
			$ip = gethostbyname( $host );
			if ( $ip && $ip !== $host && filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) === false ) {
				return new WP_Error( 'blocked_host', __( 'Webhook URL points to a private or reserved IP range.', 'acf-blocks' ), array( 'status' => 400 ) );
			}
		}

		// Sanitize form data values.
		$clean_data = array();
		foreach ( $data as $key => $value ) {
			$clean_data[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
		}

		$headers = array( 'Content-Type' => 'application/json' );

		// Parse auth headers (format: "Header-Name: value" per line).
		if ( ! empty( $auth_header ) ) {
			$lines = preg_split( '/\r?\n/', $auth_header );
			foreach ( $lines as $line ) {
				$line = trim( $line );
				if ( empty( $line ) ) {
					continue;
				}
				$parts = explode( ':', $line, 2 );
				if ( count( $parts ) === 2 ) {
					$headers[ trim( $parts[0] ) ] = trim( $parts[1] );
				}
			}
		}

		$response = wp_remote_post( $webhook_url, array(
			'timeout' => 15,
			'headers' => $headers,
			'body'    => wp_json_encode( $clean_data ),
		) );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'webhook_failed', $response->get_error_message(), array( 'status' => 502 ) );
		}

		$status = wp_remote_retrieve_response_code( $response );

		if ( $status >= 200 && $status < 300 ) {
			return new WP_REST_Response( array( 'success' => true ), 200 );
		}

		return new WP_Error(
			'webhook_error',
			sprintf( __( 'Webhook returned HTTP %d.', 'acf-blocks' ), $status ),
			array( 'status' => 502 )
		);
	}
}
