<?php
/**
 * Hero Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$headline    = get_field( 'acf_hero_headline' );
$subheadline = get_field( 'acf_hero_subheadline' );
$image       = get_field( 'acf_hero_image' );
$cta_text    = get_field( 'acf_hero_cta_text' );
$cta_url     = get_field( 'acf_hero_cta_url' );
$cta_style   = get_field( 'acf_hero_cta_style' );

$custom_class = get_field( 'acf_hero_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_hero_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';
?>

<div class="acf-hero-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $image ) : ?>
        <div class="acf-hero-image">
            <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr( $image['alt'] ); ?>" />
        </div>
    <?php endif; ?>

    <div class="acf-hero-content">
        <?php if ( $headline ) : ?>
            <h1 class="acf-hero-headline"><?php echo esc_html( $headline ); ?></h1>
        <?php endif; ?>

        <?php if ( $subheadline ) : ?>
            <p class="acf-hero-subheadline"><?php echo esc_html( $subheadline ); ?></p>
        <?php endif; ?>

        <?php if ( $cta_text && $cta_url ) : ?>
            <a href="<?php echo esc_url( $cta_url ); ?>" class="acf-hero-cta <?php echo esc_attr( $cta_style ); ?>">
                <?php echo esc_html( $cta_text ); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
