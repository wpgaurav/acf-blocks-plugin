<?php

define( 'ABSPATH', __DIR__ . '/wordpress/' );
define( 'ACF_BLOCKS_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
define( 'ACF_BLOCKS_PLUGIN_URL', 'https://example.test/wp-content/plugins/acf-blocks-plugin/' );
define( 'ACF_BLOCKS_VERSION', 'test' );

$GLOBALS['acf_blocks_test_options'] = array();
$GLOBALS['acf_blocks_test_styles']  = array();
$GLOBALS['acf_blocks_test_block_styles'] = array();

$GLOBALS['acf_blocks_test_actions'] = array();
$GLOBALS['acf_blocks_test_scripts'] = array();
$GLOBALS['acf_blocks_test_script_data'] = array();

function add_action( $hook = '', $callback = null ) { $GLOBALS['acf_blocks_test_actions'][ $hook ][] = $callback; }
function add_filter() {}
function apply_filters( $hook, $value ) { return $value; }
function trailingslashit( $value ) { return rtrim( (string) $value, '/\\' ) . '/'; }
function wp_normalize_path( $path ) { return str_replace( '\\', '/', (string) $path ); }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function __( $text, $domain = null ) { return $text; }
function absint( $value ) { return abs( (int) $value ); }
function sanitize_key( $value ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $value ) ); }
function rest_url( $path = '' ) { return 'https://example.test/wp-json/' . ltrim( $path, '/' ); }
function wp_script_is( $handle, $status = 'enqueued' ) { return 'registered' === $status && isset( $GLOBALS['acf_blocks_test_scripts'][ $handle ] ); }
function wp_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
    $GLOBALS['acf_blocks_test_scripts'][ $handle ] = compact( 'src', 'deps', 'ver', 'in_footer' );
    return true;
}
function wp_localize_script( $handle, $name, $data ) {
    if ( ! isset( $GLOBALS['acf_blocks_test_scripts'][ $handle ] ) ) {
        return false; // Core refuses to localize an unregistered handle.
    }
    $GLOBALS['acf_blocks_test_script_data'][ $handle ][ $name ] = $data;
    return true;
}
function get_option( $name, $default = false ) {
    return array_key_exists( $name, $GLOBALS['acf_blocks_test_options'] ) ? $GLOBALS['acf_blocks_test_options'][ $name ] : $default;
}
function wp_enqueue_style( $handle, $src, $dependencies = array(), $version = false ) {
    $GLOBALS['acf_blocks_test_styles'][ $handle ] = compact( 'src', 'dependencies', 'version' );
}
function wp_enqueue_block_style( $block_name, $args ) {
    $GLOBALS['acf_blocks_test_block_styles'][ $block_name ] = $args;
}

require_once dirname( __DIR__ ) . '/includes/functions.php';

// Pure transform helpers are unit-testable; the file's admin hooks are inert
// against the stubs above.
require_once dirname( __DIR__ ) . '/includes/block-migrator.php';

// Exposes acfb_minify_css()/acfb_minify_js(); the build body self-guards and
// does not run when the file is required rather than invoked.
require_once dirname( __DIR__ ) . '/tools/build-assets.php';

// Star Rating storage and REST helpers; database work only runs inside the
// functions, so requiring the file just defines them and records its hooks.
require_once dirname( __DIR__ ) . '/blocks/star-rating-block/extra.php';

// Product Box pure helpers (price parsing, savings, dates, colors). Only its
// image-size hook is registered on require.
function esc_attr( $value ) { return htmlspecialchars( (string) $value, ENT_QUOTES ); }
require_once dirname( __DIR__ ) . '/blocks/product-box/extra.php';

// Pros & Cons pure color helper.
require_once dirname( __DIR__ ) . '/blocks/pros-cons/extra.php';
