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

$background_color = acf_blocks_get_field( 'acf_cta_background_color', $block );
$text_color  = acf_blocks_get_field( 'acf_cta_text_color', $block );

$custom_class = acf_blocks_get_field( 'acf_cta_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = acf_blocks_get_field( 'acf_cta_inline', $block );
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
$legacy_heading     = acf_blocks_get_field( 'acf_cta_heading', $block );
$legacy_description = acf_blocks_get_field( 'acf_cta_description', $block );
$legacy_button_text = acf_blocks_get_field( 'acf_cta_button_text', $block );
$legacy_button_url  = acf_blocks_get_field( 'acf_cta_button_url', $block );
$legacy_button_style = acf_blocks_get_field( 'acf_cta_button_style', $block );
$has_legacy_content = $legacy_heading || $legacy_description || $legacy_button_text;

// Heading tag selection (h1-h6, p, span) - defaults to h2
$heading_tag = acf_blocks_get_field( 'acf_cta_heading_tag', $block ) ?: 'h2';
$allowed_heading_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span' );
if ( ! in_array( $heading_tag, $allowed_heading_tags ) ) {
    $heading_tag = 'h2';
}
$heading_level = 2;
if ( preg_match( '/^h([1-6])$/', $heading_tag, $level_match ) ) {
    $heading_level = (int) $level_match[1];
}

$inner_blocks_template = [
    [ 'core/heading', [ 'level' => $heading_level, 'placeholder' => 'CTA Heading...' ] ],
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
                <<?php echo esc_attr( $heading_tag ); ?> class="acf-cta-heading"><?php echo esc_html( $legacy_heading ); ?></<?php echo esc_attr( $heading_tag ); ?>>
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
