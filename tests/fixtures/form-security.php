<?php
// Isolated WordPress boundary harness: no outgoing network or live site writes.
define( 'ABSPATH', __DIR__ . '/wordpress/' );
define( 'MINUTE_IN_SECONDS', 60 );
$GLOBALS['posts'] = array();
$GLOBALS['transients'] = array();
$GLOBALS['calls'] = array();
$GLOBALS['options'] = array();
function add_action() {}
function add_filter() {}
function apply_filters( $hook, $value ) { return $value; }
function __( $text ) { return $text; }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_text_field( $value ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $value ) ) ); }
function esc_url_raw( $value ) { return (string) $value; }
function wp_parse_url( $url, $component = -1 ) { return parse_url( $url, $component ); }
function get_transient( $key ) { return $GLOBALS['transients'][ $key ] ?? false; }
function set_transient( $key, $value ) { $GLOBALS['transients'][ $key ] = $value; }
function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); }
function wp_salt() { return 'private-test-key'; }
function get_option( $key, $default = false ) { return $GLOBALS['options'][ $key ] ?? $default; }
function get_post( $id ) { return $GLOBALS['posts'][ $id ] ?? null; }
function parse_blocks( $content ) { return json_decode( $content, true ) ?: array(); }
function get_block_templates( $query = array(), $type = 'wp_template' ) { return array_values( $GLOBALS['templates'][ $type ] ?? array() ); }
function get_block_template( $id, $type = 'wp_template' ) { return $GLOBALS['templates'][ $type ][ $id ] ?? null; }
function wp_is_post_revision() { return false; }
function get_field() { return null; }
function acf_blocks_get_block_metadata_cache() { return require $GLOBALS['plugin_root'] . '/includes/generated-block-manifest.php'; }
function wp_get_sidebars_widgets() { return array( 'sidebar-1' => $GLOBALS['active_widgets'] ?? array( 'block-2' ) ); }
function get_stylesheet() { return 'theme'; }
function get_template() { return 'theme'; }
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES ); }
function esc_url( $value ) { return esc_attr( $value ); }
function esc_html( $value ) { return esc_attr( $value ); }
function esc_html_e( $value ) { echo esc_html( $value ); }
function esc_attr_e( $value ) { echo esc_attr( $value ); }
function esc_attr__( $value ) { return esc_attr( $value ); }
function wp_kses_post( $value ) { return $value; }
function rest_url( $path ) { return 'https://site.example/wp-json/' . $path; }
function wp_unique_id() { return 'instance'; }
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function wp_remote_retrieve_response_code( $value ) { return $value['response']['code'] ?? 0; }
function wp_remote_retrieve_body( $value ) { return $value['body'] ?? ''; }
function wp_remote_post( $url, $args ) { $GLOBALS['calls'][] = array( 'safe' => false, 'url' => $url, 'args' => $args ); return array( 'response' => array( 'code' => 200 ) ); }
function wp_safe_remote_post( $url, $args ) { $GLOBALS['calls'][] = array( 'safe' => true, 'url' => $url, 'args' => $args ); return array( 'response' => array( 'code' => $GLOBALS['http_status'] ?? 200 ) ); }
function wp_safe_remote_get( $url, $args ) { $GLOBALS['calls'][] = array( 'safe' => true, 'url' => $url, 'args' => $args ); return array( 'response' => array( 'code' => $GLOBALS['http_status'] ?? 200 ), 'body' => base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScLbtAAAAABJRU5ErkJggg==' ) ); }
function wp_tempnam() { return tempnam( sys_get_temp_dir(), 'acfb-test' ); }
class WP_Error { public $code; public function __construct( $code, $message = '', $data = array() ) { $this->code = $code; } public function get_error_message() { return $this->code; } }
class WP_REST_Response { public $data; public function __construct( $data ) { $this->data = $data; } }
class FormRequest { private $params; public function __construct( $params ) { $this->params = $params; } public function get_json_params() { return $this->params; } }
$root = $argv[2] ?? dirname( __DIR__, 2 );
require $root . '/includes/compat.php';
require $root . '/blocks/email-form/extra.php';
require $root . '/blocks/url-preview/extra.php';
$data = array( 'form_type' => 'webhook', 'webhook_url' => 'https://hooks.example/submit', 'webhook_auth_headers' => "Authorization: Bearer test-private-token\nX-API-Key: second-private-key", 'display_name_field' => 1, 'hidden_fields' => 1, 'hidden_fields_0_field_name' => 'list_id', 'hidden_fields_0_field_value' => '123', 'hidden_fields_0_attributes_id' => 'list-field', 'form_attributes_id' => 'signup', 'name_field_attributes_class' => 'custom-name' );
$block = array( 'name' => 'acf/email-form', 'id' => 'block_1', 'data' => $data );
$saved = array( 'blockName' => 'acf/email-form', 'attrs' => $block, 'innerBlocks' => array() );
$GLOBALS['posts'][1] = (object) array( 'ID' => 1, 'post_type' => 'page', 'post_status' => 'publish', 'post_password' => '', 'post_content' => json_encode( array( $saved ) ) );
function rendered_form( $block, $post_id = 1 ) { $is_preview = false; ob_start(); include $GLOBALS['plugin_root'] . '/blocks/email-form/email-form.php'; return ob_get_clean(); }
$GLOBALS['plugin_root'] = $root;
$scenario = $argv[1];
set_error_handler( function ( $severity, $message ) { $GLOBALS['warnings'][] = $message; return true; } );
if ( 'render' === $scenario ) {
    $html = rendered_form( $block );
    preg_match( '/window\.acfEmailFormConfigs\[.*?\] = (.*?);/', $html, $matches );
    echo json_encode( array( 'html' => $html, 'config' => json_decode( $matches[1] ?? '{}', true ), 'warnings' => $GLOBALS['warnings'] ?? array() ) );
} elseif ( 'arbitrary' === $scenario ) {
    $response = acf_email_form_proxy_handler( new FormRequest( array( 'webhookUrl' => 'https://127.0.0.1/internal', 'webhookAuthHeaders' => 'Authorization: attacker', 'data' => array( 'email' => 'test@example.com' ) ) ) );
    echo json_encode( array( 'error' => $response instanceof WP_Error ? $response->code : null, 'calls' => $GLOBALS['calls'] ) );
} elseif ( 'dimensions' === $scenario ) {
    $image = tempnam( sys_get_temp_dir(), 'acfb-fixture' );
    file_put_contents( $image, base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScLbtAAAAABJRU5ErkJggg==' ) );
    $local = acf_url_preview_get_image_dimensions( 'file://' . $image );
    unlink( $image );
    $public = acf_url_preview_get_image_dimensions( 'https://images.example/photo.png' );
    echo json_encode( array( 'local' => $local, 'public' => $public, 'calls' => $GLOBALS['calls'] ) );
} else {
    if ( in_array( $scenario, array( 'bad_headers', 'insecure' ), true ) ) {
        $block['data'][ 'bad_headers' === $scenario ? 'webhook_auth_headers' : 'webhook_url' ] = 'bad_headers' === $scenario ? 'Host: internal.example' : 'http://hooks.example/submit';
        $saved['attrs'] = $block;
        $GLOBALS['posts'][1]->post_content = json_encode( array( $saved ) );
    }
    if ( in_array( $scenario, array( 'nested', 'cycle' ), true ) ) {
        $GLOBALS['posts'][2] = clone $GLOBALS['posts'][1];
        $GLOBALS['posts'][2]->ID = 2;
        $GLOBALS['posts'][2]->post_type = 'wp_block';
        $GLOBALS['posts'][1]->post_content = json_encode( array( array( 'blockName' => 'core/group', 'attrs' => array(), 'innerBlocks' => array( array( 'blockName' => 'core/block', 'attrs' => array( 'ref' => 2 ) ) ) ) ) );
        if ( 'cycle' === $scenario ) {
            $GLOBALS['posts'][2]->post_content = json_encode( array( array( 'blockName' => 'core/block', 'attrs' => array( 'ref' => 2 ) ), $saved ) );
        }
    } elseif ( in_array( $scenario, array( 'template', 'template_ref', 'private_template' ), true ) ) {
        $GLOBALS['templates']['wp_template_part']['theme//footer'] = (object) array( 'id' => 'theme//footer', 'content' => $GLOBALS['posts'][1]->post_content, 'status' => 'publish' );
        $GLOBALS['posts'][1]->post_content = 'template_ref' === $scenario ? json_encode( array( array( 'blockName' => 'core/template-part', 'attrs' => array( 'slug' => 'footer' ) ) ) ) : '[]';
    } elseif ( in_array( $scenario, array( 'widget', 'inactive_widget' ), true ) ) {
        $GLOBALS['options']['widget_block'][2] = array( 'content' => $GLOBALS['posts'][1]->post_content );
        $GLOBALS['posts'][1]->post_content = '[]';
    }
    $html = rendered_form( $block );
    preg_match( '/window\.acfEmailFormConfigs\[.*?\] = (.*?);/', $html, $matches );
    $config = json_decode( $matches[1] ?? '{}', true );
    if ( 'unrelated' === $scenario ) { $GLOBALS['posts'][1]->post_content = json_encode( array( array( 'blockName' => 'core/paragraph', 'attrs' => array() ), $saved ) ); }
    if ( 'private_template' === $scenario ) { $GLOBALS['templates']['wp_template_part']['theme//footer']->status = 'draft'; }
    if ( 'inactive_widget' === $scenario ) { $GLOBALS['active_widgets'] = array(); }
    if ( 'changed' === $scenario ) { $GLOBALS['posts'][1]->post_content = '[]'; }
    if ( 'draft' === $scenario ) { $GLOBALS['posts'][1]->post_status = 'draft'; }
    if ( 'redirect' === $scenario ) { $GLOBALS['http_status'] = 302; }
    if ( 'tampered' === $scenario ) { $config['formRef'] = ( $config['formRef'] ?? '' ) . 'tampered'; }
    $response = acf_email_form_proxy_handler( new FormRequest( array( 'formRef' => $config['formRef'] ?? '', 'webhookUrl' => 'https://attacker.example/override', 'webhookAuthHeaders' => 'X-Override: attacker', 'data' => array( 'email_form_email' => 'test@example.com', 'array' => array( 'invalid' ) ) ) ) );
    echo json_encode( array( 'error' => $response instanceof WP_Error ? $response->code : null, 'calls' => $GLOBALS['calls'], 'config' => $config ) );
}
