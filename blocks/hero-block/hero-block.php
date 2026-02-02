<?php
/**
 * Hero Block Template.
 *
 * Uses core blocks (heading, paragraph, buttons) via InnerBlocks for content.
 * Falls back to legacy ACF fields for backward compatibility.
 * ACF fields are retained for image and styling options.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$image       = acf_blocks_get_field( 'acf_hero_image', $block );
$image_url   = acf_blocks_get_field( 'acf_hero_image_url', $block );

// Determine image source - direct URL takes priority
$img_src = '';
$img_alt = '';
if ( $image_url ) {
    $img_src = $image_url;
    $img_alt = 'Hero image';
} elseif ( $image ) {
    $img_src = $image['url'];
    $img_alt = $image['alt'] ?: 'Hero image';
}

$custom_class = acf_blocks_get_field( 'acf_hero_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = acf_blocks_get_field( 'acf_hero_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

// Check for legacy ACF field content (backward compatibility)
$legacy_headline    = acf_blocks_get_field( 'acf_hero_headline', $block );
$legacy_subheadline = acf_blocks_get_field( 'acf_hero_subheadline', $block );
$legacy_cta_text    = acf_blocks_get_field( 'acf_hero_cta_text', $block );
$legacy_cta_url     = acf_blocks_get_field( 'acf_hero_cta_url', $block );
$legacy_cta_style   = acf_blocks_get_field( 'acf_hero_cta_style', $block );
$has_legacy_content = $legacy_headline || $legacy_subheadline || $legacy_cta_text;

$inner_blocks_template = [
    [ 'core/heading', [ 'level' => 1, 'placeholder' => 'Hero Headline...' ] ],
    [ 'core/paragraph', [ 'placeholder' => 'Hero subheadline text...' ] ],
    [ 'core/buttons', [], [
        [ 'core/button', [ 'placeholder' => 'CTA text...' ] ]
    ] ]
];
?>

<div class="acf-hero-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $img_src ) : ?>
        <div class="acf-hero-image">
            <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" fetchpriority="high" decoding="async" />
        </div>
    <?php endif; ?>

    <div class="acf-hero-content">
        <?php if ( $has_legacy_content && empty( trim( $content ) ) ) : ?>
            <?php // Legacy rendering for blocks created before InnerBlocks migration ?>
            <?php if ( $legacy_headline ) : ?>
                <h1 class="acf-hero-headline"><?php echo esc_html( $legacy_headline ); ?></h1>
            <?php endif; ?>

            <?php if ( $legacy_subheadline ) : ?>
                <div class="acf-hero-subheadline"><?php echo wp_kses_post( $legacy_subheadline ); ?></div>
            <?php endif; ?>

            <?php if ( $legacy_cta_text && $legacy_cta_url ) : ?>
                <a href="<?php echo esc_url( $legacy_cta_url ); ?>" class="acf-hero-cta <?php echo esc_attr( $legacy_cta_style ); ?>">
                    <?php echo esc_html( $legacy_cta_text ); ?>
                </a>
            <?php endif; ?>
        <?php else : ?>
            <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $inner_blocks_template ) ); ?>" templateLock="false" />
        <?php endif; ?>
    </div>
</div>
