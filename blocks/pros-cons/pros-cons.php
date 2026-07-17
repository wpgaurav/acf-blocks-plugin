<?php
/**
 * Pros & Cons Block Template.
 *
 * @param array $block The block settings and attributes.
 */

/**
 * Process list HTML to add icons to list items.
 * Defined before use, wrapped in function_exists to prevent redeclaration
 * when this template is included multiple times (e.g. REST API saves).
 */
if ( ! function_exists( 'acf_pros_cons_process_list' ) ) {
    function acf_pros_cons_process_list($html, $type = 'positive') {
        if (empty($html)) {
            return '';
        }

        // Unicode icons for pros/cons
        $check_icon = '<span class="acf-pros-cons__icon" aria-hidden="true">&#x2713;</span>';
        $x_icon = '<span class="acf-pros-cons__icon" aria-hidden="true">&#x2717;</span>';

        $icon = ($type === 'positive') ? $check_icon : $x_icon;

        // Add icon to each list item
        $html = preg_replace('/<li([^>]*)>/', '<li$1>' . $icon . '<span class="acf-pros-cons__item-content">', $html);
        $html = str_replace('</li>', '</span></li>', $html);

        return $html;
    }
}

// Block attributes
$align = $block['align'] ?? '';
$anchor = $block['anchor'] ?? '';
$className = $block['className'] ?? '';

// Content fields
$show_first = acf_blocks_get_field('pc_show_first', $block) ?: 'negative';
$cons_title = acf_blocks_get_field('pc_cons_title', $block) ?: 'Cons';
$cons_list = acf_blocks_get_field('pc_cons_list', $block);
$pros_title = acf_blocks_get_field('pc_pros_title', $block) ?: 'Pros';
$pros_list = acf_blocks_get_field('pc_pros_list', $block);

// Color fields with defaults
$neg_bg = acf_blocks_get_field('pc_neg_bg_color', $block) ?: '#fef2f2';
$neg_border = acf_blocks_get_field('pc_neg_border_color', $block) ?: '#dc2626';
$neg_title_color = acf_blocks_get_field('pc_neg_title_color', $block) ?: '#991b1b';
$neg_icon_color = acf_blocks_get_field('pc_neg_icon_color', $block) ?: '#dc2626';

$pos_bg = acf_blocks_get_field('pc_pos_bg_color', $block) ?: '#f0fdf4';
$pos_border = acf_blocks_get_field('pc_pos_border_color', $block) ?: '#16a34a';
$pos_title_color = acf_blocks_get_field('pc_pos_title_color', $block) ?: '#166534';
$pos_icon_color = acf_blocks_get_field('pc_pos_icon_color', $block) ?: '#16a34a';

// Build wrapper classes
$wrapper_classes = ['acf-pros-cons'];
if ($align) {
    $wrapper_classes[] = 'align' . $align;
}
if ($className) {
    $wrapper_classes[] = $className;
}
if ($show_first === 'positive') {
    $wrapper_classes[] = 'acf-pros-cons--pros-first';
}

$anchor_attr = $anchor ? ' id="' . esc_attr($anchor) . '"' : '';
$style_vars = sprintf(
    '--pc-neg-bg:%1$s;--pc-neg-border:%2$s;--pc-neg-title:%3$s;--pc-neg-icon:%4$s;--pc-pos-bg:%5$s;--pc-pos-border:%6$s;--pc-pos-title:%7$s;--pc-pos-icon:%8$s;',
    esc_attr( $neg_bg ), esc_attr( $neg_border ), esc_attr( $neg_title_color ), esc_attr( $neg_icon_color ),
    esc_attr( $pos_bg ), esc_attr( $pos_border ), esc_attr( $pos_title_color ), esc_attr( $pos_icon_color )
);
?>

<div <?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" data-acf-block="pros-cons" style="<?php echo esc_attr( $style_vars ); ?>">

    <?php
    // Negative side
    $negative_html = '<div class="acf-pros-cons__column acf-pros-cons__negative">';
    $negative_html .= '<h3 class="acf-pros-cons__title">' . esc_html($cons_title) . '</h3>';
    if ($cons_list) {
        $negative_html .= '<div class="acf-pros-cons__list acf-pros-cons__list--negative">' . acf_pros_cons_process_list($cons_list, 'negative') . '</div>';
    }
    $negative_html .= '</div>';

    // Positive side
    $positive_html = '<div class="acf-pros-cons__column acf-pros-cons__positive">';
    $positive_html .= '<h3 class="acf-pros-cons__title">' . esc_html($pros_title) . '</h3>';
    if ($pros_list) {
        $positive_html .= '<div class="acf-pros-cons__list acf-pros-cons__list--positive">' . acf_pros_cons_process_list($pros_list, 'positive') . '</div>';
    }
    $positive_html .= '</div>';

    // Output in correct order
    if ($show_first === 'positive') {
        echo wp_kses_post($positive_html . $negative_html);
    } else {
        echo wp_kses_post($negative_html . $positive_html);
    }
    ?>
</div>
