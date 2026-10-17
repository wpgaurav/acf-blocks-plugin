<?php
/**
 * Pricing Block Template.
 *
 * @param array      $block      Block settings and attributes.
 * @param string     $content    Inner HTML. Unused.
 * @param bool       $is_preview True during editor preview.
 * @param int|string $post_id    Post ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$heading = acf_blocks_get_field( 'acf_pricing_heading', $block );
$tag     = acf_blocks_choice(
    acf_blocks_get_field( 'acf_pricing_heading_tag', $block ),
    array( 'h1', 'h2', 'h3', 'p' ),
    'h2'
);
$intro   = acf_blocks_get_field( 'acf_pricing_intro', $block );
$columns = acf_blocks_choice( acf_blocks_get_field( 'acf_pricing_columns', $block ), array( '2', '3', '4' ), '3' );
$new_tab = acf_blocks_get_field( 'acf_pricing_new_tab', $block );
$plans   = acf_blocks_get_repeater(
    'acf_pricing_plans',
    array(
        'plan_name',
        'plan_badge',
        'plan_price',
        'plan_period',
        'plan_text',
        'plan_features',
        'plan_button_text',
        'plan_button_url',
        'plan_highlight' => 'bool',
    ),
    $block
);

$plans = array_values( array_filter( $plans, static function ( $plan ) {
    foreach ( array( 'plan_name', 'plan_badge', 'plan_price', 'plan_text', 'plan_features', 'plan_button_text', 'plan_button_url' ) as $key ) {
        if ( '' !== trim( (string) ( $plan[ $key ] ?? '' ) ) ) {
            return true;
        }
    }
    return false;
} ) );

$column_count = (int) $columns;
if ( $plans && count( $plans ) < $column_count ) {
    $column_count = count( $plans );
}

$classes = array( 'acf-pricing', 'acf-pricing--cols-' . $column_count );
$align   = acf_blocks_choice( $block['align'] ?? '', array( 'left', 'center', 'right', 'wide', 'full' ), '' );
if ( $align ) {
    $classes[] = 'align' . $align;
}
if ( ! empty( $block['className'] ) ) {
    $classes[] = $block['className'];
}
$anchor = ! empty( $block['anchor'] ) ? ' id="' . esc_attr( $block['anchor'] ) . '"' : '';

if ( ! $heading && ! $intro && ! $plans && empty( $is_preview ) ) {
    return;
}

$plan_tag = 'h3';
if ( ! $heading || 'h1' === $tag || 'p' === $tag ) {
    $plan_tag = 'h2';
} elseif ( 'h3' === $tag ) {
    $plan_tag = 'h4';
}
?>

<section<?php echo $anchor; ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
    <?php if ( $heading || $intro ) : ?>
        <header class="acf-pricing__header">
            <?php if ( $heading ) : ?>
                <<?php echo $tag; ?> class="acf-pricing__heading"><?php echo esc_html( $heading ); ?></<?php echo $tag; ?>>
            <?php endif; ?>
            <?php if ( $intro ) : ?>
                <p class="acf-pricing__intro"><?php echo esc_html( $intro ); ?></p>
            <?php endif; ?>
        </header>
    <?php endif; ?>

    <?php if ( ! empty( $plans ) ) : ?>
        <div class="acf-pricing__grid">
            <?php foreach ( $plans as $plan ) :
                $features = acf_blocks_lines( $plan['plan_features'] ?? '' );
                $featured = ! empty( $plan['plan_highlight'] );
                $name     = $plan['plan_name'] ?? '';
                $button   = $plan['plan_button_text'] ?? '';
                $url      = esc_url( $plan['plan_button_url'] ?? '' );
                ?>
                <article class="acf-pricing__plan<?php echo $featured ? ' is-featured' : ''; ?>">
                    <?php if ( ! empty( $plan['plan_badge'] ) ) : ?>
                        <p class="acf-pricing__badge"><?php echo esc_html( $plan['plan_badge'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $name ) : ?>
                        <<?php echo $plan_tag; ?> class="acf-pricing__name"><?php echo esc_html( $name ); ?></<?php echo $plan_tag; ?>>
                    <?php endif; ?>
                    <?php if ( ! empty( $plan['plan_price'] ) ) : ?>
                        <p class="acf-pricing__price">
                            <span class="acf-pricing__amount"><?php echo esc_html( $plan['plan_price'] ); ?></span>
                            <?php if ( ! empty( $plan['plan_period'] ) ) : ?>
                                <span class="acf-pricing__period"><?php echo esc_html( $plan['plan_period'] ); ?></span>
                            <?php endif; ?>
                        </p>
                    <?php endif; ?>
                    <?php if ( ! empty( $plan['plan_text'] ) ) : ?>
                        <p class="acf-pricing__text"><?php echo esc_html( $plan['plan_text'] ); ?></p>
                    <?php endif; ?>
                    <?php if ( $features ) : ?>
                        <ul class="acf-pricing__features">
                            <?php foreach ( $features as $feature ) : ?>
                                <li><?php echo esc_html( $feature ); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if ( $button && $url ) : ?>
                        <a class="acf-pricing__button" href="<?php echo esc_url( $url ); ?>"<?php echo acf_blocks_link_target_attrs( $new_tab ); ?>>
                            <?php echo esc_html( $button ); ?>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
    <?php elseif ( ! empty( $is_preview ) ) : ?>
        <p class="acf-pricing__empty"><?php esc_html_e( 'Add a plan to show pricing.', 'acf-blocks' ); ?></p>
    <?php endif; ?>
</section>
