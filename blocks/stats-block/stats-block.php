<?php
/**
 * Stats Block Template.
 *
 * @param array       $block      Block settings and attributes.
 * @param string      $content    The block inner HTML (empty).
 * @param bool        $is_preview True during AJAX preview.
 * @param int|string  $post_id    The post ID.
 */

// Global array for footer scripts.
global $stats_unique_ids;
if ( ! isset( $stats_unique_ids ) ) {
    $stats_unique_ids = array();
    if ( ! function_exists( 'output_stats_footer_scripts' ) ) {
        function output_stats_footer_scripts() {
            global $stats_unique_ids;
            if ( ! empty( $stats_unique_ids ) ) {
                foreach ( $stats_unique_ids as $id ) {
                    echo '<script>MD.statsCounter("' . esc_js( $id ) . '");</script>' . "\n";
                }
            }
        }
    }
    add_action( 'wp_footer', 'output_stats_footer_scripts', 999 );
}

$unique_id = 'stats_' . uniqid();
$stats_unique_ids[] = $unique_id;

$stats_items = get_field( 'acf_stats_items' );
$layout      = get_field( 'acf_stats_layout' );
$enable_animation = get_field( 'acf_stats_enable_animation' );

$custom_class = get_field( 'acf_stats_class' );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

$inline_style = get_field( 'acf_stats_inline' );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$layout_class = $layout ? ' acf-stats-' . esc_attr( $layout ) : ' acf-stats-horizontal';
$animation_class = $enable_animation ? ' acf-has-animation' : '';
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="acf-stats-block<?php echo $layout_class . $animation_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
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
