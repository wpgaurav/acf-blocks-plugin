<?php
/**
 * Star Rating block helpers.
 */

if ( ! function_exists( 'acf_blocks_star_rating_register_assets' ) ) {
    /**
     * Register star rating block assets.
     */
    function acf_blocks_star_rating_register_assets() {
        $dir = ACF_BLOCKS_PLUGIN_DIR . 'blocks/star-rating-block/';
        $uri = ACF_BLOCKS_PLUGIN_URL . 'blocks/star-rating-block/';

        if ( file_exists( $dir . 'star-rating-block.js' ) ) {
            wp_register_script(
                'acf-blocks-star-rating',
                $uri . 'star-rating-block.js',
                array(),
                filemtime( $dir . 'star-rating-block.js' ),
                true
            );
        }
    }
    add_action( 'wp_enqueue_scripts', 'acf_blocks_star_rating_register_assets' );
}

if ( ! function_exists( 'acf_blocks_star_rating_handle_submission' ) ) {
    /**
     * Handle AJAX star rating submission.
     */
    function acf_blocks_star_rating_handle_submission() {
        check_ajax_referer( 'acf_blocks_star_rating_submit', 'nonce' );

        $post_id  = isset( $_POST['postId'] ) ? absint( wp_unslash( $_POST['postId'] ) ) : 0;
        $block_id = isset( $_POST['blockId'] ) ? sanitize_key( wp_unslash( $_POST['blockId'] ) ) : '';
        $rating   = isset( $_POST['rating'] ) ? floatval( wp_unslash( $_POST['rating'] ) ) : 0;

        if ( $post_id <= 0 || '' === $block_id ) {
            wp_send_json_error(
                array( 'message' => __( 'Missing information for this rating.', 'acf-blocks' ) ),
                400
            );
        }

        if ( $rating < 1 || $rating > 5 ) {
            wp_send_json_error(
                array( 'message' => __( 'Please choose a rating between 1 and 5 stars.', 'acf-blocks' ) ),
                400
            );
        }

        $post = get_post( $post_id );
        if ( ! $post ) {
            wp_send_json_error(
                array( 'message' => __( 'Unable to find the requested content.', 'acf-blocks' ) ),
                404
            );
        }

        $meta_key   = '_acf_blocks_star_rating_' . $block_id;
        $aggregates = get_post_meta( $post_id, $meta_key, true );

        if ( ! is_array( $aggregates ) ) {
            $aggregates = array(
                'count' => 0,
                'sum'   => 0,
            );
        }

        $aggregates['count'] = isset( $aggregates['count'] ) ? (int) $aggregates['count'] + 1 : 1;
        $aggregates['sum']   = isset( $aggregates['sum'] ) ? (float) $aggregates['sum'] + $rating : (float) $rating;

        $average = $aggregates['count'] > 0 ? $aggregates['sum'] / $aggregates['count'] : 0;

        update_post_meta( $post_id, $meta_key, $aggregates );

        $response = array(
            'average'          => round( $average, 2 ),
            'averageFormatted' => number_format_i18n( $average, 1 ),
            'count'            => (int) $aggregates['count'],
            'countText'        => sprintf(
                /* translators: %s: number of ratings */
                _n( '%s rating', '%s ratings', (int) $aggregates['count'], 'acf-blocks' ),
                number_format_i18n( (int) $aggregates['count'] )
            ),
        );

        wp_send_json_success( $response );
    }
}
add_action( 'wp_ajax_acf_blocks_star_rating_submit', 'acf_blocks_star_rating_handle_submission' );
add_action( 'wp_ajax_nopriv_acf_blocks_star_rating_submit', 'acf_blocks_star_rating_handle_submission' );
