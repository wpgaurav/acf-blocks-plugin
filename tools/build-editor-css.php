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
$files  = glob( $root . '/blocks/*/*.css' );
$css    = "/* Generated file. Run php tools/build-editor-css.php after block CSS changes. */\n";

sort( $files, SORT_STRING );

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
