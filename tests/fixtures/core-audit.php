<?php
/** Isolated WordPress API boundary probes; no network or persistent state. */
define( 'ABSPATH', __DIR__ . '/' );
define( 'ACF_BLOCKS_PLUGIN_DIR', dirname( __DIR__, 2 ) . '/' );
$GLOBALS['options'] = array();
$GLOBALS['groups'] = array();
$GLOBALS['http_result'] = null;
$GLOBALS['trust_proxy'] = false;
function add_action() {}
function add_filter() {}
function apply_filters( $name, $value ) { return 'acf_star_rating_trust_cloudflare_header' === $name && $GLOBALS['trust_proxy'] ? true : $value; }
function __( $value, $domain = null ) { return $value; }
function plugin_basename( $path ) { return basename( $path ); }
function home_url() { return 'https://example.test'; }
function add_query_arg( $key, $value, $url ) { return $url . '?' . $key . '=' . $value; }
function get_option( $name, $default = false ) { return $GLOBALS['options'][ $name ] ?? $default; }
function update_option( $name, $value, $autoload = null ) { $GLOBALS['options'][ $name ] = $value; return true; }
function delete_option( $name ) { unset( $GLOBALS['options'][ $name ] ); return true; }
function delete_transient() { return true; }
function wp_parse_args( $args, $defaults ) { return array_merge( $defaults, $args ); }
function wp_remote_post() { return $GLOBALS['http_result']; }
function wp_remote_retrieve_body( $response ) { return json_encode( $response['body'] ); }
function wp_remote_retrieve_response_code( $response ) { return $response['status']; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function wp_unslash( $value ) { return stripslashes( $value ); }
function wp_salt( $scheme ) { return 'regression-test-salt'; }
function trailingslashit( $value ) { return rtrim( $value, '/' ) . '/'; }
function untrailingslashit( $value ) { return rtrim( $value, '/' ); }
function acf_add_local_field_group( $group ) { $GLOBALS['groups'][] = $group; }
function check_ajax_referer() { return true; }
function current_user_can( $capability, $id = 0 ) { return 'upload_files' === $capability || ( 'edit_post' === $capability && 43 === $id ); }
function esc_url_raw( $url ) { return $url; }
function absint( $value ) { return abs( (int) $value ); }
function wp_send_json_error( $message ) { throw new RuntimeException( $message ); }
function get_posts() { throw new RuntimeException( 'import-reached-query' ); }
class WP_Error {
    public $code;
    public function __construct( $code, $message = '', $data = null ) { $this->code = $code; }
}
$root = ACF_BLOCKS_PLUGIN_DIR;
$probe = $argv[1] ?? '';
if ( 'license' === $probe ) {
    require $root . 'includes/license-manager.php';
    $manager = new ACF_Blocks_License_Manager( 'acf-blocks.php' );
    $license = array( 'license_key' => 'test-key', 'status' => 'valid', 'activation_hash' => 'test-activation' );
    $results = array();
    foreach ( array( 'transport' => new WP_Error( 'offline' ), 'rejected' => array( 'status' => 200, 'body' => array( 'success' => false, 'message' => 'Rejected' ) ), 'confirmed' => array( 'status' => 200, 'body' => array( 'success' => true, 'status' => 'deactivated' ) ) ) as $name => $response ) {
        $GLOBALS['options'][ ACF_Blocks_License_Manager::OPTION_KEY ] = $license;
        $GLOBALS['http_result'] = $response;
        $result = $manager->deactivate_license();
        $results[ $name ] = array( 'error' => is_wp_error( $result ), 'stored' => $GLOBALS['options'][ ACF_Blocks_License_Manager::OPTION_KEY ] );
    }
    echo json_encode( $results );
} elseif ( 'proxy' === $probe ) {
    require $root . 'blocks/star-rating-block/extra.php';
    $_SERVER = array( 'REMOTE_ADDR' => '198.51.100.10' );
    $baseline = acf_star_rating_voter_hash();
    $_SERVER['HTTP_CF_RAY'] = 'untrusted-header';
    $_SERVER['HTTP_CF_CONNECTING_IP'] = '203.0.113.1';
    $first = acf_star_rating_voter_hash();
    $_SERVER['HTTP_CF_CONNECTING_IP'] = '203.0.113.2';
    $second = acf_star_rating_voter_hash();
    $GLOBALS['trust_proxy'] = true;
    $trusted = acf_star_rating_voter_hash();
    echo json_encode( array( 'default_ignores_spoofed_headers' => $baseline === $first && $first === $second, 'explicit_trust_uses_client_ip' => $trusted !== $baseline ) );
} elseif ( 'manifest' === $probe ) {
    require $root . 'includes/functions.php';
    $blocks = acf_blocks_discover_block_metadata();
    foreach ( $blocks as $block ) {
        acf_blocks_register_field_groups( $block['folder'], $block['field_groups'] ?? null );
    }
    $keys = array_column( $GLOBALS['groups'], 'key' );
    echo json_encode( array( 'blocks' => count( $blocks ), 'groups' => count( $keys ), 'accordion' => in_array( 'group_accordion_block', $keys, true ), 'keys' => $keys ) );
} elseif ( 'import-permission' === $probe ) {
    require $root . 'blocks/url-preview/extra.php';
    $results = array();
    foreach ( array( 'other_post' => 42, 'own_post' => 43, 'unattached' => 0 ) as $name => $id ) {
        $_POST = array( 'image_url' => 'https://example.test/image.png', 'post_id' => $id );
        try {
            acf_url_preview_import_image_handler();
        } catch ( RuntimeException $error ) {
            $results[ $name ] = $error->getMessage();
        }
    }
    echo json_encode( $results );
} else {
    fwrite( STDERR, 'Unknown probe' );
    exit( 1 );
}
