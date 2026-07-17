<?php
/**
 * Callout Block Template.
 *
 * @param array $block The block settings and attributes.
 */

// Get block attributes
$align = $block['align'] ?? '';
$className = $block['className'] ?? '';
$anchor = $block['anchor'] ?? '';

// Generate block ID
$block_id = $anchor ?: 'callout-' . ( $block['id'] ?? wp_unique_id() );

// Get ACF fields for styling
$iconImageRaw = acf_blocks_get_field('callout_iconImage', $block);
$iconResolved = acf_blocks_resolve_image( $iconImageRaw, '', 'medium' );
$iconImage = $iconResolved['src'];
$iconSrcset = $iconResolved['srcset'];
$iconSizes = $iconResolved['sizes'];
$labelText = acf_blocks_get_field('callout_label', $block);
$labelPosition = acf_blocks_get_field('callout_label_position', $block) ?: 'top';
$bgColor = acf_blocks_get_field('callout_bgColor', $block);
$textColor = acf_blocks_get_field('callout_textColor', $block);
$borderColor = acf_blocks_get_field('callout_borderColor', $block);

// Build inline styles
$styles = [];
if (!empty($bgColor)) {
    $styles[] = 'background-color: ' . esc_attr($bgColor);
}
if (!empty($textColor)) {
    $styles[] = 'color: ' . esc_attr($textColor);
}
if (!empty($borderColor)) {
    $styles[] = 'border-color: ' . esc_attr($borderColor);
}
$style_attr = !empty($styles) ? ' style="' . implode('; ', $styles) . ';"' : '';

// Build classes
$classes = ['acf-callout'];
if (!empty($align)) {
    $classes[] = 'align' . $align;
}
if (!empty($className)) {
    $classes[] = $className;
}
if (!empty($iconImage)) {
    $classes[] = 'has-icon-image';
}
if (!empty($labelText)) {
    $classes[] = 'has-label';
}

// InnerBlocks template for initial block content
$inner_blocks_template = [
    [ 'core/heading', [ 'level' => 3, 'placeholder' => 'Callout heading...' ] ],
    [ 'core/paragraph', [ 'placeholder' => 'Callout content...' ] ],
];

?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $style_attr; ?>>
    <?php if (!empty($iconImage) && $labelPosition === 'top') : ?>
        <div class="acf-callout-icon-image">
            <img src="<?php echo esc_url($iconImage); ?>" alt=""<?php if ( $iconSrcset ) : ?> srcset="<?php echo esc_attr($iconSrcset); ?>" sizes="<?php echo esc_attr($iconSizes); ?>"<?php endif; ?> loading="lazy" decoding="async" />
        </div>
    <?php endif; ?>

    <?php if (!empty($labelText) && $labelPosition === 'top') : ?>
        <div class="acf-callout-label"><?php echo esc_html($labelText); ?></div>
    <?php endif; ?>

    <div class="acf-callout-content">
        <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $inner_blocks_template ) ); ?>" templateLock="false" />
    </div>

    <?php if (!empty($labelText) && $labelPosition === 'bottom') : ?>
        <div class="acf-callout-label acf-callout-label-bottom"><?php echo esc_html($labelText); ?></div>
    <?php endif; ?>
</div>
