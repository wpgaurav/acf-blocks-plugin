<?php
/**
 * Trust Bar Block Template.
 *
 * @param array      $block      Block settings and attributes.
 * @param string     $content    Inner HTML. Unused.
 * @param bool       $is_preview True during editor preview.
 * @param int|string $post_id    Post ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$items = acf_blocks_get_repeater(
    'acf_trust_items',
    array( 'trust_icon', 'trust_title', 'trust_text' ),
    $block
);

$classes = array( 'acf-trust-bar' );
if ( ! empty( $block['align'] ) ) {
    $classes[] = 'align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes[] = $block['className'];
}
$anchor = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
    <?php if ( ! empty( $items ) ) : ?>
        <ul class="acf-trust-bar__list">
            <?php foreach ( $items as $item ) :
                $title = $item['trust_title'] ?? '';
                $text  = $item['trust_text'] ?? '';
                $icon  = acf_blocks_get_icon_markup( $item['trust_icon'] ?? '' );
                if ( '' === trim( (string) $title ) && '' === trim( (string) $text ) && '' === $icon ) {
                    continue;
                }
                ?>
                <li class="acf-trust-bar__item">
                    <?php if ( $icon ) : ?>
                        <span class="acf-trust-bar__icon"><?php echo wp_kses_post( $icon ); ?></span>
                    <?php endif; ?>
                    <span class="acf-trust-bar__copy">
                        <?php if ( $title ) : ?>
                            <span class="acf-trust-bar__title"><?php echo esc_html( $title ); ?></span>
                        <?php endif; ?>
                        <?php if ( $text ) : ?>
                            <span class="acf-trust-bar__text"><?php echo esc_html( $text ); ?></span>
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ( ! empty( $is_preview ) ) : ?>
        <p class="acf-trust-bar__empty"><?php esc_html_e( 'Add a promise to show the trust bar.', 'acf-blocks' ); ?></p>
    <?php endif; ?>
</section>
