<?php
/**
 * Stats Block Template.
 *
 * Renders animated counter stats. The count-up animation is handled
 * by stats-counter.js (registered via block.json viewScript), which
 * uses IntersectionObserver with zero theme/framework dependencies.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$className   = $block['className'] ?? '';
$anchor      = $block['anchor'] ?? '';
$anchor_attr = $anchor ? ' id="' . esc_attr( $anchor ) . '"' : '';

$stats_items      = acf_blocks_get_repeater( 'acf_stats_items', [ 'acf_stat_number', 'acf_stat_label', 'acf_stat_prefix', 'acf_stat_suffix', 'acf_stat_icon' ], $block );
$layout           = acf_blocks_get_field( 'acf_stats_layout', $block );
$enable_animation = acf_blocks_get_field( 'acf_stats_enable_animation', $block );

$custom_class = acf_blocks_get_field( 'acf_stats_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

if ( $className ) {
    $custom_class .= ' ' . esc_attr( $className );
}

$inline_style      = acf_blocks_get_field( 'acf_stats_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$layout_class    = $layout ? ' acf-stats-' . esc_attr( $layout ) : ' acf-stats-horizontal';
$animation_class = $enable_animation ? ' acf-has-animation' : '';
?>

<div<?php echo $anchor_attr; ?> class="acf-stats-block<?php echo $layout_class . $animation_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php
    if ( $stats_items && is_array( $stats_items ) && count( $stats_items ) > 0 ) :
        foreach ( $stats_items as $index => $stat ) :
            $number = $stat['acf_stat_number'];
            $label  = $stat['acf_stat_label'];
            $prefix = $stat['acf_stat_prefix'];
            $suffix = $stat['acf_stat_suffix'];
            $icon   = $stat['acf_stat_icon'];
            ?>
            <div class="acf-stat-item">
                <?php if ( $icon ) : ?>
                    <div class="acf-stat-icon">
                        <?php
                        $icon_markup = function_exists( 'md_get_icon_markup' )
                            ? md_get_icon_markup( $icon )
                            : esc_html( $icon );
                        echo $icon_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        ?>
                    </div>
                <?php endif; ?>

                <div class="acf-stat-content">
                    <div class="acf-stat-number" data-target="<?php echo esc_attr( $number ); ?>">
                        <?php if ( $prefix ) : ?>
                            <span class="acf-stat-prefix"><?php echo esc_html( $prefix ); ?></span>
                        <?php endif; ?>
                        <span class="acf-stat-value"><?php echo esc_html( $number ); ?></span>
                        <?php if ( $suffix ) : ?>
                            <span class="acf-stat-suffix"><?php echo esc_html( $suffix ); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ( $label ) : ?>
                        <div class="acf-stat-label"><?php echo esc_html( $label ); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
        endforeach;
    else :
        if ( $is_preview ) {
            echo '<p><em>No stats added. Please add some stats items.</em></p>';
        }
    endif;
    ?>
</div>
