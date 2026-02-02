<?php
/**
 * Feature Grid Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (InnerBlocks content).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

// Block attributes
$anchor = !empty($block['anchor']) ? $block['anchor'] : '';
$className = !empty($block['className']) ? $block['className'] : '';
$align = !empty($block['align']) ? 'align' . $block['align'] : '';

// Detect style variation
$style_variation = '';
if (strpos($className, 'is-style-card') !== false) {
    $style_variation = 'card';
} elseif (strpos($className, 'is-style-dark') !== false) {
    $style_variation = 'dark';
} elseif (strpos($className, 'is-style-minimal') !== false) {
    $style_variation = 'minimal';
} elseif (strpos($className, 'is-style-bordered') !== false) {
    $style_variation = 'bordered';
} elseif (strpos($className, 'is-style-gradient') !== false) {
    $style_variation = 'gradient';
}

// Generate unique block ID for scoping inline styles
$block_id = $anchor ? $anchor : 'acf-feature-grid-' . uniqid();

// ACF fields
$use_innerblocks = acf_blocks_get_field('acf_fg_use_innerblocks', $block);
$heading = acf_blocks_get_field('acf_feature_grid_heading', $block);
$subheading = acf_blocks_get_field('acf_feature_grid_subheading', $block);
$features = acf_blocks_get_repeater('acf_feature_grid_items', [
    'acf_feature_icon',
    'acf_feature_image' => 'image',
    'acf_feature_title',
    'acf_feature_description',
    'acf_feature_link' => 'link',
    'acf_feature_button' => 'link',
    'acf_feature_button_style',
], $block);
$columns = acf_blocks_get_field('acf_feature_grid_columns', $block);
$layout_style = acf_blocks_get_field('acf_feature_grid_layout', $block);
$cta_button = acf_blocks_get_field('acf_fg_cta_button', $block);
$cta_style = acf_blocks_get_field('acf_fg_cta_style', $block) ?: 'primary';

$custom_class = acf_blocks_get_field('acf_feature_grid_class', $block);
$custom_class = $custom_class ? ' ' . esc_attr($custom_class) : '';

$inline_style = acf_blocks_get_field('acf_feature_grid_inline', $block);
$inline_style_attr = $inline_style ? ' style="' . esc_attr($inline_style) . '"' : '';

$columns_class = $columns ? ' columns-' . esc_attr($columns) : ' columns-3';
$layout_class = $layout_style ? ' layout-' . esc_attr($layout_style) : ' layout-default';

// Build wrapper classes
$wrapper_classes = 'acf-feature-grid-block' . $columns_class . $layout_class . $custom_class;
if ($align) {
    $wrapper_classes .= ' ' . $align;
}
if ($className) {
    $wrapper_classes .= ' ' . $className;
}
?>

<div id="<?php echo esc_attr($block_id); ?>" class="<?php echo esc_attr($wrapper_classes); ?>"<?php echo $inline_style_attr; ?>>
<?php if ($style_variation) : ?>
<?php
ob_start();
?>
        <?php if ($style_variation === 'card') : ?>
        #<?php echo esc_attr($block_id); ?> .acf-feature-item {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
            padding: 2rem;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        <?php elseif ($style_variation === 'dark') : ?>
        #<?php echo esc_attr($block_id); ?> {
            background: #1a1a2e;
            color: #fff;
            border-radius: 12px;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-grid-heading {
            color: #fff;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-grid-subheading {
            color: #a0a0a0;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-item {
            background: #2d2d44;
            border-radius: 8px;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-title {
            color: #fff;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-description {
            color: #b0b0b0;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-icon {
            color: #ffd700;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-link {
            color: #ffd700;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-link:hover {
            color: #ffed4a;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-primary,
        #<?php echo esc_attr($block_id); ?> .acf-cta-primary,
        #<?php echo esc_attr($block_id); ?> .acf-cta-large {
            background: #ffd700;
            border-color: #ffd700;
            color: #1a1a2e;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-primary:hover,
        #<?php echo esc_attr($block_id); ?> .acf-cta-primary:hover,
        #<?php echo esc_attr($block_id); ?> .acf-cta-large:hover {
            background: #ffed4a;
            border-color: #ffed4a;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-secondary,
        #<?php echo esc_attr($block_id); ?> .acf-cta-secondary {
            color: #ffd700;
            border-color: #ffd700;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-secondary:hover,
        #<?php echo esc_attr($block_id); ?> .acf-cta-secondary:hover {
            background: #ffd700;
            color: #1a1a2e;
        }
        <?php elseif ($style_variation === 'minimal') : ?>
        #<?php echo esc_attr($block_id); ?> {
            padding: 2rem 0;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-item:hover {
            background: transparent;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-icon {
            font-size: 2rem;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-title {
            font-size: 1.25rem;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-primary {
            background: transparent;
            border-color: transparent;
            color: #007bff;
            padding-left: 0;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-primary:hover {
            background: transparent;
            color: #0056b3;
        }
        #<?php echo esc_attr($block_id); ?> .acf-cta-primary,
        #<?php echo esc_attr($block_id); ?> .acf-cta-large {
            background: transparent;
            color: #007bff;
            border: 2px solid #007bff;
        }
        #<?php echo esc_attr($block_id); ?> .acf-cta-primary:hover,
        #<?php echo esc_attr($block_id); ?> .acf-cta-large:hover {
            background: #007bff;
            color: #fff;
        }
        <?php elseif ($style_variation === 'bordered') : ?>
        #<?php echo esc_attr($block_id); ?> .acf-feature-item {
            border: 3px solid #1a1a1a;
            border-radius: 0;
            background: #fff;
            padding: 2rem;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-item:hover {
            box-shadow: 6px 6px 0 #1a1a1a;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-icon {
            color: #1a1a1a;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-primary {
            background: #1a1a1a;
            border-color: #1a1a1a;
            border-radius: 0;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-primary:hover {
            background: #333;
            border-color: #333;
        }
        #<?php echo esc_attr($block_id); ?> .acf-cta-primary,
        #<?php echo esc_attr($block_id); ?> .acf-cta-large {
            background: #1a1a1a;
            border-color: #1a1a1a;
            border-radius: 0;
        }
        #<?php echo esc_attr($block_id); ?> .acf-cta-primary:hover,
        #<?php echo esc_attr($block_id); ?> .acf-cta-large:hover {
            background: #333;
            border-color: #333;
        }
        <?php elseif ($style_variation === 'gradient') : ?>
        #<?php echo esc_attr($block_id); ?> .acf-feature-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 2rem;
            color: #fff;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-title {
            color: #fff;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-description {
            color: rgba(255, 255, 255, 0.9);
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-icon {
            color: #fff;
            opacity: 0.9;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-link {
            color: #fff;
        }
        #<?php echo esc_attr($block_id); ?> .acf-feature-link:hover {
            color: rgba(255, 255, 255, 0.8);
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-primary {
            background: #fff;
            border-color: #fff;
            color: #667eea;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-primary:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(255, 255, 255, 0.9);
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-secondary {
            border-color: #fff;
            color: #fff;
        }
        #<?php echo esc_attr($block_id); ?> .acf-button-secondary:hover {
            background: #fff;
            color: #667eea;
        }
        #<?php echo esc_attr($block_id); ?> .acf-cta-primary,
        #<?php echo esc_attr($block_id); ?> .acf-cta-large {
            background: #fff;
            border-color: #fff;
            color: #667eea;
        }
        #<?php echo esc_attr($block_id); ?> .acf-cta-primary:hover,
        #<?php echo esc_attr($block_id); ?> .acf-cta-large:hover {
            background: rgba(255, 255, 255, 0.9);
        }
        <?php endif; ?>
<?php
$css = ob_get_clean();
echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
?>
<?php endif; ?>
    <?php if ($use_innerblocks) : ?>
        <div class="acf-feature-grid-header acf-feature-grid-innerblocks">
            <InnerBlocks />
        </div>
    <?php elseif ($heading || $subheading) : ?>
        <div class="acf-feature-grid-header">
            <?php if ($heading) : ?>
                <h2 class="acf-feature-grid-heading"><?php echo esc_html($heading); ?></h2>
            <?php endif; ?>

            <?php if ($subheading) : ?>
                <p class="acf-feature-grid-subheading"><?php echo esc_html($subheading); ?></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($features && is_array($features) && count($features) > 0) : ?>
        <div class="acf-feature-grid-items">
            <?php foreach ($features as $feature) : ?>
                <div class="acf-feature-item">
                    <?php if (!empty($feature['acf_feature_icon']) || !empty($feature['acf_feature_image'])) : ?>
                        <div class="acf-feature-icon-wrapper">
                            <?php if (!empty($feature['acf_feature_image'])) : ?>
                                <div class="acf-feature-image">
                                    <img src="<?php echo esc_url($feature['acf_feature_image']['url']); ?>"
                                         alt="<?php echo esc_attr($feature['acf_feature_image']['alt']); ?>"
                                         loading="lazy" decoding="async" />
                                </div>
                            <?php elseif (!empty($feature['acf_feature_icon'])) : ?>
                                <div class="acf-feature-icon">
                                    <?php
                                    $icon_markup = function_exists('md_get_icon_markup')
                                        ? md_get_icon_markup($feature['acf_feature_icon'])
                                        : esc_html($feature['acf_feature_icon']);
                                    echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="acf-feature-content">
                        <?php if (!empty($feature['acf_feature_title'])) : ?>
                            <h3 class="acf-feature-title"><?php echo esc_html($feature['acf_feature_title']); ?></h3>
                        <?php endif; ?>

                        <?php if (!empty($feature['acf_feature_description'])) : ?>
                            <div class="acf-feature-description">
                                <?php echo wpautop(esc_html($feature['acf_feature_description'])); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($feature['acf_feature_button'])) :
                            $btn = $feature['acf_feature_button'];
                            $btn_style = !empty($feature['acf_feature_button_style']) ? $feature['acf_feature_button_style'] : 'primary';
                            $btn_class = 'acf-feature-button acf-button-' . esc_attr($btn_style);
                        ?>
                            <a href="<?php echo esc_url($btn['url']); ?>"
                               class="<?php echo $btn_class; ?>"
                               <?php echo !empty($btn['target']) ? 'target="' . esc_attr($btn['target']) . '"' : ''; ?>>
                                <?php echo esc_html($btn['title'] ?: 'Learn More'); ?>
                            </a>
                        <?php elseif (!empty($feature['acf_feature_link'])) : ?>
                            <a href="<?php echo esc_url($feature['acf_feature_link']['url']); ?>"
                               class="acf-feature-link"
                               <?php echo !empty($feature['acf_feature_link']['target']) ? 'target="' . esc_attr($feature['acf_feature_link']['target']) . '"' : ''; ?>>
                                <?php echo esc_html($feature['acf_feature_link']['title'] ?: 'Learn More'); ?>
                                <span class="acf-link-arrow">→</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <?php if ($is_preview) : ?>
            <p class="acf-feature-grid-placeholder"><em>No features added. Please add some feature items in the Features tab.</em></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($cta_button) :
        $cta_class = 'acf-feature-grid-cta acf-cta-' . esc_attr($cta_style);
    ?>
        <div class="acf-feature-grid-footer">
            <a href="<?php echo esc_url($cta_button['url']); ?>"
               class="<?php echo $cta_class; ?>"
               <?php echo !empty($cta_button['target']) ? 'target="' . esc_attr($cta_button['target']) . '"' : ''; ?>>
                <?php echo esc_html($cta_button['title'] ?: 'Get Started'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
