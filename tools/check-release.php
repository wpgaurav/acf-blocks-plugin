<?php
/** Verify version alignment and the runtime files needed by the upload ZIP. */
$root = dirname( __DIR__ );
$plugin = (string) file_get_contents( $root . '/acf-blocks.php' );
preg_match( '/^ \* Version: ([0-9]+\.[0-9]+\.[0-9]+)$/m', $plugin, $header );
preg_match( "/define\( 'ACF_BLOCKS_VERSION', '([^']+)' \)/", $plugin, $constant );
$version = $header[1] ?? '';
$tag = $argv[1] ?? 'v' . $version;
if ( ! $version || ( $constant[1] ?? '' ) !== $version || $tag !== 'v' . $version ) {
    fwrite( STDERR, "Release tag, plugin header and version constant must agree.\n" );
    exit( 1 );
}
if ( false === strpos( (string) file_get_contents( $root . '/CHANGELOG.md' ), '## [' . $version . ']' ) ) {
    fwrite( STDERR, "Release changelog section is missing.\n" );
    exit( 1 );
}
$preview = (string) file_get_contents( $root . '/blocks/video-block/youtube-preview.html' );
if ( false === strpos( $preview, 'youtube-preview.min.js?ver=' . $version . '"' ) ) {
    fwrite( STDERR, "Preview script cache version does not match the release.\n" );
    exit( 1 );
}
foreach ( array( 'video.js', 'video.min.js', 'youtube-preview.html', 'youtube-preview.js', 'youtube-preview.min.js', 'extra.php' ) as $file ) {
    if ( ! is_readable( $root . '/blocks/video-block/' . $file ) ) {
        fwrite( STDERR, "Missing video runtime asset: " . $file . "\n" );
        exit( 1 );
    }
}
echo 'Release version and runtime files verified: ' . $version . "\n";
