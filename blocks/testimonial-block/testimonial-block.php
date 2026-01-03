<?php
/**
 * Testimonial Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$quote       = get_field( 'acf_testimonial_quote' );
$author_name = get_field( 'acf_testimonial_author_name' );
$author_title = get_field( 'acf_testimonial_author_title' );
$author_image = get_field( 'acf_testimonial_author_image' );
$author_image_url = get_field( 'acf_testimonial_author_image_url' );
$rating      = get_field( 'acf_testimonial_rating' );

// Determine image source - direct URL takes priority
$img_src = '';
$img_alt = $author_name ?: 'Author';
if ( $author_image_url ) {
    $img_src = $author_image_url;
} elseif ( $author_image ) {
    $img_src = $author_image['url'];
    $img_alt = $author_image['alt'] ?: $img_alt;
}

$custom_class = get_field( 'acf_testimonial_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_testimonial_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$unique_id = 'acf-testimonial-' . ( $block['id'] ?? uniqid() );

// Detect style variation from className
$className = isset( $block['className'] ) ? $block['className'] : '';
$style_variation = '';
if ( strpos( $className, 'is-style-dark' ) !== false ) {
	$style_variation = 'dark';
} elseif ( strpos( $className, 'is-style-card' ) !== false ) {
	$style_variation = 'card';
} elseif ( strpos( $className, 'is-style-minimal' ) !== false ) {
	$style_variation = 'minimal';
} elseif ( strpos( $className, 'is-style-bubble' ) !== false ) {
	$style_variation = 'bubble';
}
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="acf-testimonial-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    // Output inline styles for style variations
    if ( $style_variation === 'dark' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark {
            background-color: #1a1a2e;
            border-left-color: #ffd700;
            color: #ffffff;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-testimonial-quote,
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-testimonial-quote p {
            color: #e0e0e0;
            background: transparent;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-testimonial-quote-icon {
            color: #ffd700;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-testimonial-author-name {
            color: #ffffff;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-testimonial-author-title {
            color: #a0a0a0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-testimonial-rating .acf-testimonial-star {
            color: #555;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    elseif ( $style_variation === 'card' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card {
            border-left: none;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
            padding: 2.5rem;
            text-align: center;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-testimonial-quote-icon {
            margin: 0 auto 1rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-testimonial-author {
            flex-direction: column;
            gap: 0.75rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-testimonial-author-image img {
            width: 80px;
            height: 80px;
            border: 4px solid #f0f0f0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-testimonial-rating {
            justify-content: center;
        }
        @media (max-width: 768px) {
            #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-testimonial-author {
                align-items: center;
            }
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    elseif ( $style_variation === 'minimal' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal {
            background: transparent;
            border-left: none;
            box-shadow: none;
            border-radius: 0;
            padding: 1rem 0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-testimonial-quote {
            border-left: 3px solid #e0e0e0;
            padding-left: 1.5rem;
            font-style: normal;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-testimonial-quote-icon {
            display: none;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-testimonial-author-image img {
            width: 48px;
            height: 48px;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    elseif ( $style_variation === 'bubble' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bubble {
            background: #f5f5f5;
            border-left: none;
            border-radius: 20px;
            position: relative;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bubble::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 40px;
            border-width: 20px 20px 0;
            border-style: solid;
            border-color: #f5f5f5 transparent transparent;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bubble .acf-testimonial-quote-icon {
            display: none;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bubble .acf-testimonial-quote,
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bubble .acf-testimonial-quote p {
            margin-bottom: 0;
            background: transparent;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bubble .acf-testimonial-author {
            margin-top: 2.5rem;
            padding-left: 0;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    endif;
    ?>
    <?php if ( $quote ) : ?>
        <blockquote class="acf-testimonial-quote">
            <span class="acf-testimonial-quote-icon">&ldquo;</span>
            <?php echo wp_kses_post( $quote ); ?>
        </blockquote>
    <?php endif; ?>

    <div class="acf-testimonial-author">
        <?php if ( $img_src ) : ?>
            <div class="acf-testimonial-author-image">
                <img src="<?php echo esc_url( $img_src ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" decoding="async" />
            </div>
        <?php endif; ?>

        <div class="acf-testimonial-author-details">
            <?php if ( $author_name ) : ?>
                <div class="acf-testimonial-author-name"><?php echo esc_html( $author_name ); ?></div>
            <?php endif; ?>

            <?php if ( $author_title ) : ?>
                <div class="acf-testimonial-author-title"><?php echo esc_html( $author_title ); ?></div>
            <?php endif; ?>

            <?php if ( $rating && $rating > 0 ) : ?>
                <div class="acf-testimonial-rating">
                    <?php
                    for ( $i = 1; $i <= 5; $i++ ) {
                        echo $i <= $rating ? '<span class="acf-testimonial-star acf-testimonial-star-filled">★</span>' : '<span class="acf-testimonial-star">☆</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
