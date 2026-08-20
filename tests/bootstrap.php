<?php

define( 'ABSPATH', __DIR__ . '/wordpress/' );
define( 'ACF_BLOCKS_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
define( 'ACF_BLOCKS_PLUGIN_URL', 'https://example.test/wp-content/plugins/acf-blocks-plugin/' );
define( 'ACF_BLOCKS_VERSION', 'test' );

$GLOBALS['acf_blocks_test_options'] = array();
$GLOBALS['acf_blocks_test_styles']  = array();
$GLOBALS['acf_blocks_test_block_styles'] = array();

function add_action() {}
function add_filter() {}
function apply_filters( $hook, $value ) { return $value; }
function trailingslashit( $value ) { return rtrim( (string) $value, '/\\' ) . '/'; }
function wp_normalize_path( $path ) { return str_replace( '\\', '/', (string) $path ); }
function sanitize_text_field( $value ) { return trim( (string) $value ); }
function __( $text, $domain = null ) { return $text; }
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
