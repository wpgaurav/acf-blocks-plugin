<?php
/**
 * FAQ Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$faq_items = get_field( 'acf_faq_items' );
$enable_schema = get_field( 'acf_faq_enable_schema' ) ? true : false;

$custom_class = get_field( 'acf_faq_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_faq_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';
?>

<div class="faq-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    if ( $faq_items && is_array( $faq_items ) && count( $faq_items ) > 0 ) :
        foreach ( $faq_items as $index => $item ) :
            $question = $item['acf_faq_question'];
            $answer   = $item['acf_faq_answer'];
            $unique_id = 'faq_' . uniqid() . '_' . $index;
            ?>
            <div class="faq-item" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
                <div class="faq-question"
                     role="button"
                     tabindex="0"
                     aria-expanded="false"
                     aria-controls="<?php echo esc_attr( $unique_id ); ?>"
                     onclick="this.classList.toggle('active'); this.nextElementSibling.classList.toggle('active'); this.setAttribute('aria-expanded', this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');"
                     onkeypress="if(event.key === 'Enter' || event.key === ' ') { this.click(); }">
                    <span itemprop="name"><?php echo esc_html( $question ); ?></span>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer"
                     id="<?php echo esc_attr( $unique_id ); ?>"
                     itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
                    <div itemprop="text">
                        <?php echo wpautop( do_shortcode( $answer ) ); ?>
                    </div>
                </div>
            </div>
            <?php
        endforeach;
    else :
        if ( $is_preview ) {
            echo '<p><em>No FAQ items added. Please add some questions and answers.</em></p>';
        }
    endif;
    ?>
</div>

<?php
if ( $enable_schema && $faq_items && is_array( $faq_items ) && count( $faq_items ) > 0 ) :
    $faq_schema = array(
        "@context"   => "https://schema.org",
        "@type"      => "FAQPage",
        "mainEntity" => array(),
    );

    foreach ( $faq_items as $item ) {
        $faq_schema['mainEntity'][] = array(
            "@type"          => "Question",
            "name"           => $item['acf_faq_question'],
            "acceptedAnswer" => array(
                "@type" => "Answer",
                "text"  => strip_tags( do_shortcode( $item['acf_faq_answer'] ) ),
            ),
        );
    }
    ?>
    <script type="application/ld+json">
        <?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
    </script>
<?php endif; ?>
