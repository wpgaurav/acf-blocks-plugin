<?php
/**
 * Optional real-WordPress / MariaDB integration checks. Never uses a Studio site DB.
 *
 * Initialize an isolated MariaDB datadir with skip-networking, create the
 * acfb_storage_test database, then set ACFB_TEST_WP_ROOT, ACFB_TEST_SANDBOX,
 * and ACFB_TEST_MYSQL_SOCKET.
 * Run: php tests/storage-wordpress-integration.php
 */
require __DIR__ . '/fixtures/storage-wordpress-bootstrap.php';

function acfb_storage_spawn( $mode, $id, $content = '' ) {
    $pipes = array();
    $process = proc_open( array( PHP_BINARY, __FILE__, $mode, (string) $id, $content ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
    fclose( $pipes[0] );
    return array( $process, $pipes );
}
function acfb_storage_join( $worker ) {
    list( $process, $pipes ) = $worker;
    $output = stream_get_contents( $pipes[1] );
    $errors = stream_get_contents( $pipes[2] );
    fclose( $pipes[1] );
    fclose( $pipes[2] );
    if ( 0 !== proc_close( $process ) ) {
        throw new RuntimeException( 'Worker failed: ' . $errors . $output );
    }
    return $output;
}
function acfb_storage_post( $content ) {
    return wp_insert_post( array( 'post_title' => 'Storage regression', 'post_type' => 'post', 'post_status' => 'draft', 'post_content' => wp_slash( $content ) ), true );
}
if ( isset( $argv[1] ) && 'editor' === $argv[1] ) {
    $id = (int) $argv[2];
    wp_update_post( array( 'ID' => $id, 'post_content' => wp_slash( $argv[3] ) ), true );
    exit;
}
if ( isset( $argv[1] ) && 'enqueue' === $argv[1] ) {
    acf_blocks_queue_external_images( (int) $argv[2], get_post( (int) $argv[2] ) );
    exit;
}
if ( isset( $argv[1] ) && 'cancel' === $argv[1] ) {
    acf_blocks_migrator_cancel_background();
    exit;
}

$checks = array();
function acfb_check( $condition, $name ) {
    global $checks;
    $checks[ $name ] = (bool) $condition;
    if ( ! $condition ) {
        fwrite( STDERR, "FAIL: $name\n" );
    }
}
// Pure repairs must preserve literal comment examples and their exact JSON.
$example = '<!-- wp:acf/callout {"data":{"text":"Example: <!-- wp:paragraph -->"}} /-->';
$fixed = acf_blocks_migrator_strip_dangling_openers( $example, $count );
acfb_check( $example === $fixed && 0 === $count, 'literal comment openers in JSON are preserved' );
$blocks = acf_blocks_recovery_html_to_blocks( '&lt;img src=x onerror=alert(1)&gt; &amp; example' );
acfb_check( false === strpos( $blocks[0]['innerHTML'], '<img' ) && false !== strpos( $blocks[0]['innerHTML'], '&lt;img' ), 'recovery preserves escaped text as text' );

// Restore points round-trip JSON quotes/backslashes through the real metadata API.
$content = "<!-- /wp:post-content -->\n<!-- wp:acf/heading " . wp_json_encode( array( 'data' => array( 'heading' => 'Quoted "text" and C:\\files\\path' ) ) ) . ' /-->';
$id = acfb_storage_post( $content );
delete_option( ACF_BLOCKS_MIGRATOR_CURSOR_OPTION );
$result = acf_blocks_migrator_run_batch( 0 );
acfb_check( get_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META, true ) === $content, 'migration restore point preserves Gutenberg escapes' );
acf_blocks_migrator_revert();
acfb_check( get_post_field( 'post_content', $id ) === $content, 'revert restores exact original content' );
wp_delete_post( $id, true );

$id = acfb_storage_post( $content );
$deny_backup = function ( $check, $object_id, $key ) { return ACF_BLOCKS_MIGRATOR_BACKUP_META === $key ? false : $check; };
add_filter( 'update_post_metadata', $deny_backup, 10, 3 );
add_filter( 'add_post_metadata', $deny_backup, 10, 3 );
$result = acf_blocks_migrator_run_batch( 0 );
remove_filter( 'update_post_metadata', $deny_backup, 10 );
remove_filter( 'add_post_metadata', $deny_backup, 10 );
acfb_check( get_post_field( 'post_content', $id ) === $content && ! empty( $result['errors'] ), 'failed restore point prevents migration' );
wp_delete_post( $id, true );

$id = acfb_storage_post( '<!-- wp:acf/heading /-->' );
update_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META, wp_slash( $content ) );
update_option( ACF_BLOCKS_MIGRATOR_BATCH_OPTION, array( 'ids' => array( $id ), 'time' => time() ), false );
$deny_update = function () { return true; };
add_filter( 'wp_insert_post_empty_content', $deny_update );
$result = acf_blocks_migrator_revert();
remove_filter( 'wp_insert_post_empty_content', $deny_update );
$batch = acf_blocks_migrator_get_last_batch();
acfb_check( ! empty( $result['errors'] ) && $batch && $batch['ids'] === array( $id ), 'failed revert retains retryable session' );
acf_blocks_migrator_revert();
wp_delete_post( $id, true );

