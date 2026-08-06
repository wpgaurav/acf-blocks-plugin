<?php
/**
 * Star Rating storage, REST API, and assets.
 *
 * Uses atomic database writes instead of per-voter transients and post-meta
 * read/modify/write cycles. This prevents lost votes and avoids unbounded
 * transient rows on sites without a persistent object cache.
 */

defined( 'ABSPATH' ) || exit;

const ACF_STAR_RATING_SCHEMA_VERSION = '1';

/**
 * Return rating table names.
 *
 * @return array{votes:string,totals:string}
 */
function acf_star_rating_tables() {
    global $wpdb;
    return array(
        'votes'  => $wpdb->prefix . 'acf_block_rating_votes',
        'totals' => $wpdb->prefix . 'acf_block_rating_totals',
    );
}

/**
 * Install or update rating tables.
 */
function acf_star_rating_install_tables() {
    global $wpdb;

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $tables  = acf_star_rating_tables();
    $charset = $wpdb->get_charset_collate();

    dbDelta( "CREATE TABLE {$tables['votes']} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        post_id bigint(20) unsigned NOT NULL,
        block_id varchar(191) NOT NULL,
        voter_hash char(64) NOT NULL,
        vote_date date NOT NULL,
        rating decimal(3,2) NOT NULL,
        created_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY voter_day (post_id, block_id(100), voter_hash, vote_date),
        KEY post_block (post_id, block_id(100))
    ) {$charset};" );

    dbDelta( "CREATE TABLE {$tables['totals']} (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        post_id bigint(20) unsigned NOT NULL,
        block_id varchar(191) NOT NULL,
        rating_count bigint(20) unsigned NOT NULL DEFAULT 0,
        rating_sum decimal(20,4) NOT NULL DEFAULT 0,
        updated_at datetime NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY post_block (post_id, block_id(150))
    ) {$charset};" );

    update_option( 'acf_star_rating_schema_version', ACF_STAR_RATING_SCHEMA_VERSION, false );
}

/**
 * Ensure upgraded sites receive the rating tables without reactivation.
 */
function acf_star_rating_maybe_install_tables() {
    if ( ACF_STAR_RATING_SCHEMA_VERSION !== get_option( 'acf_star_rating_schema_version' ) ) {
        acf_star_rating_install_tables();
    }
}
add_action( 'init', 'acf_star_rating_maybe_install_tables', 1 );

/**
 * Register the frontend script.
 */
