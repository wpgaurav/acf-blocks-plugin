<?php
/**
 * Build one cacheable stylesheet for the block editor iframe.
 *
 * Usage:
 *   php tools/build-editor-css.php
 *   php tools/build-editor-css.php --check
 */

$root   = dirname( __DIR__ );
$target = $root . '/assets/css/editor-blocks.css';
$check  = in_array( '--check', $argv, true );
// Sources only — the .min.css siblings produced by tools/build-assets.php must
// never enter the bundle, or every block's CSS would be included twice.
$files = array_values(
    array_filter(
        (array) glob( $root . '/blocks/*/*.css' ),
        static function ( $file ) {
            return ! preg_match( '/\.min\.css$/', $file );
        }
    )
);
$css = "/* Generated file. Run php tools/build-editor-css.php after block CSS changes. */\n";

sort( $files, SORT_STRING );

// The token bridge must lead the bundle so the editor iframe resolves the
// --acfb-* custom properties every block stylesheet below depends on.
$tokens = $root . '/assets/css/tokens.css';
if ( is_readable( $tokens ) ) {
    $css .= "\n/* assets/css/tokens.css */\n" . trim( (string) file_get_contents( $tokens ) ) . "\n";
}

foreach ( $files as $file ) {
    $relative = substr( $file, strlen( $root ) + 1 );
    $css     .= "\n/* {$relative} */\n" . trim( (string) file_get_contents( $file ) ) . "\n";
}

if ( $check ) {
    $current = is_file( $target ) ? (string) file_get_contents( $target ) : '';
    if ( $current !== $css ) {
        fwrite( STDERR, "Editor CSS bundle is out of date.\n" );
        exit( 1 );
    }
    echo "Editor CSS bundle is current.\n";
    exit( 0 );
}

if ( ! is_dir( dirname( $target ) ) ) {
    mkdir( dirname( $target ), 0777, true );
}

file_put_contents( $target, $css );
echo 'Generated editor bundle from ' . count( $files ) . " stylesheets.\n";
