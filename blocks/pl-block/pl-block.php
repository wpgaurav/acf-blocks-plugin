<?php
/**
 * Product List Block Template.
 *
 * Ranked product listing with icon, pricing tiers, coupon codes, and CTA buttons.
 * Designed for "best of" lists and product roundups.
 *
 * @var array   $block       The block settings and attributes.
 * @var string  $content     The block inner HTML.
 * @var bool    $is_preview  True during AJAX preview.
 * @var int     $post_id     The post ID this block is saved to.
 */

// Retrieve field values
$rank         = acf_blocks_get_field('pl_block_rank', $block);
$icon         = acf_blocks_get_field('pl_block_icon', $block);
$image_url    = acf_blocks_get_field('pl_block_image_url', $block);
$product_name = acf_blocks_get_field('pl_block_product_name', $block);
$product_url  = acf_blocks_get_field('pl_block_product_url', $block);
$title_tag_raw = acf_blocks_get_field('pl_block_title_tag', $block);
$title_tag    = in_array($title_tag_raw, ['p', 'h2', 'h3', 'h4', 'h5', 'h6'], true) ? $title_tag_raw : 'p';
$description  = acf_blocks_get_field('pl_block_description', $block);
$image_width  = acf_blocks_get_field('pl_block_image_width', $block);
$width_style  = $image_width ? $image_width : '64px';

// Get repeaters with sub-field names matching block-data.json
$pricing = acf_blocks_get_repeater('pl_block_pricing', ['pl_block_pricing_title', 'pl_block_pricing_amount'], $block);
$coupons = acf_blocks_get_repeater('pl_block_coupons', ['pl_block_coupon_code', 'pl_block_coupon_offer'], $block);
$buttons = acf_blocks_get_repeater('pl_block_buttons', ['pl_block_button_text', 'pl_block_button_url', 'pl_block_button_style', 'pl_block_button_rel', 'pl_block_button_class'], $block);

// Resolve image source — direct URL takes priority over uploaded image
$img_src = '';
$img_alt = $product_name ?: 'Product';
if ($image_url) {
    $img_src = $image_url;
} elseif ($icon && is_array($icon)) {
    $img_src = $icon['url'] ?? '';
    $img_alt = !empty($icon['alt']) ? $icon['alt'] : $img_alt;
}

// Block wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'acf-pl-block']);
?>

<div <?php echo $wrapper_attributes; ?> data-acf-block="pl-block">
    <div class="acf-pl-block__header">
        <?php if ($rank) : ?>
            <div class="acf-pl-block__rank"><?php echo esc_html($rank); ?></div>
        <?php endif; ?>

        <?php if ($img_src) : ?>
            <div class="acf-pl-block__icon">
                <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async" style="width:<?php echo esc_attr($width_style); ?>;height:auto;" />
            </div>
        <?php endif; ?>

        <?php if ($product_name) : ?>
            <<?php echo $title_tag; ?> class="acf-pl-block__name">
                <?php if ($product_url) : ?>
                    <a href="<?php echo esc_url($product_url); ?>"><?php echo esc_html($product_name); ?></a>
                <?php else : ?>
                    <?php echo esc_html($product_name); ?>
                <?php endif; ?>
            </<?php echo $title_tag; ?>>
        <?php endif; ?>
    </div>

    <?php if ($description) : ?>
        <div class="acf-pl-block__description">
            <?php echo wp_kses_post($description); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($pricing) || !empty($coupons)) : ?>
    <div class="acf-pl-block__info">
        <?php if (!empty($pricing)) : ?>
            <div class="acf-pl-block__pricing">
                <div class="acf-pl-block__section-label">Pricing</div>
                <div class="acf-pl-block__pricing-list">
                    <?php foreach ($pricing as $item) : ?>
                        <?php if (!empty($item['pl_block_pricing_title'])) : ?>
                            <div class="acf-pl-block__pricing-item">
                                <span class="acf-pl-block__pricing-label"><?php echo esc_html($item['pl_block_pricing_title']); ?></span>
                                <span class="acf-pl-block__pricing-value"><?php echo esc_html($item['pl_block_pricing_amount']); ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($coupons)) : ?>
            <div class="acf-pl-block__coupons">
                <div class="acf-pl-block__section-label">Coupons</div>
                <?php foreach ($coupons as $coupon) : ?>
                    <?php if (!empty($coupon['pl_block_coupon_code'])) : ?>
                        <div class="acf-pl-block__coupon">
                            <span class="acf-pl-block__coupon-code"><?php echo esc_html($coupon['pl_block_coupon_code']); ?></span>
                            <?php if (!empty($coupon['pl_block_coupon_offer'])) : ?>
                                <span class="acf-pl-block__coupon-offer"><?php echo esc_html($coupon['pl_block_coupon_offer']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($buttons)) : ?>
        <div class="acf-pl-block__buttons">
            <?php foreach ($buttons as $button) :
                $btn_text  = $button['pl_block_button_text'] ?? '';
                $btn_url   = $button['pl_block_button_url'] ?? '';
                $btn_style = $button['pl_block_button_style'] ?? 'primary';
                $btn_rel   = $button['pl_block_button_rel'] ?? '';
                $btn_class = $button['pl_block_button_class'] ?? '';

                if (!$btn_text || !$btn_url) continue;

                $classes = ['acf-pl-block__btn', 'acf-pl-block__btn--' . esc_attr($btn_style)];
                if ($btn_class) {
                    $classes[] = $btn_class;
                }
                $class_attr = implode(' ', $classes);
                $rel_attr = $btn_rel ? ' rel="' . esc_attr($btn_rel) . '"' : '';
            ?>
                <a href="<?php echo esc_url($btn_url); ?>" class="<?php echo esc_attr($class_attr); ?>"<?php echo $rel_attr; ?>><?php echo esc_html($btn_text); ?></a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>