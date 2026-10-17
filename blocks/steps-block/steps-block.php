<?php
/**
 * Steps Block Template.
 *
 * @param array      $block      Block settings and attributes.
 * @param string     $content    Inner HTML. Unused.
 * @param bool       $is_preview True during editor preview.
 * @param int|string $post_id    Post ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$heading = acf_blocks_get_field( 'acf_steps_heading', $block );
$tag     = acf_blocks_choice(
    acf_blocks_get_field( 'acf_steps_heading_tag', $block ),
    array( 'h1', 'h2', 'h3', 'p' ),
    'h2'
);
$intro = acf_blocks_get_field( 'acf_steps_intro', $block );
$steps = acf_blocks_get_repeater(
    'acf_steps_items',
    array( 'step_icon', 'step_title', 'step_text' ),
    $block
);

$classes = array( 'acf-steps' );
if ( ! empty( $block['align'] ) ) {
    $classes[] = 'align' . $block['align'];
}
if ( ! empty( $block['className'] ) ) {
    $classes[] = $block['className'];
}
$anchor = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
    <?php if ( $heading || $intro ) : ?>
        <header class="acf-steps__header">
            <?php if ( $heading ) : ?>
                <<?php echo $tag; ?> class="acf-steps__heading"><?php echo esc_html( $heading ); ?></<?php echo $tag; ?>>
            <?php endif; ?>
            <?php if ( $intro ) : ?>
                <p class="acf-steps__intro"><?php echo esc_html( $intro ); ?></p>
            <?php endif; ?>
        </header>
    <?php endif; ?>

    <?php if ( ! empty( $steps ) ) : ?>
        <ol class="acf-steps__list">
            <?php $step_number = 0; ?>
            <?php foreach ( $steps as $step ) :
                $title = $step['step_title'] ?? '';
                $text  = $step['step_text'] ?? '';
                $icon  = acf_blocks_get_icon_markup( $step['step_icon'] ?? '' );
                if ( '' === trim( (string) $title ) && '' === trim( (string) $text ) && '' === $icon ) {
                    continue;
                }
                $step_number++;
                ?>
                <li class="acf-steps__item">
                    <span class="acf-steps__index" aria-hidden="true"><?php echo esc_html( (string) $step_number ); ?></span>
                    <div class="acf-steps__body">
                        <?php if ( $icon ) : ?>
                            <span class="acf-steps__icon"><?php echo wp_kses_post( $icon ); ?></span>
                        <?php endif; ?>
                        <?php if ( $title ) : ?>
                            <h3 class="acf-steps__title"><?php echo esc_html( $title ); ?></h3>
                        <?php endif; ?>
                        <?php if ( $text ) : ?>
                            <div class="acf-steps__text"><?php echo nl2br( wp_kses_post( $text ) ); ?></div>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
    <?php elseif ( ! empty( $is_preview ) ) : ?>
        <p class="acf-steps__empty"><?php esc_html_e( 'Add a step to build the sequence.', 'acf-blocks' ); ?></p>
    <?php endif; ?>
</section>
