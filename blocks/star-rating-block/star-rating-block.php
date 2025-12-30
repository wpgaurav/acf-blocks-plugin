<?php
/**
 * Template for the Star Rating block.
 */

if ( ! function_exists( 'acf_star_rating_render_stars' ) ) {
    function acf_star_rating_render_stars( $rating, $max = 5 ) {
        $rating = (float) $rating;
        $output = '';
        for ( $i = 1; $i <= $max; $i++ ) {
            $class = 'star';
            if ( $rating >= $i ) {
                $class .= ' filled';
            } elseif ( $rating >= ( $i - 0.5 ) ) {
                $class .= ' half';
            }
            $output .= '<span class="' . esc_attr( $class ) . '" aria-hidden="true"></span>';
        }
        return $output;
    }
}

$align = $block['align'] ?? '';
$className = $block['className'] ?? '';

$heading      = get_field( 'md_sr_heading' );
$description  = get_field( 'md_sr_description' );
$button_label = get_field( 'md_sr_button_label' ) ?: __( 'Submit Rating', 'acf-blocks' );
$thank_you    = get_field( 'md_sr_thank_you' ) ?: __( 'Thanks for rating!', 'acf-blocks' );

$initial_count  = (int) get_field( 'md_sr_initial_count' ) ?: 0;
$initial_rating = (float) get_field( 'md_sr_initial_rating' ) ?: 0;

$enable_schema = get_field( 'md_sr_enable_schema' );
$schema_type   = get_field( 'md_sr_schema_type' ) ?: 'CreativeWork';
$schema_name   = get_field( 'md_sr_schema_name' );

$anchor = ! empty( $block['anchor'] ) ? $block['anchor'] : 'star-rating-' . str_replace( 'block_', '', $block['id'] );

$class_name = array( 'acf-star-rating' );
if ( $className ) $class_name[] = $className;
if ( $align ) $class_name[] = 'align' . $align;

$post_id = get_the_ID();
$block_id = sanitize_key( $anchor ?: $block['id'] );
$meta_key = '_acf_star_rating_' . $block_id;

$count = 0;
$sum = 0;
if ( $post_id ) {
    $aggregates = get_post_meta( $post_id, $meta_key, true );
    if ( is_array( $aggregates ) ) {
        $count = (int) ( $aggregates['count'] ?? 0 );
        $sum = (float) ( $aggregates['sum'] ?? 0 );
    }
}

$total_count = $count + $initial_count;
$total_sum = $sum + ( $initial_count * $initial_rating );
$average = $total_count > 0 ? $total_sum / $total_count : 0;

$average_display = number_format_i18n( $average, 1 );
$count_display = sprintf( _n( '%s rating', '%s ratings', $total_count, 'acf-blocks' ), number_format_i18n( $total_count ) );

wp_enqueue_script( 'acf-star-rating-block' );

static $acf_star_rating_localized = false;
if ( ! $acf_star_rating_localized ) {
    wp_localize_script( 'acf-star-rating-block', 'acfStarRating', array(
        'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
        'nonce'        => wp_create_nonce( 'acf_star_rating_submit' ),
        'errorMessage' => __( 'Something went wrong. Please try again.', 'acf-blocks' ),
    ) );
    $acf_star_rating_localized = true;
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
        <div class="acf-star-rating__stars"><?php echo acf_star_rating_render_stars( $average ); ?></div>
        <div class="acf-star-rating__average" aria-live="polite"><?php echo esc_html( $average_display ); ?></div>
        <div class="acf-star-rating__count"><?php echo esc_html( $count_display ); ?></div>
    </div>

    <form class="acf-star-rating__form" data-block-id="<?php echo esc_attr( $block_id ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-thank-you="<?php echo esc_attr( $thank_you ); ?>" data-error="<?php echo esc_attr__( 'Select a rating first.', 'acf-blocks' ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'acf_star_rating_submit' ) ); ?>">
        <div class="acf-star-rating__options">
            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
                <label class="acf-star-rating__option">
                    <input type="radio" name="acf_star_rating" value="<?php echo esc_attr( $i ); ?>" aria-label="<?php echo esc_attr( sprintf( _n( '%d star', '%d stars', $i, 'acf-blocks' ), $i ) ); ?>" />
                    <span class="star-icon">&#9733;</span>
                </label>
            <?php endfor; ?>
        </div>
        <button type="submit" class="acf-star-rating__submit"><?php echo esc_html( $button_label ); ?></button>
        <p class="acf-star-rating__thank-you" hidden aria-live="polite"></p>
        <p class="acf-star-rating__error" hidden role="alert"></p>
    </form>

    <?php if ( $enable_schema && $total_count > 0 ) :
        $json_data = [
            '@context' => 'https://schema.org/',
            '@type'    => $schema_type,
            'name'     => $schema_name ?: get_the_title(),
            'url'      => get_permalink(),
            'aggregateRating' => [
                '@type'       => 'AggregateRating',
                'ratingValue' => round( $average, 1 ),
                'bestRating'  => 5,
                'worstRating' => 1,
                'ratingCount' => $total_count
            ]
        ];
        if ( has_excerpt() ) $json_data['description'] = get_the_excerpt();
        if ( has_post_thumbnail() ) $json_data['image'] = get_the_post_thumbnail_url( $post_id, 'full' );
        if ( in_array( $schema_type, [ 'Article', 'BlogPosting', 'CreativeWork' ], true ) ) {
            $json_data['author'] = [ '@type' => 'Person', 'name' => get_the_author() ];
            $json_data['datePublished'] = get_the_date( 'c' );
            $json_data['dateModified'] = get_the_modified_date( 'c' );
        }
    ?>
    <script type="application/ld+json"><?php echo wp_json_encode( $json_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
    <?php endif; ?>
</div>
