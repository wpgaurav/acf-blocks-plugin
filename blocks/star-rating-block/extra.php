<?php
/**
 * Star Rating block helpers.
 */

if ( ! function_exists( 'acf_star_rating_register_assets' ) ) {
    function acf_star_rating_register_assets() {
        $dir = ACF_BLOCKS_PLUGIN_DIR . 'blocks/star-rating-block/';
        $url = ACF_BLOCKS_PLUGIN_URL . 'blocks/star-rating-block/';
        if ( file_exists( $dir . 'star-rating-block.js' ) ) {
            wp_register_script(
                'acf-star-rating-block',
                $url . 'star-rating-block.js',
                array(),
                filemtime( $dir . 'star-rating-block.js' ),
                true
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'acf_star_rating_register_assets' );
}

if ( ! function_exists( 'acf_star_rating_handle_submission' ) ) {
    function acf_star_rating_handle_submission() {
        check_ajax_referer( 'acf_star_rating_submit', 'nonce' );

        $post_id  = isset( $_POST['postId'] ) ? absint( $_POST['postId'] ) : 0;
        $block_id = isset( $_POST['blockId'] ) ? sanitize_key( $_POST['blockId'] ) : '';
        $rating   = isset( $_POST['rating'] ) ? floatval( $_POST['rating'] ) : 0;

        if ( $post_id <= 0 || '' === $block_id ) {
            wp_send_json_error( array( 'message' => __( 'Missing information.', 'acf-blocks' ) ), 400 );
        }

        if ( $rating < 1 || $rating > 5 ) {
            wp_send_json_error( array( 'message' => __( 'Rating must be between 1 and 5.', 'acf-blocks' ) ), 400 );
        }

        if ( ! get_post( $post_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Content not found.', 'acf-blocks' ) ), 404 );
        }

        $meta_key   = '_acf_star_rating_' . $block_id;
        $aggregates = get_post_meta( $post_id, $meta_key, true );

        if ( ! is_array( $aggregates ) ) {
            $aggregates = array( 'count' => 0, 'sum' => 0 );
        }

        $aggregates['count'] = (int) ( $aggregates['count'] ?? 0 ) + 1;
        $aggregates['sum']   = (float) ( $aggregates['sum'] ?? 0 ) + $rating;
        $average = $aggregates['sum'] / $aggregates['count'];

        update_post_meta( $post_id, $meta_key, $aggregates );

        wp_send_json_success( array(
            'average'          => round( $average, 2 ),
            'averageFormatted' => number_format_i18n( $average, 1 ),
            'count'            => $aggregates['count'],
            'countText'        => sprintf( _n( '%s rating', '%s ratings', $aggregates['count'], 'acf-blocks' ), number_format_i18n( $aggregates['count'] ) ),
        ) );
    }
}
add_action( 'wp_ajax_acf_star_rating_submit', 'acf_star_rating_handle_submission' );
add_action( 'wp_ajax_nopriv_acf_star_rating_submit', 'acf_star_rating_handle_submission' );
