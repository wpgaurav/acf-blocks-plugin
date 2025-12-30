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

// ACF fields
$use_innerblocks = get_field('acf_fg_use_innerblocks');
$heading = get_field('acf_feature_grid_heading');
$subheading = get_field('acf_feature_grid_subheading');
$features = get_field('acf_feature_grid_items');
$columns = get_field('acf_feature_grid_columns');
$layout_style = get_field('acf_feature_grid_layout');
$cta_button = get_field('acf_fg_cta_button');
$cta_style = get_field('acf_fg_cta_style') ?: 'primary';

$custom_class = get_field('acf_feature_grid_class');
$custom_class = $custom_class ? ' ' . esc_attr($custom_class) : '';

$inline_style = get_field('acf_feature_grid_inline');
$inline_style_attr = $inline_style ? ' style="' . esc_attr($inline_style) . '"' : '';

$columns_class = $columns ? ' columns-' . esc_attr($columns) : ' columns-3';
$layout_class = $layout_style ? ' layout-' . esc_attr($layout_style) : ' layout-default';
$anchor_attr = $anchor ? ' id="' . esc_attr($anchor) . '"' : '';

// Build wrapper classes
$wrapper_classes = 'acf-feature-grid-block' . $columns_class . $layout_class . $custom_class;
if ($align) {
    $wrapper_classes .= ' ' . $align;
}
if ($className) {
    $wrapper_classes .= ' ' . $className;
}
?>

<div class="<?php echo esc_attr($wrapper_classes); ?>"<?php echo $anchor_attr . $inline_style_attr; ?>>
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
