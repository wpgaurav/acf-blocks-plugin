<?php
if (!defined('ABSPATH')) exit;

$columns = get_field('comp_columns') ?: 2; // Default to 2 columns
$cta_text = get_field('comp_cta_text');
$cta_url = get_field('comp_cta_url');
$cta_url_rel_tag = get_field('comp_cta_url_rel_tag');

echo '<div class="compare-container grid-' . esc_attr($columns) . '">';

if (have_rows('comp_columns_data')):
    while (have_rows('comp_columns_data')): the_row();
        $title = get_sub_field('comp_title');
        $title_bg = get_sub_field('comp_title_bg');
        $title_color = get_sub_field('comp_title_color');
        $text = get_sub_field('comp_text');
        $column_style = get_sub_field('comp_column_style');
        $list_class = get_sub_field('comp_list_class');

        echo '<div class="compare-column" style="' . esc_attr($column_style) . '">';
        if ($title) {
            echo '<h3 class="med-title" style="background:' . esc_attr($title_bg) . '; color:' . esc_attr($title_color) . ';">' . esc_html($title) . '</h3>';
        }
        if ($text) {
            echo '<div class="compare-text">' . wp_kses_post($text) . '</div>';
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
    echo '<div class="compare-cta"><a href="' . esc_url($cta_url) . '" class="button" rel="' . esc_attr($cta_url_rel_tag) . '">' . esc_html($cta_text) . '</a></div>';
}
echo '</div>';