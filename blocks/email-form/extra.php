<?php
/**
 * Server-side configuration and REST submission helpers for email forms.
 *
 * @package ACF_Blocks
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read a saved block's webhook configuration without ACF's current render state.
 *
 * @param array $attributes Saved block attributes.
 * @return array|null
 */
function acf_email_form_saved_config( $attributes ) {
	$data = isset( $attributes['data'] ) && is_array( $attributes['data'] ) ? $attributes['data'] : array();
	$config = array();
	foreach ( array( 'form_type', 'webhook_url', 'webhook_auth_headers' ) as $name ) {
		$value = $data[ $name ] ?? $data[ 'field_' . $name ] ?? '';
		$config[ $name ] = is_string( $value ) ? $value : '';
	}
	return 'webhook' === $config['form_type'] && '' !== $config['webhook_url'] ? $config : null;
}

/**
 * A keyed digest lets us match configuration without disclosing it publicly.
 *
 * @param array $config Webhook configuration.
 * @return string
 */
function acf_email_form_config_key( $config ) {
	return hash_hmac( 'sha256', wp_json_encode( $config ), wp_salt( 'auth' ) );
}

/**
 * Whether post content may supply a publicly submitted form.
 *
 * @param WP_Post|null $post Source post.
 * @return bool
 */
function acf_email_form_public_post( $post ) {
	return $post && 'publish' === $post->post_status && empty( $post->post_password );
}

/**
 * Resolve an active theme template, including templates supplied as theme files.
 *
 * @param string $id   Theme//slug identifier.
 * @param string $type Template post type.
 * @return WP_Block_Template|null
 */
function acf_email_form_template( $id, $type ) {
	if ( ! function_exists( 'get_block_template' ) || ! in_array( $type, array( 'wp_template', 'wp_template_part' ), true ) ) {
		return null;
	}
	$theme = explode( '//', $id, 2 )[0];
	if ( function_exists( 'get_stylesheet' ) && ! in_array( $theme, array( get_stylesheet(), get_template() ), true ) ) {
		return null;
	}
	$template = get_block_template( $id, $type );
	return $template && 'publish' === $template->status ? $template : null;
}

/**
 * Find a matching config in saved content. Never render blocks while resolving.
 *
 * The budget and visited set bound nested/cyclic reusable blocks and templates.
 *
 * @param array  $blocks  Parsed blocks.
 * @param string $key     Keyed config digest.
 * @param array  $visited Visited reusable sources.
 * @param int    $budget  Shared block budget.
 * @param int    $depth   Nesting depth.
 * @return array|null
 */
function acf_email_form_find_config( $blocks, $key, &$visited, &$budget, $depth = 0 ) {
	if ( $depth > 20 ) {
		return null;
	}
	foreach ( $blocks as $block ) {
		if ( --$budget < 0 || ! is_array( $block ) ) {
			return null;
		}
		$name = $block['blockName'] ?? '';
		$attrs = isset( $block['attrs'] ) && is_array( $block['attrs'] ) ? $block['attrs'] : array();
		if ( 'acf/email-form' === $name ) {
			$config = acf_email_form_saved_config( $attrs );
			if ( $config && hash_equals( $key, acf_email_form_config_key( $config ) ) ) {
				return $config;
			}
		}
		$content = '';
		$source = '';
		if ( 'core/block' === $name && ! empty( $attrs['ref'] ) ) {
			$source = 'post:' . absint( $attrs['ref'] );
			$post = get_post( absint( $attrs['ref'] ) );
			if ( acf_email_form_public_post( $post ) && 'wp_block' === $post->post_type ) {
				$content = $post->post_content;
			}
		} elseif ( 'core/template-part' === $name && ! empty( $attrs['slug'] ) && function_exists( 'get_stylesheet' ) ) {
			$id = ( $attrs['theme'] ?? get_stylesheet() ) . '//' . $attrs['slug'];
			$source = 'template:' . $id;
			$template = acf_email_form_template( $id, 'wp_template_part' );
			$content = $template ? $template->content : '';
		}
		if ( $content && strlen( $content ) <= 1048576 && ! isset( $visited[ $source ] ) ) {
			$visited[ $source ] = true;
			$found = acf_email_form_find_config( parse_blocks( $content ), $key, $visited, $budget, $depth + 1 );
			if ( $found ) {
				return $found;
			}
		}
		$found = acf_email_form_find_config( $block['innerBlocks'] ?? array(), $key, $visited, $budget, $depth + 1 );
		if ( $found ) {
			return $found;
		}
	}
	return null;
}