function acf_star_rating_register_assets() {
    $dir = ACF_BLOCKS_PLUGIN_DIR . 'blocks/star-rating-block/';
    if ( file_exists( $dir . 'star-rating-block.js' ) ) {
        $asset = acf_blocks_asset( 'blocks/star-rating-block/star-rating-block.js' );
        wp_register_script(
            'acf-star-rating-block',
            $asset['url'],
            array(),
            ACF_BLOCKS_VERSION,
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'acf_star_rating_register_assets' );

/**
 * Register the public rating endpoint.
 */
function acf_star_rating_register_rest_route() {
    register_rest_route( 'acf-blocks/v1', '/ratings', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => 'acf_star_rating_rest_submit',
        'permission_callback' => '__return_true',
        'args'                => array(
            'postId'  => array( 'required' => true, 'sanitize_callback' => 'absint' ),
            'blockId' => array( 'required' => true, 'sanitize_callback' => 'sanitize_key' ),
            'rating'  => array( 'required' => true, 'sanitize_callback' => 'floatval' ),
            'token'   => array( 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ),
            'initialCount'  => array( 'required' => false, 'default' => 0, 'sanitize_callback' => 'absint' ),
            'initialRating' => array( 'required' => false, 'default' => 0, 'sanitize_callback' => 'floatval' ),
        ),
    ) );
}
add_action( 'rest_api_init', 'acf_star_rating_register_rest_route' );

/**
 * Create a cache-safe public submission token for one rendered block.
 *
 * @param int    $post_id  Post ID.
 * @param string $block_id Block ID.
 * @return string
 */
function acf_star_rating_submission_token( $post_id, $block_id ) {
    return hash_hmac( 'sha256', absint( $post_id ) . '|' . sanitize_key( $block_id ), wp_salt( 'auth' ) );
}

/**
 * Confirm that a legacy tokenless request references a real rating block.
 *
 * @param array  $blocks   Parsed post blocks.
 * @param string $block_id Submitted block ID.
 * @return bool
 */
function acf_star_rating_has_block_id( $blocks, $block_id ) {
    foreach ( $blocks as $block ) {
        if ( 'acf/star-rating' === ( $block['blockName'] ?? '' ) ) {
            $attrs     = (array) ( $block['attrs'] ?? array() );
            $candidate = ! empty( $attrs['anchor'] )
                ? $attrs['anchor']
                : 'star-rating-' . str_replace( 'block_', '', (string) ( $attrs['id'] ?? '' ) );
            if ( sanitize_key( $candidate ) === $block_id ) {
                return true;
            }
        }
        if ( ! empty( $block['innerBlocks'] ) && acf_star_rating_has_block_id( $block['innerBlocks'], $block_id ) ) {
            return true;
        }
    }
    return false;
}

/**
 * Produce a privacy-preserving daily voter hash.
 *
 * @return string
 */
function acf_star_rating_voter_hash() {
    $ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';

    // Cloudflare overwrites this header at the trusted edge. Other proxy
    // headers are intentionally ignored unless a site opts in via the filter.
    $trust_cloudflare = (bool) apply_filters(
        'acf_star_rating_trust_cloudflare_header',
        ! empty( $_SERVER['HTTP_CF_RAY'] )
    );
    if ( $trust_cloudflare && ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        $candidate = sanitize_text_field( wp_unslash( $_SERVER['HTTP_CF_CONNECTING_IP'] ) );
        if ( filter_var( $candidate, FILTER_VALIDATE_IP ) ) {
            $ip = $candidate;
        }
    }

    $ip = apply_filters( 'acf_star_rating_client_ip', $ip );
    return hash_hmac( 'sha256', $ip . '|' . gmdate( 'Y-m-d' ), wp_salt( 'nonce' ) );
}

/**
 * Seed the totals table from legacy post meta when necessary.
 *
 * @param int    $post_id  Post ID.
 * @param string $block_id Block ID.
 */
function acf_star_rating_seed_legacy_total( $post_id, $block_id ) {
    global $wpdb;
    $tables = acf_star_rating_tables();
    $exists = $wpdb->get_var( $wpdb->prepare(
        "SELECT id FROM {$tables['totals']} WHERE post_id = %d AND block_id = %s LIMIT 1",
        $post_id,
        $block_id
    ) );
    if ( $exists ) {
        return;
    }

    $legacy = get_post_meta( $post_id, '_acf_star_rating_' . $block_id, true );
    if ( ! is_array( $legacy ) || empty( $legacy['count'] ) ) {
        return;
    }

    $wpdb->insert( $tables['totals'], array(
        'post_id'      => $post_id,
        'block_id'     => $block_id,
        'rating_count' => (int) $legacy['count'],
        'rating_sum'   => (float) ( $legacy['sum'] ?? 0 ),
        'updated_at'   => current_time( 'mysql', true ),
    ), array( '%d', '%s', '%d', '%f', '%s' ) );
}

/**
 * Insert one vote and atomically increment its aggregate.
 *
 * @param int    $post_id  Post ID.
 * @param string $block_id Block ID.
 * @param float  $rating   Rating.
 * @param string $token    Optional cache-safe public submission token.
 * @return array|WP_Error
 */
function acf_star_rating_submit_vote( $post_id, $block_id, $rating, $token = '' ) {
    global $wpdb;

    $post_id  = absint( $post_id );
    $block_id = sanitize_key( $block_id );
    $rating   = (float) $rating;
    $post     = get_post( $post_id );

    if ( ! $post_id || ! $block_id || ! $post ) {
        return new WP_Error( 'acf_rating_missing', __( 'Content was not found.', 'acf-blocks' ), array( 'status' => 404 ) );
    }
    if ( $rating < 1 || $rating > 5 ) {
        return new WP_Error( 'acf_rating_invalid', __( 'Rating must be between 1 and 5.', 'acf-blocks' ), array( 'status' => 400 ) );
    }

    $expected_token = acf_star_rating_submission_token( $post_id, $block_id );
    if ( '' !== $token ) {
        if ( ! hash_equals( $expected_token, (string) $token ) ) {
            return new WP_Error( 'acf_rating_token', __( 'The rating request could not be verified.', 'acf-blocks' ), array( 'status' => 403 ) );
        }
    } elseif ( ! acf_star_rating_has_block_id( parse_blocks( $post->post_content ), $block_id ) ) {
        return new WP_Error( 'acf_rating_block', __( 'The rating block could not be verified.', 'acf-blocks' ), array( 'status' => 403 ) );
    }

    acf_star_rating_seed_legacy_total( $post_id, $block_id );
    $tables = acf_star_rating_tables();
    $voter_hash = acf_star_rating_voter_hash();
    $vote_date  = gmdate( 'Y-m-d' );
    $inserted = $wpdb->query( $wpdb->prepare(
        "INSERT IGNORE INTO {$tables['votes']}
            (post_id, block_id, voter_hash, vote_date, rating, created_at)
         VALUES (%d, %s, %s, %s, %f, %s)",
        $post_id,
        $block_id,
        $voter_hash,
        $vote_date,
        $rating,
        current_time( 'mysql', true )
    ) );

    if ( false === $inserted ) {
        return new WP_Error( 'acf_rating_storage', __( 'The rating could not be saved.', 'acf-blocks' ), array( 'status' => 500 ) );
    }
    if ( 0 === $inserted ) {
        return new WP_Error( 'acf_rating_duplicate', __( 'You have already submitted a rating today.', 'acf-blocks' ), array( 'status' => 429 ) );
    }

    $now = current_time( 'mysql', true );
    $updated = $wpdb->query( $wpdb->prepare(
        "INSERT INTO {$tables['totals']}
            (post_id, block_id, rating_count, rating_sum, updated_at)
         VALUES (%d, %s, 1, %f, %s)
         ON DUPLICATE KEY UPDATE
            rating_count = rating_count + 1,
            rating_sum = rating_sum + VALUES(rating_sum),
            updated_at = VALUES(updated_at)",
        $post_id,
        $block_id,
        $rating,
        $now
    ) );

    if ( false === $updated ) {
        $wpdb->delete( $tables['votes'], array(
            'post_id'    => $post_id,
            'block_id'   => $block_id,
            'voter_hash' => $voter_hash,
            'vote_date'  => $vote_date,
        ), array( '%d', '%s', '%s', '%s' ) );
        return new WP_Error( 'acf_rating_storage', __( 'The rating could not be saved.', 'acf-blocks' ), array( 'status' => 500 ) );
    }

    return acf_star_rating_get_aggregate( $post_id, $block_id, true );
}

/**
 * Get one block's aggregate.
 *
 * @param int    $post_id     Post ID.
 * @param string $block_id    Block ID.
 * @param bool   $force_fresh Skip request cache.
 * @return array{count:int,sum:float,average:float}
 */
function acf_star_rating_get_aggregate( $post_id, $block_id, $force_fresh = false ) {
    global $wpdb;
    static $cache        = array();
    static $loaded_posts = array();

    $cache_key = $post_id . ':' . $block_id;
    if ( ! $force_fresh && isset( $cache[ $cache_key ] ) ) {
        return $cache[ $cache_key ];
    }

    $tables = acf_star_rating_tables();
    $row = null;

    if ( ! $force_fresh && empty( $loaded_posts[ $post_id ] ) ) {
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT block_id, rating_count, rating_sum FROM {$tables['totals']} WHERE post_id = %d",
            $post_id
        ), ARRAY_A );
        foreach ( (array) $rows as $aggregate_row ) {
            $count = (int) $aggregate_row['rating_count'];
            $sum   = (float) $aggregate_row['rating_sum'];
            $cache[ $post_id . ':' . $aggregate_row['block_id'] ] = array(
                'count'   => $count,
                'sum'     => $sum,
                'average' => $count > 0 ? $sum / $count : 0,
            );
        }
        $loaded_posts[ $post_id ] = true;
        if ( isset( $cache[ $cache_key ] ) ) {
            return $cache[ $cache_key ];
        }
    } elseif ( $force_fresh ) {
        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT rating_count, rating_sum FROM {$tables['totals']} WHERE post_id = %d AND block_id = %s LIMIT 1",
            $post_id,
            $block_id
        ), ARRAY_A );
    }

    if ( ! $row ) {
        $legacy = get_post_meta( $post_id, '_acf_star_rating_' . $block_id, true );
        $row = is_array( $legacy ) ? array(
            'rating_count' => (int) ( $legacy['count'] ?? 0 ),
            'rating_sum'   => (float) ( $legacy['sum'] ?? 0 ),
        ) : array( 'rating_count' => 0, 'rating_sum' => 0 );
    }

    $count = (int) $row['rating_count'];
    $sum   = (float) $row['rating_sum'];
    $cache[ $cache_key ] = array(
        'count'   => $count,
        'sum'     => $sum,
        'average' => $count > 0 ? $sum / $count : 0,
    );
    return $cache[ $cache_key ];
}

