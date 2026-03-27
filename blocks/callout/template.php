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
$block_id = $anchor ?: 'callout-' . ( $block['id'] ?? uniqid() );

// Detect style variation
$style_variation = '';
if (strpos($className, 'is-style-dark') !== false) {
    $style_variation = 'dark';
} elseif (strpos($className, 'is-style-testimonial') !== false) {
    $style_variation = 'testimonial';
} elseif (strpos($className, 'is-style-dashed-light') !== false) {
    $style_variation = 'dashed-light';
} elseif (strpos($className, 'is-style-dashed-dark') !== false) {
    $style_variation = 'dashed-dark';
} elseif (strpos($className, 'is-style-highlight') !== false) {
    $style_variation = 'highlight';
}

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
    <?php if ($style_variation === 'dark'): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr($block_id); ?>.acf-callout {
            background-color: #0a0a0a;
            border-color: #0a0a0a;
            color: #ffffff;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-label {
            color: #ffd700;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout a {
            color: #ffffff;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-button__link {
            background-color: transparent;
            border: 2px solid #ffffff;
            color: #ffffff;
            border-radius: 50px;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-button__link:hover {
            background-color: #ffffff;
            color: #0a0a0a;
        }
    <?php $css = ob_get_clean(); echo '<style>' . acf_blocks_minify_css( $css ) . '</style>'; ?>
    <?php elseif ($style_variation === 'testimonial'): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr($block_id); ?>.acf-callout {
            background-color: #fdf6e3;
            border-color: #f5e6c8;
            color: #3d3d3d;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-label {
            color: #b8860b;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-paragraph:has(⭐),
        #<?php echo esc_attr($block_id); ?>.acf-callout [class*="stars"],
        #<?php echo esc_attr($block_id); ?>.acf-callout [class*="rating"] {
            color: #d4a600;
        }
        :is(.dark,.is-dark-theme,[data-theme=dark]) #<?php echo esc_attr($block_id); ?>.acf-callout {
            background-color: #2a2520;
            border-color: #3d3020;
            color: #e0d8c8;
        }
        :is(.dark,.is-dark-theme,[data-theme=dark]) #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-label {
            color: #d4a600;
        }
    <?php $css = ob_get_clean(); echo '<style>' . acf_blocks_minify_css( $css ) . '</style>'; ?>
    <?php elseif ($style_variation === 'dashed-light'): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr($block_id); ?>.acf-callout {
            background-color: #ffffff;
            border: 3px dashed #c0c0c0;
            color: #3d3d3d;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-label {
            color: #7cb342;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list {
            list-style: none;
            padding-left: 0;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list li {
            position: relative;
            padding-left: max(2rem,32px);
            margin-bottom: max(0.75rem,12px);
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #7cb342;
            font-weight: bold;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-button__link {
            background-color: #ffe135;
            color: #0a0a0a;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 0 #ccb42a;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-button__link:hover {
            transform: translateY(2px);
            box-shadow: 0 2px 0 #ccb42a;
        }
        :is(.dark,.is-dark-theme,[data-theme=dark]) #<?php echo esc_attr($block_id); ?>.acf-callout {
            background-color: #1e1e1e;
            border-color: #555;
            color: #e0e0e0;
        }
        :is(.dark,.is-dark-theme,[data-theme=dark]) #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-label {
            color: #8bc34a;
        }
        :is(.dark,.is-dark-theme,[data-theme=dark]) #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list li::before {
            color: #8bc34a;
        }
    <?php $css = ob_get_clean(); echo '<style>' . acf_blocks_minify_css( $css ) . '</style>'; ?>
    <?php elseif ($style_variation === 'dashed-dark'): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr($block_id); ?>.acf-callout {
            background-color: #3d3d3d;
            border: 3px dashed #5a5a5a;
            color: #ffffff;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-label {
            color: #ffd700;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout a {
            color: #ffffff;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list {
            list-style: none;
            padding-left: 0;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list li {
            position: relative;
            padding-left: max(2rem,32px);
            margin-bottom: max(0.75rem,12px);
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #7cb342;
            font-weight: bold;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-button__link {
            background-color: #ffe135;
            color: #0a0a0a;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 0 #ccb42a;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-button__link:hover {
            transform: translateY(2px);
            box-shadow: 0 2px 0 #ccb42a;
        }
    <?php $css = ob_get_clean(); echo '<style>' . acf_blocks_minify_css( $css ) . '</style>'; ?>
    <?php elseif ($style_variation === 'highlight'): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr($block_id); ?>.acf-callout {
            background-color: #f0fff0;
            border: 3px dashed #90ee90;
            color: #2d4a2d;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-label {
            color: #228b22;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-icon-image img {
            max-width: 60px;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list {
            list-style: none;
            padding-left: 0;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list li {
            position: relative;
            padding-left: max(2rem,32px);
            margin-bottom: max(0.75rem,12px);
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list li::before {
            content: "→";
            position: absolute;
            left: 0;
            color: #228b22;
            font-weight: bold;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-button__link {
            background-color: #ffe135;
            color: #0a0a0a;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 0 #ccb42a;
        }
        #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-button__link:hover {
            transform: translateY(2px);
            box-shadow: 0 2px 0 #ccb42a;
        }
        :is(.dark,.is-dark-theme,[data-theme=dark]) #<?php echo esc_attr($block_id); ?>.acf-callout {
            background-color: #1a2e1a;
            border-color: #2d6e2d;
            color: #c8e8c8;
        }
        :is(.dark,.is-dark-theme,[data-theme=dark]) #<?php echo esc_attr($block_id); ?>.acf-callout .acf-callout-label {
            color: #4caf50;
        }
        :is(.dark,.is-dark-theme,[data-theme=dark]) #<?php echo esc_attr($block_id); ?>.acf-callout .wp-block-list li::before {
            color: #4caf50;
        }
    <?php $css = ob_get_clean(); echo '<style>' . acf_blocks_minify_css( $css ) . '</style>'; ?>
    <?php endif; ?>
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