/**
 * Determine whether a block widget is still in an active sidebar.
 *
 * @param int $id Widget number.
 * @return bool
 */
function acf_email_form_active_widget( $id ) {
	if ( ! function_exists( 'wp_get_sidebars_widgets' ) ) {
		return false;
	}
	foreach ( wp_get_sidebars_widgets() as $sidebar => $widgets ) {
		if ( 'wp_inactive_widgets' !== $sidebar && 0 !== strpos( $sidebar, 'orphaned_widgets' ) && in_array( 'block-' . $id, (array) $widgets, true ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Read one persisted source. Unknown, private and removed sources fail closed.
 *
 * @param array $source Source locator.
 * @return string
 */
function acf_email_form_source_content( $source ) {
	$type = $source['type'] ?? '';
	$id = $source['id'] ?? '';
	if ( 'post' === $type && is_numeric( $id ) ) {
		$post = get_post( absint( $id ) );
		return acf_email_form_public_post( $post ) ? $post->post_content : '';
	}
	if ( in_array( $type, array( 'wp_template', 'wp_template_part' ), true ) && is_string( $id ) ) {
		$template = acf_email_form_template( $id, $type );
		return $template ? $template->content : '';
	}
	if ( 'widget' === $type && is_numeric( $id ) && acf_email_form_active_widget( absint( $id ) ) ) {
		$widgets = get_option( 'widget_block', array() );
		return $widgets[ absint( $id ) ]['content'] ?? '';
	}
	return '';
}

/**
 * Match a config within one source using bounded traversal.
 *
 * @param array  $source Source locator.
 * @param string $key    Config digest.
 * @return array|null
 */
function acf_email_form_source_config( $source, $key ) {
	$content = acf_email_form_source_content( $source );
	if ( ! is_string( $content ) || '' === $content || strlen( $content ) > 1048576 ) {
		return null;
	}
	$visited = array();
	$budget = 1000;
	return acf_email_form_find_config( parse_blocks( $content ), $key, $visited, $budget );
}

/**
 * Sign one public source only when it owns the matching configuration.
 *
 * @param array  $source Source locator.
 * @param string $key    Config digest.
 * @return string
 */
function acf_email_form_sign_source( $source, $key ) {
	if ( ! acf_email_form_source_config( $source, $key ) ) {
		return '';
	}
	$source['key'] = $key;
	$payload = rtrim( strtr( base64_encode( wp_json_encode( $source ) ), '+/', '-_' ), '=' );
	return $payload . '.' . hash_hmac( 'sha256', $payload, wp_salt( 'auth' ) );
}

/**
 * Create a non-expiring reference suitable for cached public pages.
 *
 * The signature locates current saved content rather than storing a second copy
 * of credentials. Editing/removing a form or making its source private revokes it.
 *
 * @param array      $config  Rendered webhook config.
 * @param int|string $post_id Current render post ID.
 * @return string Empty when no public persisted source owns the configuration.
 */
function acf_email_form_reference( $config, $post_id ) {
	$key = acf_email_form_config_key( $config );
	if ( is_numeric( $post_id ) && absint( $post_id ) ) {
		$reference = acf_email_form_sign_source( array( 'type' => 'post', 'id' => absint( $post_id ) ), $key );
		if ( $reference ) {
			return $reference;
		}
	}
	// A template/part form can render with the page's ID instead of its own ID.
	if ( function_exists( 'get_block_templates' ) ) {
		foreach ( array( 'wp_template', 'wp_template_part' ) as $type ) {
			foreach ( array_slice( get_block_templates( array(), $type ), 0, 100 ) as $template ) {
				$reference = acf_email_form_sign_source( array( 'type' => $type, 'id' => $template->id ), $key );
				if ( $reference ) {
					return $reference;
				}
			}
		}
	}
	foreach ( array_slice( (array) get_option( 'widget_block', array() ), 0, 500, true ) as $id => $widget ) {
		if ( is_numeric( $id ) && is_array( $widget ) && isset( $widget['content'] ) ) {
			$reference = acf_email_form_sign_source( array( 'type' => 'widget', 'id' => absint( $id ) ), $key );
			if ( $reference ) {
				return $reference;
			}
		}
	}
	return '';
}

/**
 * Verify a public reference and load its current configuration.
 *
 * @param mixed $reference Public reference.
 * @return array|null
 */
function acf_email_form_resolve_reference( $reference ) {
	if ( ! is_string( $reference ) || strlen( $reference ) > 2048 ) {
		return null;
	}
	$parts = explode( '.', $reference );
	if ( 2 !== count( $parts ) || ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'auth' ) ), $parts[1] ) ) {
		return null;
	}
	$source = json_decode( base64_decode( strtr( $parts[0], '-_', '+/' ), true ), true );
	if ( ! is_array( $source ) || ! isset( $source['key'] ) || ! is_string( $source['key'] ) || ! preg_match( '/^[a-f0-9]{64}$/D', $source['key'] ) ) {
		return null;
	}
	return acf_email_form_source_config( $source, $source['key'] );
}

/**
 * Parse each configured header separately without flattening its line breaks.
 *
 * @param string $raw Header configuration.
 * @return array|WP_Error
 */
function acf_email_form_headers( $raw ) {
	$headers = array( 'Content-Type' => 'application/json' );
	foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
		if ( '' === trim( $line ) ) {
			continue;
		}
		$parts = explode( ':', $line, 2 );
		$name = trim( $parts[0] );
		$value = isset( $parts[1] ) ? trim( $parts[1] ) : '';
		if ( 2 !== count( $parts ) || ! preg_match( '/^[!#$%&\'*+.^_`|~0-9a-z-]+$/iD', $name ) || preg_match( '/[\x00-\x1f\x7f]/', $value ) || in_array( strtolower( $name ), array( 'host', 'content-length', 'transfer-encoding', 'connection', 'upgrade', 'proxy-authorization', 'proxy-connection' ), true ) ) {
			return new WP_Error( 'invalid_headers', __( 'The form has invalid webhook headers.', 'acf-blocks' ), array( 'status' => 400 ) );
		}
		$headers[ $name ] = $value;
	}
	return $headers;
}

