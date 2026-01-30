<?php
/**
 * CTA Block Template.
 *
 * Uses core blocks (heading, paragraph, buttons) via InnerBlocks for content.
 * Falls back to legacy ACF fields for backward compatibility.
 * ACF fields are retained for styling options.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$background_color = get_field( 'acf_cta_background_color' );
$text_color  = get_field( 'acf_cta_text_color' );

$custom_class = get_field( 'acf_cta_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_cta_inline' );
$style_parts = [];

if ( $background_color ) {
    $style_parts[] = 'background-color: ' . esc_attr( $background_color );
}

if ( $text_color ) {
    $style_parts[] = 'color: ' . esc_attr( $text_color );
}

if ( $inline_style ) {
    $style_parts[] = esc_attr( $inline_style );
}

$inline_style_attr = ! empty( $style_parts ) ? ' style="' . implode( '; ', $style_parts ) . '"' : '';

// Check for legacy ACF field content (backward compatibility)
$legacy_heading     = get_field( 'acf_cta_heading' );
$legacy_description = get_field( 'acf_cta_description' );
$legacy_button_text = get_field( 'acf_cta_button_text' );
$legacy_button_url  = get_field( 'acf_cta_button_url' );
$legacy_button_style = get_field( 'acf_cta_button_style' );
$has_legacy_content = $legacy_heading || $legacy_description || $legacy_button_text;

$inner_blocks_template = [
    [ 'core/heading', [ 'level' => 2, 'placeholder' => 'CTA Heading...' ] ],
    [ 'core/paragraph', [ 'placeholder' => 'CTA description text...' ] ],
    [ 'core/buttons', [], [
        [ 'core/button', [ 'placeholder' => 'Button text...' ] ]
    ] ]
];
?>

<div class="acf-cta-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <div class="acf-cta-content">
        <?php if ( $has_legacy_content && empty( trim( $content ) ) ) : ?>
            <?php // Legacy rendering for blocks created before InnerBlocks migration ?>
            <?php if ( $legacy_heading ) : ?>
                <h2 class="acf-cta-heading"><?php echo esc_html( $legacy_heading ); ?></h2>
            <?php endif; ?>

            <?php if ( $legacy_description ) : ?>
                <div class="acf-cta-description">
                    <?php echo wp_kses_post( $legacy_description ); ?>
                </div>
            <?php endif; ?>

            <?php if ( $legacy_button_text && $legacy_button_url ) : ?>
                <a href="<?php echo esc_url( $legacy_button_url ); ?>" class="acf-cta-button <?php echo esc_attr( $legacy_button_style ); ?>">
                    <?php echo esc_html( $legacy_button_text ); ?>
                </a>
            <?php endif; ?>
        <?php else : ?>
            <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $inner_blocks_template ) ); ?>" templateLock="false" />
        <?php endif; ?>
    </div>
</div>
