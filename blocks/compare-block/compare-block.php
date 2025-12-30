<?php
if (!defined('ABSPATH')) exit;

$columns = get_field('comp_columns') ?: 2; // Default to 2 columns
$cta_text = get_field('comp_cta_text');
$cta_url = get_field('comp_cta_url');
$cta_url_rel_tag = get_field('comp_cta_url_rel_tag');

$unique_id = 'acf-compare-' . ( $block['id'] ?? uniqid() );

// Detect style variation from className
$className = isset( $block['className'] ) ? $block['className'] : '';
$style_variation = '';
if ( strpos( $className, 'is-style-dark' ) !== false ) {
	$style_variation = 'dark';
} elseif ( strpos( $className, 'is-style-card' ) !== false ) {
	$style_variation = 'card';
} elseif ( strpos( $className, 'is-style-minimal' ) !== false ) {
	$style_variation = 'minimal';
} elseif ( strpos( $className, 'is-style-bordered' ) !== false ) {
	$style_variation = 'bordered';
}

echo '<div id="' . esc_attr($unique_id) . '" class="acf-compare-container acf-grid-' . esc_attr($columns) . '">';

// Output inline styles for style variations
if ( $style_variation === 'dark' ) :
    ob_start();
    ?>
    #<?php echo esc_attr( $unique_id ); ?>.is-style-dark {
        background: #1a1a2e;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-compare-column {
        background: #2d2d44;
        border-color: #3d3d5c;
        color: #ffffff;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-compare-column:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-compare-text {
        color: #b0b0b0;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-compare-column ul li {
        color: #e0e0e0;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-compare-column ul li:before {
        color: #4ade80;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-compare-cta .button {
        background: #ffd700;
        color: #1a1a2e;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-dark .acf-compare-cta .button:hover {
        background: #ffed4a;
    }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php
elseif ( $style_variation === 'card' ) :
    ob_start();
    ?>
    #<?php echo esc_attr( $unique_id ); ?>.is-style-card {
        background: transparent;
        box-shadow: none;
        padding: 0;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-compare-column {
        background: #ffffff;
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-compare-column:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-med-title {
        border-radius: 8px;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-compare-cta .button {
        border-radius: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 1rem 2rem;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-compare-cta .button:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }
    @media (max-width: 576px) {
        #<?php echo esc_attr( $unique_id ); ?>.is-style-card .acf-compare-column {
            padding: 1.5rem;
        }
    }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php
elseif ( $style_variation === 'minimal' ) :
    ob_start();
    ?>
    #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal {
        background: transparent;
        box-shadow: none;
        border-radius: 0;
        padding: 0;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-compare-column {
        background: transparent;
        border: none;
        border-radius: 0;
        padding: 1rem 0;
        border-bottom: 1px solid #e0e0e0;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-compare-column:hover {
        transform: none;
        box-shadow: none;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-med-title {
        background: transparent !important;
        padding: 0;
        text-align: left;
        margin-bottom: 0.75rem;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-compare-cta .button {
        background: transparent;
        color: #0073aa;
        padding: 0;
        text-decoration: underline;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-minimal .acf-compare-cta .button:hover {
        background: transparent;
        color: #005177;
    }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php
elseif ( $style_variation === 'bordered' ) :
    ob_start();
    ?>
    #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered {
        background: #ffffff;
        border: 3px solid #1a1a1a;
        border-radius: 0;
        box-shadow: none;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-compare-column {
        background: #ffffff;
        border: 2px solid #1a1a1a;
        border-radius: 0;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-compare-column:hover {
        transform: none;
        box-shadow: 4px 4px 0 #1a1a1a;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-med-title {
        border-radius: 0;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-compare-cta .button {
        background: #1a1a1a;
        border-radius: 0;
    }
    #<?php echo esc_attr( $unique_id ); ?>.is-style-bordered .acf-compare-cta .button:hover {
        background: #333333;
    }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>
    <?php
endif;

if (have_rows('comp_columns_data')):
    while (have_rows('comp_columns_data')): the_row();
        $title = get_sub_field('comp_title');
        $title_bg = get_sub_field('comp_title_bg');
        $title_color = get_sub_field('comp_title_color');
        $text = get_sub_field('comp_text');
        $column_style = get_sub_field('comp_column_style');
        $list_class = get_sub_field('comp_list_class');

        echo '<div class="acf-compare-column" style="' . esc_attr($column_style) . '">';
        if ($title) {
            echo '<h3 class="acf-med-title" style="background:' . esc_attr($title_bg) . '; color:' . esc_attr($title_color) . ';">' . esc_html($title) . '</h3>';
        }
        if ($text) {
            echo '<div class="acf-compare-text">' . wp_kses_post($text) . '</div>';
        }
        if (have_rows('comp_repeater_list')):
            echo '<ul class="' . esc_attr($list_class) . '">';
            while (have_rows('comp_repeater_list')): the_row();
                echo '<li>' . esc_html(get_sub_field('comp_list_item')) . '</li>';
            endwhile;
            echo '</ul>';
        endif;
        echo '</div>';
    endwhile;
endif;

if ($cta_text && $cta_url) {
    echo '<div class="acf-compare-cta"><a href="' . esc_url($cta_url) . '" class="button" rel="' . esc_attr($cta_url_rel_tag) . '">' . esc_html($cta_text) . '</a></div>';
}
echo '</div>';