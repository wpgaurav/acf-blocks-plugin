<?php
/**
 * Testimonial Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$quote       = get_field( 'acf_testimonial_quote' );
$author_name = get_field( 'acf_testimonial_author_name' );
$author_title = get_field( 'acf_testimonial_author_title' );
$author_image = get_field( 'acf_testimonial_author_image' );
$rating      = get_field( 'acf_testimonial_rating' );

$custom_class = get_field( 'acf_testimonial_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_testimonial_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';
?>

<div class="acf-testimonial-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $quote ) : ?>
        <blockquote class="acf-testimonial-quote">
            <span class="acf-testimonial-quote-icon">&ldquo;</span>
            <?php echo wpautop( esc_html( $quote ) ); ?>
        </blockquote>
    <?php endif; ?>

    <div class="acf-testimonial-author">
        <?php if ( $author_image ) : ?>
            <div class="acf-testimonial-author-image">
                <img src="<?php echo esc_url( $author_image['url'] ); ?>" alt="<?php echo esc_attr( $author_image['alt'] ); ?>" loading="lazy" decoding="async" />
            </div>
        <?php endif; ?>

        <div class="acf-testimonial-author-details">
            <?php if ( $author_name ) : ?>
                <div class="acf-testimonial-author-name"><?php echo esc_html( $author_name ); ?></div>
            <?php endif; ?>

            <?php if ( $author_title ) : ?>
                <div class="acf-testimonial-author-title"><?php echo esc_html( $author_title ); ?></div>
            <?php endif; ?>

            <?php if ( $rating && $rating > 0 ) : ?>
                <div class="acf-testimonial-rating">
                    <?php
                    for ( $i = 1; $i <= 5; $i++ ) {
                        echo $i <= $rating ? '<span class="acf-testimonial-star acf-testimonial-star-filled">★</span>' : '<span class="acf-testimonial-star">☆</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