$url = 'http://93.184.216.34/localizer-' . wp_generate_uuid4() . '.png';
$acf = '<!-- wp:acf/card ' . wp_json_encode( array( 'data' => array( 'image' => $url ) ), JSON_UNESCAPED_SLASHES ) . ' /-->';
$id = acfb_storage_post( $acf );
$other = acfb_storage_post( $acf );
update_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array(), false );
acf_blocks_queue_external_images( $id, get_post( $id ) );
$http = function ( $response, $args, $requested ) use ( $url, $id, $other, $acf ) {
    if ( $requested !== $url ) {
        return $response;
    }
    // Two genuinely independent PHP/WordPress DB connections overlap the
    // worker's download. The queue was already claimed before they run.
    acfb_storage_join( acfb_storage_spawn( 'editor', $id, $acf . "\nEditor added this paragraph." ) );
    acfb_storage_join( acfb_storage_spawn( 'enqueue', $other ) );
    file_put_contents( $args['filename'], base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9YLW98sAAAAASUVORK5CYII=' ) );
    return array( 'headers' => array(), 'body' => '', 'response' => array( 'code' => 200, 'message' => 'OK' ), 'cookies' => array(), 'filename' => $args['filename'] );
};
add_filter( 'pre_http_request', $http, 1000, 3 );
acf_blocks_process_image_queue();
remove_filter( 'pre_http_request', $http, 1000 );
clean_post_cache( $id );
acfb_check( false !== strpos( get_post_field( 'post_content', $id ), 'Editor added this paragraph.' ), 'concurrent editor save survives localization' );
$queue = get_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION );
wp_cache_delete( ACF_BLOCKS_IMAGE_QUEUE_OPTION, 'options' );
$queue = get_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION );
acfb_check( isset( $queue[ $other ] ), 'concurrent enqueue survives older worker completion' );
acfb_check( isset( $queue[ $id ] ) && empty( $queue[ $id ]['claim'] ), 'newer save remains queued after old worker finishes' );

// A clean commit still goes through WP's revision, cache and post-save hooks.
$snapshot = get_post( $other );
$observed = 0;
$hook = function ( $saved_id ) use ( $other, &$observed ) { if ( $saved_id === $other ) { $observed++; } };
add_action( 'save_post', $hook );
if ( function_exists( 'acf_blocks_storage_update_post' ) ) {
    $result = acf_blocks_storage_update_post( $snapshot, $acf . "\nLocalized content." );
    acfb_check( ! is_wp_error( $result ) && 1 === $observed && false !== strpos( get_post_field( 'post_content', $other ), 'Localized content.' ) && count( wp_get_post_revisions( $other ) ) > 0, 'atomic commit preserves WordPress hooks cache and revisions' );
    $wpdb->query( 'START TRANSACTION' );
    $probe = 'acfb_transaction_probe_' . wp_generate_uuid4();
    $wpdb->insert( $wpdb->options, array( 'option_name' => $probe, 'option_value' => 'rollback-me', 'autoload' => 'off' ) );
    $result = acf_blocks_storage_update_post( get_post( $other ), 'Do not write inside another transaction' );
    $wpdb->query( 'ROLLBACK' );
    acfb_check( is_wp_error( $result ) && null === $wpdb->get_var( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $probe ) ), 'existing transaction is left open and untouched' );
} else {
    acfb_check( false, 'atomic commit preserves WordPress hooks cache and revisions' );
    acfb_check( false, 'existing transaction is left open and untouched' );
}
remove_action( 'save_post', $hook );
wp_delete_post( $id, true );
wp_delete_post( $other, true );
update_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array(), false );

