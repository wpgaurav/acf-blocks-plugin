<?php
/**
 * Product Review Block Template
 *
 * @param array $block The block settings and attributes.
 */

// Get block attributes
$align = $block['align'] ?? '';
$className = $block['className'] ?? '';
$anchor = $block['anchor'] ?? '';

// Build classes
$classes = ['acf-product-review'];
if (!empty($align)) {
    $classes[] = 'align' . $align;
}
if (!empty($className)) {
    $classes[] = $className;
}

// Generate unique ID for scoped styles
$block_id = 'pr-' . uniqid();
$anchor_attr = !empty($anchor) ? ' id="' . esc_attr($anchor) . '"' : '';

/**
 * Render stars as inline SVG for performance
 */
if (!function_exists('acf_render_star_svg')) {
    function acf_render_star_svg($rating, $size = 20) {
        $fullStars = floor($rating);
        $hasHalf = ($rating - $fullStars) >= 0.5;
        $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
        $output = '';
        $color = '#ffb400';
        $emptyColor = '#e0e0e0';

        // Full star SVG
        $fullSvg = '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="' . $color . '" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

        // Half star SVG
        $halfSvg = '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="half-' . uniqid() . '"><stop offset="50%" stop-color="' . $color . '"/><stop offset="50%" stop-color="' . $emptyColor . '"/></linearGradient></defs><path fill="url(#half-' . uniqid() . ')" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

        // Empty star SVG
        $emptySvg = '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="' . $emptyColor . '" xmlns="http://www.w3.org/2000/svg"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';

        for ($i = 0; $i < $fullStars; $i++) {
            $output .= $fullSvg;
        }
        if ($hasHalf) {
            // Unique gradient ID for half star
            $gradId = 'half-grad-' . uniqid();
            $output .= '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="' . $gradId . '"><stop offset="50%" stop-color="' . $color . '"/><stop offset="50%" stop-color="' . $emptyColor . '"/></linearGradient></defs><path fill="url(#' . $gradId . ')" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>';
        }
        for ($i = 0; $i < $emptyStars; $i++) {
            $output .= $emptySvg;
        }

        return $output;
    }
}

// Get ACF fields
$product_name = acf_blocks_get_field('product_name', $block);
$show_title = acf_blocks_get_field('show_title', $block);
$title_tag_raw = acf_blocks_get_field('title_tag', $block);
$title_tag = in_array($title_tag_raw, ['p', 'h2', 'h3', 'h4', 'h5', 'h6'], true) ? $title_tag_raw : 'p';
$image_id = acf_blocks_get_field('product_image', $block);
$image_direct_url = acf_blocks_get_field('product_image_url', $block);
$overall_rating = acf_blocks_get_field('overall_rating', $block);
$features = acf_blocks_get_repeater('features', [ 'feature_name', 'feature_rating' => 'number' ], $block);
$pros = acf_blocks_get_repeater('pros', [ 'pro_text' ], $block);
$cons = acf_blocks_get_repeater('cons', [ 'con_text' ], $block);
$summary = acf_blocks_get_field('summary', $block);
$author_name = acf_blocks_get_field('author_name', $block);
$enable_json = acf_blocks_get_field('enable_json_ld', $block);

// Offer fields
$offer_url = acf_blocks_get_field('offer_url', $block) ?: '';
$offer_currency = acf_blocks_get_field('offer_price_currency', $block) ?: 'USD';
$offer_price = acf_blocks_get_field('offer_price', $block) ?: '';
$offer_cta_text = acf_blocks_get_field('offer_cta_text', $block) ?: 'Get Offer';
$payment_term = acf_blocks_get_field('payment_term', $block) ?: '';
$link_rel = acf_blocks_get_field('link_rel', $block) ?: 'nofollow sponsored';
$link_target = acf_blocks_get_field('link_target', $block) ?: '_blank';

// Schema fields
$product_brand = acf_blocks_get_field('product_brand', $block) ?: '';
$product_sku = acf_blocks_get_field('product_sku', $block) ?: '';
$product_availability = acf_blocks_get_field('product_availability', $block) ?: 'InStock';
$price_valid_until = acf_blocks_get_field('price_valid_until', $block) ?: '';
$review_date_modified = acf_blocks_get_field('review_date_modified', $block) ?: '';

// Determine image URL - direct URL takes priority
$image_url = '';
if ( $image_direct_url ) {
    $image_url = $image_direct_url;
} elseif ( $image_id ) {
    $image_url = wp_get_attachment_image_url($image_id, 'full');
}
?>

