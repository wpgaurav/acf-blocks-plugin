<?php
/**
 * Accordion Block Template.
 *
 * Uses native HTML <details>/<summary> elements for zero-JavaScript accordion functionality.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$groups            = get_field( 'acf_accord_groups' );
$enable_faq_schema = get_field( 'acf_accord_enable_faq_schema' ) ? true : false;

$custom_class = get_field( 'acf_accordion_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style      = get_field( 'acf_accordion_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$unique_id = 'acf-accordion-' . ( $block['id'] ?? uniqid() );

// Detect style variation from className
$className = isset( $block['className'] ) ? $block['className'] : '';
$style_variation = '';
if ( strpos( $className, 'is-style-card' ) !== false ) {
	$style_variation = 'card';
} elseif ( strpos( $className, 'is-style-dark' ) !== false ) {
	$style_variation = 'dark';
} elseif ( strpos( $className, 'is-style-minimal' ) !== false ) {
	$style_variation = 'minimal';
} elseif ( strpos( $className, 'is-style-bordered' ) !== false ) {
	$style_variation = 'bordered';
}
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="acf-accordion<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    // Output inline styles for style variations
    if ( $style_variation === 'card' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 0.5rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-accordion-item {
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            overflow: hidden;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-accordion-title {
            padding: 1rem 1.25rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-accordion-content {
            padding: 0 1.25rem 1rem;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    elseif ( $style_variation === 'dark' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark {
            background: #1a1a2e;
            border-radius: 12px;
            padding: 1.5rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-accordion-item {
            border-bottom: 1px solid #3d3d5c;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-accordion-item:last-child {
            border-bottom: none;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-accordion-title {
            color: #fff;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-accordion-content,
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-accordion-content p {
            color: #b0b0b0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-accordion-icon::before,
        #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-accordion-icon::after {
            background-color: #ffd700;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    elseif ( $style_variation === 'minimal' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-accordion-item {
            border-bottom: 1px solid #e0e0e0;
            margin-bottom: 0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-accordion-title {
            padding: 1rem 0;
            font-weight: 500;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-accordion-icon {
            width: 1rem;
            height: 1rem;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    elseif ( $style_variation === 'bordered' ) :
        ob_start();
        ?>
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-accordion-item {
            border: 2px solid #1a1a1a;
            margin-bottom: 0.5rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-accordion-title {
            padding: 1rem 1.25rem;
            background: #fff;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-accordion-item[open] .acf-accordion-title {
            border-bottom: 2px solid #1a1a1a;
        }
        #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-accordion-content {
            padding: 1rem 1.25rem;
            background: #fff;
        }
        <?php
        $css = ob_get_clean();
        echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    endif;
    ?>
    <?php
    if ( $groups && is_array( $groups ) && count( $groups ) > 0 ) :
        $index = 0;
        foreach ( $groups as $group ) :
            $is_first = ( 0 === $index );
            ?>
            <details class="acf-accordion-item"<?php echo $is_first ? ' open' : ''; ?>>
                <summary class="acf-accordion-title">
                    <?php echo do_shortcode( wp_kses_post( $group['acf_accord_group_title'] ) ); ?>
                    <span class="acf-accordion-icon" aria-hidden="true"></span>
                </summary>
                <div class="acf-accordion-content">
                    <?php echo wpautop( do_shortcode( wp_kses_post( $group['acf_accord_group_content'] ) ) ); ?>
                </div>
            </details>
            <?php
            $index++;
        endforeach;
    else :
        if ( $is_preview ) {
            echo '<p><em>' . esc_html__( 'No accordion groups added. Please add some groups.', 'acf-blocks' ) . '</em></p>';
        }
    endif;
    ?>
</div>

<?php
if ( $enable_faq_schema && $groups && is_array( $groups ) && count( $groups ) > 0 ) :
    $faq_schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array(),
    );

    foreach ( $groups as $group ) {
        $faq_schema['mainEntity'][] = array(
            '@type'          => 'Question',
            'name'           => wp_strip_all_tags( do_shortcode( $group['acf_accord_group_title'] ) ),
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => wp_strip_all_tags( do_shortcode( $group['acf_accord_group_content'] ) ),
            ),
        );
    }
    ?>
    <script type="application/ld+json">
        <?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
    </script>
<?php endif; ?>
