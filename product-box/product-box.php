<?php
/**
 * Product Box Block Template.
 *
 * @var array   $block       The block settings and attributes.
 * @var string  $content     The block inner HTML (empty).
 * @var bool    $is_preview  True during AJAX preview.
 * @var int     $post_id     The post ID this block is saved to.
 */

// Retrieve field values.
$image       = get_field('pb_image');
$title       = get_field('pb_title');
$rating      = get_field('pb_rating');
$description = get_field('pb_description');
?>

<div class="product-box grid-2" style="align-items:center">
    <?php if( $image ): ?>
    <div class="product-box__image">
        <?php echo wp_get_attachment_image( $image['ID'], 'md-block', false, array( 'class' => 'product-box__image-img' ) ); ?>
    </div>
<?php endif; ?>

	<div>
    <?php if( $title ): ?>
        <p class="product-box__title fw-900 med-title"><?php echo esc_html($title); ?></p>
    <?php endif; ?>

    <div class="product-box__rating">
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

    <?php if( $description ): ?>
        <div class="product-box__description">
            <?php echo $description; // WYSIWYG content ?>
        </div>
    <?php endif; ?>

    <?php if( have_rows('pb_buttons') ): ?>
        <div class="product-box__buttons" style="display: flex ; flex-direction: row; align-content: center; justify-content: center; align-items: center; flex-wrap: wrap;">
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
	</div>
</div>
