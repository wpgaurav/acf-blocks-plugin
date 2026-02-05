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

// Determine image source - direct URL takes priority
$img_src = '';
$img_alt = $title ?: 'Product image';
if ($image_url) {
    $img_src = $image_url;
} elseif ($image) {
    $img_src = $image['url'];
    $img_alt = $image['alt'] ?: $img_alt;
}

// Button icon SVGs
$button_icons = [
    'cart' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>',
    'amazon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M.045 18.02c.072-.116.187-.124.348-.022 3.636 2.11 7.594 3.166 11.87 3.166 2.852 0 5.668-.533 8.447-1.595l.315-.14c.138-.06.234-.1.293-.13.226-.088.39-.046.493.126.104.172.067.354-.112.546-.218.209-.512.449-.883.718a18.9 18.9 0 0 1-2.75 1.628c-1.037.51-2.186.95-3.447 1.32-.94.26-1.889.43-2.846.5-1.26.088-2.54.05-3.845-.13-2.39-.356-4.568-1.146-6.548-2.368-.125-.076-.157-.175-.095-.296l.76.077zM6.09 15.1c-.14-.266-.1-.457.12-.57 1.04-.49 2.14-.908 3.29-1.25.14-.046.26-.016.35.094.03.03.11.13.24.3.66.85 1.35 1.57 2.08 2.15.05.04.08.1.08.17 0 .14-.17.22-.52.22-1.16 0-2.28-.3-3.36-.88-.15-.07-.27-.17-.38-.31-.23-.27-.45-.56-.64-.87l-.27.96zm17.32-1.32c-.01.02-.09.14-.27.36-.18.22-.39.455-.64.695-.25.24-.485.445-.705.615-.22.17-.36.273-.42.313-.1.06-.17.055-.24-.02-.07-.08-.03-.18.12-.3l.04-.04c.22-.18.47-.43.74-.76.27-.33.51-.69.72-1.08.2-.39.33-.76.38-1.11.05-.35.03-.71-.06-1.07-.09-.36-.27-.67-.54-.91-.27-.24-.6-.39-.99-.44-.39-.05-.78-.01-1.18.11-.4.12-.75.33-1.07.62-.32.29-.57.59-.74.91-.17.32-.26.61-.27.87-.01.26.08.48.27.66.19.18.48.28.87.3.39.02.84-.05 1.34-.22.5-.17.93-.42 1.3-.75l.03-.02c.07-.05.12-.06.15-.02.03.04.01.09-.05.14-.31.31-.7.57-1.18.78-.48.21-.97.35-1.46.43-.49.08-.96.09-1.39.04-.43-.05-.78-.18-1.05-.39-.27-.21-.41-.53-.41-.95 0-.48.13-.95.4-1.42.27-.47.64-.89 1.11-1.26.47-.37.98-.65 1.53-.83.55-.18 1.08-.25 1.59-.2.51.05.95.22 1.32.5.37.28.61.71.71 1.27.1.56.03 1.07-.21 1.52z"/></svg>',
    'external' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>',
    'check' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
];

// Block wrapper attributes
$wrapper_attributes = get_block_wrapper_attributes(['class' => 'acf-product-box']);
?>

<div <?php echo $wrapper_attributes; ?>>
    <?php if ($badge_text) : ?>
        <div class="acf-product-box__badge" style="background-color: <?php echo esc_attr($badge_color); ?>;">
            <?php echo esc_html($badge_text); ?>
        </div>
    <?php endif; ?>

    <div class="acf-product-box__layout">
        <?php if ($img_src) : ?>
            <div class="acf-product-box__image">
                <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async" />
            </div>
        <?php endif; ?>

        <div class="acf-product-box__content">
            <?php if ($title) : ?>
                <h3 class="acf-product-box__title">
                    <?php if ($title_url) : ?>
                        <a href="<?php echo esc_url($title_url); ?>"><?php echo esc_html($title); ?></a>
                    <?php else : ?>
                        <?php echo esc_html($title); ?>
                    <?php endif; ?>
                </h3>
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
                    <?php foreach ($buttons as $button) :
                        $cta_text  = $button['pb_cta_text'] ?? '';
                        $cta_url   = $button['pb_cta_url'] ?? '';
                        $cta_style = $button['pb_cta_style'] ?? 'primary';
                        $cta_icon  = $button['pb_cta_icon'] ?? 'none';
                        $cta_class = $button['pb_cta_class'] ?? '';
                        $cta_rel   = $button['pb_cta_rel'] ?? '';

                        if (!$cta_text || !$cta_url) continue;

                        $btn_classes = ['acf-product-box__btn', 'acf-product-box__btn--' . esc_attr($cta_style)];
                        if ($cta_class) {
                            $btn_classes[] = $cta_class;
                        }
                        $class_attr = implode(' ', $btn_classes);
                        $rel_attr = $cta_rel ? ' rel="' . esc_attr($cta_rel) . '"' : '';
                        $icon_html = ($cta_icon !== 'none' && isset($button_icons[$cta_icon])) ? '<span class="acf-product-box__btn-icon">' . $button_icons[$cta_icon] . '</span>' : '';
                    ?>
                        <a href="<?php echo esc_url($cta_url); ?>" class="<?php echo esc_attr($class_attr); ?>"<?php echo $rel_attr; ?>>
                            <?php echo $icon_html; ?>
                            <span><?php echo esc_html($cta_text); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
