<?php
/**
 * Testimonial Block Template.
 *
 * Uses core blocks via InnerBlocks for the quote content.
 * Falls back to legacy ACF fields for backward compatibility.
 * ACF fields are retained for author info, image, rating, and styling.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$author_name = acf_blocks_get_field( 'acf_testimonial_author_name', $block );
$author_title = acf_blocks_get_field( 'acf_testimonial_author_title', $block );
$author_image = acf_blocks_get_field( 'acf_testimonial_author_image', $block );
$author_image_url = acf_blocks_get_field( 'acf_testimonial_author_image_url', $block );
$rating      = acf_blocks_get_field( 'acf_testimonial_rating', $block );

// Determine image source - direct URL takes priority
$img_src = '';
$img_alt = $author_name ?: 'Author';
if ( $author_image_url ) {
    $img_src = $author_image_url;
} elseif ( $author_image ) {
    $resolved = acf_blocks_resolve_image( $author_image, $img_alt, 'thumbnail' );
    $img_src = $resolved['src'];
    $img_alt = $resolved['alt'];
}

$custom_class = acf_blocks_get_field( 'acf_testimonial_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = acf_blocks_get_field( 'acf_testimonial_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$unique_id = 'acf-testimonial-' . ( $block['id'] ?? uniqid() );

// Check for legacy ACF field content (backward compatibility)
$legacy_quote = acf_blocks_get_field( 'acf_testimonial_quote', $block );
$has_legacy_content = ! empty( $legacy_quote );

$inner_blocks_template = [
    [ 'core/paragraph', [ 'placeholder' => 'Write testimonial quote here...' ] ]
];
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="acf-testimonial-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <blockquote class="acf-testimonial-quote">
        <span class="acf-testimonial-quote-icon">&ldquo;</span>
        <?php if ( $has_legacy_content && empty( trim( $content ) ) ) : ?>
            <?php echo wp_kses_post( $legacy_quote ); ?>
        <?php else : ?>
            <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $inner_blocks_template ) ); ?>" templateLock="false" />
        <?php endif; ?>
    </blockquote>

    <div class="acf-testimonial-author">
        <?php if ( $img_src ) : ?>
            <div class="acf-testimonial-author-image">
                <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" decoding="async" />
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
