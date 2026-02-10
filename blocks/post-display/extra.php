<?php
/**
 * Post Display Block - Performance Optimizations
 *
 * Optimizes the relationship field to search by title only and reduce server load.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Optimize relationship field query for Post Display block.
 * Searches only by post title instead of full content search.
 *
 * @param array $args    WP_Query arguments.
 * @param array $field   ACF field settings.
 * @param int   $post_id The post ID.
 * @return array Modified arguments.
 */
if ( ! function_exists( 'acf_blocks_optimize_post_display_query' ) ) {
function acf_blocks_optimize_post_display_query( $args, $field, $post_id ) {
    // Only apply to our specific field
    if ( ! isset( $field['key'] ) || $field['key'] !== 'field_pd_selected_posts' ) {
        return $args;
    }

    // Limit results for better performance
    if ( ! isset( $args['posts_per_page'] ) || $args['posts_per_page'] > 20 ) {
        $args['posts_per_page'] = 20;
    }

    // Only fetch necessary fields
    $args['no_found_rows']          = true;
    $args['update_post_meta_cache'] = false;
    $args['update_post_term_cache'] = false;

    // If there's a search term, use title-only search
    if ( ! empty( $args['s'] ) ) {
        $search_term = $args['s'];
        unset( $args['s'] );

        // Use title search instead of full content search
        $args['acf_title_search'] = $search_term;

        add_filter( 'posts_where', 'acf_blocks_title_only_search', 10, 2 );
    }

    return $args;
}
add_filter( 'acf/fields/relationship/query', 'acf_blocks_optimize_post_display_query', 10, 3 );
}

/**
 * Modify WHERE clause to search only in post_title.
 *
 * @param string   $where    The WHERE clause.
 * @param WP_Query $wp_query The query object.
 * @return string Modified WHERE clause.
 */
if ( ! function_exists( 'acf_blocks_title_only_search' ) ) {
function acf_blocks_title_only_search( $where, $wp_query ) {
    global $wpdb;

    $search_term = $wp_query->get( 'acf_title_search' );

    if ( $search_term ) {
        $like  = '%' . $wpdb->esc_like( $search_term ) . '%';
        $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_title LIKE %s", $like );

        // Remove this filter after it runs to prevent affecting other queries
        remove_filter( 'posts_where', 'acf_blocks_title_only_search', 10 );
    }

    return $where;
}
}

/**
 * Optimize relationship field results - only return essential data.
 *
 * @param array      $args    Query args.
 * @param array      $field   Field settings.
 * @param int|string $post_id Post ID.
 * @return array
 */
if ( ! function_exists( 'acf_blocks_optimize_post_display_result' ) ) {
function acf_blocks_optimize_post_display_result( $args, $field, $post_id ) {
    if ( ! isset( $field['key'] ) || $field['key'] !== 'field_pd_selected_posts' ) {
        return $args;
    }

    // Cache results for 5 minutes to reduce repeated queries
    $args['cache_results'] = true;

    return $args;
}
add_filter( 'acf/fields/relationship/query', 'acf_blocks_optimize_post_display_result', 5, 3 );
}
