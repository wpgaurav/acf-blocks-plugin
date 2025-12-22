<?php
$title = get_field('pc_block_title');
$title_color = get_field('pc_block_title_color');
$title_bg_color = get_field('pc_block_title_bg_color');
$image = get_field('pc_block_product_image');
$description = get_field('pc_block_description');
$root_class = get_field('pc_block_root_class');
$button_text = get_field('pc_block_button_text');
$button_url = get_field('pc_block_button_url');
$button_rel = get_field('pc_block_button_rel');
$text_link = get_field('pc_block_text_link');
$text_link_url = get_field('pc_block_text_link_url');
$text_link_rel = get_field('pc_block_text_link_rel');
?>

<div class="product-card <?php echo esc_attr($root_class); ?>">
    <div class="product-header" style="background-color: <?php echo esc_attr($title_bg_color); ?>; color: <?php echo esc_attr($title_color); ?>;">
        <h2><?php echo esc_html($title); ?></h2>
    </div>
    <?php if ($image): ?>
        <div class="product-image">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
        </div>
    <?php endif; ?>
    <div class="product-content">
        <p><?php echo esc_html($description); ?></p>
        <?php if ($button_url && $button_text): ?>
            <a href="<?php echo esc_url($button_url); ?>" class="product-button" rel="<?php echo esc_attr($button_rel); ?>">
                <?php echo esc_html($button_text); ?>
            </a>
        <?php endif; ?>
        <?php if ($text_link && $text_link_url): ?>
            <p class="product-link">
                <a href="<?php echo esc_url($text_link_url); ?>" rel="<?php echo esc_attr($text_link_rel); ?>">
                    <?php echo esc_html($text_link); ?>
                </a>
            </p>
        <?php endif; ?>
    </div>
</div>