/**
 * Format an aggregate for frontend clients.
 *
 * @param array $aggregate     Aggregate data.
 * @param int   $initial_count Configured seed count.
 * @param float $initial_rating Configured seed average.
 * @return array
 */
function acf_star_rating_response_data( $aggregate, $initial_count = 0, $initial_rating = 0 ) {
    $count   = (int) $aggregate['count'] + absint( $initial_count );
    $sum     = (float) $aggregate['sum'] + ( absint( $initial_count ) * max( 0, min( 5, (float) $initial_rating ) ) );
    $average = $count > 0 ? $sum / $count : 0;
    return array(
        'average'          => round( $average, 2 ),
        'averageFormatted' => number_format_i18n( $average, 1 ),
        'count'            => $count,
        'sum'              => $sum,
        'countText'        => sprintf( _n( '%s rating', '%s ratings', $count, 'acf-blocks' ), number_format_i18n( $count ) ),
    );
}

/**
 * REST submission callback.
 *
 * @param WP_REST_Request $request Request.
 * @return WP_REST_Response|WP_Error
 */
function acf_star_rating_rest_submit( $request ) {
    $result = acf_star_rating_submit_vote(
        $request->get_param( 'postId' ),
        $request->get_param( 'blockId' ),
        $request->get_param( 'rating' ),
        $request->get_param( 'token' )
    );
    return is_wp_error( $result ) ? $result : rest_ensure_response( acf_star_rating_response_data(
        $result,
        $request->get_param( 'initialCount' ),
        $request->get_param( 'initialRating' )
    ) );
}

/**
 * Backward-compatible admin-ajax endpoint for cached older scripts.
 */
function acf_star_rating_handle_submission() {
    $result = acf_star_rating_submit_vote(
        isset( $_POST['postId'] ) ? absint( $_POST['postId'] ) : 0,
        isset( $_POST['blockId'] ) ? sanitize_key( wp_unslash( $_POST['blockId'] ) ) : '',
        isset( $_POST['rating'] ) ? (float) $_POST['rating'] : 0
    );
    if ( is_wp_error( $result ) ) {
        $data = $result->get_error_data();
        wp_send_json_error( array( 'message' => $result->get_error_message() ), (int) ( $data['status'] ?? 400 ) );
    }
    wp_send_json_success( acf_star_rating_response_data( $result ) );
}
add_action( 'wp_ajax_acf_star_rating_submit', 'acf_star_rating_handle_submission' );
add_action( 'wp_ajax_nopriv_acf_star_rating_submit', 'acf_star_rating_handle_submission' );
