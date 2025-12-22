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

$layout_class = $layout ? ' gallery-' . esc_attr( $layout ) : ' gallery-grid';
$columns_class = $columns ? ' columns-' . esc_attr( $columns ) : ' columns-3';
$gap_class = $gap ? ' gap-' . esc_attr( $gap ) : ' gap-medium';
$lightbox_class = $lightbox ? ' has-lightbox' : '';
?>

<div class="gallery-block<?php echo $layout_class . $columns_class . $gap_class . $lightbox_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    if ( $images && is_array( $images ) && count( $images ) > 0 ) :
        foreach ( $images as $index => $image ) :
            $image_url = $image['url'];
            $image_alt = $image['alt'] ? $image['alt'] : 'Gallery image ' . ( $index + 1 );
            $image_caption = $image['caption'];
            ?>
            <div class="gallery-item">
                <?php if ( $lightbox ) : ?>
                    <a href="<?php echo esc_url( $image_url ); ?>"
                       class="gallery-link"
                       data-lightbox="gallery"
                       data-title="<?php echo esc_attr( $image_alt ); ?>">
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
                        <div class="gallery-overlay">
                            <span class="gallery-icon">🔍</span>
                        </div>
                    </a>
                <?php else : ?>
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
                <?php endif; ?>

                <?php if ( $image_caption ) : ?>
                    <div class="gallery-caption">
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
