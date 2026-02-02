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

$className = $block['className'] ?? '';

$stats_items = acf_blocks_get_repeater( 'acf_stats_items', [ 'acf_stat_number', 'acf_stat_label', 'acf_stat_prefix', 'acf_stat_suffix', 'acf_stat_icon' ], $block );
$layout      = acf_blocks_get_field( 'acf_stats_layout', $block );
$enable_animation = acf_blocks_get_field( 'acf_stats_enable_animation', $block );

// Detect style variation
$style_variation = '';
if ( strpos( $className, 'is-style-card' ) !== false ) {
    $style_variation = 'card';
} elseif ( strpos( $className, 'is-style-dark' ) !== false ) {
    $style_variation = 'dark';
} elseif ( strpos( $className, 'is-style-minimal' ) !== false ) {
    $style_variation = 'minimal';
} elseif ( strpos( $className, 'is-style-bordered' ) !== false ) {
    $style_variation = 'bordered';
} elseif ( strpos( $className, 'is-style-gradient' ) !== false ) {
    $style_variation = 'gradient';
}

$custom_class = acf_blocks_get_field( 'acf_stats_class', $block );
$custom_class = $custom_class ? ' ' . esc_attr( $custom_class ) : '';

if ( $className ) {
    $custom_class .= ' ' . esc_attr( $className );
}

$inline_style = acf_blocks_get_field( 'acf_stats_inline', $block );
$inline_style_attr = $inline_style ? ' style="' . esc_attr( $inline_style ) . '"' : '';

$layout_class = $layout ? ' acf-stats-' . esc_attr( $layout ) : ' acf-stats-horizontal';
$animation_class = $enable_animation ? ' acf-has-animation' : '';
?>

<div id="<?php echo esc_attr( $unique_id ); ?>" class="acf-stats-block<?php echo $layout_class . $animation_class . $custom_class; ?>"<?php echo $inline_style_attr; ?>>
    <?php if ( $style_variation === 'card' ): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #eee;
            padding: 2rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php elseif ( $style_variation === 'dark' ): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block {
            background: #1a1a2e;
            border-radius: 12px;
            padding: 3rem 2rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item {
            background: #2d2d44;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item:hover {
            background: #3d3d5c;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-number {
            color: #ffd700;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-prefix,
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-suffix {
            color: #a0a0a0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-label {
            color: #e0e0e0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-icon {
            color: #ffd700;
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php elseif ( $style_variation === 'minimal' ): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block {
            padding: 2rem 0;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item {
            background: transparent;
            border-radius: 0;
            border-bottom: 2px solid #e0e0e0;
            padding: 1.5rem 1rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item:hover {
            background: transparent;
            box-shadow: none;
            transform: none;
            border-bottom-color: #007bff;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-number {
            font-size: 2.5rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-label {
            text-transform: none;
            letter-spacing: 0;
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php elseif ( $style_variation === 'bordered' ): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item {
            background: #fff;
            border: 3px solid #1a1a1a;
            border-radius: 0;
            padding: 2rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item:hover {
            box-shadow: 6px 6px 0 #1a1a1a;
            transform: translate(-3px, -3px);
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-number {
            color: #1a1a1a;
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php elseif ( $style_variation === 'gradient' ): ?>
    <?php ob_start(); ?>
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            color: #fff;
            padding: 2rem;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-number {
            color: #fff;
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-prefix,
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-suffix {
            color: rgba(255, 255, 255, 0.8);
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-label {
            color: rgba(255, 255, 255, 0.9);
        }
        #<?php echo esc_attr( $unique_id ); ?>.acf-stats-block .acf-stat-icon {
            color: #fff;
            opacity: 0.9;
        }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php endif; ?>
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
