<?php
/**
 * Product Box Block Template.
 *
 * Uses core blocks via InnerBlocks for title and description content.
 * Falls back to legacy ACF fields for backward compatibility.
 * ACF fields are retained for image, rating, and buttons.
 *
 * @var array   $block       The block settings and attributes.
 * @var string  $content     The block inner HTML (empty).
 * @var bool    $is_preview  True during AJAX preview.
 * @var int     $post_id     The post ID this block is saved to.
 */

// Retrieve field values.
$image       = acf_blocks_get_field('pb_image', $block);
$image_url   = acf_blocks_get_field('pb_image_url', $block);
$rating      = acf_blocks_get_field('pb_rating', $block);

// Determine image source - direct URL takes priority
$img_src = '';
$img_alt = 'Product image';
if ( $image_url ) {
    $img_src = $image_url;
} elseif ( $image ) {
    $img_src = $image['url'];
    $img_alt = $image['alt'] ?: $img_alt;
}

// Check for legacy ACF field content (backward compatibility)
$legacy_title       = acf_blocks_get_field('pb_title', $block);
$legacy_description = acf_blocks_get_field('pb_description', $block);
$has_legacy_content = $legacy_title || $legacy_description;

$inner_blocks_template = [
    [ 'core/heading', [ 'level' => 3, 'placeholder' => 'Product title...', 'className' => 'acf-product-box__title fw-900 med-title' ] ],
    [ 'core/paragraph', [ 'placeholder' => 'Product description...' ] ]
];
?>

<div class="acf-product-box grid-2" style="align-items:center">
    <?php if( $img_src ): ?>
    <div class="acf-product-box__image">
        <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" class="acf-product-box__image-img" loading="lazy" decoding="async" />
    </div>
<?php endif; ?>

	<div>
    <?php if ( $has_legacy_content && empty( trim( $content ) ) ) : ?>
        <?php // Legacy rendering for blocks created before InnerBlocks migration ?>
        <?php if( $legacy_title ): ?>
            <p class="acf-product-box__title fw-900 med-title"><?php echo esc_html($legacy_title); ?></p>
        <?php endif; ?>

        <div class="acf-product-box__rating">
            <?php
            // Loop to output star icons based on rating.
            for ( $i = 1; $i <= 5; $i++ ) {
                if ( $rating >= $i ) {
                    echo '<i class="md-icon-star-full"></i>';
                } elseif ( $rating >= ( $i - 0.5 ) ) {
                    echo '<i class="md-icon-star-half"></i>';
                } else {
                    echo '<i class="md-icon-star-empty"></i>';
                }
            }
            ?>
        </div>

        <?php if( $legacy_description ): ?>
            <div class="acf-product-box__description">
                <?php echo $legacy_description; // WYSIWYG content ?>
            </div>
        <?php endif; ?>

        <?php if( have_rows('pb_buttons') ): ?>
            <div class="acf-product-box__buttons" style="display: flex ; flex-direction: row; align-content: center; justify-content: center; align-items: center; flex-wrap: wrap;">
                <?php while( have_rows('pb_buttons') ): the_row();
                    $cta_text  = get_sub_field('pb_cta_text');
                    $cta_url   = get_sub_field('pb_cta_url');
                    $cta_class = get_sub_field('pb_cta_class');
                    $cta_rel   = get_sub_field('pb_cta_rel');

                    // Only add class and rel if they're not empty.
                    $class_attr = $cta_class ? ' class="' . esc_attr($cta_class) . '"' : '';
                    $rel_attr   = $cta_rel ? ' rel="' . esc_attr($cta_rel) . '"' : '';
                ?>
                    <?php if( $cta_text && $cta_url ): ?>
                        <a href="<?php echo esc_url($cta_url); ?>"<?php echo $class_attr . $rel_attr; ?>>
                            <?php echo esc_html($cta_text); ?>
                        </a>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="acf-product-box__rating">
            <?php
            for ( $i = 1; $i <= 5; $i++ ) {
                if ( $rating >= $i ) {
                    echo '<i class="md-icon-star-full"></i>';
                } elseif ( $rating >= ( $i - 0.5 ) ) {
                    echo '<i class="md-icon-star-half"></i>';
                } else {
                    echo '<i class="md-icon-star-empty"></i>';
                }
            }
            ?>
        </div>

        <div class="acf-product-box__description">
            <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $inner_blocks_template ) ); ?>" templateLock="false" />
        </div>

        <?php if( have_rows('pb_buttons') ): ?>
            <div class="acf-product-box__buttons" style="display: flex ; flex-direction: row; align-content: center; justify-content: center; align-items: center; flex-wrap: wrap;">
                <?php while( have_rows('pb_buttons') ): the_row();
                    $cta_text  = get_sub_field('pb_cta_text');
                    $cta_url   = get_sub_field('pb_cta_url');
                    $cta_class = get_sub_field('pb_cta_class');
                    $cta_rel   = get_sub_field('pb_cta_rel');

                    $class_attr = $cta_class ? ' class="' . esc_attr($cta_class) . '"' : '';
                    $rel_attr   = $cta_rel ? ' rel="' . esc_attr($cta_rel) . '"' : '';
                ?>
                    <?php if( $cta_text && $cta_url ): ?>
                        <a href="<?php echo esc_url($cta_url); ?>"<?php echo $class_attr . $rel_attr; ?>>
                            <?php echo esc_html($cta_text); ?>
                        </a>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
	</div>
</div>
