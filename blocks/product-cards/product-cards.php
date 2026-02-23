<?php
/**
 * Product Cards Block Template.
 *
 * A customizable product card block with header, image, description, and CTA buttons.
 *
 * @var array   $block       The block settings and attributes.
 * @var string  $content     The block inner HTML.
 * @var bool    $is_preview  True during AJAX preview.
 * @var int     $post_id     The post ID this block is saved to.
 */

$title = acf_blocks_get_field('pc_block_title', $block);
$title_color = acf_blocks_get_field('pc_block_title_color', $block) ?: '#FFFFFF';
$title_bg_color = acf_blocks_get_field('pc_block_title_bg_color', $block) ?: '#007bff';
$title_tag_raw = acf_blocks_get_field('pc_block_title_tag', $block);
$title_tag = in_array($title_tag_raw, ['p', 'h2', 'h3', 'h4', 'h5', 'h6'], true) ? $title_tag_raw : 'p';
$image = acf_blocks_get_field('pc_block_product_image', $block);
$description = acf_blocks_get_field('pc_block_description', $block);
$root_class = acf_blocks_get_field('pc_block_root_class', $block);
$button_text = acf_blocks_get_field('pc_block_button_text', $block);
$button_url = acf_blocks_get_field('pc_block_button_url', $block);
$button_rel = acf_blocks_get_field('pc_block_button_rel', $block);
$text_link = acf_blocks_get_field('pc_block_text_link', $block);
$text_link_url = acf_blocks_get_field('pc_block_text_link_url', $block);
$text_link_rel = acf_blocks_get_field('pc_block_text_link_rel', $block);

// Build wrapper classes
$wrapper_classes = ['acf-product-cards'];
if ($root_class) {
    $wrapper_classes[] = $root_class;
}

$wrapper_attributes = get_block_wrapper_attributes(['class' => implode(' ', $wrapper_classes)]);
?>

<div <?php echo $wrapper_attributes; ?>>
    <div class="acf-product-cards__header" style="background-color: <?php echo esc_attr($title_bg_color); ?>; color: <?php echo esc_attr($title_color); ?>;">
        <<?php echo $title_tag; ?> class="acf-product-cards__title"><?php echo esc_html($title); ?></<?php echo $title_tag; ?>>
    </div>
    <?php if ($image): ?>
        <div class="acf-product-cards__image">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async" />
        </div>
    <?php endif; ?>
    <div class="acf-product-cards__content">
        <?php if ($description): ?>
            <p class="acf-product-cards__description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php if ($button_url && $button_text): ?>
            <a href="<?php echo esc_url($button_url); ?>" class="acf-product-cards__button"<?php echo $button_rel ? ' rel="' . esc_attr($button_rel) . '"' : ''; ?>>
                <?php echo esc_html($button_text); ?>
            </a>
        <?php endif; ?>
        <?php if ($text_link && $text_link_url): ?>
            <p class="acf-product-cards__link">
                <a href="<?php echo esc_url($text_link_url); ?>"<?php echo $text_link_rel ? ' rel="' . esc_attr($text_link_rel) . '"' : ''; ?>>
                    <?php echo esc_html($text_link); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</div>