// Model another request repopulating a persistent post cache before COMMIT.
$id = acfb_storage_post( 'Original content' );
$snapshot = get_post( $id );
$external = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
$cache_race = false;
$cache_refill = function ( $post_id ) use ( $id, $external, &$cache_race ) {
    global $wpdb;
    if ( $post_id !== $id ) {
        return;
    }
    $own = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $id ) );
    $committed = $external->get_row( $external->prepare( "SELECT * FROM {$wpdb->posts} WHERE ID = %d", $id ) );
    if ( 'Transformed content' === $own && 'Original content' === $committed->post_content ) {
        wp_cache_set( $id, $committed, 'posts' );
        $cache_race = true;
    }
};
add_action( 'clean_post_cache', $cache_refill );
if ( function_exists( 'acf_blocks_storage_update_post' ) ) {
    $result = acf_blocks_storage_update_post( $snapshot, 'Transformed content' );
    acfb_check( ! is_wp_error( $result ) && $cache_race && 'Transformed content' === get_post( $id )->post_content, 'post-commit invalidation removes another connection stale cache refill' );
} else {
    acfb_check( false, 'post-commit invalidation removes another connection stale cache refill' );
}
remove_action( 'clean_post_cache', $cache_refill );
$external->close();
wp_delete_post( $id, true );

// Cancel in a second process while the background worker is inside a save.
$id = acfb_storage_post( $content );
$cancel_ids = array( $id );
for ( $i = 0; $i < ACF_BLOCKS_MIGRATOR_BATCH_SIZE; $i++ ) {
    $cancel_ids[] = acfb_storage_post( $content );
}
acf_blocks_migrator_start_background();
$cancel = function ( $saved_id ) use ( $id ) {
    if ( $saved_id === $id ) {
        acfb_storage_join( acfb_storage_spawn( 'cancel', $id ) );
    }
};
add_action( 'save_post', $cancel );
acf_blocks_migrator_process_background();
remove_action( 'save_post', $cancel );
wp_cache_delete( ACF_BLOCKS_MIGRATOR_BACKGROUND_OPTION, 'options' );
$state = get_option( ACF_BLOCKS_MIGRATOR_BACKGROUND_OPTION );
acfb_check( empty( $state['running'] ) && ! wp_next_scheduled( ACF_BLOCKS_MIGRATOR_BACKGROUND_EVENT ), 'concurrent cancellation cannot be revived by stale worker state' );
acfb_check( get_post_field( 'post_content', $cancel_ids[1] ) === $content && 1 === (int) $state['migrated'], 'cancellation stops the next post and retains completed progress' );
foreach ( $cancel_ids as $cancel_id ) { wp_delete_post( $cancel_id, true ); }

// SVGs must be rejected even if an old unsafe cached copy already exists.
$url = 'http://93.184.216.34/script.svg';
$dir = acf_blocks_get_local_image_dir();
$file = $dir['dir'] . acf_blocks_readable_image_filename( $url, 'svg' );
file_put_contents( $file, '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>' );
acfb_check( false === acf_blocks_download_external_image( $url ), 'cached script-bearing SVG is never reused' );
wp_delete_file( $file );

// A new file whose attachment cannot be created must not be left public.
$url = 'http://93.184.216.34/attachment-failure-' . wp_generate_uuid4() . '.png';
$raster = function ( $response, $args ) {
    file_put_contents( $args['filename'], base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9YLW98sAAAAASUVORK5CYII=' ) );
    return array( 'headers' => array(), 'body' => '', 'response' => array( 'code' => 200, 'message' => 'OK' ), 'cookies' => array() );
};
add_filter( 'pre_http_request', $raster, 1000, 2 );
add_filter( 'wp_insert_post_empty_content', $deny_update );
$result = acf_blocks_download_external_image( $url );
remove_filter( 'wp_insert_post_empty_content', $deny_update );
remove_filter( 'pre_http_request', $raster, 1000 );
$file = $dir['dir'] . acf_blocks_readable_image_filename( $url, 'png' );
acfb_check( false === $result && ! file_exists( $file ), 'attachment failure removes the newly downloaded public file' );

// Fast mutation stress: independently queued posts all survive option updates.
$ids = array();
$workers = array();
for ( $i = 0; $i < 6; $i++ ) {
    $post_id = acfb_storage_post( $acf );
    $ids[] = $post_id;
    $workers[] = acfb_storage_spawn( 'enqueue', $post_id );
}
foreach ( $workers as $worker ) { acfb_storage_join( $worker ); }
wp_cache_delete( ACF_BLOCKS_IMAGE_QUEUE_OPTION, 'options' );
$queue = get_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array() );
acfb_check( count( array_intersect( $ids, array_keys( $queue ) ) ) === count( $ids ), 'six simultaneous enqueue processes preserve every job' );
foreach ( $ids as $post_id ) { wp_delete_post( $post_id, true ); }
update_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array(), false );
acf_blocks_migrator_clear_backups();
echo wp_json_encode( $checks, JSON_PRETTY_PRINT ) . "\n";
exit( in_array( false, $checks, true ) ? 1 : 0 );
