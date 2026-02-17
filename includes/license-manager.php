<?php
/**
 * License Manager for ACF Blocks.
 *
 * @package ACF_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * License Manager class.
 */
class ACF_Blocks_License_Manager {

	const LICENSE_SERVER  = 'https://gauravtiwari.org/';
	const ITEM_ID        = 1150934;
	const OPTION_KEY     = 'acf_blocks_license';
	const LAST_CHECK_KEY = 'acf_blocks_license_last_check';
	const UPDATE_TRANSIENT = 'acf_blocks_update_info';

	/** @var string */
	private $plugin_file;

	/** @var string */
	private $plugin_basename;

	public function __construct( $plugin_file ) {
		$this->plugin_file     = $plugin_file;
		$this->plugin_basename = plugin_basename( $plugin_file );
	}

	public function hook() {
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 99 );
		add_action( 'admin_init', array( $this, 'handle_license_actions' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		add_action( 'delete_site_transient_update_plugins', array( $this, 'clear_update_transient' ) );

		add_filter( 'plugin_action_links_' . $this->plugin_basename, array( $this, 'plugin_action_links' ) );

		if ( ! wp_next_scheduled( 'acf_blocks_verify_license' ) ) {
			wp_schedule_event( time(), 'weekly', 'acf_blocks_verify_license' );
		}
		add_action( 'acf_blocks_verify_license', array( $this, 'verify_remote_license' ) );
	}

	public function add_submenu_page() {
		add_options_page(
			__( 'ACF Blocks License', 'acf-blocks' ),
			__( 'ACF Blocks License', 'acf-blocks' ),
			'manage_options',
			'acf-blocks-license',
			array( $this, 'render_license_page' )
		);
	}

	public function handle_license_actions() {
		if ( ! isset( $_POST['acf_blocks_license_action'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		check_admin_referer( 'acf_blocks_license_nonce', 'acf_blocks_license_nonce' );

		$action = sanitize_text_field( $_POST['acf_blocks_license_action'] );

		if ( 'activate' === $action ) {
			$key = sanitize_text_field( trim( $_POST['license_key'] ?? '' ) );
			if ( empty( $key ) ) {
				add_settings_error( 'acf_blocks_license', 'empty_key', __( 'Please enter a license key.', 'acf-blocks' ), 'error' );
				return;
			}
			$result = $this->activate_license( $key );
			if ( is_wp_error( $result ) ) {
				add_settings_error( 'acf_blocks_license', 'activation_error', $result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'acf_blocks_license', 'activated', __( 'License activated successfully.', 'acf-blocks' ), 'success' );
			}
		} elseif ( 'deactivate' === $action ) {
			$result = $this->deactivate_license();
			if ( is_wp_error( $result ) ) {
				add_settings_error( 'acf_blocks_license', 'deactivation_error', $result->get_error_message(), 'error' );
			} else {
				add_settings_error( 'acf_blocks_license', 'deactivated', __( 'License deactivated successfully.', 'acf-blocks' ), 'success' );
			}
		}
	}

	public function activate_license( $key ) {
		$response = $this->api_request( 'activate_license', array(
			'license_key' => $key,
			'item_id'     => self::ITEM_ID,
			'site_url'    => home_url(),
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( empty( $response['success'] ) || 'valid' !== ( $response['status'] ?? '' ) ) {
			return new WP_Error( 'activation_failed', $response['message'] ?? __( 'License activation failed.', 'acf-blocks' ) );
		}

		$license_data = array(
			'license_key'     => $key,
			'status'          => 'valid',
			'activation_hash' => $response['activation_hash'] ?? '',
			'expiration_date' => $response['expiration_date'] ?? 'lifetime',
			'activated_at'    => current_time( 'mysql' ),
		);

		update_option( self::OPTION_KEY, $license_data );
		update_option( self::LAST_CHECK_KEY, time() );
		delete_transient( self::UPDATE_TRANSIENT );

		return $license_data;
	}

	public function deactivate_license() {
		$license = $this->get_license_data();
		if ( empty( $license['license_key'] ) ) {
			return new WP_Error( 'no_license', __( 'No license key found.', 'acf-blocks' ) );
		}

		$this->api_request( 'deactivate_license', array(
			'license_key' => $license['license_key'],
			'item_id'     => self::ITEM_ID,
			'site_url'    => home_url(),
		) );

		$default = array(
			'license_key' => '', 'status' => 'inactive', 'activation_hash' => '',
			'expiration_date' => '', 'activated_at' => '',
		);
		update_option( self::OPTION_KEY, $default );
		delete_option( self::LAST_CHECK_KEY );
		delete_transient( self::UPDATE_TRANSIENT );

		return $default;
	}

	public function verify_remote_license() {
		$license = $this->get_license_data();
		if ( empty( $license['license_key'] ) || 'valid' !== ( $license['status'] ?? '' ) ) {
			return;
		}

		$params = array( 'item_id' => self::ITEM_ID, 'site_url' => home_url() );
		if ( ! empty( $license['activation_hash'] ) ) {
			$params['activation_hash'] = $license['activation_hash'];
		} else {
			$params['license_key'] = $license['license_key'];
		}

		$response = $this->api_request( 'check_license', $params );
		if ( is_wp_error( $response ) ) {
			return;
		}

		if ( 'valid' !== ( $response['status'] ?? 'invalid' ) ) {
			$license['status'] = $response['status'];
			update_option( self::OPTION_KEY, $license );
		}
		update_option( self::LAST_CHECK_KEY, time() );
	}

	public function check_for_update( $transient_data ) {
		if ( ! is_object( $transient_data ) ) {
			$transient_data = new stdClass();
		}

		if ( ! empty( $transient_data->response[ $this->plugin_basename ] ) ) {
			return $transient_data;
		}

		$license = $this->get_license_data();
		if ( empty( $license['license_key'] ) || 'valid' !== ( $license['status'] ?? '' ) ) {
			return $transient_data;
		}

		$update_info = get_transient( self::UPDATE_TRANSIENT );
		if ( false === $update_info ) {
			$params = array( 'item_id' => self::ITEM_ID, 'site_url' => home_url() );
			if ( ! empty( $license['activation_hash'] ) ) {
				$params['activation_hash'] = $license['activation_hash'];
			} else {
				$params['license_key'] = $license['license_key'];
			}
			$update_info = $this->api_request( 'get_license_version', $params );
			if ( ! is_wp_error( $update_info ) ) {
				set_transient( self::UPDATE_TRANSIENT, $update_info, 12 * HOUR_IN_SECONDS );
			}
		}

		if ( is_wp_error( $update_info ) || empty( $update_info['new_version'] ) ) {
			return $transient_data;
		}

		if ( version_compare( $update_info['new_version'], ACF_BLOCKS_VERSION, '>' ) ) {
			$transient_data->response[ $this->plugin_basename ] = (object) array(
				'id'           => $this->plugin_basename,
				'slug'         => 'acf-blocks',
				'plugin'       => $this->plugin_basename,
				'new_version'  => $update_info['new_version'],
				'url'          => $update_info['url'] ?? 'https://gauravtiwari.org/plugins/acf-blocks/',
				'package'      => $update_info['package'] ?? '',
				'icons'        => $update_info['icons'] ?? array(),
				'banners'      => $update_info['banners'] ?? array(),
				'tested'       => $update_info['tested'] ?? '',
				'requires_php' => $update_info['requires_php'] ?? '7.4',
			);
		}

		return $transient_data;
	}

	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || 'acf-blocks' !== ( $args->slug ?? '' ) ) {
			return $result;
		}

		$update_info = get_transient( self::UPDATE_TRANSIENT );
		if ( empty( $update_info ) || is_wp_error( $update_info ) ) {
			return $result;
		}

		return (object) array(
			'name'          => $update_info['name'] ?? 'ACF Blocks',
			'slug'          => 'acf-blocks',
			'version'       => $update_info['new_version'] ?? '',
			'author'        => '<a href="https://gauravtiwari.org">Gaurav Tiwari</a>',
			'homepage'      => $update_info['homepage'] ?? 'https://gauravtiwari.org/plugins/acf-blocks/',
			'download_link' => $update_info['package'] ?? '',
			'trunk'         => $update_info['trunk'] ?? '',
			'last_updated'  => $update_info['last_updated'] ?? '',
			'sections'      => $update_info['sections'] ?? array(),
			'banners'       => $update_info['banners'] ?? array(),
			'icons'         => $update_info['icons'] ?? array(),
			'requires'      => $update_info['requires'] ?? '6.0',
			'requires_php'  => $update_info['requires_php'] ?? '7.4',
			'tested'        => $update_info['tested'] ?? '',
		);
	}

	public function clear_update_transient() {
		delete_transient( self::UPDATE_TRANSIENT );
	}

	public function plugin_action_links( $links ) {
		array_unshift( $links, sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=acf-blocks-license' ),
			__( 'License', 'acf-blocks' )
		) );
		return $links;
	}

	public function admin_notices() {
		$screen = get_current_screen();
		if ( ! $screen || 'settings_page_acf-blocks-license' === $screen->id ) {
			return;
		}
		if ( 'plugins' !== $screen->id ) {
			return;
		}

		$license = $this->get_license_data();
		if ( 'valid' === ( $license['status'] ?? '' ) ) {
			return;
		}

		if ( 'expired' === ( $license['status'] ?? '' ) ) {
			printf(
				'<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
				esc_html__( 'Your ACF Blocks license has expired.', 'acf-blocks' ),
				esc_url( admin_url( 'options-general.php?page=acf-blocks-license' ) ),
				esc_html__( 'Manage License', 'acf-blocks' )
			);
		}
	}

	public function render_license_page() {
		$license = $this->get_license_data();
		$status  = $license['status'] ?? 'inactive';
		$key     = $license['license_key'] ?? '';
		$expires = $license['expiration_date'] ?? '';

		settings_errors( 'acf_blocks_license' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'ACF Blocks License', 'acf-blocks' ); ?></h1>
			<div class="card" style="max-width: 600px; margin-top: 20px;">
				<h2 style="margin-top: 0;"><?php esc_html_e( 'License Status', 'acf-blocks' ); ?></h2>

				<?php if ( 'valid' === $status ) : ?>
					<div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
						<strong style="color: #155724;">&#10003; <?php esc_html_e( 'License Active', 'acf-blocks' ); ?></strong>
						<?php if ( $expires && 'lifetime' !== $expires ) : ?>
							<br><small><?php printf( esc_html__( 'Expires: %s', 'acf-blocks' ), esc_html( $expires ) ); ?></small>
						<?php elseif ( 'lifetime' === $expires ) : ?>
							<br><small><?php esc_html_e( 'Lifetime license', 'acf-blocks' ); ?></small>
						<?php endif; ?>
					</div>
					<form method="post">
						<?php wp_nonce_field( 'acf_blocks_license_nonce', 'acf_blocks_license_nonce' ); ?>
						<input type="hidden" name="acf_blocks_license_action" value="deactivate">
						<p><code style="font-size: 14px; padding: 4px 8px;"><?php echo esc_html( $this->mask_key( $key ) ); ?></code></p>
						<p><input type="submit" class="button" value="<?php esc_attr_e( 'Deactivate License', 'acf-blocks' ); ?>"></p>
					</form>

				<?php elseif ( 'expired' === $status ) : ?>
					<div style="background: #fff3cd; border: 1px solid #ffc107; padding: 12px 16px; border-radius: 4px; margin-bottom: 16px;">
						<strong style="color: #856404;">&#9888; <?php esc_html_e( 'License Expired', 'acf-blocks' ); ?></strong>
					</div>
					<p><a href="https://gauravtiwari.org/product/acf-blocks/" class="button button-primary" target="_blank"><?php esc_html_e( 'Renew License', 'acf-blocks' ); ?></a></p>
					<hr>
					<form method="post">
						<?php wp_nonce_field( 'acf_blocks_license_nonce', 'acf_blocks_license_nonce' ); ?>
						<input type="hidden" name="acf_blocks_license_action" value="activate">
						<p><label for="license_key"><strong><?php esc_html_e( 'Or enter a new license key:', 'acf-blocks' ); ?></strong></label><br>
						<input type="text" id="license_key" name="license_key" class="regular-text" style="margin-top: 4px;"></p>
						<p><input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Activate License', 'acf-blocks' ); ?>"></p>
					</form>

				<?php else : ?>
					<p><?php esc_html_e( 'Enter your license key to enable automatic updates and support.', 'acf-blocks' ); ?></p>
					<form method="post">
						<?php wp_nonce_field( 'acf_blocks_license_nonce', 'acf_blocks_license_nonce' ); ?>
						<input type="hidden" name="acf_blocks_license_action" value="activate">
						<p><label for="license_key"><strong><?php esc_html_e( 'License Key', 'acf-blocks' ); ?></strong></label><br>
						<input type="text" id="license_key" name="license_key" class="regular-text" style="margin-top: 4px;"></p>
						<p><input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Activate License', 'acf-blocks' ); ?>"></p>
					</form>
					<hr>
					<p><small><?php printf( esc_html__( 'Don\'t have a license? %sGet one here%s.', 'acf-blocks' ), '<a href="https://gauravtiwari.org/product/acf-blocks/" target="_blank">', '</a>' ); ?></small></p>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	public function get_license_data() {
		$defaults = array(
			'license_key' => '', 'status' => 'inactive', 'activation_hash' => '',
			'expiration_date' => '', 'activated_at' => '',
		);
		$data = get_option( self::OPTION_KEY, array() );
		return is_array( $data ) ? wp_parse_args( $data, $defaults ) : $defaults;
	}

	public function is_valid() {
		$license = $this->get_license_data();
		return 'valid' === ( $license['status'] ?? '' );
	}

	private function api_request( $action, $params = array() ) {
		$url = add_query_arg( 'fluent-cart', $action, self::LICENSE_SERVER );
		$params['current_version'] = defined( 'ACF_BLOCKS_VERSION' ) ? ACF_BLOCKS_VERSION : '1.0.0';

		$response = wp_remote_post( $url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $params ) );
		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'api_error', __( 'Could not connect to the license server.', 'acf-blocks' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );
		$code = wp_remote_retrieve_response_code( $response );

		if ( $code >= 400 || empty( $body ) ) {
			return new WP_Error( 'api_error', $body['message'] ?? __( 'License server error.', 'acf-blocks' ) );
		}

		return $body;
	}

	private function mask_key( $key ) {
		if ( strlen( $key ) <= 8 ) {
			return $key;
		}
		return substr( $key, 0, 4 ) . str_repeat( '*', strlen( $key ) - 8 ) . substr( $key, -4 );
	}
}
