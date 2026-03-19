<?php
/**
 * Product Box Block Template.
 *
 * Amazon-style product listing with badge, pricing, features, and multiple CTA buttons.
 * Supports both light and dark mode themes.
 *
 * @var array   $block       The block settings and attributes.
 * @var string  $content     The block inner HTML.
 * @var bool    $is_preview  True during AJAX preview.
 * @var int     $post_id     The post ID this block is saved to.
 */

// Retrieve field values using the compatibility helper
$image            = acf_blocks_get_field('pb_image', $block);
$image_url        = acf_blocks_get_field('pb_image_url', $block);
$badge_text       = acf_blocks_get_field('pb_badge_text', $block);
$badge_color      = acf_blocks_get_field('pb_badge_color', $block) ?: '#22c55e';
$title            = acf_blocks_get_field('pb_title', $block);
$title_url        = acf_blocks_get_field('pb_title_url', $block);
$title_tag_raw    = acf_blocks_get_field('pb_title_tag', $block);
$title_tag        = in_array($title_tag_raw, ['p', 'h2', 'h3', 'h4', 'h5', 'h6'], true) ? $title_tag_raw : 'p';
$rating           = acf_blocks_get_field('pb_rating', $block);
$rating_count     = acf_blocks_get_field('pb_rating_count', $block);
$original_price   = acf_blocks_get_field('pb_original_price', $block);
$discount_percent = acf_blocks_get_field('pb_discount_percent', $block);
$current_price    = acf_blocks_get_field('pb_current_price', $block);
$price_note       = acf_blocks_get_field('pb_price_note', $block);
$description      = acf_blocks_get_field('pb_description', $block);

// Get features repeater
$features = acf_blocks_get_repeater('pb_features', ['pb_feature_text'], $block);

// Get buttons repeater
$buttons = acf_blocks_get_repeater('pb_buttons', ['pb_cta_text', 'pb_cta_url', 'pb_cta_style', 'pb_cta_icon', 'pb_cta_class', 'pb_cta_rel'], $block);

// Detect style variations
$className = $block['className'] ?? '';
$is_no_image = strpos($className, 'is-style-no-image') !== false;
$is_top_image = strpos($className, 'is-style-top-image') !== false;

// Resolve image source using the smart sizing helper
$image_size = $is_top_image ? 'product-box-wide' : 'product-box-image';
$resolved_image = acf_product_box_resolve_image( $image, $image_url, $title ?: 'Product image', $image_size );
$img_src = $resolved_image['src'];
$img_alt = $resolved_image['alt'];

// Fallback to no-image layout when no image is available
if ( ! $img_src && ! $is_no_image ) {
    $is_no_image = true;
    $is_top_image = false;
    $className .= ' is-style-no-image';
}

// Block wrapper attributes
$wrapper_classes = 'acf-product-box';
if ( $is_no_image && strpos($block['className'] ?? '', 'is-style-no-image') === false ) {
    $wrapper_classes .= ' is-style-no-image';
}
$wrapper_attributes = get_block_wrapper_attributes(['class' => $wrapper_classes]);
?>

<div <?php echo $wrapper_attributes; ?> data-acf-block="product-box">
    <?php if ($badge_text) : ?>
        <div class="acf-product-box__badge" style="background-color: <?php echo esc_attr($badge_color); ?>;">
            <?php echo esc_html($badge_text); ?>
        </div>
    <?php endif; ?>

    <?php if ($is_top_image && $img_src) : ?>
        <div class="acf-product-box__hero-image">
            <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async" />
        </div>
    <?php endif; ?>

    <div class="acf-product-box__layout">
        <?php if (!$is_no_image && !$is_top_image && $img_src) : ?>
            <div class="acf-product-box__image">
                <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async" />
            </div>
        <?php endif; ?>

        <div class="acf-product-box__content">
            <?php if ($title) : ?>
                <<?php echo $title_tag; ?> class="acf-product-box__title">
                    <?php if ($title_url) : ?>
                        <a href="<?php echo esc_url($title_url); ?>"><?php echo esc_html($title); ?></a>
                    <?php else : ?>
                        <?php echo esc_html($title); ?>
                    <?php endif; ?>
                </<?php echo $title_tag; ?>>
            <?php endif; ?>

            <?php if ($rating && $rating > 0) : ?>
                <div class="acf-product-box__rating">
                    <span class="acf-product-box__stars">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($rating >= $i) {
                                echo '<span class="star star--full">★</span>';
                            } elseif ($rating >= ($i - 0.5)) {
                                echo '<span class="star star--half">★</span>';
                            } else {
                                echo '<span class="star star--empty">★</span>';
                            }
                        }
                        ?>
                    </span>
                    <?php if ($rating_count) : ?>
                        <span class="acf-product-box__rating-count"><?php echo esc_html($rating_count); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($features)) : ?>
                <ul class="acf-product-box__features">
                    <?php foreach ($features as $feature) : ?>
                        <?php if (!empty($feature['pb_feature_text'])) : ?>
                            <li><?php echo esc_html($feature['pb_feature_text']); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($original_price || $current_price || $description || !empty($buttons)) : ?>
    <div class="acf-product-box__bottom">
        <?php if ($original_price || $current_price) : ?>
            <div class="acf-product-box__pricing">
                <?php if ($original_price) : ?>
                    <span class="acf-product-box__original-price"><?php echo esc_html($original_price); ?></span>
                <?php endif; ?>
                <?php if ($discount_percent) : ?>
                    <span class="acf-product-box__discount"><?php echo esc_html($discount_percent); ?></span>
                <?php endif; ?>
                <?php if ($current_price) : ?>
                    <span class="acf-product-box__current-price"><?php echo esc_html($current_price); ?></span>
                <?php endif; ?>
            </div>
            <?php if ($price_note) : ?>
                <div class="acf-product-box__price-note"><?php echo esc_html($price_note); ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($description) : ?>
            <div class="acf-product-box__description">
                <?php echo wp_kses_post($description); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($buttons)) : ?>
            <div class="acf-product-box__buttons">
                <?php
                $btn_index = 1;
                foreach ($buttons as $button) :
                    $cta_text  = $button['pb_cta_text'] ?? '';
                    $cta_url   = $button['pb_cta_url'] ?? '';
                    $cta_style = $button['pb_cta_style'] ?? 'primary';
                    $cta_icon  = $button['pb_cta_icon'] ?? 'none';
                    $cta_class = $button['pb_cta_class'] ?? '';
                    $cta_rel   = $button['pb_cta_rel'] ?? '';

                    if (!$cta_text || !$cta_url) continue;

                    $btn_classes = [
                        'acf-product-box__btn',
                        'acf-product-box__btn--' . esc_attr($cta_style),
                        'btn-' . $btn_index
                    ];
                    if ($cta_class) {
                        $btn_classes[] = $cta_class;
                    }
                    $class_attr = implode(' ', $btn_classes);
                    $rel_attr = $cta_rel ? ' rel="' . esc_attr($cta_rel) . '"' : '';
                ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="<?php echo esc_attr($class_attr); ?>"<?php echo $rel_attr; ?>>
                        <?php if ($cta_icon !== 'none') : ?><i class="md-icon-<?php echo esc_attr($cta_icon); ?>" aria-hidden="true"></i> <?php endif; ?><?php echo esc_html($cta_text); ?>
                    </a>
                <?php
                    $btn_index++;
                endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

