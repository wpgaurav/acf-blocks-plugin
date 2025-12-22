<?php
/**
 * CTA Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

$heading     = get_field( 'acf_cta_heading' );
$description = get_field( 'acf_cta_description' );
$button_text = get_field( 'acf_cta_button_text' );
$button_url  = get_field( 'acf_cta_button_url' );
$button_style = get_field( 'acf_cta_button_style' );
$background_color = get_field( 'acf_cta_background_color' );
$text_color  = get_field( 'acf_cta_text_color' );

$custom_class = get_field( 'acf_cta_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_cta_inline' );
$style_parts = [];

if ( $background_color ) {
    $style_parts[] = 'background-color: ' . esc_attr( $background_color );
}

if ( $text_color ) {
    $style_parts[] = 'color: ' . esc_attr( $text_color );
}

if ( $inline_style ) {
    $style_parts[] = esc_attr( $inline_style );
}

$inline_style_attr = ! empty( $style_parts ) ? ' style="' . implode( '; ', $style_parts ) . '"' : '';
?>

<div class="acf-cta-block<?php echo $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <div class="acf-cta-content">
        <?php if ( $heading ) : ?>
            <h2 class="acf-cta-heading"><?php echo esc_html( $heading ); ?></h2>
        <?php endif; ?>

        <?php if ( $description ) : ?>
            <div class="acf-cta-description">
                <?php echo wpautop( esc_html( $description ) ); ?>
            </div>
        <?php endif; ?>

        <?php if ( $button_text && $button_url ) : ?>
            <a href="<?php echo esc_url( $button_url ); ?>" class="acf-cta-button <?php echo esc_attr( $button_style ); ?>">
                <?php echo esc_html( $button_text ); ?>
            </a>
        <?php endif; ?>
    </div>
</div>
