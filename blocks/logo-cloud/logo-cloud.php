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

$logo_items = array();
foreach ( $logos as $logo ) {
    $name     = $logo['logo_name'] ?? '';
    $resolved = acf_blocks_resolve_image( $logo['logo_image'] ?? '', $name, 'medium' );
    $raw_src  = $resolved['src'];
    if ( ! $raw_src && ! empty( $logo['logo_image_url'] ) ) {
        $raw_src = $logo['logo_image_url'];
    }
    $src  = $raw_src ? esc_url( $raw_src ) : '';
    $link = esc_url( $logo['logo_link'] ?? '' );
    if ( ! $src && '' === trim( (string) $name ) ) {
        continue;
    }
    $logo_items[] = array(
        'name'   => $name,
        'src'    => $src,
        'alt'    => $resolved['alt'] ?: $name,
        'link'   => $link,
        'srcset' => ( $src && $src === esc_url( $resolved['src'] ) ) ? $resolved['srcset'] : '',
        'sizes'  => $resolved['sizes'],
    );
}

if ( ! $heading && ! $logo_items && empty( $is_preview ) ) {
    return;
}

$classes = array( 'acf-logo-cloud' );
$align   = acf_blocks_choice( $block['align'] ?? '', array( 'left', 'center', 'right', 'wide', 'full' ), '' );
if ( $align ) {
    $classes[] = 'align' . $align;
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

    <?php if ( $logo_items ) : ?>
        <ul class="acf-logo-cloud__list">
            <?php foreach ( $logo_items as $logo ) : ?>
                <li class="acf-logo-cloud__item">
                    <?php if ( $logo['link'] ) : ?>
                        <a class="acf-logo-cloud__link" href="<?php echo esc_url( $logo['link'] ); ?>"<?php echo acf_blocks_link_target_attrs( $new_tab ); ?>>
                    <?php endif; ?>
                    <?php if ( $logo['src'] ) : ?>
                        <img class="acf-logo-cloud__logo" src="<?php echo esc_url( $logo['src'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>"<?php if ( ! empty( $logo['srcset'] ) ) : ?> srcset="<?php echo esc_attr( $logo['srcset'] ); ?>" sizes="<?php echo esc_attr( $logo['sizes'] ); ?>"<?php endif; ?> loading="lazy" decoding="async" />
                    <?php else : ?>
                        <span class="acf-logo-cloud__name"><?php echo esc_html( $logo['name'] ); ?></span>
                    <?php endif; ?>
                    <?php if ( $logo['link'] ) : ?>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ( ! empty( $is_preview ) ) : ?>
        <p class="acf-logo-cloud__empty"><?php esc_html_e( 'Add a logo to show the cloud.', 'acf-blocks' ); ?></p>
    <?php endif; ?>
</section>
