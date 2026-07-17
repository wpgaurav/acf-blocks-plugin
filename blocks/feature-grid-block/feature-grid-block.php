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

// Generate unique block ID for scoping inline styles
$block_id = $anchor ? $anchor : 'acf-feature-grid-' . wp_unique_id();

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
                            <?php if (!empty($feature['acf_feature_image'])) :
                                $feat_img = $feature['acf_feature_image'];
                                $feat_srcset = '';
                                $feat_sizes = '';
                                if ( ! empty( $feat_img['ID'] ) ) {
                                    $feat_srcset_val = wp_get_attachment_image_srcset( (int) $feat_img['ID'], 'medium' );
                                    if ( $feat_srcset_val ) {
                                        $feat_srcset = $feat_srcset_val;
                                        $feat_sizes = wp_get_attachment_image_sizes( (int) $feat_img['ID'], 'medium' ) ?: '';
                                    }
                                }
                            ?>
                                <div class="acf-feature-image">
                                    <img src="<?php echo esc_url($feat_img['url']); ?>"
                                         alt="<?php echo esc_attr($feat_img['alt']); ?>"
                                         <?php if ( $feat_srcset ) : ?>srcset="<?php echo esc_attr($feat_srcset); ?>" sizes="<?php echo esc_attr($feat_sizes); ?>"<?php endif; ?>
                                         loading="lazy" decoding="async" />
                                </div>
                            <?php elseif (!empty($feature['acf_feature_icon'])) : ?>
                                <div class="acf-feature-icon">
                                    <?php
                                    $icon_markup = acf_blocks_get_icon_markup( $feature['acf_feature_icon'] );
                                    echo wp_kses_post( $icon_markup );
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
                               class="<?php echo esc_attr($btn_class); ?>"
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
               class="<?php echo esc_attr($cta_class); ?>"
               <?php echo !empty($cta_button['target']) ? 'target="' . esc_attr($cta_button['target']) . '"' : ''; ?>>
                <?php echo esc_html($cta_button['title'] ?: 'Get Started'); ?>
            </a>
        </div>
    <?php endif; ?>
</div>
