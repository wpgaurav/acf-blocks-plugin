<?php
/**
 * Helper function to convert a numeric rating (out of 5)
 * into star icons.
 *
 * @param float $rating The numeric rating.
 * @return string The HTML for star icons.
 */
if ( ! function_exists( 'render_stars' ) ) {
    function render_stars( $rating ) {
        $fullStars  = floor( $rating );
        $halfStar   = ( $rating - $fullStars ) >= 0.5 ? 1 : 0;
        $emptyStars = 5 - $fullStars - $halfStar;
        $output     = '';
        
        for ( $i = 0; $i < $fullStars; $i++ ) {
            $output .= '<i class="md-icon-star-full" aria-hidden="true"></i>';
        }
        if ( $halfStar ) {
            $output .= '<i class="md-icon-star-half" aria-hidden="true"></i>';
        }
        for ( $i = 0; $i < $emptyStars; $i++ ) {
            $output .= '<i class="md-icon-star-empty" aria-hidden="true"></i>';
        }
        return $output;
    }
}

$product_name    = get_field( 'product_name' );
$image_id        = get_field( 'product_image' );
$overall_rating  = get_field( 'overall_rating' );
$features        = get_field( 'features' );
$pros            = get_field( 'pros' );
$cons            = get_field( 'cons' );
$summary         = get_field( 'summary' );
$author_name     = get_field( 'author_name' );
$enable_json     = get_field( 'enable_json_ld' );

// New offer fields. If not set, default to an empty string.
$offer_url       = get_field( 'offer_url' ) ?: '';
$offer_currency  = get_field( 'offer_price_currency' ) ?: 'USD';
$offer_price     = get_field( 'offer_price' ) ?: '0.00';
$offer_cta_text     = get_field( 'offer_cta_text' ) ?: 'Get Offer';
$payment_term     = get_field( 'payment_term' ) ?: '';

$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'full' ) : '';
?>

<div class="product-review">
    <?php if ( $product_name ) : ?>
        <h3 class="product-title"><?php echo esc_html( $product_name ); ?></h3>
    <?php endif; ?>

    <?php if ( $image_url ) : ?>
        <img class="product-image" src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $product_name ); ?>">
    <?php endif; ?>

    <?php if ( $overall_rating ) : ?>
        <div class="overall-rating">
            <div class="rating-stars">
                <?php echo render_stars( $overall_rating ); ?>
            </div>
            <div class="rating-number">
                <?php echo number_format( $overall_rating, 1 ); ?>/5
            </div>
        </div>
    <?php endif; ?>

    <?php if ( $features ) : ?>
        <div class="feature-ratings">
            <h4>Feature Ratings</h4>
            <ul>
                <?php foreach ( $features as $feature ) : ?>
                    <li>
                        <span class="feature-name"><?php echo esc_html( $feature['feature_name'] ); ?></span>
                        <span class="feature-rating">
                            <?php echo render_stars( $feature['feature_rating'] ); ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
	
    <div class="pros-cons">
        <?php if ( $pros ) : ?>
            <div class="pros">
                <h4>Pros</h4>
                <ul class="is-style-list-checkmark">
                    <?php foreach ( $pros as $pro ) : ?>
                        <li><?php echo esc_html( $pro['pro_text'] ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ( $cons ) : ?>
            <div class="cons">
                <h4>Cons</h4>
                <ul class="is-style-list-cross">
                    <?php foreach ( $cons as $con ) : ?>
                        <li><?php echo esc_html( $con['con_text'] ); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <?php if ( $summary ) : ?>
        <div class="summary">
            <h4>Summary</h4>
            <?php echo wp_kses_post( wpautop( $summary ) ); ?>
        </div>
    <?php endif; ?>
	<?php if ( $offer_price ) : ?>
        <p>
			<span class="bold mb-single">Price:</span> <?php echo esc_html( $offer_currency ); ?> <?php echo esc_html( $offer_price ); ?> <?php echo esc_html( $payment_term ); ?>
	</p>
	<?php endif; ?>
	<?php if ( $offer_url ) : ?>
	<a href="<?php echo esc_html( $offer_url ); ?>" rel="nofollow sponsored" class="button__default"><?php echo esc_html( $offer_cta_text ); ?></a>
    <?php endif; ?>

    <?php if ( $enable_json && $product_name ) : ?>
    <?php
    $json_data = [
        '@context'    => 'https://schema.org/',
        '@type'       => 'Product',
        'name'        => $product_name,
        'image'       => $image_url,
        'description' => $summary,
        'review'      => [
            '@type'        => 'Review',
            'reviewRating' => [
                '@type'       => 'Rating',
                'ratingValue' => $overall_rating,
                'bestRating'  => 5
            ],
            'author'       => [
                '@type' => 'Person',
                'name'  => $author_name
            ],
            'positiveNotes' => [
                '@type'           => 'ItemList',
                'itemListElement' => []
            ],
            'negativeNotes' => [
                '@type'           => 'ItemList',
                'itemListElement' => []
            ]
        ],
        'offers'      => [
            '@type'         => 'Offer',
            'url'           => $offer_url,
            'priceCurrency' => $offer_currency,
            'price'         => $offer_price
        ]
    ];

    // Populate positiveNotes.
    if ( ! empty( $pros ) && is_array( $pros ) ) {
        $pos_index = 1;
        foreach ( $pros as $pro ) {
            if ( ! empty( $pro['pro_text'] ) ) {
                $json_data['review']['positiveNotes']['itemListElement'][] = [
                    '@type'    => 'ListItem',
                    'position' => $pos_index,
                    'name'     => $pro['pro_text']
                ];
                $pos_index++;
            }
        }
    }

    // Populate negativeNotes.
    if ( ! empty( $cons ) && is_array( $cons ) ) {
        $neg_index = 1;
        foreach ( $cons as $con ) {
            if ( ! empty( $con['con_text'] ) ) {
                $json_data['review']['negativeNotes']['itemListElement'][] = [
                    '@type'    => 'ListItem',
                    'position' => $neg_index,
                    'name'     => $con['con_text']
                ];
                $neg_index++;
            }
        }
    }
    ?>
    <script type="application/ld+json">
    <?php echo wp_json_encode( $json_data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ); ?>
    </script>
    <?php endif; ?>
</div>
