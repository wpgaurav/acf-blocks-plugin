<?php
$title = acf_blocks_get_field('pc_block_title', $block);
$title_color = acf_blocks_get_field('pc_block_title_color', $block);
$title_bg_color = acf_blocks_get_field('pc_block_title_bg_color', $block);
$image = acf_blocks_get_field('pc_block_product_image', $block);
$description = acf_blocks_get_field('pc_block_description', $block);
$root_class = acf_blocks_get_field('pc_block_root_class', $block);
$button_text = acf_blocks_get_field('pc_block_button_text', $block);
$button_url = acf_blocks_get_field('pc_block_button_url', $block);
$button_rel = acf_blocks_get_field('pc_block_button_rel', $block);
$text_link = acf_blocks_get_field('pc_block_text_link', $block);
$text_link_url = acf_blocks_get_field('pc_block_text_link_url', $block);
$text_link_rel = acf_blocks_get_field('pc_block_text_link_rel', $block);
?>

<div class="acf-product-cards <?php echo esc_attr($root_class); ?>">
    <div class="acf-product-cards__header" style="background-color: <?php echo esc_attr($title_bg_color); ?>; color: <?php echo esc_attr($title_color); ?>;">
        <h2><?php echo esc_html($title); ?></h2>
    </div>
    <?php if ($image): ?>
        <div class="acf-product-cards__image">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async" />
        </div>
    <?php endif; ?>
    <div class="acf-product-cards__content">
        <p><?php echo esc_html($description); ?></p>
        <?php if ($button_url && $button_text): ?>
            <a href="<?php echo esc_url($button_url); ?>" class="acf-product-cards__button" rel="<?php echo esc_attr($button_rel); ?>">
                <?php echo esc_html($button_text); ?>
            </a>
        <?php endif; ?>
        <?php if ($text_link && $text_link_url): ?>
            <p class="acf-product-cards__link">
                <a href="<?php echo esc_url($text_link_url); ?>" rel="<?php echo esc_attr($text_link_rel); ?>">
                    <?php echo esc_html($text_link); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</div>
