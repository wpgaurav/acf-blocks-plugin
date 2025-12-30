<?php
/**
 * Pros & Cons Block Template.
 *
 * @param array $block The block settings and attributes.
 */

// Block attributes
$align = $block['align'] ?? '';
$anchor = $block['anchor'] ?? '';
$className = $block['className'] ?? '';

// Content fields
$show_first = get_field('pc_show_first') ?: 'negative';
$cons_title = get_field('pc_cons_title') ?: 'Cons';
$cons_list = get_field('pc_cons_list');
$pros_title = get_field('pc_pros_title') ?: 'Pros';
$pros_list = get_field('pc_pros_list');

// Color fields with defaults
$neg_bg = get_field('pc_neg_bg_color') ?: '#fef2f2';
$neg_border = get_field('pc_neg_border_color') ?: '#dc2626';
$neg_title_color = get_field('pc_neg_title_color') ?: '#dc2626';
$neg_icon_color = get_field('pc_neg_icon_color') ?: '#dc2626';

$pos_bg = get_field('pc_pos_bg_color') ?: '#f0fdf4';
$pos_border = get_field('pc_pos_border_color') ?: '#16a34a';
$pos_title_color = get_field('pc_pos_title_color') ?: '#16a34a';
$pos_icon_color = get_field('pc_pos_icon_color') ?: '#16a34a';

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

// Generate unique ID for scoped inline styles
$block_id = 'pc-' . uniqid();
?>

<div <?php echo $anchor_attr; ?> class="<?php echo esc_attr(implode(' ', $wrapper_classes)); ?>" data-pc-id="<?php echo esc_attr($block_id); ?>">
    <?php
    ob_start();
    ?>
    [data-pc-id="<?php echo esc_attr($block_id); ?>"] .acf-pros-cons__negative {
        background-color: <?php echo esc_attr($neg_bg); ?>;
        border-left-color: <?php echo esc_attr($neg_border); ?>;
    }
    [data-pc-id="<?php echo esc_attr($block_id); ?>"] .acf-pros-cons__negative .acf-pros-cons__title {
        color: <?php echo esc_attr($neg_title_color); ?>;
    }
    [data-pc-id="<?php echo esc_attr($block_id); ?>"] .acf-pros-cons__negative .acf-pros-cons__icon {
        color: <?php echo esc_attr($neg_icon_color); ?>;
        background-color: <?php echo esc_attr($neg_icon_color); ?>20;
    }
    [data-pc-id="<?php echo esc_attr($block_id); ?>"] .acf-pros-cons__positive {
        background-color: <?php echo esc_attr($pos_bg); ?>;
        border-left-color: <?php echo esc_attr($pos_border); ?>;
    }
    [data-pc-id="<?php echo esc_attr($block_id); ?>"] .acf-pros-cons__positive .acf-pros-cons__title {
        color: <?php echo esc_attr($pos_title_color); ?>;
    }
    [data-pc-id="<?php echo esc_attr($block_id); ?>"] .acf-pros-cons__positive .acf-pros-cons__icon {
        color: <?php echo esc_attr($pos_icon_color); ?>;
        background-color: <?php echo esc_attr($pos_icon_color); ?>20;
    }
    <?php
    $css = ob_get_clean();
    echo '<style>' . acf_blocks_minify_css( $css ) . '</style>';
    ?>

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
        echo $positive_html . $negative_html;
    } else {
        echo $negative_html . $positive_html;
    }
    ?>
</div>

<?php
/**
 * Process list HTML to add icons to list items.
 */
function acf_pros_cons_process_list($html, $type = 'positive') {
    if (empty($html)) {
        return '';
    }

    // SVG icons (inline for performance - no extra HTTP requests)
    $check_icon = '<span class="acf-pros-cons__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span>';
    $x_icon = '<span class="acf-pros-cons__icon" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></span>';

    $icon = ($type === 'positive') ? $check_icon : $x_icon;

    // Add icon to each list item
    $html = preg_replace('/<li([^>]*)>/', '<li$1>' . $icon . '<span class="acf-pros-cons__item-content">', $html);
    $html = str_replace('</li>', '</span></li>', $html);

    return $html;
}
