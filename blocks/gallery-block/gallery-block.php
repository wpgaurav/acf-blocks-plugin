<?php
/**
 * Gallery Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$images      = acf_blocks_get_field( 'acf_gallery_images', $block );
$layout      = acf_blocks_get_field( 'acf_gallery_layout', $block );
$columns     = acf_blocks_get_field( 'acf_gallery_columns', $block );
$gap         = acf_blocks_get_field( 'acf_gallery_gap', $block );
$lightbox    = acf_blocks_get_field( 'acf_gallery_enable_lightbox', $block );

$custom_class = acf_blocks_get_field( 'acf_gallery_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = acf_blocks_get_field( 'acf_gallery_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$layout_class = $layout ? ' acf-gallery-' . esc_attr( $layout ) : ' acf-gallery-grid';
$columns_class = $columns ? ' acf-gallery-columns-' . esc_attr( $columns ) : ' acf-gallery-columns-3';
$gap_class = $gap ? ' acf-gallery-gap-' . esc_attr( $gap ) : ' acf-gallery-gap-medium';
$lightbox_class = $lightbox ? ' acf-gallery-has-lightbox' : '';
?>

<div class="acf-gallery-block<?php echo $layout_class . $columns_class . $gap_class . $lightbox_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    if ( $images && is_array( $images ) && count( $images ) > 0 ) :
        foreach ( $images as $index => $image ) :
            // Resolve image — handles both ACF arrays and raw numeric IDs from compat layer
            $resolved = acf_blocks_resolve_image( $image, 'Gallery image ' . ( $index + 1 ), 'medium_large' );
            $image_url = $resolved['src'];
            $image_alt = $resolved['alt'];
            $image_caption = is_array( $image ) ? ( $image['caption'] ?? '' ) : '';
            // Full-size URL for lightbox
            if ( is_array( $image ) && ! empty( $image['url'] ) ) {
                $full_url = $image['url'];
            } elseif ( is_numeric( $image ) ) {
                $full_url = wp_get_attachment_url( (int) $image ) ?: $image_url;
            } else {
                $full_url = $image_url;
            }
            // Display URL is the sized version, lightbox uses full
            $display_url = $image_url;
            // Eager load first 4 images, lazy load the rest
            $loading_attr = $index < 4 ? 'eager' : 'lazy';
            ?>
            <div class="acf-gallery-item">
                <?php if ( $lightbox ) : ?>
                    <a href="<?php echo esc_url( $full_url ); ?>"
                       class="acf-gallery-link"
                       data-lightbox="gallery"
                       data-title="<?php echo esc_attr( $image_alt ); ?>">
                        <img src="<?php echo esc_url( $display_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="<?php echo esc_attr( $loading_attr ); ?>" decoding="async" />
                        <div class="acf-gallery-overlay">
                            <span class="acf-gallery-icon">🔍</span>
                        </div>
                    </a>
                <?php else : ?>
                    <img src="<?php echo esc_url( $display_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" loading="<?php echo esc_attr( $loading_attr ); ?>" decoding="async" />
                <?php endif; ?>

                <?php if ( $image_caption ) : ?>
                    <div class="acf-gallery-caption">
                        <?php echo esc_html( $image_caption ); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
        endforeach;
    else :
        if ( $is_preview ) {
            echo '<p><em>No images added. Please add some images to the gallery.</em></p>';
        }
    endif;
    ?>
</div>
