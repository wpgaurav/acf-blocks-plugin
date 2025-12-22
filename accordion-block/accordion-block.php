<?php
/**
 * Accordion Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

// Global array for footer scripts.
global $accordion_unique_ids;
if ( ! isset( $accordion_unique_ids ) ) {
    $accordion_unique_ids = array();
    if ( ! function_exists( 'output_accordion_footer_scripts' ) ) {
        function output_accordion_footer_scripts() {
            global $accordion_unique_ids;
            if ( ! empty( $accordion_unique_ids ) ) {
                foreach ( $accordion_unique_ids as $id ) {
                    echo '<script>MD.accordion("' . esc_js( $id ) . '");</script>' . "\n";
                }
            }
        }
    }
    add_action( 'wp_footer', 'output_accordion_footer_scripts', 999 );
}

$unique_id = 'accordion_' . uniqid();
$accordion_unique_ids[] = $unique_id;

$groups            = get_field( 'acf_accord_groups' );
$enable_faq_schema = get_field( 'acf_accord_enable_faq_schema' ) ? true : false;

$custom_class = get_field( 'acf_accordion_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_accordion_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="accordion<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    if ( $groups && is_array( $groups ) && count( $groups ) > 0 ) :
        $index = 1;
        foreach ( $groups as $group ) :
            $is_active = ( $index === 1 );
            $aria_expanded = $is_active ? 'true' : 'false';
            $active_class = $is_active ? ' active' : '';
            $title_id = esc_attr( $unique_id . '_' . $index . '_title' );
            $panel_id = esc_attr( $unique_id . '_' . $index . '_panel' );
            ?>
            <div id="<?php echo esc_attr( $unique_id . '_' . $index ); ?>" class="accordion-group<?php echo $active_class; ?>">
                <div class="accordion-title" 
                     role="button" 
                     tabindex="0" 
                     aria-expanded="<?php echo $aria_expanded; ?>" 
                     aria-controls="<?php echo $panel_id; ?>"
                     id="<?php echo $title_id; ?>"
                     data-accordion="<?php echo esc_attr( $index ); ?>">
                    <?php echo do_shortcode( $group['acf_accord_group_title'] ); ?>
                </div>
                <div class="accordion-content"
                     id="<?php echo $panel_id; ?>"
                     role="region"
                     aria-labelledby="<?php echo $title_id; ?>"
                     <?php echo $is_active ? '' : 'hidden'; ?>>
                    <?php 
                    // Use wpautop() to preserve line breaks in the accordion content.
                    echo wpautop( do_shortcode( $group['acf_accord_group_content'] ) ); 
                    ?>
                </div>
            </div>
            <?php
            $index++;
        endforeach;
    else :
        if ( $is_preview ) {
            echo '<p><em>No accordion groups added. Please add some groups.</em></p>';
        }
    endif;
    ?>
</div>

<?php
if ( $enable_faq_schema ) :
    $faq_schema = array(
        "@context"   => "https://schema.org",
        "@type"      => "FAQPage",
        "mainEntity" => array(),
    );

    if ( $groups && is_array( $groups ) && count( $groups ) > 0 ) {
        foreach ( $groups as $group ) {
            $faq_schema['mainEntity'][] = array(
                "@type"          => "Question",
                "name"           => do_shortcode( $group['acf_accord_group_title'] ),
                "acceptedAnswer" => array(
                    "@type" => "Answer",
                    "text"  => do_shortcode( $group['acf_accord_group_content'] ),
                ),
            );
        }
    }
    ?>
    <script type="application/ld+json">
        <?php echo wp_json_encode( $faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
    </script>
<?php endif; ?>