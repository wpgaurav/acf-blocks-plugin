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

$groups            = acf_blocks_get_repeater( 'acf_accord_groups', [ 'acf_accord_group_title', 'acf_accord_group_content' ], $block );
$enable_faq_schema = acf_blocks_get_field( 'acf_accord_enable_faq_schema', $block ) ? true : false;

$custom_class = acf_blocks_get_field( 'acf_accordion_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style      = acf_blocks_get_field( 'acf_accordion_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$unique_id = 'acf-accordion-' . ( $block['id'] ?? uniqid() );
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="acf-accordion<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    if ( $groups && is_array( $groups ) && count( $groups ) > 0 ) :
        $index = 0;
        foreach ( $groups as $group ) :
            $is_first = ( 0 === $index );
            ?>
            <details class="acf-accordion-item"<?php echo $is_first ? ' open' : ''; ?>>
                <summary class="acf-accordion-title">
                    <?php echo do_shortcode( wp_kses_post( $group['acf_accord_group_title'] ) ); ?>
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