/** Register the public endpoint; destination configuration stays server-side. */
function acf_email_form_register_rest_route() {
	register_rest_route( 'email-form-proxy/v1', '/submit', array(
		'methods'             => 'POST',
		'callback'            => 'acf_email_form_proxy_handler',
		'permission_callback' => '__return_true',
	) );
}
add_action( 'rest_api_init', 'acf_email_form_register_rest_route' );

/**
 * Forward a public form submission to its saved, validated HTTPS destination.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function acf_email_form_proxy_handler( $request ) {
	$ip = sanitize_text_field( $_SERVER['REMOTE_ADDR'] ?? '' );
	$rate_key = 'acf_ef_' . md5( $ip );
	$count = (int) get_transient( $rate_key );
	if ( $count >= 5 ) {
		return new WP_Error( 'rate_limited', __( 'Too many submissions. Please try again later.', 'acf-blocks' ), array( 'status' => 429 ) );
	}
	set_transient( $rate_key, $count + 1, MINUTE_IN_SECONDS );
	$params = $request->get_json_params();
	$config = acf_email_form_resolve_reference( is_array( $params ) ? ( $params['formRef'] ?? '' ) : '' );
	if ( ! $config ) {
		return new WP_Error( 'invalid_form', __( 'This form is unavailable. Please reload the page.', 'acf-blocks' ), array( 'status' => 400 ) );
	}
	if ( 'https' !== strtolower( (string) wp_parse_url( $config['webhook_url'], PHP_URL_SCHEME ) ) ) {
		return new WP_Error( 'insecure_webhook', __( 'Webhook URL must use HTTPS.', 'acf-blocks' ), array( 'status' => 400 ) );
	}
	$headers = acf_email_form_headers( $config['webhook_auth_headers'] );
	if ( is_wp_error( $headers ) ) {
		return $headers;
	}
	$clean_data = array();
	$data = isset( $params['data'] ) && is_array( $params['data'] ) ? $params['data'] : array();
	foreach ( $data as $key => $value ) {
		if ( is_scalar( $value ) ) {
			$clean_data[ sanitize_text_field( $key ) ] = sanitize_text_field( $value );
		}
	}
	$response = wp_safe_remote_post( $config['webhook_url'], array(
		'timeout'             => 15,
		'redirection'         => 0, // Never forward authentication to a redirect target.
		'limit_response_size' => 65536,
		'headers'             => $headers,
		'body'                => wp_json_encode( $clean_data ),
	) );
	if ( is_wp_error( $response ) ) {
		return new WP_Error( 'webhook_failed', __( 'The form could not be submitted. Please try again later.', 'acf-blocks' ), array( 'status' => 502 ) );
	}
	$status = wp_remote_retrieve_response_code( $response );
	if ( $status >= 200 && $status < 300 ) {
		return new WP_REST_Response( array( 'success' => true ), 200 );
	}
	return new WP_Error( 'webhook_error', sprintf( __( 'Webhook returned HTTP %d.', 'acf-blocks' ), $status ), array( 'status' => 502 ) );
}
