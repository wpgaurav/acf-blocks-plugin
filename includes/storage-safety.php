<?php
/**
 * Database coordination for bounded background jobs.
 *
 * @package ACF_Blocks
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/** Acquire a connection-scoped lock, released automatically if PHP exits. */
function acf_blocks_storage_lock( $resource, $wait = 2 ) {
    global $wpdb;
    $name = 'acfb:' . md5( $wpdb->options . ':' . $resource );
    return '1' === (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, %d)', $name, $wait ) ) ? $name : false;
}

/** Release only the lock held by this database connection. */
function acf_blocks_storage_unlock( $name ) {
    global $wpdb;
    if ( $name ) {
        $wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $name ) );
    }
}

/** Read an option from the database, avoiding another worker's stale cache. */
function acf_blocks_storage_read_option( $name, $default = false ) {
    global $wpdb;
    $row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM {$wpdb->options} WHERE option_name = %s", $name ) );
    return $row ? maybe_unserialize( $row->option_value ) : $default;
}

/**
 * Track our content transaction and its post-commit work within this request.
 * Deferred work is discarded on rollback and never runs under the row lock.
 */
function acf_blocks_storage_content_transaction( $operation = 'active', $callback = null ) {
    static $active = false;
    static $deferred = array();
    if ( 'begin' === $operation ) {
        $active = true;
        $deferred = array();
    } elseif ( 'defer' === $operation ) {
        if ( ! $active ) {
            return false;
        }
        $deferred[] = $callback;
        return true;
    } elseif ( 'commit' === $operation || 'rollback' === $operation ) {
        $pending = $deferred;
        $deferred = array();
        $active = false;
        if ( 'commit' === $operation ) {
            foreach ( $pending as $work ) {
                call_user_func( $work );
            }
        }
    }
    return $active;
}

/** Probe without committing or changing an active caller-owned transaction. */
function acf_blocks_storage_transaction_available() {
    global $wpdb;
    $suppressed = $wpdb->suppress_errors( true );
    // Both MySQL and MariaDB reject next-transaction characteristics during
    // an active transaction (error 1568); unlike START this cannot commit it.
    $available = $wpdb->query( 'SET TRANSACTION READ WRITE' );
    $wpdb->suppress_errors( $suppressed );
    return false !== $available;
}

/** Serialize short read/modify/write operations without expiring leases. */
function acf_blocks_storage_mutate_option( $name, $callback, $default = array() ) {
    // Advisory locks cannot protect queue versions still hidden by a caller's
    // transaction. Our save hooks defer their enqueue until the content commits.
    if ( ! acf_blocks_storage_transaction_available() ) {
        return new WP_Error( 'acf_blocks_storage_transaction', __( 'Job updates must wait until the current transaction commits.', 'acf-blocks' ) );
    }
    $lock = acf_blocks_storage_lock( 'option:' . $name );
    if ( ! $lock ) {
        return new WP_Error( 'acf_blocks_storage_busy', __( 'Another worker is updating this job. Please retry.', 'acf-blocks' ) );
    }
    try {
        $value = call_user_func( $callback, acf_blocks_storage_read_option( $name, $default ) );
        if ( is_wp_error( $value ) ) {
            return $value;
        }
        // update_option() compares against its cache, which can predate our lock.
        wp_cache_delete( $name, 'options' );
        wp_cache_delete( 'alloptions', 'options' );
        wp_cache_delete( 'notoptions', 'options' );
        update_option( $name, $value, false );
        if ( acf_blocks_storage_read_option( $name, $default ) !== $value ) {
            return new WP_Error( 'acf_blocks_storage_failed', __( 'Could not persist the background job state.', 'acf-blocks' ) );
        }
        return $value;
    } finally {
        acf_blocks_storage_unlock( $lock );
    }
}

/**
 * Commit transformed content only while the source row is still unchanged.
 *
 * WordPress performs the write and all normal revisions/cache/hooks. The
 * InnoDB row lock prevents an editor save between comparison and that write.
 * Nontransactional tables fail closed instead of risking an editor's changes.
 * Never perform remote I/O while holding this lock.
 *
 * @param WP_Post $snapshot Original post read before transforming content.
 * @param string  $content  New, unslashed content.
 * @return int|WP_Error
 */
function acf_blocks_storage_update_post( $snapshot, $content ) {
    global $wpdb;
    $engine = $wpdb->get_var( $wpdb->prepare(
        'SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = %s',
        $wpdb->posts
    ) );
    if ( 'innodb' !== strtolower( (string) $engine ) ) {
        return new WP_Error( 'acf_blocks_storage_engine', __( 'Safe background content updates require an InnoDB posts table.', 'acf-blocks' ) );
    }
    // Do not implicitly commit a transaction owned by another integration.
    if ( ! acf_blocks_storage_transaction_available() ) {
        return new WP_Error( 'acf_blocks_storage_transaction', __( 'An existing database transaction prevents this background update.', 'acf-blocks' ) );
    }
    if ( false === $wpdb->query( 'START TRANSACTION' ) ) {
        return new WP_Error( 'acf_blocks_storage_transaction', __( 'Could not start a safe content update.', 'acf-blocks' ) );
    }
    $committed = false;
    acf_blocks_storage_content_transaction( 'begin' );
    try {
        $current = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE ID = %d FOR UPDATE", $snapshot->ID ) );
        if ( ! $current || $current->post_content !== $snapshot->post_content
            || $current->post_modified !== $snapshot->post_modified
            || $current->post_modified_gmt !== $snapshot->post_modified_gmt ) {
            return new WP_Error( 'acf_blocks_content_changed', __( 'The post changed during processing. Localization will retry with its latest content.', 'acf-blocks' ) );
        }
        // The initial get_post() object may have been cached before a status or
        // author edit. Refresh it under the row lock before WP merges its data.
        clean_post_cache( $snapshot->ID );
        $result = wp_update_post( array( 'ID' => $snapshot->ID, 'post_content' => wp_slash( $content ) ), true );
        if ( is_wp_error( $result ) || ! $result ) {
            return is_wp_error( $result ) ? $result : new WP_Error( 'acf_blocks_update_failed', __( 'Could not update the post.', 'acf-blocks' ) );
        }
        if ( false === $wpdb->query( 'COMMIT' ) ) {
            return new WP_Error( 'acf_blocks_storage_transaction', __( 'Could not commit the content update.', 'acf-blocks' ) );
        }
        $committed = true;
        // A separate connection can refill a persistent object cache with
        // the old committed row while WP's hooks run before our COMMIT.
        clean_post_cache( $snapshot->ID );
        acf_blocks_storage_content_transaction( 'commit' );
        return $result;
    } finally {
        if ( ! $committed ) {
            $wpdb->query( 'ROLLBACK' );
            acf_blocks_storage_content_transaction( 'rollback' );
            clean_post_cache( $snapshot->ID );
        }
    }
}
