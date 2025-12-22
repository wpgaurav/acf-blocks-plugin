<?php
/**
 * FAQ Block Template.
 *
 * Uses native HTML <details>/<summary> elements for zero-JavaScript FAQ functionality.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$faq_items     = get_field( 'acf_faq_items' );
$enable_schema = get_field( 'acf_faq_enable_schema' ) ? true : false;

$custom_class = get_field( 'acf_faq_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style      = get_field( 'acf_faq_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';
?>

<div class="faq-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    if ( $faq_items && is_array( $faq_items ) && count( $faq_items ) > 0 ) :
        foreach ( $faq_items as $index => $item ) :
            $question = $item['acf_faq_question'];
            $answer   = $item['acf_faq_answer'];
            ?>
            <details class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                <summary class="faq-question">
                    <span itemprop="name"><?php echo esc_html( $question ); ?></span>
                    <span class="faq-icon" aria-hidden="true"></span>
                </summary>
                <div class="faq-answer" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div itemprop="text">
                        <?php echo wpautop( do_shortcode( wp_kses_post( $answer ) ) ); ?>
                    </div>
                </div>
            </details>
            <?php
        endforeach;
    else :
        if ( $is_preview ) {
            echo '<p><em>' . esc_html__( 'No FAQ items added. Please add some questions and answers.', 'acf-blocks' ) . '</em></p>';
        }
    endif;
    ?>
</div>

<?php
if ( $enable_schema && $faq_items && is_array( $faq_items ) && count( $faq_items ) > 0 ) :
    $faq_schema = array(
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array(),
    );

    foreach ( $faq_items as $item ) {
        $faq_schema['mainEntity'][] = array(
            '@type'          => 'Question',
            'name'           => $item['acf_faq_question'],
            'acceptedAnswer' => array(
                '@type' => 'Answer',
                'text'  => wp_strip_all_tags( do_shortcode( $item['acf_faq_answer'] ) ),
            ),
        );
    }
    ?>
    <script type="application/ld+json">
        <?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
    </script>
<?php endif; ?>
