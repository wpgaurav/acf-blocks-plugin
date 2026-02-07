<?php
if (!defined('ABSPATH')) exit;

$columns = acf_blocks_get_field('comp_columns', $block) ?: 2; // Default to 2 columns
$cta_text = acf_blocks_get_field('comp_cta_text', $block);
$cta_url = acf_blocks_get_field('comp_cta_url', $block);
$cta_url_rel_tag = acf_blocks_get_field('comp_cta_url_rel_tag', $block);

$unique_id = 'acf-compare-' . ( $block['id'] ?? uniqid() );

echo '<div id="' . esc_attr($unique_id) . '" class="acf-compare-container acf-grid-' . esc_attr($columns) . '">';

$comp_data = acf_blocks_get_repeater('comp_columns_data', [
    'comp_title',
    'comp_title_bg',
    'comp_title_color',
    'comp_text',
    'comp_column_style',
    'comp_list_class',
], $block);

if ( ! empty( $comp_data ) ) :
    foreach ( $comp_data as $col_index => $col ) :
        $title = $col['comp_title'] ?? '';
        $title_bg = $col['comp_title_bg'] ?? '';
        $title_color = $col['comp_title_color'] ?? '';
        $text = $col['comp_text'] ?? '';
        $column_style = $col['comp_column_style'] ?? '';
        $list_class = $col['comp_list_class'] ?? '';

        // Get nested repeater list items.
        $list_items = acf_blocks_get_nested_repeater(
            'comp_columns_data_' . $col_index . '_comp_repeater_list',
            [ 'comp_list_item' ],
            $block['data'] ?? array()
        );

        echo '<div class="acf-compare-column" style="' . esc_attr($column_style) . '">';
        if ($title) {
            echo '<h3 class="acf-med-title" style="background:' . esc_attr($title_bg) . '; color:' . esc_attr($title_color) . ';">' . esc_html($title) . '</h3>';
        }
        if ($text) {
            echo '<div class="acf-compare-text">' . wp_kses_post($text) . '</div>';
        }
        if ( ! empty( $list_items ) ) :
            echo '<ul class="' . esc_attr($list_class) . '">';
            foreach ( $list_items as $item ) :
                echo '<li>' . esc_html($item['comp_list_item'] ?? '') . '</li>';
            endforeach;
            echo '</ul>';
        endif;
        echo '</div>';
    endforeach;
endif;

if ($cta_text && $cta_url) {
    echo '<div class="acf-compare-cta"><a href="' . esc_url($cta_url) . '" class="button" rel="' . esc_attr($cta_url_rel_tag) . '">' . esc_html($cta_text) . '</a></div>';
}
echo '</div>';