<div class="<?php echo esc_attr(implode(' ', $classes)); ?>"<?php echo $anchor_attr; ?> data-pr-id="<?php echo esc_attr($block_id); ?>">
    <?php if ($product_name && $show_title) : ?>
        <<?php echo $title_tag; ?> class="acf-product-review-title"><?php echo esc_html($product_name); ?></<?php echo $title_tag; ?>>
    <?php endif; ?>

    <?php if ($image_url) : ?>
        <img class="acf-product-review-image" src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($product_name); ?>" loading="lazy" decoding="async" />
    <?php endif; ?>

    <?php if ($overall_rating) : ?>
        <div class="acf-product-review-overall-rating">
            <div class="acf-product-review-rating-stars">
                <?php echo acf_render_star_svg($overall_rating, 24); ?>
            </div>
            <div class="acf-product-review-rating-number">
                <?php echo number_format($overall_rating, 1); ?>/5
            </div>
        </div>
    <?php endif; ?>

    <?php if ($features) : ?>
        <div class="acf-product-review-feature-ratings">
            <h4>Feature Ratings</h4>
            <ul>
                <?php foreach ($features as $feature) : ?>
                    <li>
                        <span class="acf-product-review-feature-name"><?php echo esc_html($feature['feature_name']); ?></span>
                        <span class="acf-product-review-feature-rating">
                            <?php echo acf_render_star_svg($feature['feature_rating'], 16); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="acf-product-review-pros-cons">
        <?php if ($pros) : ?>
            <div class="acf-product-review-pros">
                <h4>Pros</h4>
                <ul class="acf-product-review-list-checkmark">
                    <?php foreach ($pros as $pro) : ?>
                        <li><?php echo esc_html($pro['pro_text']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($cons) : ?>
            <div class="acf-product-review-cons">
                <h4>Cons</h4>
                <ul class="acf-product-review-list-cross">
                    <?php foreach ($cons as $con) : ?>
                        <li><?php echo esc_html($con['con_text']); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($summary) : ?>
        <div class="acf-product-review-summary">
            <h4>Summary</h4>
            <?php echo wp_kses_post(wpautop($summary)); ?>
        </div>
    <?php endif; ?>

    <?php if ($offer_price) : ?>
        <p class="acf-product-review-price">
            <span class="acf-product-review-bold">Price:</span> <?php echo esc_html($offer_currency); ?> <?php echo esc_html($offer_price); ?> <?php echo esc_html($payment_term); ?>
        </p>
    <?php endif; ?>

    <?php if ($offer_url) : ?>
        <a href="<?php echo esc_url($offer_url); ?>"<?php echo $link_rel ? ' rel="' . esc_attr($link_rel) . '"' : ''; ?> target="<?php echo esc_attr($link_target); ?>" class="acf-product-review-button"><?php echo esc_html($offer_cta_text); ?></a>
    <?php endif; ?>

    <?php if ($enable_json && $product_name) : ?>
    <?php
    // Build comprehensive Google Product schema
    $json_data = [
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $product_name,
    ];

    if ($image_url) {
        $json_data['image'] = $image_url;
    }

    if ($summary) {
        $json_data['description'] = wp_strip_all_tags($summary);
    }

    if ($product_brand) {
        $json_data['brand'] = [
            '@type' => 'Brand',
            'name' => $product_brand
        ];
    }

    if ($product_sku) {
        $json_data['sku'] = $product_sku;
    }

    // Review data
    $json_data['review'] = [
        '@type' => 'Review',
        'reviewRating' => [
            '@type' => 'Rating',
            'ratingValue' => $overall_rating,
            'bestRating' => 5,
            'worstRating' => 1
        ],
        'datePublished' => get_the_date('c'),
    ];

    // Add dateModified if set (signals freshness to Google)
    if ($review_date_modified) {
        $json_data['review']['dateModified'] = $review_date_modified;
    }

    // Add reviewBody (full review text for rich snippets)
    if ($summary) {
        $json_data['review']['reviewBody'] = wp_strip_all_tags($summary);
    }

    if ($author_name) {
        $json_data['review']['author'] = [
            '@type' => 'Person',
            'name' => $author_name
        ];
    }

    // Positive notes (pros)
    if (!empty($pros) && is_array($pros)) {
        $json_data['review']['positiveNotes'] = [
            '@type' => 'ItemList',
            'itemListElement' => []
        ];
        $pos_index = 1;
        foreach ($pros as $pro) {
            if (!empty($pro['pro_text'])) {
                $json_data['review']['positiveNotes']['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $pos_index,
                    'name' => $pro['pro_text']
                ];
                $pos_index++;
            }
        }
    }

    // Negative notes (cons)
    if (!empty($cons) && is_array($cons)) {
        $json_data['review']['negativeNotes'] = [
            '@type' => 'ItemList',
            'itemListElement' => []
        ];
        $neg_index = 1;
        foreach ($cons as $con) {
            if (!empty($con['con_text'])) {
                $json_data['review']['negativeNotes']['itemListElement'][] = [
                    '@type' => 'ListItem',
                    'position' => $neg_index,
                    'name' => $con['con_text']
                ];
                $neg_index++;
            }
        }
    }

    // Offer data
    if ($offer_url || $offer_price) {
        $json_data['offers'] = [
            '@type' => 'Offer',
            'availability' => 'https://schema.org/' . $product_availability
        ];

        if ($offer_url) {
            $json_data['offers']['url'] = $offer_url;
        }

        if ($offer_price) {
            $json_data['offers']['price'] = $offer_price;
            $json_data['offers']['priceCurrency'] = $offer_currency;
        }

        // Add priceValidUntil (recommended by Google for Offer schema)
        // Default to December 31st of current year if not set
        $json_data['offers']['priceValidUntil'] = $price_valid_until ?: date('Y') . '-12-31';
    }
    ?>
    <script type="application/ld+json">
    <?php echo wp_json_encode($json_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php endif; ?>
</div>
