<?php
/**
 * Gallery Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$images      = get_field( 'acf_gallery_images' );
$layout      = get_field( 'acf_gallery_layout' );
$columns     = get_field( 'acf_gallery_columns' );
$gap         = get_field( 'acf_gallery_gap' );
$lightbox    = get_field( 'acf_gallery_enable_lightbox' );

$custom_class = get_field( 'acf_gallery_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_gallery_inline' );
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
            $image_url = $image['url'];
            $image_alt = $image['alt'] ? $image['alt'] : 'Gallery image ' . ( $index + 1 );
            $image_caption = $image['caption'];
            // Use medium_large size for grid display, full for lightbox
            $display_url = isset( $image['sizes']['medium_large'] ) ? $image['sizes']['medium_large'] : $image_url;
            // Eager load first 4 images, lazy load the rest
            $loading_attr = $index < 4 ? 'eager' : 'lazy';
            ?>
            <div class="acf-gallery-item">
                <?php if ( $lightbox ) : ?>
                    <a href="<?php echo esc_url( $image_url ); ?>"
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
