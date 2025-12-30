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

// Get ACF fields for styling
$iconImage = get_field('callout_iconImage');
$labelText = get_field('callout_label');
$labelPosition = get_field('callout_label_position') ?: 'top';
$bgColor = get_field('callout_bgColor');
$textColor = get_field('callout_textColor');
$borderColor = get_field('callout_borderColor');

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

$anchor_attr = !empty($anchor) ? ' id="' . esc_attr($anchor) . '"' : '';
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $anchor_attr . $style_attr; ?>>
    <?php if (!empty($iconImage) && $labelPosition === 'top') : ?>
        <div class="acf-callout-icon-image">
            <img src="<?php echo esc_url($iconImage); ?>" alt="" loading="lazy" />
        </div>
    <?php endif; ?>

    <?php if (!empty($labelText) && $labelPosition === 'top') : ?>
        <div class="acf-callout-label"><?php echo esc_html($labelText); ?></div>
    <?php endif; ?>

    <div class="acf-callout-content">
        <InnerBlocks templateLock="false" />
    </div>

    <?php if (!empty($labelText) && $labelPosition === 'bottom') : ?>
        <div class="acf-callout-label acf-callout-label-bottom"><?php echo esc_html($labelText); ?></div>
    <?php endif; ?>
</div>
