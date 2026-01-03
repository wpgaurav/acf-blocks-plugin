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
$image_url   = get_field( 'acf_hero_image_url' );
$cta_text    = get_field( 'acf_hero_cta_text' );
$cta_url     = get_field( 'acf_hero_cta_url' );
$cta_style   = get_field( 'acf_hero_cta_style' );

// Determine image source - direct URL takes priority
$img_src = '';
$img_alt = '';
if ( $image_url ) {
    $img_src = $image_url;
    $img_alt = $headline ?: 'Hero image';
} elseif ( $image ) {
    $img_src = $image['url'];
    $img_alt = $image['alt'] ?: $headline;
}

$custom_class = get_field( 'acf_hero_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_hero_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';
?>

<div class="acf-hero-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $img_src ) : ?>
        <div class="acf-hero-image">
            <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" fetchpriority="high" decoding="async" />
        </div>
    <?php endif; ?>

    <div class="acf-hero-content">
        <?php if ( $headline ) : ?>
            <h1 class="acf-hero-headline"><?php echo esc_html( $headline ); ?></h1>
        <?php endif; ?>

        <?php if ( $subheadline ) : ?>
            <div class="acf-hero-subheadline"><?php echo wp_kses_post( $subheadline ); ?></div>
        <?php endif; ?>

        <?php if ( $cta_text && $cta_url ) : ?>
            <a href="<?php echo esc_url( $cta_url ); ?>" class="acf-hero-cta <?php echo esc_attr( $cta_style ); ?>">
                <?php echo esc_html( $cta_text ); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
