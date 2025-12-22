<?php
/**
 * Template for the Star Rating block.
 */

if ( ! function_exists( 'md_star_rating_render_average_stars' ) ) {
    /**
     * Render a set of star elements for the provided average.
     *
     * @param float $rating Average rating.
     * @param int   $max    Number of stars.
     * @return string
     */
    function md_star_rating_render_average_stars( $rating, $max = 5 ) {
        $rating = (float) $rating;
        $max    = (int) $max;

        if ( $max <= 0 ) {
            return '';
        }

        $output = '';

        for ( $i = 1; $i <= $max; $i++ ) {
            $classes = array( 'star' );

            if ( $rating >= $i ) {
                $classes[] = 'filled';
            } elseif ( $rating >= ( $i - 0.5 ) ) {
                $classes[] = 'half';
            }

            $output .= sprintf( '<span class="%s" aria-hidden="true"></span>', esc_attr( implode( ' ', $classes ) ) );
        }

        return $output;
    }
}

$heading      = get_field( 'md_sr_heading' );
$description  = get_field( 'md_sr_description' );
$button_label = get_field( 'md_sr_button_label' );
$thank_you    = get_field( 'md_sr_thank_you' );

$button_label = $button_label ? $button_label : __( 'Submit Rating', 'acf-blocks' );
$thank_you    = $thank_you ? $thank_you : __( 'Thanks for rating!', 'acf-blocks' );

$anchor = ! empty( $block['anchor'] ) ? $block['anchor'] : '';
if ( '' === $anchor ) {
    $anchor = 'star-rating-' . str_replace( 'block_', '', $block['id'] );
}

$class_name = array( 'acf-star-rating' );

if ( ! empty( $block['className'] ) ) {
    $class_name[] = $block['className'];
}

if ( ! empty( $block['align'] ) ) {
    $class_name[] = 'align' . $block['align'];
}

$post_id = get_the_ID();
$block_id = sanitize_key( $anchor ? $anchor : $block['id'] );
$meta_key = '_md_star_rating_' . $block_id;

$count   = 0;
$sum     = 0;
$average = 0;

if ( $post_id ) {
    $aggregates = get_post_meta( $post_id, $meta_key, true );
    if ( is_array( $aggregates ) ) {
        $count   = isset( $aggregates['count'] ) ? (int) $aggregates['count'] : 0;
        $sum     = isset( $aggregates['sum'] ) ? (float) $aggregates['sum'] : 0;
        $average = $count > 0 ? $sum / $count : 0;
    }
}

$average_display = number_format_i18n( $average, 1 );
$count_display   = sprintf(
    _n( '%s rating', '%s ratings', $count, 'acf-blocks' ),
    number_format_i18n( $count )
);

wp_enqueue_script( 'md-star-rating-block' );

static $md_star_rating_localized = false;

if ( ! $md_star_rating_localized ) {
    wp_localize_script(
        'md-star-rating-block',
        'mdStarRating',
        array(
            'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'md_star_rating_submit' ),
            'errorMessage' => __( 'Something went wrong. Please try again later.', 'acf-blocks' ),
        )
    );

    $md_star_rating_localized = true;
}
?>
<div id="<?php echo esc_attr( $anchor ); ?>" class="<?php echo esc_attr( implode( ' ', $class_name ) ); ?>">
    <?php if ( $heading ) : ?>
        <h3 class="acf-star-rating__heading"><?php echo esc_html( $heading ); ?></h3>
    <?php endif; ?>

    <?php if ( $description ) : ?>
        <p class="acf-star-rating__description"><?php echo esc_html( $description ); ?></p>
    <?php endif; ?>

    <div class="acf-star-rating__aggregate">
        <div class="acf-star-rating__stars">
            <?php echo md_star_rating_render_average_stars( $average ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <div class="acf-star-rating__average" aria-live="polite"><?php echo esc_html( $average_display ); ?></div>
        <div class="acf-star-rating__count"><?php echo esc_html( $count_display ); ?></div>
    </div>

    <form class="acf-star-rating__form" data-block-id="<?php echo esc_attr( $block_id ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-thank-you="<?php echo esc_attr( $thank_you ); ?>" data-error="<?php echo esc_attr__( 'Select a star rating before submitting.', 'acf-blocks' ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'md_star_rating_submit' ) ); ?>">
        <div class="acf-star-rating__options">
            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                <label class="acf-star-rating__option">
                    <input type="radio" name="md_star_rating" value="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( _n( '%d star', '%d stars', $i, 'acf-blocks' ), $i ) ); ?>" />
                    <span class="star-icon">&#9733;</span>
                    <span class="screen-reader-text"><?php echo esc_html( sprintf( _n( '%d star', '%d stars', $i, 'acf-blocks' ), $i ) ); ?></span>
                </label>
            <?php endfor; ?>
        </div>
        <button type="submit" class="acf-star-rating__submit"><?php echo esc_html( $button_label ); ?></button>
        <p class="acf-star-rating__thank-you" hidden aria-live="polite"></p>
        <p class="acf-star-rating__error" hidden role="alert"></p>
    </form>
</div>
