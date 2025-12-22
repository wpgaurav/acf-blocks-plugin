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

$unique_id = 'accordion-' . ( $block['id'] ?? uniqid() );
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="accordion<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    if ( $groups && is_array( $groups ) && count( $groups ) > 0 ) :
        $index = 0;
        foreach ( $groups as $group ) :
            $is_first = ( 0 === $index );
            ?>
            <details class="accordion-item"<?php echo $is_first ? ' open' : ''; ?>>
                <summary class="accordion-title">
                    <?php echo do_shortcode( wp_kses_post( $group['acf_accord_group_title'] ) ); ?>
                    <span class="accordion-icon" aria-hidden="true"></span>
                </summary>
                <div class="accordion-content">
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
