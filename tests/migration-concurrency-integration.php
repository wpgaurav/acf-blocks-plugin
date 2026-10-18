<?php
/** Real WP/isolated MariaDB migration entrypoint serialization regressions. */
require __DIR__ . '/fixtures/storage-wordpress-bootstrap.php';

function acfb_migration_worker( $mode ) {
    $pipes = array();
    $process = proc_open( array( PHP_BINARY, __FILE__, $mode ), array( 0 => array( 'pipe', 'r' ), 1 => array( 'pipe', 'w' ), 2 => array( 'pipe', 'w' ) ), $pipes );
    fclose( $pipes[0] );
    $output = stream_get_contents( $pipes[1] );
    $errors = stream_get_contents( $pipes[2] );
    fclose( $pipes[1] );
    fclose( $pipes[2] );
    if ( 0 !== proc_close( $process ) ) {
        throw new RuntimeException( $errors . $output );
    }
    return json_decode( $output, true );
}
if ( isset( $argv[1] ) ) {
    if ( 'manual' === $argv[1] ) {
        $result = acf_blocks_migrator_run_batch( 0 );
    } elseif ( 'revert' === $argv[1] ) {
        $result = acf_blocks_migrator_revert();
    } else {
        $result = acf_blocks_migrator_clear_backups();
        $result = is_wp_error( $result ) ? array( 'errors' => array( $result->get_error_message() ) ) : array( 'count' => $result );
    }
    echo wp_json_encode( $result );
    exit;
}
function acfb_migration_post() {
    $content = '<!-- /wp:post-content -->' . "\n" . '<!-- wp:acf/heading {"data":{"heading":"Original"}} /-->';
    return wp_insert_post( array( 'post_title' => 'Migration concurrency', 'post_status' => 'draft', 'post_type' => 'post', 'post_content' => wp_slash( $content ) ), true );
}
function acfb_migration_fresh_batch() {
    wp_cache_delete( ACF_BLOCKS_MIGRATOR_BATCH_OPTION, 'options' );
    return acf_blocks_migrator_get_last_batch();
}
$checks = array();
// Pause a manual batch immediately before its backup write. A second PHP
// process must reject another batch before it can snapshot/migrate that post.
acf_blocks_migrator_clear_backups();
$id = acfb_migration_post();
$original = get_post_field( 'post_content', $id );
$peer = null;
$before_backup = function ( $check, $object_id, $key ) use ( $id, &$peer ) {
    if ( $object_id === $id && ACF_BLOCKS_MIGRATOR_BACKUP_META === $key && null === $peer ) {
        $peer = acfb_migration_worker( 'manual' );
    }
    return $check;
};
add_filter( 'update_post_metadata', $before_backup, 10, 3 );
add_filter( 'add_post_metadata', $before_backup, 10, 3 );
$result = acf_blocks_migrator_run_batch( 0 );
remove_filter( 'update_post_metadata', $before_backup, 10 );
remove_filter( 'add_post_metadata', $before_backup, 10 );
clean_post_cache( $id );
$checks['parallel manual batches preserve the winning restore point'] = 1 === $result['count'] && ! empty( $peer['errors'] ) && get_post_meta( $id, ACF_BLOCKS_MIGRATOR_BACKUP_META, true ) === $original;
acf_blocks_migrator_clear_backups();
wp_delete_post( $id, true );

foreach ( array( 'revert', 'discard' ) as $mode ) {
    $first = acfb_migration_post();
    $first_original = get_post_field( 'post_content', $first );
    acf_blocks_migrator_run_batch( 0 );
    $first_migrated = get_post_field( 'post_content', $first );
    $second = acfb_migration_post();
    $second_original = get_post_field( 'post_content', $second );
    $peer = null;
    $during_save = function ( $post_id ) use ( $second, $mode, &$peer ) {
        if ( $post_id === $second && null === $peer ) {
            $peer = acfb_migration_worker( $mode );
        }
    };
    add_action( 'save_post', $during_save, 99 );
    $result = acf_blocks_migrator_run_batch( 0 );
    remove_action( 'save_post', $during_save, 99 );
    clean_post_cache( $first );
    clean_post_cache( $second );
    $batch = acfb_migration_fresh_batch();
    $checks['manual migration serializes concurrent ' . $mode] = 1 === $result['count'] && ! empty( $peer['errors'] )
        && $batch && in_array( $first, $batch['ids'], true ) && in_array( $second, $batch['ids'], true )
        && get_post_meta( $first, ACF_BLOCKS_MIGRATOR_BACKUP_META, true ) === $first_original
        && get_post_meta( $second, ACF_BLOCKS_MIGRATOR_BACKUP_META, true ) === $second_original
        && get_post_field( 'post_content', $first ) === $first_migrated;
    acf_blocks_migrator_clear_backups();
    wp_delete_post( $first, true );
    wp_delete_post( $second, true );
}
echo wp_json_encode( $checks, JSON_PRETTY_PRINT ) . "\n";
exit( in_array( false, $checks, true ) ? 1 : 0 );
