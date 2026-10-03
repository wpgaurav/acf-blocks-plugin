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

// Color fields: only colors an editor chose become inline custom properties.
// Unset colors fall back to theme tokens in pros-cons.css, which follow dark mode.
$style_vars = acf_pros_cons_style_vars( array(
    '--pc-neg-bg'     => acf_blocks_get_field('pc_neg_bg_color', $block),
    '--pc-neg-border' => acf_blocks_get_field('pc_neg_border_color', $block),
    '--pc-neg-title'  => acf_blocks_get_field('pc_neg_title_color', $block),
    '--pc-neg-icon'   => acf_blocks_get_field('pc_neg_icon_color', $block),
    '--pc-pos-bg'     => acf_blocks_get_field('pc_pos_bg_color', $block),
    '--pc-pos-border' => acf_blocks_get_field('pc_pos_border_color', $block),
    '--pc-pos-title'  => acf_blocks_get_field('pc_pos_title_color', $block),
    '--pc-pos-icon'   => acf_blocks_get_field('pc_pos_icon_color', $block),
) );

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
$style_attr = '' !== $style_vars ? ' style="' . esc_attr( $style_vars ) . '"' : '';
?>

<div <?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" data-acf-block="pros-cons"<?php echo $style_attr; ?>>

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
