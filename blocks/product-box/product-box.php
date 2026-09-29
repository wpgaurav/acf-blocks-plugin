<?php
/**
 * Product Box Block Template.
 *
 * Amazon-style product listing with badge, pricing, features, and multiple CTA buttons.
 * Supports both light and dark mode themes.
 *
 * Every field added after 2.11.4 is optional and off when empty, so a block
 * saved by an older version renders the same box it always did.
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
$title_tag        = acf_blocks_validate_heading_tag( $title_tag_raw, 'p' );
$rating           = acf_blocks_get_field('pb_rating', $block);
$rating_count     = acf_blocks_get_field('pb_rating_count', $block);
$original_price   = acf_blocks_get_field('pb_original_price', $block);
$discount_percent = acf_blocks_get_field('pb_discount_percent', $block);
$current_price    = acf_blocks_get_field('pb_current_price', $block);
$price_note       = acf_blocks_get_field('pb_price_note', $block);
$description      = acf_blocks_get_field('pb_description', $block);
$label            = acf_blocks_get_field('pb_label', $block);
$title_rel        = acf_blocks_get_field('pb_title_rel', $block);
$new_tab          = (bool) (int) acf_blocks_get_field('pb_new_tab', $block);
$disclosure       = acf_blocks_get_field('pb_disclosure', $block);
$image_fit        = acf_blocks_get_field('pb_image_fit', $block);
$image_fit        = in_array( $image_fit, [ 'contain', 'cover' ], true ) ? $image_fit : 'contain';
$image_ratio      = acf_blocks_get_field('pb_image_ratio', $block);
$image_ratio      = in_array( $image_ratio, [ 'auto', '16-9', '4-3', '1-1' ], true ) ? $image_ratio : 'auto';

// Highlights
$rank          = (int) acf_blocks_get_field('pb_rank', $block);
$rank          = $rank >= 1 && $rank <= 99 ? $rank : 0;
$score_max     = (int) acf_blocks_get_field('pb_score_max', $block);
$score_max     = in_array( $score_max, [ 5, 10, 100 ], true ) ? $score_max : 10;
$score_raw     = acf_blocks_get_field('pb_score', $block);
$score         = is_numeric( $score_raw ) ? max( 0, min( (float) $score_raw, $score_max ) ) : 0;
$score_label   = acf_blocks_get_field('pb_score_label', $block) ?: __( 'Our score', 'acf-blocks' );
$verdict       = acf_blocks_get_field('pb_verdict', $block);
$verdict_label = acf_blocks_get_field('pb_verdict_label', $block) ?: __( 'Why it wins', 'acf-blocks' );

// Price extras
$show_savings  = (bool) (int) acf_blocks_get_field('pb_show_savings', $block);
$savings       = $show_savings ? acf_product_box_savings( $original_price, $current_price ) : '';
$price_checked = acf_product_box_format_date( acf_blocks_get_field('pb_price_checked', $block) );

// Buttons and display
$cta_emphasis  = (bool) (int) acf_blocks_get_field('pb_cta_emphasis', $block);
$btn_arrow     = (bool) (int) acf_blocks_get_field('pb_btn_arrow', $block);
$btn_shine     = acf_blocks_get_field('pb_btn_shine', $block);
$btn_shine     = in_array( $btn_shine, [ 'hover', 'repeat' ], true ) ? $btn_shine : '';
$is_spotlight  = 'spotlight' === acf_blocks_get_field('pb_box_style', $block);
$accent        = acf_product_box_sanitize_hex( acf_blocks_get_field('pb_accent_color', $block) );

// Get features repeater
$features = acf_blocks_get_repeater('pb_features', ['pb_feature_text'], $block);

// Spec chips and perks, dropping empty rows
$specs = array_values( array_filter( acf_blocks_get_repeater('pb_specs', ['pb_spec_text'], $block), function ( $row ) {
    return ! empty( $row['pb_spec_text'] );
} ) );
$perks = array_values( array_filter( acf_blocks_get_repeater('pb_perks', ['pb_perk_icon', 'pb_perk_text'], $block), function ( $row ) {
    return ! empty( $row['pb_perk_text'] );
} ) );

// Get buttons repeater
$buttons = acf_blocks_get_repeater('pb_buttons', ['pb_cta_text', 'pb_cta_url', 'pb_cta_style', 'pb_cta_icon', 'pb_cta_class', 'pb_cta_rel'], $block);

// Detect style variations
$className = $block['className'] ?? '';
$is_no_image = strpos($className, 'is-style-no-image') !== false;
$is_top_image = strpos($className, 'is-style-top-image') !== false;

// Resolve image source. "Show full image" requests an uncropped size so the
// whole product survives; only "Fill & crop" uses the hard-cropped sizes.
$image_size = acf_product_box_image_size( $is_top_image, $image_fit, $image_ratio );
$resolved_image = acf_product_box_resolve_image( $image, $image_url, $title ?: 'Product image', $image_size );
$img_src = $resolved_image['src'];

$img_attrs = '';
if ( $img_src ) {
    $img_attrs = ' src="' . esc_url( $img_src ) . '" alt="' . esc_attr( $resolved_image['alt'] ) . '"';
    if ( ! empty( $resolved_image['width'] ) && ! empty( $resolved_image['height'] ) ) {
        $img_attrs .= ' width="' . (int) $resolved_image['width'] . '" height="' . (int) $resolved_image['height'] . '"';
    }
    if ( ! empty( $resolved_image['srcset'] ) ) {
        $img_attrs .= ' srcset="' . esc_attr( $resolved_image['srcset'] ) . '" sizes="' . esc_attr( $resolved_image['sizes'] ) . '"';
    }
    $img_attrs .= ' loading="lazy" decoding="async"';
}

$title_link_attrs = acf_product_box_link_attrs( $title_rel, $new_tab );

// Fallback to no-image layout when no image is available
if ( ! $img_src && ! $is_no_image ) {
    $is_no_image = true;
    $is_top_image = false;
    $className .= ' is-style-no-image';
}

// Block wrapper attributes
$wrapper_classes = 'acf-product-box has-image-fit-' . $image_fit;
if ( $is_top_image ) {
    $wrapper_classes .= ' has-image-ratio-' . $image_ratio;
}
if ( $is_no_image && strpos($block['className'] ?? '', 'is-style-no-image') === false ) {
    $wrapper_classes .= ' is-style-no-image';
}
if ( $label ) {
    $wrapper_classes .= ' has-label';
}
if ( $badge_text ) {
    $wrapper_classes .= ' has-badge';
}
if ( $is_spotlight ) {
    $wrapper_classes .= ' is-spotlight';
}
if ( $cta_emphasis ) {
    $wrapper_classes .= ' has-cta-emphasis';
}
if ( $btn_shine ) {
    $wrapper_classes .= ' has-btn-shine-' . $btn_shine;
}

$wrapper_args = [ 'class' => $wrapper_classes ];
if ( $accent ) {
    // One color drives every accent surface; the text color on it is picked for contrast.
    $wrapper_args['style'] = sprintf(
        '--pb-accent:%1$s;--pb-on-accent:%2$s;--pb-btn-primary-bg:%1$s;--pb-btn-primary-text:%2$s;--pb-btn-primary-hover:color-mix(in srgb,%1$s 85%%,black);--pb-discount-bg:%1$s;--pb-discount-text:%2$s',
        $accent,
        acf_product_box_contrast_text( $accent )
    );
}
$wrapper_attributes = get_block_wrapper_attributes( $wrapper_args );

$has_header = $rank || $score > 0;
$has_price  = $original_price || $current_price;
?>

<div <?php echo $wrapper_attributes; ?> data-acf-block="product-box">
    <?php if ($badge_text) : ?>
        <div class="acf-product-box__badge" style="background-color: <?php echo esc_attr($badge_color); ?>;">
            <?php echo esc_html($badge_text); ?>
        </div>
    <?php endif; ?>

    <?php if ($is_top_image && $img_src) : ?>
        <div class="acf-product-box__hero-image">
            <?php if ($title_url) : ?><a class="acf-product-box__image-link" href="<?php echo esc_url($title_url); ?>" tabindex="-1" aria-hidden="true"<?php echo $title_link_attrs; ?>><?php endif; ?>
            <img<?php echo $img_attrs; ?> />
            <?php if ($title_url) : ?></a><?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="acf-product-box__layout">
        <?php if (!$is_no_image && !$is_top_image && $img_src) : ?>
            <div class="acf-product-box__image">
                <?php if ($title_url) : ?><a class="acf-product-box__image-link" href="<?php echo esc_url($title_url); ?>" tabindex="-1" aria-hidden="true"<?php echo $title_link_attrs; ?>><?php endif; ?>
                <img<?php echo $img_attrs; ?> />
                <?php if ($title_url) : ?></a><?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="acf-product-box__content">
            <?php if ($label) : ?>
                <span class="acf-product-box__label"><?php echo esc_html($label); ?></span>
            <?php endif; ?>

            <?php if ($has_header) : ?><div class="acf-product-box__header"><?php endif; ?>

            <?php if ($rank) : ?>
                <span class="acf-product-box__rank"><span class="acf-product-box__rank-hash">#</span><?php echo (int) $rank; ?></span>
            <?php endif; ?>

            <?php if ($title) : ?>
                <<?php echo $title_tag; ?> class="acf-product-box__title">
                    <?php if ($title_url) : ?>
                        <a href="<?php echo esc_url($title_url); ?>"<?php echo $title_link_attrs; ?>><?php echo esc_html($title); ?></a>
                    <?php else : ?>
                        <?php echo esc_html($title); ?>
                    <?php endif; ?>
                </<?php echo $title_tag; ?>>
            <?php endif; ?>

            <?php if ($score > 0) :
                $score_text = number_format_i18n( $score, 100 === $score_max ? 0 : 1 );
                $score_pct  = round( $score / $score_max * 100, 1 );
                ?>
                <div class="acf-product-box__score" role="img" aria-label="<?php echo esc_attr( sprintf( __( '%1$s: %2$s out of %3$d', 'acf-blocks' ), $score_label, $score_text, $score_max ) ); ?>">
                    <span class="acf-product-box__score-ring" style="--pb-score:<?php echo esc_attr( $score_pct ); ?>"><span class="acf-product-box__score-value"><?php echo esc_html( $score_text ); ?></span></span>
                    <span class="acf-product-box__score-label"><?php echo esc_html( $score_label ); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($has_header) : ?></div><?php endif; ?>

            <?php if ($rating && $rating > 0) : ?>
                <div class="acf-product-box__rating">
                    <span class="acf-product-box__rating-score" aria-hidden="true"><?php echo esc_html( number_format_i18n( (float) $rating, 1 ) ); ?></span>
                    <span class="acf-product-box__stars" role="img" aria-label="<?php echo esc_attr( sprintf( __( 'Rating: %s out of 5', 'acf-blocks' ), number_format_i18n( (float) $rating, 1 ) ) ); ?>">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            if ($rating >= $i) {
                                echo '<span class="star star--full" aria-hidden="true">★</span>';
                            } elseif ($rating >= ($i - 0.5)) {
                                echo '<span class="star star--half" aria-hidden="true">★</span>';
                            } else {
                                echo '<span class="star star--empty" aria-hidden="true">★</span>';
                            }
                        }
                        ?>
                    </span>
                    <?php if ($rating_count) : ?>
                        <span class="acf-product-box__rating-count"><?php echo esc_html($rating_count); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($verdict) : ?>
                <p class="acf-product-box__verdict"><strong class="acf-product-box__verdict-label"><?php echo esc_html($verdict_label); ?></strong> <?php echo esc_html($verdict); ?></p>
            <?php endif; ?>

            <?php if (!empty($specs)) : ?>
                <ul class="acf-product-box__specs">
                    <?php foreach ($specs as $spec) : ?>
                        <li><?php echo esc_html($spec['pb_spec_text']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($features)) : ?>
                <ul class="acf-product-box__features<?php echo count($features) >= 6 ? ' has-many' : ''; ?>">
                    <?php foreach ($features as $feature) : ?>
                        <?php if (!empty($feature['pb_feature_text'])) : ?>
                            <li><?php echo esc_html($feature['pb_feature_text']); ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($has_price || $description || !empty($buttons) || $disclosure || $price_checked || !empty($perks)) : ?>
    <div class="acf-product-box__bottom">
        <?php if ($has_price || $price_checked) : ?>
            <div class="acf-product-box__price-block">
                <?php if ($has_price) : ?>
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

                <?php if ($savings) : ?>
                    <p class="acf-product-box__savings"><?php printf( esc_html( __( 'You save %s', 'acf-blocks' ) ), '<strong>' . esc_html( $savings ) . '</strong>' ); ?></p>
                <?php endif; ?>

                <?php if ($price_checked) : ?>
                    <p class="acf-product-box__price-checked"><?php printf( esc_html( __( 'Price checked %s', 'acf-blocks' ) ), '<time datetime="' . esc_attr( $price_checked['iso'] ) . '">' . esc_html( $price_checked['display'] ) . '</time>' ); ?></p>
                <?php endif; ?>
            </div>
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

                    $is_first_btn = 1 === $btn_index;
                    $btn_classes = [
                        'acf-product-box__btn',
                        'acf-product-box__btn--' . esc_attr($cta_style),
                        'btn-' . $btn_index
                    ];
                    if ($cta_emphasis) {
                        $btn_classes[] = $is_first_btn ? 'is-emphasized' : 'is-quiet';
                    }
                    if ($cta_class) {
                        $btn_classes[] = $cta_class;
                    }
                    $class_attr = implode(' ', $btn_classes);
                    $link_attrs = acf_product_box_link_attrs( $cta_rel, $new_tab );
                ?>
                    <a href="<?php echo esc_url($cta_url); ?>" class="<?php echo esc_attr($class_attr); ?>"<?php echo $link_attrs; ?>>
                        <?php if ($cta_icon !== 'none') : ?><i class="md-icon-<?php echo esc_attr($cta_icon); ?>" aria-hidden="true"></i> <?php endif; ?><?php echo esc_html($cta_text); ?>
                        <?php if ($is_first_btn && $btn_arrow) { echo acf_product_box_icon( 'arrow', 'acf-product-box__btn-arrow' ); } ?>
                        <?php if ($is_first_btn && $btn_shine) : ?><span class="acf-product-box__btn-shine" aria-hidden="true"></span><?php endif; ?>
                    </a>
                <?php
                    $btn_index++;
                endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($perks)) : ?>
            <ul class="acf-product-box__perks">
                <?php foreach ($perks as $perk) : ?>
                    <li><?php echo acf_product_box_icon( $perk['pb_perk_icon'] ?? '', 'acf-product-box__perk-icon' ) ?: acf_product_box_icon( 'check', 'acf-product-box__perk-icon' ); ?><?php echo esc_html($perk['pb_perk_text']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($disclosure) : ?>
            <p class="acf-product-box__disclosure"><?php echo esc_html($disclosure); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
