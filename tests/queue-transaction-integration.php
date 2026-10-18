<?php
/** Regression: save_post enqueues must not expose an uncommitted queue version. */
require __DIR__ . '/fixtures/storage-wordpress-bootstrap.php';
if ( isset( $argv[1] ) && 'enqueue' === $argv[1] ) {
    add_filter( 'pre_update_option_' . ACF_BLOCKS_IMAGE_QUEUE_OPTION, function ( $value ) use ( $argv ) {
        // The worker has read/computed its version; its UPDATE may now block
        // behind the parent transaction's uncommitted option-row lock.
        file_put_contents( $argv[3], 'ready' );
        return $value;
    } );
    acf_blocks_queue_external_images( (int) $argv[2], get_post( (int) $argv[2] ) );
    exit;
}
$acf = '<!-- wp:acf/card {"data":{"image":"http://93.184.216.34/transaction.png"}} /-->';
$first = wp_insert_post( array( 'post_title' => 'Transaction enqueue 1', 'post_status' => 'draft', 'post_content' => wp_slash( $acf ) ), true );
$second = wp_insert_post( array( 'post_title' => 'Transaction enqueue 2', 'post_status' => 'draft', 'post_content' => wp_slash( $acf ) ), true );
update_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array(), false );
$marker = WP_TEMP_DIR . '/enqueue-barrier-' . wp_generate_uuid4();
$child = null;
$pipes = array();
$overlap = function ( $id ) use ( $first, $second, $marker, &$child, &$pipes ) {
    if ( $id !== $first ) {
        return;
    }
    $child = proc_open( array( PHP_BINARY, __FILE__, 'enqueue', (string) $second, $marker ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
    fclose( $pipes[0] );
    $deadline = microtime( true ) + 5;
    while ( ! file_exists( $marker ) && microtime( true ) < $deadline ) {
        usleep( 1000 );
    }
    if ( ! file_exists( $marker ) ) {
        throw new RuntimeException( 'Independent enqueuer did not reach its write barrier.' );
    }
    // Joining here would deadlock the old implementation. Let the owned
    // content transaction commit before waiting for the independent UPDATE.
};
add_action( 'save_post', $overlap, 99 );
$result = acf_blocks_storage_update_post( get_post( $first ), $acf . "\nUpdated." );
remove_action( 'save_post', $overlap, 99 );
$output = stream_get_contents( $pipes[1] ) . stream_get_contents( $pipes[2] );
fclose( $pipes[1] );
fclose( $pipes[2] );
$exit = proc_close( $child );
wp_delete_file( $marker );
$queue = acf_blocks_storage_read_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array() );
$checks = array( 'transaction save enqueue and independent enqueue both survive commit' => ! is_wp_error( $result ) && 0 === $exit && isset( $queue[ $first ], $queue[ $second ] ) );
// A failed COMMIT must discard all save-hook work queued by that transaction.
update_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array(), false );
$before_rollback = get_post_field( 'post_content', $first );
$fail_commit = function ( $query ) { return 'COMMIT' === $query ? 'ACFB_INVALID_COMMIT' : $query; };
add_filter( 'query', $fail_commit );
$suppressed = $wpdb->suppress_errors( true );
$rolled_back = acf_blocks_storage_update_post( get_post( $first ), $acf . "\nMust roll back." );
$wpdb->suppress_errors( $suppressed );
remove_filter( 'query', $fail_commit );
$checks['rollback discards deferred enqueues and restores committed content'] = is_wp_error( $rolled_back )
    && ! acf_blocks_storage_read_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array() )
    && get_post_field( 'post_content', $first ) === $before_rollback;

// Caller-owned transactions must keep queue state outside their transaction.
$wpdb->query( 'START TRANSACTION' );
$before = acf_blocks_storage_read_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array() );
$mutation = acf_blocks_storage_mutate_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, function () { return array( 'unsafe' => true ); } );
$after = acf_blocks_storage_read_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array() );
$wpdb->query( 'ROLLBACK' );
$checks['option mutations fail safely inside a caller-owned transaction'] = is_wp_error( $mutation ) && $before === $after;
// A corrupting metadata filter must not leave a restore point that a retry trusts.
$original = "<!-- /wp:post-content -->\n<!-- wp:acf/heading /-->";
$backup_post = wp_insert_post( array( 'post_title' => 'Verified backup', 'post_status' => 'draft', 'post_content' => wp_slash( $original ) ), true );
$corrupt = function () { return 'invalid restore point'; };
add_filter( 'sanitize_post_meta_' . ACF_BLOCKS_MIGRATOR_BACKUP_META, $corrupt );
$first_try = acf_blocks_migrator_run_batch( 0 );
remove_filter( 'sanitize_post_meta_' . ACF_BLOCKS_MIGRATOR_BACKUP_META, $corrupt );
$removed = ! metadata_exists( 'post', $backup_post, ACF_BLOCKS_MIGRATOR_BACKUP_META );
$second_try = acf_blocks_migrator_run_batch( 0 );
$checks['failed backup verification removes only its row before retry'] = ! empty( $first_try['errors'] ) && $removed
    && get_post_meta( $backup_post, ACF_BLOCKS_MIGRATOR_BACKUP_META, true ) === $original;
acf_blocks_migrator_clear_backups();
wp_delete_post( $backup_post, true );
wp_delete_post( $first, true );
wp_delete_post( $second, true );
update_option( ACF_BLOCKS_IMAGE_QUEUE_OPTION, array(), false );
echo wp_json_encode( $checks, JSON_PRETTY_PRINT ) . "\n";
if ( $output ) { fwrite( STDERR, $output ); }
exit( in_array( false, $checks, true ) ? 1 : 0 );
