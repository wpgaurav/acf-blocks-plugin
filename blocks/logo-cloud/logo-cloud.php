<?php
/**
 * Logo Cloud Block Template.
 *
 * @param array      $block      Block settings and attributes.
 * @param string     $content    Inner HTML. Unused.
 * @param bool       $is_preview True during editor preview.
 * @param int|string $post_id    Post ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$heading = acf_blocks_get_field( 'acf_logo_heading', $block );
$new_tab = acf_blocks_get_field( 'acf_logo_new_tab', $block );
$logos   = acf_blocks_get_repeater(
    'acf_logo_items',
    array(
        'logo_image'     => 'image',
        'logo_image_url' => 'text',
        'logo_name'      => 'text',
        'logo_link'      => 'text',
    ),
    $block
);

$classes = array( 'acf-logo-cloud' );
if ( ! empty( $block['align'] ) ) {
    $classes[] = 'align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes[] = $block['className'];
}
$anchor = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
    <?php if ( $heading ) : ?>
        <p class="acf-logo-cloud__heading"><?php echo esc_html( $heading ); ?></p>
    <?php endif; ?>

    <?php if ( ! empty( $logos ) ) : ?>
        <ul class="acf-logo-cloud__list">
            <?php foreach ( $logos as $logo ) :
                $name     = $logo['logo_name'] ?? '';
                $resolved = acf_blocks_resolve_image( $logo['logo_image'] ?? '', $name, 'medium' );
                $src      = $resolved['src'];
                if ( ! $src && ! empty( $logo['logo_image_url'] ) ) {
                    $src = $logo['logo_image_url'];
                }
                $alt  = $resolved['alt'] ?: $name;
                $link = $logo['logo_link'] ?? '';
                if ( ! $src && ! $name ) {
                    continue;
                }
                ?>
                <li class="acf-logo-cloud__item">
                    <?php if ( $link ) : ?>
                        <a class="acf-logo-cloud__link" href="<?php echo esc_url( $link ); ?>"<?php echo acf_blocks_link_target_attrs( $new_tab ); ?>>
                    <?php endif; ?>
                    <?php if ( $src ) : ?>
                        <img class="acf-logo-cloud__logo" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( $alt ); ?>"<?php if ( ! empty( $resolved['srcset'] ) && $src === $resolved['src'] ) : ?> srcset="<?php echo esc_attr( $resolved['srcset'] ); ?>" sizes="<?php echo esc_attr( $resolved['sizes'] ); ?>"<?php endif; ?> loading="lazy" decoding="async" />
                    <?php else : ?>
                        <span class="acf-logo-cloud__name"><?php echo esc_html( $name ); ?></span>
                    <?php endif; ?>
                    <?php if ( $link ) : ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ( ! empty( $is_preview ) ) : ?>
        <p class="acf-logo-cloud__empty"><?php esc_html_e( 'Add a logo to show the cloud.', 'acf-blocks' ); ?></p>
    <?php endif; ?>
</section>
